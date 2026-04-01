<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class  Station_cuaca extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('csvimport');
		if (!$this->session->userdata('logged_in')) {
			redirect('login');
		}
	}

	### Dari Beranda ##########
	function set_sensordash()
	{
		$tabel = $this->input->get('tabel');
		$idparam = $this->input->get('id_param');

		$this->session->set_userdata('tabel', $tabel);
		$tgl = date('Y-m-d');
		$this->session->set_userdata('pada', $tgl);
		$this->session->set_userdata('data', 'hari');

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where id='" . $idparam . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			$tipe_grafik = 'spline';
			if ($parameter->field_sensor == 'sensor9' or $parameter->field_sensor == 'sensor8') {
				$tipe_grafik = 'column';
			}
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $tipe_grafik,
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $parameter->logger_code . '";');
			$log = $querylogger->row();
			$lokasilog = $log->nama_lokasi;
			$this->session->set_userdata('namalokasi', $lokasilog);
		}

		redirect('station_cuaca/analisa');
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
			$tipe_grafik = 'spline';
			if ($parameter->field_sensor == 'sensor9' or $parameter->field_sensor == 'sensor8') {
				$tipe_grafik = 'column';
			}
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $tipe_grafik
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
			$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $parameter->logger_code . '";');
			$log = $querylogger->row();
			$lokasilog = $log->nama_lokasi;
			$this->session->set_userdata('namalokasi', $lokasilog);
		}

		redirect('station_cuaca/analisa');
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
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where id='" . $idparam . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id_param,
				'nama_parameter' => $parameter->nama_parameter,
				'kolom' => $parameter->kolom_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $parameter->tipe_graf
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('arr/analisa');
	}

	### Set Pos #####
	public function pilihposarr()
	{
		$data = array();

		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='1' and t_logger.user_id = '4' and t_logger.icon = 'ws' ");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}



	function set_pos()
	{

		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $idlog . '"');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$this->session->set_userdata('namalokasi', $log->nama_lokasi);

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' order by id limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			$tipe_grafik = 'spline';
			if ($parameter->field_sensor == 'sensor9' or $parameter->field_sensor == 'sensor8') {
				$tipe_grafik = 'column';
			}
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $tipe_grafik
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('station_cuaca/analisa');
	}

	function set_pos2()
	{
		echo $this->input->post('pilih_arr');
		exit;
		$idlog = $this->input->post('pilih_arr');
		$bidang = $this->session->userdata['bidang'];
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_logger=t_lokasi.idlokasi where id_logger="' . $idlog . '"');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$this->session->set_userdata('namalokasi', $log->nama_lokasi);

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
				'tipe_grafik2' => $parameter->tipe_graf
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		echo json_encode($q_parameter);
		exit;
		redirect('awlr/komparasi');
	}
	##### set Parameter #####
	public function pilihparameter($idlogger)
	{
		$data = array();
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "'");
		foreach ($q_parameter->result() as $param) {
			$data[] = array(
				'idParameter' => $param->id, 'namaParameter' => $param->alias_sensor, 'fieldParameter' => $param->field_sensor
			);
		}

		$data_param = json_encode($data);
		return json_decode($data_param);
	}

	function set_parameter()
	{
		$q_parameter = $this->db->query("SELECT * FROM t_sensor where id='" . $this->input->post('mnsensor') . "'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			$tipe_grafik = 'spline';
			if ($parameter->field_sensor == 'sensor9' or $parameter->field_sensor == 'sensor8') {
				$tipe_grafik = 'column';
			}

			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger' => $parameter->logger_code,
				'idparameter' => $parameter->id,
				'nama_parameter' => $parameter->alias_sensor,
				'kolom' => $parameter->field_sensor,
				'satuan' => $parameter->satuan,
				'tipe_grafik' => $tipe_grafik
			);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('station_cuaca/analisa');
	}


	function sesi_data()
	{
		if ($this->input->post('data') == 'hari') {
			$tgl = date('Y-m-d');
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'bulan') {
			$tgl = date('Y-m');
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'tahun') {
			$tgl = date('Y');
			$this->session->set_userdata('pada', $tgl);
		} elseif ($this->input->post('data') == 'range') {
			$dari = date('Y-m-d H:i', (mktime(date('H'), 0, 0, date('m'), date('d') - 1, date('Y'))));

			$sampai = date('Y-m-d H:i', (mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'))));

			$this->session->set_userdata('dari', $dari);
			$this->session->set_userdata('sampai', $sampai);
		}
		$this->session->set_userdata('data', $this->input->post('data'));
		redirect('station_cuaca/analisa');
	}

	function settgl()
	{
		$tgl = str_replace('/', '-', $this->input->post('tgl'));
		$this->session->set_userdata('pada', $tgl);
		redirect('station_cuaca/analisa');
	}

	function setbulan()
	{
		$tgl = str_replace('/', '-', $this->input->post('bulan'));
		$this->session->set_userdata('pada', $tgl);
		redirect('station_cuaca/analisa');
	}

	function settahun()
	{
		$tgl = str_replace('/', '-', $this->input->post('tahun'));
		$this->session->set_userdata('pada', $tgl);
		redirect('station_cuaca/analisa');
	}

	function setrange()
	{
		$this->session->set_userdata('dari', $this->input->post('dari'));
		$this->session->set_userdata('sampai', $this->input->post('sampai'));
		redirect('station_cuaca/analisa');
	}


	function analisa()
	{

		if (!$this->session->userdata('logged_in')) {
			return redirect('login');
		}

		$mode       = $this->session->userdata('data');
		$pada       = $this->session->userdata('pada');
		$idLogger   = $this->session->userdata('idlogger');
		$tabel      = $this->session->userdata('tabel');
		$tipeGrafik = $this->session->userdata('tipe_grafik');
		$satuan     = $this->session->userdata('satuan');
		$sensorKey  = $this->session->userdata('kolom');
		$namaParam  = $this->session->userdata('nama_parameter');

		$isSum      = ($tipeGrafik === 'column');
		$aggFunc    = $isSum ? 'SUM' : 'AVG';
		$namaSensor = ($isSum ? 'Akumulasi_' : 'Rerata_') . $namaParam;
		$kolomAgg   = $sensorKey;

		$ts = strtotime($pada) ?: time();
		$Y  = (int)date('Y', $ts);
		$m  = (int)date('m', $ts);
		$d  = (int)date('d', $ts);

		$timeStart = $timeEnd = '';
		$selectTimeParts = [];
		$groupCols = [];

		if ($mode === 'hari') {
			$timeStart = sprintf('%04d-%02d-%02d 00:00:00', $Y, $m, $d);
			$timeEnd   = sprintf('%04d-%02d-%02d 23:59:59', $Y, $m, $d);
			$selectTimeParts = ["HOUR(waktu) AS jam", "DAY(waktu) AS hari", "MONTH(waktu) AS bulan", "YEAR(waktu) AS tahun"];
			$groupCols = ["HOUR(waktu)", "DAY(waktu)", "MONTH(waktu)", "YEAR(waktu)"];
		} elseif ($mode === 'bulan') {
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $Y);
			$timeStart = sprintf('%04d-%02d-01 00:00:00', $Y, $m);
			$timeEnd   = sprintf('%04d-%02d-%02d 23:59:59', $Y, $m, $daysInMonth);
			$selectTimeParts = ["DAY(waktu) AS hari", "MONTH(waktu) AS bulan", "YEAR(waktu) AS tahun"];
			$groupCols = ["DAY(waktu)", "MONTH(waktu)", "YEAR(waktu)"];
		} else {
			$timeStart = sprintf('%04d-01-01 00:00:00', $Y);
			$timeEnd   = sprintf('%04d-12-31 23:59:59', $Y);
			$selectTimeParts = ["MONTH(waktu) AS bulan", "YEAR(waktu) AS tahun"];
			$groupCols = ["MONTH(waktu)", "YEAR(waktu)"];
		}

		$this->db->select(implode(',', array_merge(
			['waktu'],
			$selectTimeParts,
			["{$aggFunc}({$kolomAgg}) AS {$namaSensor}", "MIN({$kolomAgg}) AS min", "MAX({$kolomAgg}) AS max"]
		)))
			->from($tabel)
			->where('code_logger', $idLogger)
			->where('waktu >=', $timeStart)
			->where('waktu <=', $timeEnd);

		foreach ($groupCols as $gc) { $this->db->group_by($gc); }
		$this->db->order_by('waktu', 'ASC');
		$rows = $this->db->get()->result_array();

		$data = [];
		$range = [];
		$data_tabel = [];

		if ($mode === 'hari') {
			$byHour = [];
			foreach ($rows as $r) { $byHour[(int)$r['jam']] = $r; }
			for ($h = 0; $h < 24; $h++) {
				if (!isset($byHour[$h])) {
					$byHour[$h] = ['jam' => $h, 'hari' => $d, 'bulan' => $m, 'tahun' => $Y, $namaSensor => null, 'min' => null, 'max' => null];
				}
			}
			ksort($byHour);
			foreach ($byHour as $r) {
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ",{$r['hari']},{$r['jam']})";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;
				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . number_format((float)$r['min'], 3, '.', '') . "," . number_format((float)$r['max'], 3, '.', '') . "]";
				}
				$jsm = ($r['jam'] > 9) ? $r['jam'] : ('0' . $r['jam']);
				$data_tabel[] = [
					'waktu' => $jsm . ':00',
					'dta'   => ($avg !== null) ? number_format($avg, 3, '.', '') : '-',
					'min'   => ($avg !== null) ? number_format((float)$r['min'], 3, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 3, '.', '') : '-'
				];
			}
			$tooltip = "Waktu %d-%m-%Y %H:%M";
			$nosensor = $sensorKey;
		} elseif ($mode === 'bulan') {
			$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $m, $Y);
			$byDay = [];
			foreach ($rows as $r) { $byDay[(int)$r['hari']] = $r; }
			for ($i = 1; $i <= $daysInMonth; $i++) {
				if (!isset($byDay[$i])) {
					$byDay[$i] = ['hari' => $i, 'bulan' => $m, 'tahun' => $Y, $namaSensor => null, 'min' => null, 'max' => null];
				}
			}
			ksort($byDay);
			foreach ($byDay as $r) {
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ",{$r['hari']})";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;
				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . number_format((float)$r['min'], 3, '.', '') . "," . number_format((float)$r['max'], 3, '.', '') . "]";
				}
				$w = sprintf('%04d-%02d-%02d', $r['tahun'], $r['bulan'], $r['hari']);
				$data_tabel[] = [
					'waktu' => $w,
					'dta'   => ($avg !== null) ? number_format($avg, 3, '.', '') : '-',
					'min'   => ($avg !== null) ? number_format((float)$r['min'], 3, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 3, '.', '') : '-'
				];
			}
			$tooltip = "Tanggal %d-%m-%Y";
			$nosensor = $sensorKey;
		} else {
			$byMonth = [];
			foreach ($rows as $r) { $byMonth[(int)$r['bulan']] = $r; }
			for ($i = 1; $i <= 12; $i++) {
				if (!isset($byMonth[$i])) {
					$byMonth[$i] = ['bulan' => $i, 'tahun' => $Y, $namaSensor => null, 'min' => null, 'max' => null];
				}
			}
			ksort($byMonth);
			foreach ($byMonth as $r) {
				$utc = "Date.UTC({$r['tahun']}," . ($r['bulan'] - 1) . ")";
				$avg = isset($r[$namaSensor]) ? (float)$r[$namaSensor] : null;
				if ($avg !== null) {
					$data[]  = "[ {$utc}," . number_format($avg, 3, '.', '') . "]";
					$range[] = "[ {$utc}," . number_format((float)$r['min'], 3, '.', '') . "," . number_format((float)$r['max'], 3, '.', '') . "]";
				}
				$mm = ($r['bulan'] > 9) ? $r['bulan'] : ('0' . $r['bulan']);
				$data_tabel[] = [
					'waktu' => $r['tahun'] . '-' . $mm,
					'dta'   => ($avg !== null) ? number_format($avg, 3, '.', '') : '-',
					'min   '=> ($avg !== null) ? number_format((float)$r['min'], 3, '.', '') : '-',
					'max'   => ($avg !== null) ? number_format((float)$r['max'], 3, '.', '') : '-'
				];
			}
			$tooltip = "Tanggal %d-%m-%Y";
			$nosensor = $sensorKey;
		}

		$dataAnalisa = [
			'idLogger'    => $idLogger,
			'namaSensor'  => $namaSensor,
			'satuan'      => $satuan,
			'tipe_grafik' => $tipeGrafik,
			'data'        => $data,
			'data_tabel'  => $data_tabel,
			'nosensor'    => $nosensor,
			'range'       => $range,
			'tooltip'     => $tooltip
		];

		$payload = json_encode($dataAnalisa);
		$out = [];
		$out['data_sensor']     = json_decode($payload);
		$out['pilih_pos']       = $this->pilihposarr();
		$out['pilih_parameter'] = $this->pilihparameter($idLogger);
		$out['konten']          = 'konten/back/awr/analisa_awr';

		$this->load->view('template_admin/site', $out);
	}


	function livedata()
	{
		if ($this->session->userdata('logged_in')) {
			$data['konten'] = 'konten/back/arr/analisa_livearr';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}
}
