<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Copilot extends CI_Controller
{

	private $api_key;
	private $model;
	private $base_url;
	private $max_tokens;
	private $temperature;
	private $user_id;

	function __construct()
	{
		parent::__construct();

		// Cek login — copilot hanya untuk user yang sudah login
		if (!$this->session->userdata('logged_in')) {
			header('Content-Type: application/json');
			echo json_encode([
				'model'      => 'system',
				'created_at' => date('Y-m-d\TH:i:s.u\Z'),
				'message'    => [
					'role'    => 'assistant',
					'content' => 'Sesi Anda telah berakhir. Silakan login kembali.'
				]
			]);
			exit;
		}

		// Load config
		$this->config->load('copilot', TRUE);
		$this->api_key     = $this->config->item('openai_api_key', 'copilot');
		$this->model       = $this->config->item('openai_model', 'copilot');
		$this->base_url    = $this->config->item('openai_base_url', 'copilot');
		$this->max_tokens  = $this->config->item('openai_max_tokens', 'copilot');
		$this->temperature = $this->config->item('openai_temperature', 'copilot');
		$this->user_id     = $this->config->item('psda_user_id', 'copilot');

		// Load DateResolver library untuk parsing ekspresi tanggal natural language
		$this->load->library('DateResolver');
	}

	/**
	 * ================================================================
	 * ENDPOINT UTAMA: POST /copilot/chat
	 * ================================================================
	 */
	public function chat()
	{
		// Hanya terima POST
		if ($this->input->method() !== 'post') {
			header('Content-Type: application/json');
			$this->_respond('Method tidak diizinkan. Gunakan POST.');
			return;
		}

		// Parse request body
		$raw  = file_get_contents('php://input');
		$body = json_decode($raw, true);

		if (!$body || !isset($body['messages']) || !is_array($body['messages'])) {
			header('Content-Type: application/json');
			$this->_respond('Format request tidak valid.');
			return;
		}

		$uuid     = isset($body['uuid']) ? $body['uuid'] : null;
		$messages = $body['messages'];

		// Ambil pesan user terakhir
		$last_user_message = '';
		for ($i = count($messages) - 1; $i >= 0; $i--) {
			if ($messages[$i]['role'] === 'user') {
				$last_user_message = $messages[$i]['content'];
				break;
			}
		}

		// Simpan pertanyaan ke t_history
		try {
			if ($uuid && $last_user_message) {
				$this->db->insert('t_history', [
					'uuid'    => $uuid,
					'type'    => 'question',
					'content' => $last_user_message
				]);
			}
		} catch (Exception $e) {
			log_message('error', 'Copilot: gagal simpan history question - ' . $e->getMessage());
		}

		// ── Tentukan level konteks ──
		$needs_data = $this->_needs_data_context($last_user_message);

		// ── Build system prompt (kirim history untuk context inheritance) ──
		$system_prompt = $this->_build_system_prompt($last_user_message, $needs_data, $messages);

		// ── Susun messages untuk OpenAI ──
		$openai_messages   = [];
		$openai_messages[] = ['role' => 'system', 'content' => $system_prompt];

		$recent_messages = array_slice($messages, -20);
		foreach ($recent_messages as $msg) {
			$openai_messages[] = [
				'role'    => $msg['role'],
				'content' => $msg['content']
			];
		}

		// ── Setup SSE streaming headers ──
		header('Content-Type: text/event-stream');
		header('Cache-Control: no-cache');
		header('Connection: keep-alive');
		header('X-Accel-Buffering: no'); // nginx

		// Disable output buffering
		while (ob_get_level()) ob_end_flush();
		if (function_exists('apache_setenv')) {
			apache_setenv('no-gzip', '1');
		}
		ini_set('zlib.output_compression', '0');

		// ── Emit chart / CSV meta event jika dideteksi ──────────
		$loggers_light   = $this->_get_logger_names();
		$mentioned_meta  = $this->_find_mentioned_loggers($last_user_message, $loggers_light);
		$date_range_meta = $this->_parse_date_range($last_user_message);

		// Jika pesan saat ini tidak punya logger/date (mis: "tampilkan grafiknya"),
		// cari dari riwayat chat sebelumnya.
		$is_chart_req = $this->_detect_chart_request($last_user_message);
		$is_csv_req   = $this->_detect_csv_request($last_user_message);

		if (($is_chart_req || $is_csv_req) && (empty($mentioned_meta) || !$date_range_meta)) {
			[$mentioned_meta, $date_range_meta] = $this->_extract_context_from_history(
				$messages, $last_user_message, $loggers_light,
				$mentioned_meta, $date_range_meta
			);
		}

		// Deteksi granularitas dari pesan saat ini, fallback ke history
		$interval_min = $this->_parse_granularity($last_user_message);
		if ($interval_min === 1440) {
			foreach (array_reverse($messages) as $_hm) {
				if (!isset($_hm['role']) || $_hm['role'] !== 'user' || $_hm['content'] === $last_user_message) continue;
				$_g = $this->_parse_granularity($_hm['content']);
				if ($_g !== 1440) { $interval_min = $_g; break; }
			}
		}

		if (!empty($mentioned_meta) && $date_range_meta) {
			// Chart
			if ($is_chart_req) {
				$chart_payload = $this->_build_chart_payload($mentioned_meta, $date_range_meta, $last_user_message, $interval_min);
				if ($chart_payload) {
					echo 'data: ' . json_encode(['meta' => $chart_payload]) . "\n\n";
					if (ob_get_level()) ob_flush();
					flush();
				}
			}
			// CSV download
			if ($is_csv_req) {
				foreach ($mentioned_meta as $lg) {
					echo 'data: ' . json_encode(['meta' => [
						'type'      => 'csv_download',
						'id_logger' => $lg['id_logger'],
						'nama'      => $lg['nama_lokasi'],
						'awal'      => $date_range_meta['awal'],
						'akhir'     => $date_range_meta['akhir'],
					]]) . "\n\n";
					if (ob_get_level()) ob_flush();
					flush();
				}
			}
		}

		// ── Stream dari OpenAI ──
		$full_content = '';
		$error_occurred = false;

		$this->_stream_openai($openai_messages, function($token) use (&$full_content) {
			$full_content .= $token;
			echo "data: " . json_encode(['token' => $token]) . "\n\n";
			if (ob_get_level()) ob_flush();
			flush();
		}, function($error_data) use (&$error_occurred) {
			$error_occurred = true;
			echo "data: " . json_encode(['error' => $error_data]) . "\n\n";
			if (ob_get_level()) ob_flush();
			flush();
		});

		// ── Kirim event DONE ──
		if (!$error_occurred && $full_content) {
			// Simpan jawaban ke t_history
			try {
				if ($uuid) {
					$this->db->insert('t_history', [
						'uuid'    => $uuid,
						'type'    => 'answer',
						'content' => $full_content
					]);
				}
			} catch (Exception $e) {
				log_message('error', 'Copilot: gagal simpan history answer - ' . $e->getMessage());
			}
		}

		echo "data: [DONE]\n\n";
		if (ob_get_level()) ob_flush();
		flush();
	}


	/**
	 * ================================================================
	 * ENDPOINT: POST /copilot/transcribe
	 * ================================================================
	 * Terima file audio multipart → OpenAI Whisper-1 → return {text}
	 */
	public function transcribe()
	{
		header('Content-Type: application/json');

		if ($this->input->method() !== 'post') {
			echo json_encode(['error' => 'Method not allowed']);
			return;
		}

		if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
			echo json_encode(['error' => 'File audio tidak diterima']);
			return;
		}

		$file_path = $_FILES['audio']['tmp_name'];
		$file_name = isset($_FILES['audio']['name']) ? $_FILES['audio']['name'] : 'recording.webm';
		$mime_type = isset($_FILES['audio']['type']) ? $_FILES['audio']['type'] : 'audio/webm';

		$ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
		curl_setopt_array($ch, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => [
				'file'     => new CURLFile($file_path, $mime_type, $file_name),
				'model'    => 'whisper-1',
				'language' => 'id',
			],
			CURLOPT_HTTPHEADER     => [
				'Authorization: Bearer ' . $this->api_key,
			],
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => true,
		]);

		$result    = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$errno     = curl_errno($ch);
		$error     = curl_error($ch);
		curl_close($ch);

		if ($errno) {
			log_message('error', "Copilot transcribe cURL error [{$errno}]: {$error}");
			echo json_encode(['error' => 'Gagal terhubung ke layanan transkripsi.']);
			return;
		}

		if ($http_code !== 200) {
			log_message('error', "Copilot transcribe HTTP {$http_code}: {$result}");
			echo json_encode(['error' => 'Transkripsi gagal.']);
			return;
		}

		$data = json_decode($result, true);
		echo json_encode(['text' => isset($data['text']) ? trim($data['text']) : '']);
	}


	/**
	 * ================================================================
	 * ENDPOINT: GET /copilot/export_csv
	 * ================================================================
	 * Params: id_logger, awal (YYYY-MM-DD), akhir (YYYY-MM-DD)
	 * Output: file CSV download
	 */
	public function export_csv()
	{
		$id_logger = $this->input->get('id_logger');
		$awal      = $this->input->get('awal');
		$akhir     = $this->input->get('akhir');

		if (!$id_logger || !$awal || !$akhir) {
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Parameter id_logger, awal, akhir wajib diisi.']);
			return;
		}

		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $awal) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $akhir)) {
			header('Content-Type: application/json');
			echo json_encode(['error' => 'Format tanggal tidak valid. Gunakan YYYY-MM-DD.']);
			return;
		}

		// Ambil nama logger untuk filename
		try {
			$logger_info = $this->db
				->select('nama_lokasi')
				->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id', 'left')
				->where('code_logger', $id_logger)
				->get('t_logger')->row();
		} catch (Exception $e) {
			$logger_info = null;
		}

		$nama    = ($logger_info && $logger_info->nama_lokasi) ? $logger_info->nama_lokasi : 'Logger_' . $id_logger;
		$nama_fs = preg_replace('/[^a-zA-Z0-9]/', '_', $nama);
		$filename = "Data_{$nama_fs}_{$awal}_{$akhir}.csv";

		$data = $this->_get_data_range($id_logger, $awal, $akhir);

		header('Content-Type: text/csv; charset=UTF-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		// BOM untuk kompatibilitas Excel UTF-8
		echo "\xEF\xBB\xBF";

		if (empty($data)) {
			echo '"Tidak ada data pada rentang ' . $awal . ' s/d ' . $akhir . '"';
			return;
		}

		// Header row
		echo implode(',', array_map(function ($h) {
			return '"' . str_replace('"', '""', $h) . '"';
		}, array_keys($data[0]))) . "\n";

		// Data rows
		foreach ($data as $row) {
			echo implode(',', array_map(function ($v) {
				return '"' . str_replace('"', '""', (string) $v) . '"';
			}, array_values($row))) . "\n";
		}
	}


	/**
	 * ================================================================
	 * Helper: Kirim response error dengan format standar
	 * ================================================================
	 */
	private function _respond($content, $debug = null)
	{
		$response = [
			'model'      => $this->model ?? 'system',
			'created_at' => date('Y-m-d\TH:i:s.u\Z'),
			'message'    => [
				'role'    => 'assistant',
				'content' => $content
			]
		];
		if ($debug) {
			$response['debug'] = $debug;
		}
		echo json_encode($response);
	}


	/**
	 * ================================================================
	 * Helper: Extract logger & date context dari riwayat chat
	 * ================================================================
	 * Digunakan ketika pesan saat ini tidak menyebut logger atau tanggal
	 * (mis: "tampilkan grafiknya", "download csvnya").
	 * Scan N pesan user terakhir untuk mewarisi konteks.
	 *
	 * @return array  [mentioned_loggers, date_range]
	 */
	private function _extract_context_from_history($all_messages, $current_message, $loggers_light, $current_mentioned, $current_date_range)
	{
		$mentioned  = $current_mentioned;
		$date_range = $current_date_range;

		// Kumpulkan pesan user kecuali yang sedang diproses, urutkan terbaru dulu
		$user_msgs = [];
		foreach (array_reverse($all_messages) as $msg) {
			if (!isset($msg['role']) || $msg['role'] !== 'user') continue;
			if (trim($msg['content']) === trim($current_message))  continue;
			$user_msgs[] = $msg['content'];
		}

		// Scan maksimal 6 pesan ke belakang
		$scanned = 0;
		foreach ($user_msgs as $hist_content) {
			if ($scanned >= 6) break;
			$scanned++;

			if (empty($mentioned)) {
				$found = $this->_find_mentioned_loggers($hist_content, $loggers_light);
				if (!empty($found)) $mentioned = $found;
			}

			if (!$date_range) {
				$found_range = $this->_parse_date_range($hist_content);
				if ($found_range) $date_range = $found_range;
			}

			// Stop jika sudah dapat keduanya
			if (!empty($mentioned) && $date_range) break;
		}

		return [$mentioned, $date_range];
	}


	/**
	 * ================================================================
	 * Deteksi apakah pesan user membutuhkan data konteks
	 * ================================================================
	 * Untuk pesan sapaan atau pertanyaan umum, TIDAK perlu query DB.
	 * Ini yang membuat chat simpel seperti "halo" jadi cepat.
	 */
	private function _needs_data_context($message)
	{
		$msg = strtolower(trim($message));

		// Daftar kata kunci sapaan / percakapan ringan yang TIDAK butuh data
		$greetings = [
			'halo', 'hai', 'hello', 'hi', 'hey', 'selamat pagi',
			'selamat siang', 'selamat malam', 'selamat sore',
			'terima kasih', 'makasih', 'thanks', 'thank you',
			'apa kabar', 'siapa kamu', 'kamu siapa', 'apa itu',
			'ok', 'oke', 'baik', 'siap', 'ya', 'tidak',
		];

		foreach ($greetings as $g) {
			if ($msg === $g || strpos($msg, $g) === 0) {
				return 'none';
			}
		}

		// Kata kunci yang butuh data logger
		$data_keywords = [
			'data', 'pos', 'logger', 'curah', 'hujan', 'suhu', 'temperatur',
			'kelembaban', 'angin', 'tekanan', 'duga', 'air', 'tma',
			'kaliurang', 'beji', 'tegal', 'pakem', 'sleman', 'jogja',
			'yogyakarta', 'bantul', 'gunungkidul', 'kulon progo',
			'tampilkan', 'lihat', 'tunjukkan', 'berapa', 'bandingkan',
			'perbandingan', 'tertinggi', 'terendah', 'rata-rata',
			'kemarin', 'minggu', 'bulan', 'tahun', 'hari ini',
			'online', 'offline', 'terhubung', 'terputus', 'koneksi',
			'hujan', 'sedang hujan', 'tidak hujan',
			'analisis', 'tren', 'trend',
			// Fitur tambahan
			'grafik', 'chart', 'plot', 'diagram', 'visualisasi', 'graph',
			'download', 'unduh', 'export', 'ekspor', 'csv', 'excel',
			// Granularitas
			'per jam', 'perjam', 'per 2 jam', 'per 3 jam', 'per 6 jam', 'per 12 jam',
			'setiap jam', 'hourly', 'harian', 'per hari', 'per menit',
		];

		foreach ($data_keywords as $kw) {
			if (strpos($msg, $kw) !== false) {
				return 'full';
			}
		}

		// Default: kirim context ringan saja (daftar nama logger tanpa query status)
		return 'light';
	}


	/**
	 * ================================================================
	 * Build system prompt berdasarkan level kebutuhan data
	 * ================================================================
	 */
	private function _build_system_prompt($user_message, $needs_data, $messages = [])
	{
		$tanggal = date('Y-m-d H:i:s');

		$prompt  = "Kamu adalah Copilot, asisten AI untuk sistem monitoring telemetri milik Dinas PUPESDM Daerah Istimewa Yogyakarta. ";
		$prompt .= "Waktu saat ini: {$tanggal}.\n\n";

		$prompt .= "ATURAN PERILAKU:\n";
		$prompt .= "- Jawab dengan bahasa Indonesia yang sopan dan informatif.\n";
		$prompt .= "- Jika lokasi tidak jelas, minta klarifikasi.\n";
		$prompt .= "- Jika data tidak tersedia, jawab jujur.\n";
		$prompt .= "- Jangan mengarang data. Hanya gunakan data yang diberikan.\n";
		$prompt .= "- Format jawaban rapi: list untuk daftar, paragraf singkat untuk analisis.\n";
		$prompt .= "- Jika ditanya di luar domain monitoring, arahkan kembali dengan sopan.\n";
		$prompt .= "- Jika user meminta grafik/chart: jawab bahwa grafik ditampilkan otomatis di atas pesan ini.\n";
		$prompt .= "- Jika user meminta download/export CSV: jawab bahwa tombol download tersedia di atas pesan ini.\n";
		$prompt .= "- WAJIB: Setiap kali menampilkan data sensor (historis maupun data terbaru), SELALU akhiri dengan\n";
		$prompt .= "  blok 'Ringkasan:' yang berisi 2-4 poin singkat yang relevan:\n";
		$prompt .= "  * Untuk data historis: nilai tertinggi, terendah, rata-rata, dan tren (naik/turun/stabil).\n";
		$prompt .= "  * Untuk data terbaru: status saat ini (normal/waspada/hujan), kondisi koneksi,\n";
		$prompt .= "    dan satu kalimat interpretasi singkat tentang kondisi pos tersebut.\n\n";

		// ── No data needed (sapaan) ──
		if ($needs_data === 'none') {
			$prompt .= "User sedang menyapa atau bertanya hal umum. Jawab dengan ramah tanpa menyebutkan data spesifik kecuali diminta.\n";
			return $prompt;
		}

		// ── Light context (daftar logger saja, tanpa query status per logger) ──
		if ($needs_data === 'light') {
			$loggers = $this->_get_logger_names();
			if (!empty($loggers)) {
				$prompt .= "DAFTAR POS MONITORING:\n";
				foreach ($loggers as $lg) {
					$prompt .= "- {$lg['nama_lokasi']} (ID: {$lg['id_logger']})\n";
				}
				$prompt .= "\n";
			}
			return $prompt;
		}

		// ── Full context (data lengkap) ──
		// 1. Daftar logger dengan status koneksi
		$loggers = $this->_get_logger_list();
		if (!empty($loggers)) {
			$prompt .= "DAFTAR LOGGER:\n";
			foreach ($loggers as $lg) {
				$kab = isset($lg['kabupaten']) ? $lg['kabupaten'] : '-';
				$kon = isset($lg['koneksi']) ? $lg['koneksi'] : '-';
				$das = isset($lg['das']) ? $lg['das'] : '-';
				$prompt .= "- {$lg['nama_lokasi']} (ID: {$lg['id_logger']}, Kab: {$kab}, Koneksi: {$kon}, DAS: {$das})\n";
			}
			$prompt .= "\n";
		}

		// 2. Status hujan
		$rain = $this->_get_rain_summary();
		if (!empty($rain)) {
			$prompt .= "STATUS HUJAN SAAT INI:\n";
			foreach ($rain as $r) {
				$prompt .= "- {$r['nama_lokasi']}: {$r['curah_hujan']} — {$r['status']}\n";
			}
			$prompt .= "\n";
		}

		// 3. Data terbaru / historis pos yang disebutkan user
		if (!empty($loggers)) {
			$mentioned  = $this->_find_mentioned_loggers($user_message, $loggers);
			$date_range = $this->_parse_date_range($user_message);

			// Context inheritance: jika pesan ini tidak menyebut logger/date,
			// ambil dari pesan user sebelumnya dalam riwayat
			if (!empty($messages) && (empty($mentioned) || !$date_range)) {
				$loggers_light = $this->_get_logger_names();
				[$mentioned, $date_range] = $this->_extract_context_from_history(
					$messages, $user_message, $loggers_light, $mentioned, $date_range
				);
				// Pastikan $mentioned pakai format logger penuh (ada code_logger)
				if (!empty($mentioned) && !isset($mentioned[0]['id_logger'])) {
					$mentioned = $this->_find_mentioned_loggers(
						$mentioned[0]['nama_lokasi'], $loggers
					);
				}
			}

			// Deteksi granularitas: cari dari pesan saat ini OR dari history
			$interval_min_prompt = $this->_parse_granularity($user_message);
			if ($interval_min_prompt === 1440 && !empty($messages)) {
				foreach (array_reverse($messages) as $_m) {
					if (!isset($_m['role']) || $_m['role'] !== 'user' || $_m['content'] === $user_message) continue;
					$g = $this->_parse_granularity($_m['content']);
					if ($g !== 1440) { $interval_min_prompt = $g; break; }
				}
			}
			$gran_label = $interval_min_prompt >= 1440
				? 'hari'
				: ($interval_min_prompt >= 60 ? ($interval_min_prompt / 60) . ' jam' : $interval_min_prompt . ' menit');

			if (!empty($mentioned)) {
				// Cek apakah user minta data historis (range tanggal)
				if (!$date_range) {
					$date_range = $this->_parse_date_range($user_message);
				}

				if ($date_range) {
					// User minta data historis
					$prompt .= "DATA HISTORIS (per {$gran_label}, {$date_range['awal']} s/d {$date_range['akhir']}):\n";
					foreach ($mentioned as $lg) {
						$hist = $this->_get_data_range($lg['id_logger'], $date_range['awal'], $date_range['akhir'], $interval_min_prompt);
						if (!empty($hist)) {
							$prompt .= "📍 {$lg['nama_lokasi']}:\n";
							foreach ($hist as $row) {
								$line_parts = [];
								foreach ($row as $key => $val) {
									$line_parts[] = "{$key}: {$val}";
								}
								$prompt .= "   " . implode(' | ', $line_parts) . "\n";
							}
						} else {
							$prompt .= "📍 {$lg['nama_lokasi']}: Tidak ada data pada rentang ini.\n";
						}
					}
					$prompt .= "\n";
				} else {
					// Data terbaru saja
					$prompt .= "DATA TERBARU POS YANG DISEBUTKAN:\n";
					foreach ($mentioned as $lg) {
						$latest = $this->_get_latest_data($lg['id_logger']);
						if ($latest) {
							$prompt .= "📍 {$lg['nama_lokasi']}:\n";
							foreach ($latest as $key => $val) {
								$prompt .= "   - {$key}: {$val}\n";
							}
						}
					}
					$prompt .= "\n";
				}
			}
		}

		$prompt .= "Jawab pertanyaan user berdasarkan data di atas.";
		return $prompt;
	}


	/**
	 * ================================================================
	 * Stream OpenAI API response via cURL WRITEFUNCTION
	 * ================================================================
	 * $on_token(string $token)  — dipanggil setiap token diterima
	 * $on_error(array $error)   — dipanggil jika ada error
	 */
	private function _stream_openai($messages, callable $on_token, callable $on_error)
	{
		$url = $this->base_url . '/chat/completions';

		$payload = json_encode([
			'model'                 => $this->model,
			'messages'              => $messages,
			'max_completion_tokens' => (int) $this->max_tokens,
			'stream'                => true,
		]);

		$buffer = '';

		$ch = curl_init($url);
		curl_setopt_array($ch, [
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => $payload,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json',
				'Authorization: Bearer ' . $this->api_key,
				'Accept: text/event-stream',
			],
			CURLOPT_TIMEOUT        => 120,
			CURLOPT_CONNECTTIMEOUT => 10,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_RETURNTRANSFER => false,
			CURLOPT_WRITEFUNCTION  => function($ch, $chunk) use (&$buffer, $on_token, $on_error) {
				$buffer .= $chunk;

				// Proses setiap baris lengkap
				while (($pos = strpos($buffer, "\n")) !== false) {
					$line = trim(substr($buffer, 0, $pos));
					$buffer = substr($buffer, $pos + 1);

					if ($line === '' || $line === 'data: [DONE]') continue;

					if (strpos($line, 'data: ') === 0) {
						$json_str = substr($line, 6);
						$data = json_decode($json_str, true);

						if (!$data) continue;

						// Cek error dari OpenAI
						if (isset($data['error'])) {
							$on_error([
								'type'    => 'openai_stream_error',
								'oai_msg' => $data['error']['message'] ?? 'Unknown',
								'model'   => $this->model
							]);
							continue;
						}

						// Extract token delta
						if (isset($data['choices'][0]['delta']['content'])) {
							$token = $data['choices'][0]['delta']['content'];
							$on_token($token);
						}
					}
				}

				return strlen($chunk);
			},
		]);

		$success = curl_exec($ch);
		$errno   = curl_errno($ch);
		$error   = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($errno) {
			log_message('error', "Copilot stream cURL error [{$errno}]: {$error}");
			$on_error(['type' => 'curl_error', 'errno' => $errno, 'error' => $error]);
		}

		if ($http_code && $http_code !== 200) {
			// Jika ada error di buffer yang belum diproses
			if ($buffer) {
				$err_data = json_decode($buffer, true);
				$oai_msg = isset($err_data['error']['message']) ? $err_data['error']['message'] : $buffer;
				$on_error([
					'type'      => 'openai_error',
					'http_code' => $http_code,
					'oai_msg'   => $oai_msg,
					'model'     => $this->model
				]);
			}
		}
	}


	/**
	 * ================================================================
	 * Ambil daftar nama logger saja (RINGAN, tanpa cek status)
	 * ================================================================
	 * Untuk konteks ringan — cukup nama dan ID.
	 */
	private function _get_logger_names()
	{
		try {
			return $this->db
				->select('code_logger as id_logger, nama_lokasi')
				->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')
				->where('t_logger.user_id', $this->user_id)
				->order_by('code_logger', 'asc')
				->get('t_logger')
				->result_array();
		} catch (Exception $e) {
			log_message('error', 'Copilot _get_logger_names error: ' . $e->getMessage());
			return [];
		}
	}


	/**
	 * ================================================================
	 * Ambil daftar logger lengkap dengan status koneksi
	 * ================================================================
	 */
	private function _get_logger_list()
	{
		try {
			$data = $this->db
				->select('code_logger as id_logger, nama_lokasi, kabupaten, das')
				->join('t_alamat', 't_alamat.id_logger = t_logger.code_logger', 'left')
				->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')
				->join('t_informasi', 't_logger.code_logger = t_informasi.logger_id', 'left')
				->where('t_logger.user_id', $this->user_id)
				->order_by('code_logger', 'asc')
				->get('t_logger')
				->result_array();

			if (empty($data)) return [];

			// Cek koneksi per logger — batch ambil semua temp data sekaligus
			$batas_waktu = date('Y-m-d H:i', strtotime('-1 hour'));
			foreach ($data as &$logger) {
				$logger['koneksi'] = $this->_check_koneksi($logger['id_logger'], $batas_waktu);
			}

			return $data;
		} catch (Exception $e) {
			log_message('error', 'Copilot _get_logger_list error: ' . $e->getMessage());
			return [];
		}
	}


	/**
	 * ================================================================
	 * Cek status koneksi satu logger (query ringan)
	 * ================================================================
	 */
	private function _check_koneksi($id_logger, $batas_waktu)
	{
		try {
			$data_logger = $this->db
				->select('temp_tabel')
				->where('code_logger', $id_logger)
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->get('t_logger')->row();

			if (!$data_logger || !$data_logger->temp_tabel) return 'Unknown';

			$temp = $this->db
				->select('waktu')
				->where('code_logger', $id_logger)
				->order_by('waktu', 'desc')
				->limit(1)
				->get($data_logger->temp_tabel)
				->row();

			if (!$temp) return 'Tidak Ada Data';

			return ($batas_waktu < $temp->waktu) ? 'Terhubung' : 'Terputus';
		} catch (Exception $e) {
			return 'Error';
		}
	}


	/**
	 * ================================================================
	 * Ambil data terbaru per logger
	 * ================================================================
	 */
	private function _get_latest_data($id_logger)
	{
		try {
			$param = $this->db
				->where('logger_code', $id_logger)
				->not_like('t_sensor.alias_sensor', 'Logger')
				->get('t_sensor')
				->result_array();

			if (empty($param)) return null;

			$data_logger = $this->db
				->where('t_logger.code_logger', $id_logger)
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->get('t_logger')->row();

			if (!$data_logger) return null;

			$selectParts = [];
			$curah_hujan = [];
			foreach ($param as $v) {
				if ($v['satuan'] != 'mm') {
					$selectParts[] = 'CONCAT(' . $v["field_sensor"] . ', " ' . $v["satuan"] . '") as ' . $v['alias_sensor'];
				} else {
					$curah_hujan[] = 'CONCAT(format(sum(' . $v["field_sensor"] . '),2), " ' . $v["satuan"] . '") as ' . $v['alias_sensor'];
				}
			}

			$query_data = [];
			if ($selectParts) {
				$query = implode(",\n", $selectParts);
				$query_data = $this->db->query("SELECT waktu as Waktu, " . $query . " FROM " . $data_logger->temp_tabel . " where code_logger='" . $this->db->escape_str($id_logger) . "'")->row_array();
			} else {
				$query_data = $this->db->query("SELECT waktu as Waktu FROM " . $data_logger->temp_tabel . " where code_logger='" . $this->db->escape_str($id_logger) . "'")->row_array();
			}

			if (!$query_data) return null;

			// Curah hujan per jam saat ini
			if ($curah_hujan) {
				$query_hujan = implode(",\n", $curah_hujan);
				$hour_now = date('Y-m-d H');
				$query_data2 = $this->db->query("SELECT " . $query_hujan . " FROM " . $data_logger->tabel . " where code_logger='" . $this->db->escape_str($id_logger) . "' and waktu >= '" . $hour_now . ":00' and waktu <= '" . $hour_now . ":59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->row_array();
				if ($query_data2) {
					$query_data = array_merge($query_data, $query_data2);
				}
			}

			// Status koneksi
			$waktu = $query_data['Waktu'];
			$awal  = date('Y-m-d H:i', strtotime('-1 hour'));
			$query_data['Koneksi'] = $awal < $waktu ? 'Terhubung' : 'Terputus';

			return $query_data;
		} catch (Exception $e) {
			log_message('error', 'Copilot _get_latest_data error [' . $id_logger . ']: ' . $e->getMessage());
			return null;
		}
	}


	/**
	 * ================================================================
	 * Ambil ringkasan status hujan
	 * ================================================================
	 */
	private function _get_rain_summary()
	{
		try {
			$data = $this->db
				->select('code_logger as id_logger, t_lokasi.nama_lokasi')
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')
				->where('t_logger.katlog_id', '1')
				->where('t_logger.user_id', $this->user_id)
				->get('t_logger')
				->result_array();

			$result = [];
			foreach ($data as $v) {
				$sensor_hujan = $this->db
					->select('field_sensor, alias_sensor, satuan')
					->where('logger_code', $v['id_logger'])
					->like('alias_sensor', 'Curah_Hujan')
					->get('t_sensor')
					->result_array();

				if (empty($sensor_hujan)) continue;

				$sensor = count($sensor_hujan) > 1 ? $sensor_hujan[1] : $sensor_hujan[0];

				$hour_now    = date('Y-m-d H');
				$query_hujan = 'format(sum(' . $sensor['field_sensor'] . '),2) as ' . $sensor['alias_sensor'];
				$query       = 'SELECT ' . $query_hujan . ' FROM weather_station where code_logger="' . $v['id_logger'] . '" and waktu >= "' . $hour_now . ':00" and waktu <= "' . $hour_now . ':59" group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;';

				$query_data2 = $this->db->query($query)->row_array();
				$nama_sensor = $sensor['alias_sensor'];
				$nilai_ch    = isset($query_data2[$nama_sensor]) ? $query_data2[$nama_sensor] : 0;

				if ($nilai_ch >= 20) {
					$status = 'Hujan Sangat Lebat';
				} elseif ($nilai_ch >= 10) {
					$status = 'Hujan Lebat';
				} elseif ($nilai_ch >= 5) {
					$status = 'Hujan Sedang';
				} elseif ($nilai_ch >= 1) {
					$status = 'Hujan Ringan';
				} elseif ($nilai_ch >= 0.1) {
					$status = 'Hujan Sangat Ringan';
				} else {
					$status = 'Tidak Hujan';
				}

				$result[] = [
					'nama_lokasi' => $v['nama_lokasi'],
					'curah_hujan' => $nilai_ch . ' mm',
					'status'      => $status
				];
			}

			return $result;
		} catch (Exception $e) {
			log_message('error', 'Copilot _get_rain_summary error: ' . $e->getMessage());
			return [];
		}
	}


	/**
	 * ================================================================
	 * Cari logger yang disebutkan dalam pesan user
	 * ================================================================
	 */
	private function _find_mentioned_loggers($user_message, $loggers)
	{
		if (empty($user_message) || empty($loggers)) return [];

		$mentioned = [];
		$message_lower = strtolower($user_message);

		// Kata-kata generik yang diabaikan
		$skip_words = ['pos', 'awr', 'arr', 'awlr', 'curah', 'hujan', 'stasiun', 'station', 'data', 'logger'];

		foreach ($loggers as $lg) {
			$nama = strtolower($lg['nama_lokasi']);
			$parts = explode(' ', $nama);

			foreach ($parts as $part) {
				$part = trim($part);
				if (strlen($part) <= 3) continue;
				if (in_array($part, $skip_words)) continue;

				if (strpos($message_lower, $part) !== false) {
					$mentioned[] = $lg;
					break;
				}
			}
		}

		return $mentioned;
	}


	/**
	 * ================================================================
	 * Parse rentang tanggal dari pesan user
	 * ================================================================
	 * Wrapper untuk DateResolver library. Menerima full user message,
	 * mencoba resolve setiap penggalan kata, dan return ['awal','akhir'].
	 */
	private function _parse_date_range($message)
	{
		$today = date('Y-m-d');

		// DateResolver sudah handle normalisasi alias Indonesia (seminggu→1 minggu, dll)
		// dan konversi angka teks (satu→1, dua→2, dll) secara internal.
		// Coba resolve pesan penuh dulu.
		$result = $this->dateresolver->resolve($message);

		if (!$result || (isset($result['status']) && $result['status'] === 'error')) {
			// Coba ekstrak sub-frase waktu dari kalimat panjang
			$time_pattern = '/('
				. 'hari\s+ini|kemarin|kemaren|besok|lusa'         // exact hari
				. '|(?:se(?:minggu|pekan|bulan|hari|tahun))'      // kontraksi se-
				. '(?:\s*(?:terakhir|lalu|yang\s+lalu|kebelakang|ke\s+belakang|ke\s+depan|mendatang))?'
				. '|(?:satu|dua|tiga|empat|lima|enam|tujuh|delapan|sembilan|sepuluh|\d+)'  // angka teks/digit
				. '\s*(?:hari|minggu|pekan|bulan|tahun)'           // unit
				. '(?:\s*(?:terakhir|lalu|yang\s+lalu|kebelakang|ke\s+belakang|ke\s+depan|mendatang))?'  // modifier
				. '|minggu\s+(?:ini|lalu|kemarin|depan)'           // minggu relatif
				. '|bulan\s+(?:ini|lalu|kemarin|depan)'            // bulan relatif
				. '|tahun\s+(?:ini|lalu|kemarin)'                  // tahun relatif
				. ')/iu';

			if (preg_match($time_pattern, $message, $m)) {
				$result = $this->dateresolver->resolve(trim($m[0]));
			}
		}

		if (!$result || (isset($result['status']) && $result['status'] === 'error')) {
			return null;
		}

		// Konversi format DateResolver → format Copilot (awal/akhir)
		$type = isset($result['type']) ? $result['type'] : '';

		switch ($type) {
			case 'range':
				return [
					'awal'  => $result['start'],
					'akhir' => $result['end'],
				];

			case 'day':
				return [
					'awal'  => $result['tanggal'],
					'akhir' => $result['tanggal'],
				];

			case 'month':
				// "bulan" → hari pertama s/d hari terakhir bulan itu
				$bulan = $result['bulan']; // format Y-m
				return [
					'awal'  => $bulan . '-01',
					'akhir' => date('Y-m-t', strtotime($bulan . '-01')),
				];

			case 'year':
				$thn = $result['tahun'];
				return [
					'awal'  => $thn . '-01-01',
					'akhir' => $thn . '-12-31',
				];

			default:
				return null;
		}
	}


	/**
	 * ================================================================
	 * Parse granularitas dari pesan user
	 * ================================================================
	 * Return: interval dalam menit (default 1440 = per hari)
	 */
	private function _parse_granularity($message)
	{
		$msg = strtolower(trim($message));

		// Per N menit
		if (preg_match('/per\s*(\d+)\s*menit/', $msg, $m)) {
			$n = max(5, (int)$m[1]); // minimal 5 menit
			return $n;
		}

		// Setengah jam / per 30 menit
		if (strpos($msg, 'setengah jam') !== false || strpos($msg, '30 menit') !== false) {
			return 30;
		}

		// Per N jam
		if (preg_match('/(?:per|setiap)\s*(\d+)\s*jam/', $msg, $m)) {
			$n = max(1, (int)$m[1]);
			return $n * 60;
		}

		// Per jam (tanpa angka)
		if (strpos($msg, 'per jam') !== false || strpos($msg, 'perjam') !== false
		    || strpos($msg, 'setiap jam') !== false || strpos($msg, 'hourly') !== false) {
			return 60;
		}

		// Harian / per hari (explicit) → 1440
		if (strpos($msg, 'per hari') !== false || strpos($msg, 'harian') !== false || strpos($msg, 'daily') !== false) {
			return 1440;
		}

		// Default: per hari
		return 1440;
	}


	/**
	 * ================================================================
	 * Ambil data historis per logger dalam rentang tanggal
	 * ================================================================
	 * Data di-aggregate sesuai interval: avg untuk non-hujan, sum untuk hujan
	 */
	private function _get_data_range($id_logger, $awal, $akhir, $interval_min = 1440)
	{
		try {
			$param = $this->db
				->where('logger_code', $id_logger)
				->not_like('t_sensor.alias_sensor', 'Logger')
				->get('t_sensor')
				->result_array();

			if (empty($param)) return [];

			$data_logger = $this->db
				->where('t_logger.code_logger', $id_logger)
				->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')
				->get('t_logger')->row();

			if (!$data_logger) return [];

			// Build query columns
			$cols = [];
			foreach ($param as $v) {
				if ($v['satuan'] == 'mm') {
					$cols[] = 'CONCAT(FORMAT(sum(' . $v['field_sensor'] . '), 2), " ' . $v['satuan'] . '") as ' . $v['alias_sensor'];
				} else {
					$cols[] = 'CONCAT(FORMAT(avg(' . $v['field_sensor'] . '), 3), " ' . $v['satuan'] . '") as ' . $v['alias_sensor'];
				}
			}

			$select_cols = implode(', ', $cols);
			$id_esc    = $this->db->escape_str($id_logger);
			$awal_esc  = $this->db->escape_str($awal);
			$akhir_esc = $this->db->escape_str($akhir);

			// Tentukan format waktu & label kolom berdasarkan interval
			$interval_sec = $interval_min * 60;
			if ($interval_min < 1440) {
				// Sub-hari: tampilkan jam
				$time_fmt  = '%Y-%m-%d %H:%i';
				$col_label = 'Waktu';
				// Row limit: cukup untuk 3 hari data per interval
				$max_rows  = min(120, intval(4320 / $interval_min));
			} else {
				$time_fmt  = '%Y-%m-%d';
				$col_label = 'Tanggal';
				$max_rows  = 30;
			}

			$sql = "SELECT DATE_FORMAT(
				FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(waktu) / {$interval_sec}) * {$interval_sec}),
				'{$time_fmt}'
			) AS {$col_label}, {$select_cols}
			FROM {$data_logger->tabel}
			WHERE code_logger = '{$id_esc}'
			AND waktu >= '{$awal_esc} 00:00'
			AND waktu <= '{$akhir_esc} 23:59'
			GROUP BY FLOOR(UNIX_TIMESTAMP(waktu) / {$interval_sec})
			ORDER BY 1 ASC";

			$result = $this->db->query($sql)->result_array();

			// Batasi baris agar prompt tidak terlalu besar
			if (count($result) > $max_rows) {
				$result = array_slice($result, 0, $max_rows);
			}

			return $result;
		} catch (Exception $e) {
			log_message('error', 'Copilot _get_data_range error [' . $id_logger . ']: ' . $e->getMessage());
			return [];
		}
	}


	/**
	 * ================================================================
	 * Deteksi apakah user meminta grafik/chart
	 * ================================================================
	 */
	private function _detect_chart_request($message)
	{
		$msg      = strtolower($message);
		$keywords = ['grafik', 'chart', 'plot', 'diagram', 'visualisasi', 'graph'];
		foreach ($keywords as $kw) {
			if (strpos($msg, $kw) !== false) return true;
		}
		return false;
	}


	/**
	 * ================================================================
	 * Deteksi apakah user meminta download/export CSV
	 * ================================================================
	 */
	private function _detect_csv_request($message)
	{
		$msg      = strtolower($message);
		$keywords = ['download', 'unduh', 'export', 'ekspor', 'csv', 'excel', 'simpan data'];
		foreach ($keywords as $kw) {
			if (strpos($msg, $kw) !== false) return true;
		}
		return false;
	}


	/**
	 * ================================================================
	 * Build payload chart untuk SSE meta event
	 * ================================================================
	 * Mengembalikan struktur Chart.js-compatible atau null jika data kosong.
	 */
	private function _build_chart_payload($mentioned_loggers, $date_range, $user_message, $interval_min = 1440)
	{
		$msg   = strtolower($user_message);
		$chart_type = 'line';
		if (strpos($msg, 'hujan') !== false || strpos($msg, 'curah') !== false) {
			$chart_type = 'bar';
		}

		$palette = [
			'rgb(67,97,238)', 'rgb(247,37,133)', 'rgb(76,201,240)',
			'rgb(114,9,183)', 'rgb(58,134,255)', 'rgb(251,86,7)',
			'rgb(6,214,160)', 'rgb(255,209,102)',
		];

		$labels   = [];
		$datasets = [];
		$cidx     = 0;

		foreach ($mentioned_loggers as $lg) {
			$rows = $this->_get_data_range($lg['id_logger'], $date_range['awal'], $date_range['akhir'], $interval_min);
			if (empty($rows)) continue;

			// Kumpulkan label tanggal
			foreach ($rows as $row) {
				$lbl = isset($row['Tanggal']) ? $row['Tanggal'] : (isset($row['Waktu']) ? $row['Waktu'] : '');
				if ($lbl && !in_array($lbl, $labels)) $labels[] = $lbl;
			}

			// Tentukan kolom parameter (selain tanggal)
			$all_keys = array_keys($rows[0]);
			$param_keys = array_values(array_filter($all_keys, function ($k) {
				return !in_array(strtolower($k), ['tanggal', 'waktu', 'koneksi']);
			}));

			// Jika bar chart (hujan), filter hanya kolom hujan
			if ($chart_type === 'bar') {
				$rain_keys = array_filter($param_keys, function ($k) {
					$kl = strtolower($k);
					return strpos($kl, 'hujan') !== false || strpos($kl, 'curah') !== false || strpos($kl, 'rain') !== false;
				});
				if (!empty($rain_keys)) $param_keys = array_values($rain_keys);
			}

			// Batasi max 3 parameter per logger
			$param_keys = array_slice($param_keys, 0, 3);

			foreach ($param_keys as $pkey) {
				$values = array_map(function ($row) use ($pkey) {
					if (!isset($row[$pkey])) return null;
					// Strip satuan: "1.50 mm" → 1.50
					$num = preg_replace('/[^0-9.\-]/', '', explode(' ', trim($row[$pkey]))[0]);
					return $num !== '' ? (float) $num : null;
				}, $rows);

				$color = $palette[$cidx % count($palette)];
				$cidx++;
				$bg    = str_replace('rgb(', 'rgba(', str_replace(')', ',0.18)', $color));

				$dataset = [
					'label'           => $lg['nama_lokasi'] . ' - ' . str_replace('_', ' ', $pkey),
					'data'            => $values,
					'borderColor'     => $color,
				];

				if ($chart_type === 'bar') {
					// Bar/column style untuk curah hujan
					$dataset['backgroundColor'] = str_replace('rgb(', 'rgba(', str_replace(')', ',0.7)', $color));
					$dataset['borderWidth']      = 1;
					$dataset['borderRadius']     = 4;
				} else {
					// Line style untuk TMA, debit, dll
					$dataset['backgroundColor'] = $bg;
					$dataset['tension']         = 0.35;
					$dataset['fill']            = true;
					$dataset['pointRadius']     = 3;
				}

				$datasets[] = $dataset;
			}
		}

		if (empty($datasets) || empty($labels)) return null;

		return [
			'type'       => 'chart',
			'chart_type' => $chart_type,
			'labels'     => $labels,
			'datasets'   => $datasets,
			'title'      => implode(', ', array_column($mentioned_loggers, 'nama_lokasi'))
			             . ' (' . $date_range['awal'] . ' s/d ' . $date_range['akhir'] . ')',
		];
	}
}
