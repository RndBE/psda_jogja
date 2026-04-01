<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Awlr extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->library('csvimport');
		$this->load->model('m_awlr');
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
	}

	public function do_upload() {
		$config['upload_path']   = './upload_csv/';
		$config['allowed_types'] = 'csv';
		$config['max_size']      = 2048;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('file')) {
			echo json_encode([
				'status' => 'error',
				'error'  => strip_tags($this->upload->display_errors())
			]);
		} else {
			$data = $this->upload->data();
			echo json_encode([
				'status'     => 'success',
				'file_name'  => $data['file_name']
			]);
		}
	}

	public function process_files() {
		header('Content-Type: application/json');
		$input = json_decode(file_get_contents('php://input'), true);
		$files = $input['files'];
		$tabel = 'awlr'; // ganti sesuai kebutuhan

		$results = [];

		foreach ($files as $file) {
			$results[] = $this->upload_db_json($tabel, $file);
		}

		echo json_encode(['status' => 'done', 'results' => $results]);
	}


	public function upload_db_json($tabel, $nama_file) {
		$file_path = './upload_csv/' . $nama_file;

		if (!file_exists($file_path)) {
			log_message('error', "❌ File tidak ditemukan: $file_path");
			return ['file' => $nama_file, 'status' => 'error', 'message' => 'File tidak ditemukan'];
		}

		include_once APPPATH . 'third_party/PHPExcel/PHPExcel.php';

		try {
			$csvreader = PHPExcel_IOFactory::createReader('CSV');
			$loadcsv = $csvreader->load($file_path);
			$sheet = $loadcsv->getActiveSheet()->getRowIterator();

			$dataup = [];
			$numrow = 1;
			$duplikat = 0;
			$baris_valid = 0;

			foreach ($sheet as $row) {
				try {
					if ($numrow > 1) {
						$cellIterator = $row->getCellIterator();
						$cellIterator->setIterateOnlyExistingCells(false);
						$get = [];

						foreach ($cellIterator as $cell) {
							$get[] = $cell->getValue();
						}

						if (count($get) < 19) {
							log_message('error', "❌ Kolom tidak lengkap di baris $numrow file: $nama_file");
							$numrow++;
							continue;
						}

						$waktu = date("Y-m-d H:i", strtotime($get[1] . ' ' . $get[2]));

						$cek = $this->db->where('code_logger', $get[0])
							->where('waktu', $waktu)
							->get($tabel)->row();

						if (!$cek) {
							$dataup[] = [
								'code_logger' => $get[0],
								'waktu' => $waktu,
								'sensor1' => $get[3],
								'sensor2' => $get[4],
								'sensor3' => $get[5],
								'sensor4' => $get[6],
								'sensor5' => $get[7],
								'sensor6' => $get[8],
								'sensor7' => $get[9],
								'sensor8' => $get[10],
								'sensor9' => $get[11],
								'sensor10' => $get[12],
								'sensor11' => $get[13],
								'sensor12' => $get[14],
								'sensor13' => $get[15],
								'sensor14' => $get[16],
								'sensor15' => $get[17],
								'sensor16' => $get[18],
							];
							$baris_valid++;
						} else {
							$duplikat++;
						}
					}
				} catch (Throwable $e) {
					log_message('error', "❌ Gagal parsing baris $numrow file $nama_file: " . $e->getMessage());
					return [
						'file' => $nama_file,
						'status' => 'error',
						'message' => "Gagal parsing baris $numrow: " . $e->getMessage()
					];
				}
				$numrow++;
			}

			if (!empty($dataup)) {
				$this->db->insert_batch($tabel, $dataup);
			}

			$loadcsv->disconnectWorksheets();
			unset($loadcsv);
			unset($csvreader);
			// ✅ Hapus file setelah proses
			if (file_exists($file_path)) {
				unlink($file_path);
				log_message('debug', "🗑️ File dihapus: $file_path");
			}

			return [
				'file' => $nama_file,
				'status' => 'success',
				'inserted' => $baris_valid,
				'duplicate' => $duplikat
			];
		} catch (Throwable $e) {
			log_message('error', "💥 Fatal error saat proses $nama_file: " . $e->getMessage());
			return [
				'file' => $nama_file,
				'status' => 'error',
				'message' => $e->getMessage()
			];
		}
	}

	### Dari Beranda ##########
	function set_sensordash()
	{
		$tabel = $this->input->get('tabel');
		$idparam = $this->input->get('id_param');

		$this->session->set_userdata('id_param', $this->input->get('id_param'));
		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');
		$this->session->set_userdata('tanggal', $tgl);
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where id='" . $idparam . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => 'spline',
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $parameter->logger_code . '";');
			$log = $querylogger->row();
			$lokasilog = $log->nama_lokasi;
			$this->session->set_userdata('namalokasi', $lokasilog);
		}
		$this->session->set_userdata('controller', 'awlr');
		redirect('awlr/analisa');
	}

	function set_sensordash2()
	{
		$tabel = $this->input->get('tabel');
		$idparam = $this->input->get('id_param');

		$this->session->set_userdata('id_param', $this->input->get('id_param'));
		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');
		$this->session->set_userdata('tanggal', $tgl);
		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idparam . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_id,
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $parameter->tipe_graf,
				'kolom_acuan' => $parameter->kolom_acuan,
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $parameter->logger_id . '";');
			$log = $querylogger->row();
			$lokasilog = $log->nama_lokasi;
			$this->session->set_userdata('namalokasi', $lokasilog);
		}

		redirect('komparasi');
	}
	############################################


	### Dari Analisa ##########
	function set_sensorselect()
	{

		$idlogger = $this->uri->segment(3);
		$tabel = $this->uri->segment(4);
		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => 'spline'
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $parameter->logger_code . '";');
			$log = $querylogger->row();
			$lokasilog = $log->nama_lokasi;
			$this->session->set_userdata('namalokasi', $lokasilog);
		}
		$this->session->set_userdata('controller', 'awlr');
		redirect('awlr/analisa');
	}
	############################################

	function set_param()
	{
		$tabel = $this->uri->segment(3);
		$idparam = $this->uri->segment(4);
		$lok = str_replace('_', ' ', $this->uri->segment(5));
		$this->session->set_userdata('namalokasi', $lok);
		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');
		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where id_param='" . $idparam . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_id,
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $parameter->tipe_graf,
				'kolom_acuan' => $parameter->kolom_acuan
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('awlr/analisa');
	}

	### Set Pos #####
	public function pilihposawlr()
	{
		$data = array();
		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='8' AND t_logger.user_id='4' or t_logger.code_logger = '10114'");

		foreach ($q_pos->result() as $pos) {
			if($pos->code_logger == '10114'){
				$data[] = array(
					'idLogger' => $pos->code_logger, 'namaPos' => "Pos AWLR Plataran"
				);
			}else{
				$data[] = array(
					'idLogger' => $pos->code_logger, 'namaPos' => $pos->nama_lokasi
				);
			}

		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	public function pilihposarr()
	{
		$data = array();
		if ($this->session->userdata('leveluser') == 'admin' or $this->session->userdata('leveluser') == 'user') {
			$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger = t_lokasi.idlokasi where kategori_log='1'");
		} else {
			$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger = t_lokasi.idlokasi where kategori_log='1' AND t_logger.bidang='$bidang'");
		}

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->id_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	function set_pos()
	{
		$idlog = $this->input->post('pilihpos');
		if($idlog == '10114') {
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $idlog . '" and t_logger.icon = "awlr"');
			$this->session->set_userdata('tabel', 'weather_station');
		}else{
			$this->session->set_userdata('tabel', 'awlr');
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $idlog . '"');
		}

		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $log->code_logger;
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('id_logger', $id_logger);
		if($idlog == '10114') {
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where alias_sensor='Kedalaman_Air_Sumur' order by id limit 1");
		}else{
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' order by id limit 1");
		}


		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => 'spline'
			);
			$this->session->set_userdata('id_param', $parameter->id);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('awlr/analisa');
	}

	function set_pos2()
	{
		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $log->id_logger;
		$this->session->set_userdata('namalokasi', $lokasilog);
		$this->session->set_userdata('id_logger', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "' order by id_param limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_id,
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $parameter->tipe_graf,
				'kolom_acuan' => $parameter->kolom_acuan
			);
			$this->session->set_userdata('id_param', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('komparasi');
	}

	function set_pos4()
	{
		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $log->id_logger;
		$this->session->set_userdata('namalokasi3', $lokasilog);
		$this->session->set_userdata('id_logger3', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "' order by id_param limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger3' => $parameter->logger_id,
				'idparameter3' => $parameter->id_param,
				'nama_parameter3' => $parameter->nama_parameter,
				'kolom3' => $parameter->kolom_sensor,
				'satuan3' => $parameter->satuan,
				'tipe_grafik3' => $parameter->tipe_graf,
				'kolom_acuan3' => $parameter->kolom_acuan
			);
			$this->session->set_userdata('id_param', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('komparasi');
	}

	function set_pos3()
	{
		$idlog = $this->input->post('pilihpos2');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $log->id_logger;
		$this->session->set_userdata('namalokasi2', $lokasilog);
		$this->session->set_userdata('id_logger2', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM parameter_sensor where logger_id='" . $idlog . "' order by id_param limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger2' => $parameter->logger_id,
				'idparameter2' => $parameter->id_param,
				'nama_parameter2' => $parameter->nama_parameter,
				'kolom2' => $parameter->kolom_sensor,
				'satuan2' => $parameter->satuan,
				'tipe_grafik2' => $parameter->tipe_graf,
				'kolom_acuan2' => $parameter->kolom_acuan
			);
			$this->session->set_userdata('id_param2', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('komparasi');
	}
	##### set Parameter #####
	public function pilihparameter($idlogger)
	{
		$data = array();
		if($idlogger == '10114'){
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "' and alias_sensor != 'Curah_Hujan' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");
		}else{
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");
		}

		foreach ($q_parameter->result() as $param) {
			$data[] = array(
				'idParameter' => $param->id, 'namaParameter' => $param->alias_sensor, 'fieldParameter' => $param->field_sensor
			);
		}

		$data_param = json_encode($data);
		return json_decode($data_param);
	}

	function riset_data()
	{
		$current_time = time(); // get current timestamp
		$current_minute = date('i', $current_time); // get the minute value (00-59)

		// calculate the total number of minutes elapsed since the start of the day
		$total_minutes = ((int)date('H', $current_time) * 60) + (int)$current_minute;

		echo "Current total minute in the day: " . $total_minutes;
	}

	function set_parameter()
	{
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where id='" . $this->input->post('mnsensor') . "'");
		$this->session->set_userdata('id_param', $this->input->post('mnsensor'));
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => 'spline'
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('awlr/analisa');
	}


	function sesi_data()
	{
		if ($this->input->post('data') == 'hari') {
			$tgl = date('Y-m-d');
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'bulan') {
			$tgl = date('Y-m');
			$this->session->set_userdata('bulan', $tgl);
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'tahun') {
			$tgl = date('Y');
			$this->session->set_userdata('tahun', $tgl);
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'range') {
			$dari = date('Y-m-d H:i', (mktime(date('H'), 0, 0, date('m'), date('d') - 1, date('Y'))));

			$sampai = date('Y-m-d H:i', (mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'))));

			$this->session->set_userdata('dari', $dari);
			$this->session->set_userdata('sampai', $sampai);
		}
		$this->session->set_userdata('data', $this->input->post('data'));
		redirect('awlr/analisa');
	}

	function settgl()
	{
		$tgl = str_replace('/', '-', $this->input->post('tgl'));
		$this->session->set_userdata('tanggal', $tgl);
		$this->session->set_userdata('pada', $tgl);
		redirect('awlr/analisa');
	}

	function settgl2()
	{
		$tgl = str_replace('/', '-', $this->input->post('tgl'));
		$this->session->set_userdata('tanggal', $tgl);
		$this->session->set_userdata('pada', $tgl);
		redirect('komparasi');
	}

	function setbulan()
	{
		$tgl = str_replace('/', '-', $this->input->post('bulan'));
		$this->session->set_userdata('bulan', $tgl);
		$this->session->set_userdata('pada', $tgl);
		redirect('awlr/analisa');
	}

	function settahun()
	{
		$tgl = str_replace('/', '-', $this->input->post('tahun'));
		$this->session->set_userdata('tahun', $tgl);
		$this->session->set_userdata('pada', $tgl);
		redirect('awlr/analisa');
	}

	function setrange()
	{
		$this->session->set_userdata('dari', $this->input->post('dari'));
		$this->session->set_userdata('sampai', $this->input->post('sampai'));
		redirect('awlr/analisa');
	}


	function analisa2()
	{

		if ($this->session->userdata('logged_in')) {
			if ($this->session->userdata('tabel') == 'arr') {
				redirect('arr/analisa');
			}
			$data = array();
			$data_tabel = array();
			$min = array();
			$max = array();
			$range = array();
			$string = $this->session->userdata('pada');
			$timestamp = strtotime($string);
			$hari =  date("d", $timestamp);
			$bulan =  date("m", $timestamp);
			$tahun = date("Y", $timestamp);
			####################################################################################### HARI ##################
			if ($this->session->userdata('data') == 'hari') {
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
				if ($sensor == 'debit') {
					$kolom = $this->session->userdata('kolom_acuan');
				} else {
					$kolom = $this->session->userdata('kolom');
				}
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu, HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max FROM " . $this->session->userdata('tabel') . "  USE INDEX (waktu) where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . " 00:00' and waktu <= '" . $this->session->userdata('pada') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();

				foreach ($query_data as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . "-1," . $datalog['hari'] . "," . $datalog['jam'] . ")," . number_format($datalog[$nama_sensor], 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . "-1," . $datalog['hari'] . "," . $datalog['jam'] . ")," . $datalog['min'] . "," . $datalog['max'] . "]";
				}
				for ($i = 0; $i < 24; $i++) {
					if (array_search($i, array_column($query_data, 'jam')) !== false) {
					} else {
						array_push($query_data, array('jam' => $i, $nama_sensor => '-', 'hari' => $hari, 'bulan' => $bulan, 'tahun' => $tahun, 'min' => '-', 'max' => '-'));
					}
				}
				array_multisort(array_column($query_data, "jam"), SORT_ASC, $query_data);
				foreach ($query_data as $datalog) {
					$jsm = ($datalog['jam'] > 9) ? $datalog['jam'] : '0' . $datalog['jam'];
					$data_tabel[] = array(
						'waktu' =>  $jsm . ':00',
						'dta' => ($datalog[$nama_sensor] != '-') ? number_format($datalog[$nama_sensor], 2) : '-',
						'min' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['min'], 2) : '-',
						'max' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['max'], 2) : '-'
					);
				}

				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $kolom,
					'range' => $range,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);

			}
			####################################################################################### BULAN ##################
			elseif ($this->session->userdata('data') == 'bulan') {
				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
				if ($sensor == 'debit') {
					$kolom = $this->session->userdata('kolom_acuan');
				} else {
					$kolom = $this->session->userdata('kolom');
				}
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');
				$d = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

				$query_data = $this->db->query("SELECT waktu,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu)  order by waktu asc;")->result_array();
				foreach ($query_data as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . "-1," . $datalog['hari'] . ")," . number_format($datalog[$nama_sensor], 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . "-1," . $datalog['hari'] . ")," . $datalog['min'] . "," . $datalog['max'] . "]";
				}
				for ($i = 1; $i <= $d; $i++) {
					if (array_search($i, array_column($query_data, 'hari')) !== false) {
					} else {
						array_push($query_data, array('hari' => $i, $nama_sensor => '-', 'bulan' => $bulan, 'tahun' => $tahun, 'min' => '-', 'max' => '-'));
					}
				}
				array_multisort(array_column($query_data, "hari"), SORT_ASC, $query_data);

				foreach ($query_data as $datalog) {
					$data_tabel[] = array(
						'waktu' =>  $datalog['tahun'] . '-' . $datalog['bulan'] . '-' . $datalog['hari'],
						'dta' => ($datalog[$nama_sensor] != '-') ? number_format($datalog[$nama_sensor], 2) : '-',
						'min' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['min'], 2) : '-',
						'max' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['max'], 2) : '-'
					);
				}

				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $sensor,
					'range' => $range,
					'tooltip' => "Tanggal %d-%m-%Y"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}
			####################################################################################### TAHUN ##################
			elseif ($this->session->userdata('data') == 'tahun') {


				$sensor = $this->session->userdata('kolom');
				$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
				if ($sensor == 'debit') {
					$kolom = $this->session->userdata('kolom_acuan');
				} else {
					$kolom = $this->session->userdata('kolom');
				}
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu)  order by waktu asc;")->result_array();

				foreach ($query_data as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] .  ")," . number_format($datalog[$nama_sensor], 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . ")," . $datalog['min'] . "," . $datalog['max'] . "]";
				}
				for ($i = 1; $i <= 12; $i++) {
					if (array_search($i, array_column($query_data, 'bulan')) !== false) {
					} else {
						array_push($query_data, array('bulan' => $i, $nama_sensor => '-', 'tahun' => $tahun, 'min' => '-', 'max' => '-'));
					}
				}
				array_multisort(array_column($query_data, "bulan"), SORT_ASC, $query_data);

				foreach ($query_data as $datalog) {
					$jsm = ($datalog['bulan'] > 9) ? $datalog['bulan'] : '0' . $datalog['bulan'];
					$data_tabel[] = array(
						'waktu' =>  $datalog['tahun'] . '-' . $jsm,
						'dta' => ($datalog[$nama_sensor] != '-') ? number_format($datalog[$nama_sensor], 2) : '-',
						'min' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['min'], 2) : '-',
						'max' => ($datalog[$nama_sensor] != '-') ? number_format($datalog['max'], 2) : '-'
					);
				}

				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('idlogger'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik'),
					'data' => $data,
					'data_tabel' => $data_tabel,
					'nosensor' => $sensor,
					'range' => $range,
					'tooltip' => "Tanggal %d-%m-%Y"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}


			$data['pilih_pos'] = $this->pilihposawlr();
			$data['pilih_parameter'] = $this->pilihparameter($this->session->userdata('idlogger'));
			$data['konten'] = 'konten/back/awlr/analisa_awlr2';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	public function analisa()
	{
		if (!$this->session->userdata('logged_in')) {
			return redirect('login');
		}

		if ($this->session->userdata('tabel') === 'arr') {
			return redirect('arr/analisa');
		}

		$mode        = $this->session->userdata('data');             // 'hari' | 'bulan' | 'tahun'
		$pada        = $this->session->userdata('pada');             // 'YYYY-MM-DD' atau 'YYYY-MM'
		$idLogger    = $this->session->userdata('idlogger');
		$tabel       = $this->session->userdata('tabel');
		$tipeGrafik  = $this->session->userdata('tipe_grafik');
		$satuan      = $this->session->userdata('satuan');
		$sensorKey   = $this->session->userdata('kolom');            // contoh: 'sensor1' | 'debit'
		$namaParam   = $this->session->userdata('nama_parameter');    // contoh: 'TMA' | 'Debit'
		$kolomAcuan  = $this->session->userdata('kolom_acuan');      // jika debit → pakai ini

		$kolom = ($sensorKey === 'debit') ? $kolomAcuan : $sensorKey;
		$namaSensor = 'Rerata_' . $namaParam;

		$ts = strtotime($pada);
		if ($ts === false) {
			$ts = time();
		}
		$Y = (int) date('Y', $ts);
		$m = (int) date('m', $ts);
		$d = (int) date('d', $ts);

		$timeStart = null;
		$timeEnd   = null; 
		$groupCols = [];   
		$selectTimeParts = [];

		if ($mode === 'hari') {
			$timeStart = sprintf('%04d-%02d-%02d 00:00:00', $Y, $m, $d);
			$timeEnd   = sprintf('%04d-%02d-%02d 23:59:59', $Y, $m, $d);
			$groupCols = ["HOUR(waktu)"];
			$selectTimeParts = [
				"HOUR(waktu) AS jam",
				"DAY(waktu) AS hari",
				"MONTH(waktu) AS bulan",
				"YEAR(waktu) AS tahun",
			];
		} elseif ($mode === 'bulan') {
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $Y);
			$timeStart = sprintf('%04d-%02d-01 00:00:00', $Y, $m);
			$timeEnd   = sprintf('%04d-%02d-%02d 23:59:59', $Y, $m, $daysInMonth);
			$groupCols = ["DAY(waktu)"];
			$selectTimeParts = [
				"DAY(waktu) AS hari",
				"MONTH(waktu) AS bulan",
				"YEAR(waktu) AS tahun",
			];
		} else { // 'tahun'
			$timeStart = sprintf('%04d-01-01 00:00:00', $Y);
			$timeEnd   = sprintf('%04d-12-31 23:59:59', $Y);
			$groupCols = ["MONTH(waktu)"];
			$selectTimeParts = [
				"MONTH(waktu) AS bulan",
				"YEAR(waktu) AS tahun",
			];
		}

		$this->db->select(implode(',', array_merge(
			['waktu'],
			$selectTimeParts,
			[
				"AVG($kolom) AS {$namaSensor}",
				"MIN($kolom) AS min",
				"MAX($kolom) AS max"
			]
		)))
			->from($tabel)
			->where('code_logger', $idLogger)
			->where('waktu >=', $timeStart)
			->where('waktu <=', $timeEnd);

		foreach ($groupCols as $gc) {
			$this->db->group_by($gc);
		}
		if ($mode === 'hari') {
			$this->db->group_by('DAY(waktu)')->group_by('MONTH(waktu)')->group_by('YEAR(waktu)');
			$this->db->order_by('waktu', 'ASC');
		} elseif ($mode === 'bulan') {
			$this->db->group_by('MONTH(waktu)')->group_by('YEAR(waktu)');
			$this->db->order_by('waktu', 'ASC');
		} else { // tahun
			$this->db->group_by('YEAR(waktu)');
			$this->db->order_by('waktu', 'ASC');
		}

		$rows = $this->db->get()->result_array();

		$data       = [];   // untuk series chart
		$range      = [];   // min–max area
		$data_tabel = [];   // tabel di view

		if ($mode === 'hari') {
			$byHour = [];
			foreach ($rows as $r) {
				$byHour[(int)$r['jam']] = $r;
			}
			for ($h = 0; $h < 24; $h++) {
				if (!isset($byHour[$h])) {
					$byHour[$h] = [
						'jam' => $h,
						'hari' => $d,
						'bulan' => $m,
						'tahun' => $Y,
						$namaSensor => null,
						'min' => null,
						'max' => null,
					];
				}
			}
			ksort($byHour);

			foreach ($byHour as $r) {
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ",{$r['hari']},{$r['jam']})";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;

				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . (float)$r['min'] . "," . (float)$r['max'] . "]";
				}

				$jsm = ($r['jam'] > 9) ? $r['jam'] : ('0' . $r['jam']);
				$data_tabel[] = [
					'waktu' => $jsm . ':00',
					'dta'   => ($avg !== null) ? number_format($avg, 2, '.', '') : '-',
					'min'   => ($avg !== null) ? number_format((float)$r['min'], 2, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 2, '.', '') : '-',
				];
			}

			$tooltip = "Waktu %d-%m-%Y %H:%M";
			$noSensorOut = $kolom;

		} elseif ($mode === 'bulan') {
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $Y);

			$byDay = [];
			foreach ($rows as $r) {
				$byDay[(int)$r['hari']] = $r;
			}
			for ($i = 1; $i <= $daysInMonth; $i++) {
				if (!isset($byDay[$i])) {
					$byDay[$i] = [
						'hari' => $i,
						'bulan' => $m,
						'tahun' => $Y,
						$namaSensor => null,
						'min' => null,
						'max' => null,
					];
				}
			}
			ksort($byDay);

			foreach ($byDay as $r) {
				// JS Date.UTC(Y, m-1, d)
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ",{$r['hari']})";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;

				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . (float)$r['min'] . "," . (float)$r['max'] . "]";
				}

				$w = sprintf('%04d-%02d-%02d', $r['tahun'], $r['bulan'], $r['hari']);
				$data_tabel[] = [
					'waktu' => $w,
					'dta'   => ($avg !== null) ? number_format($avg, 2, '.', '') : '-',
					'min'   => ($avg !== null) ? number_format((float)$r['min'], 2, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 2, '.', '') : '-',
				];
			}

			$tooltip = "Tanggal %d-%m-%Y";
			$noSensorOut = $sensorKey;

		} else { // tahun
			$byMonth = [];
			foreach ($rows as $r) {
				$byMonth[(int)$r['bulan']] = $r;
			}
			for ($i = 1; $i <= 12; $i++) {
				if (!isset($byMonth[$i])) {
					$byMonth[$i] = [
						'bulan' => $i,
						'tahun' => $Y,
						$namaSensor => null,
						'min' => null,
						'max' => null,
					];
				}
			}
			ksort($byMonth);

			foreach ($byMonth as $r) {
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ")";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;

				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . (float)$r['min'] . "," . (float)$r['max'] . "]";
				}

				$mm = ($r['bulan'] > 9) ? $r['bulan'] : ('0' . $r['bulan']);
				$data_tabel[] = [
					'waktu' => $r['tahun'] . '-' . $mm,
					'dta'   => ($avg !== null) ? number_format($avg, 2, '.', '') : '-',
					'min'   => ($avg !== null) ? number_format((float)$r['min'], 2, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 2, '.', '') : '-',
				];
			}

			$tooltip = "Tanggal %d-%m-%Y";
			$noSensorOut = $sensorKey;
		}

		$dataAnalisa = [
			'idLogger'    => $idLogger,
			'namaSensor'  => $namaSensor,
			'satuan'      => $satuan,
			'tipe_grafik' => $tipeGrafik,
			'data'        => $data,        
			'data_tabel'  => $data_tabel,  
			'nosensor'    => $noSensorOut, 
			'range'       => $range,       
			'tooltip'     => $tooltip
		];

		$payload = json_encode($dataAnalisa);
		$viewData = [];
		$viewData['data_sensor']      = json_decode($payload);
		$viewData['pilih_pos']        = $this->pilihposawlr();
		$viewData['pilih_parameter']  = $this->pilihparameter($idLogger);
		$viewData['konten']           = 'konten/back/awlr/analisa_awlr2';

		$this->load->view('template_admin/site', $viewData);
	}


	function livedata()
	{
		if ($this->session->userdata('logged_in')) {
			$data['konten'] = 'konten/back/awlr/analisa_liveawlr';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	function editlengkungdebit()
	{
		if ($this->session->userdata('logged_in')) {
			$logger = $this->session->userdata('idlogger');
			$data = array(
				'a' => $this->input->post('a'),
				'b' => $this->input->post('b'),
				'c' => $this->input->post('c'),
				'tahun' => $this->input->post('tahun')
			);
			$this->m_awlr->update_lengkungdebit($logger, $data);
			redirect('awlr/analisa');
		} else {
			redirect('login');
		}
	}

	function editsiaga()
	{
		if ($this->session->userdata('logged_in')) {
			$logger = $this->session->userdata('idlogger');
			$data = array(
				'siaga2' => $this->input->post('waspada'),
				'siaga1' => $this->input->post('siaga')
			);
			$this->m_awlr->update_siaga($logger, $data);
			redirect('awlr/analisa');
		} else {
			redirect('login');
		}
	}


}
