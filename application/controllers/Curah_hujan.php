<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class  Curah_hujan extends CI_Controller
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

		redirect('curah_hujan/analisa');
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

		redirect('curah_hujan/analisa');
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

		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='1' and t_logger.user_id = '4' and icon='arr' ");

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

		redirect('curah_hujan/analisa');
	}

	##### set Parameter #####
	public function pilihparameter($idlogger)
	{
		$data = array();
		if($idlogger == '10114'){
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "' and alias_sensor != 'Kedalaman_Air_Sumur'");
		}else{
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlogger . "'");
		}
		
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
		redirect('curah_hujan/analisa');
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
		redirect('curah_hujan/analisa');
	}

	function settgl()
	{
		$tgl = str_replace('/', '-', $this->input->post('tgl'));
		$this->session->set_userdata('pada', $tgl);
		redirect('curah_hujan/analisa');
	}

	function setbulan()
	{
		$tgl = str_replace('/', '-', $this->input->post('bulan'));
		$this->session->set_userdata('pada', $tgl);
		redirect('curah_hujan/analisa');
	}

	function settahun()
	{
		$tgl = str_replace('/', '-', $this->input->post('tahun'));
		$this->session->set_userdata('pada', $tgl);
		redirect('curah_hujan/analisa');
	}

	function setrange()
	{
		$this->session->set_userdata('dari', $this->input->post('dari'));
		$this->session->set_userdata('sampai', $this->input->post('sampai'));
		redirect('curah_hujan/analisa');
	}


	function analisa()
	{

		if ($this->session->userdata('logged_in')) {
			$data = array();
			$min = array();
			$max = array();
			$range = array();
			$data_tabel = array();
			$string = $this->session->userdata('pada');
			$timestamp = strtotime($string);
			$hari =  date("d", $timestamp);
			$bulan =  date("m", $timestamp);
			$tahun = date("Y", $timestamp);
			if ($this->session->userdata('data') == 'hari') {
				$sensor = $this->session->userdata('kolom');
				if ($this->session->userdata('tipe_grafik') == 'column') {
					$nama_sensor = "Akumulasi_" . $this->session->userdata('nama_parameter');
					$select = 'sum(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				} else {
					$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
					$select = 'avg(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				}
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu,HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " USE INDEX (waktu)  where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . " 00:00' and waktu <= '" . $this->session->userdata('pada') . " 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();

				foreach ($query_data as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog['tahun'] . "," . $datalog['bulan'] . "-1," . $datalog['hari'] . "," . $datalog['jam'] . ")," . number_format($datalog[$nama_sensor], 3,'.', '') . "]";
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
					'nosensor' => $sensor,
					'range' => $range,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			} elseif ($this->session->userdata('data') == 'bulan') {
				$sensor = $this->session->userdata('kolom');
				if ($this->session->userdata('tipe_grafik') == 'column') {
					$nama_sensor = "Akumulasi_" . $this->session->userdata('nama_parameter');
					$select = 'sum(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				} else {
					$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
					$select = 'avg(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				}
				$satuan = $this->session->userdata('satuan');

				$d = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

				$query_data = $this->db->query("SELECT waktu,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " USE INDEX (waktu)  where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >= '" . $this->session->userdata('pada') . "-01 00:00' and waktu <= '" . $this->session->userdata('pada') . "-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu)  order by waktu asc;")->result_array();
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
			} elseif ($this->session->userdata('data') == 'tahun') {


				$sensor = $this->session->userdata('kolom');
				if ($this->session->userdata('tipe_grafik') == 'column') {
					$nama_sensor = "Akumulasi_" . $this->session->userdata('nama_parameter');
					$select = 'sum(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				} else {
					$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
					$select = 'avg(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				}
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu > '" . $this->session->userdata('pada') . "-01-01 00:00' and waktu < '" . $this->session->userdata('pada') . "-12-31 23:59' group by MONTH(waktu),YEAR(waktu)  order by waktu asc;")->result_array();
				
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
			} elseif ($this->session->userdata('data') == 'range') {
				$data = array();
				$min = array();
				$max = array();

				$sensor = $this->session->userdata('kolom');
				if ($this->session->userdata('tipe_grafik') == 'column') {
					$nama_sensor = "Akumulasi_" . $this->session->userdata('nama_parameter');
					$select = 'sum(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				} else {
					$nama_sensor = "Rerata_" . $this->session->userdata('nama_parameter');
					$select = 'avg(' . $this->session->userdata('kolom') . ')as ' . $nama_sensor;
				}
				$satuan = $this->session->userdata('satuan');

				$query_data = $this->db->query("SELECT waktu,HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $sensor . ") as min,max(" . $sensor . ") as max FROM " . $this->session->userdata('tabel') . " where code_logger='" . $this->session->userdata('idlogger') . "' and waktu >='" . $this->session->userdata('dari') . "' and waktu <='" . $this->session->userdata('sampai') . "' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

				foreach ($query_data->result() as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($datalog->$nama_sensor, 3) . "]";
					$range[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . $datalog->min . "," . $datalog->max . "]";
					$data_tabel[] = array(
						'waktu' => date('Y-m-d H', strtotime($datalog->waktu)) . ':00:00',
						'dta' => number_format($datalog->$nama_sensor, 2),
						'min' => number_format($datalog->min, 2),
						'max' => number_format($datalog->max, 2)
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
					'tooltip' => "Waktu %d-%m-%Y %H:%M",
					'tooltipper' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam = json_encode($dataAnalisa);
				$data['data_sensor'] = json_decode($dataparam);
			}


			$data['pilih_pos'] = $this->pilihposarr();
			$data['pilih_parameter'] = $this->pilihparameter($this->session->userdata('idlogger'));
			$data['konten'] = 'konten/back/arr/analisa_arr';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}
	
	function importcsv() {

		$data['error'] = '';    //initialize image upload error array to empty

		$this->load->library('upload');
		$config['upload_path'] = './upload_csv/';
		$config['allowed_types'] = 'csv|txt';
		$config['max_size'] = 1000;
		$this->upload->initialize($config);

		// If upload failed, display error
		if (!$this->upload->do_upload('userfile')) {
			echo 'UPLOAD GAGAL';
			exit;
		} else {
			$file_data = $this->upload->data();
			$file_path =  './upload_csv/'.$file_data['file_name'];
			
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			$csvreader = PHPExcel_IOFactory::createReader('CSV');
			$loadcsv = $csvreader->load($file_path); // Load file yang tadi diupload ke folder csv
			$sheet = $loadcsv->getActiveSheet()->getRowIterator();

			$dataup = array();

			$numrow = 1;
			foreach($sheet as $row){
				
				if($numrow > 1){
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells(false);

					$get = array(); 
					foreach ($cellIterator as $cell) {
						array_push($get, $cell->getValue());
					}
					$waktu =  date("Y-m-d H:i", strtotime($get[1].' '.$get[2]));
					$cek = $this->db->where('code_logger',$get[0])->where('waktu',$waktu)->get('weather_station')->row();
					
					if(!$cek){
						array_push($dataup, array(
							'code_logger'=>$get[0],
							'waktu'=>$waktu,
							'sensor1'=>$get[3],
							'sensor2'=>$get[4],
							'sensor3'=>$get[5],
							'sensor4'=>$get[6],
							'sensor5'=>$get[7],
							'sensor6'=>$get[8],
							'sensor7'=>$get[9],
							'sensor8'=>$get[10],
							'sensor9'=>$get[11],
							'sensor10'=>$get[12],
							'sensor11'=>$get[13],
							'sensor12'=>$get[14],
							'sensor13'=>$get[15],
							'sensor14'=>$get[16],
							'sensor15'=>$get[17],
							'sensor16'=>$get[18],
						));
					}
				}

				$numrow++;
			}
			$this->db->insert_batch('weather_station', $dataup);
			sleep(3);
			if(file_exists($file_path)){
				$this->session->set_flashdata('success', 'Csv Data Sukses di import');
			}else{
				$this->session->set_flashdata('success', 'Csv Data Sukses di import <br/> Sinkronisasi data gagal');
			}
			redirect('curah_hujan/analisa');
		}
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
