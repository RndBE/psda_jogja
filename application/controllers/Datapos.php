<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Datapos extends CI_Controller {
	function __construct() {
		parent::__construct();

		//	$this->load->model('m_ketinggian');
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

	function getSatuanByParameter($data, $parameterName) {
		foreach ($data as $item) {
			if (
				isset($item['alias_sensor'], $item['satuan']) &&
				strtolower($item['alias_sensor']) === strtolower($parameterName)
			) {
				return $item['satuan'];
			}
		}
		return null; // jika tidak ditemukan
	}


	public function api()
	{
		$idlogger=$this->input->get('id_logger');
		$tgl_awal=$this->input->get('awal');
		$tgl_akhir=$this->input->get('akhir');
		$select = "";
		$nama_pos = '';
		if(isset($idlogger) && isset($tgl_awal) && isset($tgl_akhir)){
			$dt_logger = $this->db->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.code_logger',$idlogger)->get('t_logger')->row();
			$tabel = $dt_logger->tabel;

			$query_parameter=$this->db->query("SELECT * FROM t_logger INNER JOIN t_sensor ON t_logger.code_logger = t_sensor.logger_code where t_sensor.logger_code = '".$idlogger."'  order by cast(SUBSTRING(field_sensor,7) as unsigned)");
			foreach($query_parameter->result() as $parameter)
			{
				if($parameter->field_sensor == "sensor8" || $parameter->field_sensor == "sensor9" ){
					$select .= "sum(".$parameter->field_sensor.") as ".$parameter->alias_sensor.",";
				}else
				{
					$select .= "avg(".$parameter->field_sensor.") as ".$parameter->alias_sensor.",";
				}

			}
			$select_fix = substr($select, 0, -1);

			$query_data = $this->db->query('select waktu,'.$select_fix.' from '.$tabel.' use index(waktu) where code_logger = "' . $idlogger . '" and waktu >= "' . $tgl_awal . ' 00:00" and waktu <= "' . $tgl_akhir . ' 23:59" group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc ');
			$qd = [];
			if(!$query_data->result()){
				$query_data ="kosong";	
			}else{
				foreach($query_data->result_array() as $k =>$v){
					$qd[$k]['waktu'] = $v['waktu'];
					foreach($v as $x => $q){
						if($x != 'waktu'){
							$qd[$k][str_replace('_',' ',$x)] = number_format($q,2,'.','');
						}

					}
				}
			}
		}else
		{
			$query_parameter="kosong";
			$query_data ="kosong";
		}

		echo json_encode($qd);
	}

	public function process_files() {
		header('Content-Type: application/json');
		$input = json_decode(file_get_contents('php://input'), true);
		$files = $input['files'];
		$tabel = 'weather_station'; // ganti sesuai kebutuhan

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

	public function set_lokasi()
	{
		$data = explode(',', $this->input->post('id_logger'));
		$this->session->set_userdata('data_idlogger',$data[0]);
		$this->session->set_userdata('data_tabel',$data[1]);
		redirect('datapos');
	}

	public function set_range()
	{
		$tgl_awal=$this->input->post('dari');
		$tgl_akhir=$this->input->post('sampai');
		$this->session->set_userdata('data_tglawal',$tgl_awal);
		$this->session->set_userdata('data_tglakhir',$tgl_akhir);

		redirect('datapos');
	}


	public function index2()
	{
		$idlogger=$this->session->userdata('data_idlogger');
		$tgl_awal=$this->session->userdata('data_tglawal');
		$tgl_akhir=$this->session->userdata('data_tglakhir');

		$data['pilih_pos'] = $this->db->from('t_logger')->join('t_lokasi','t_lokasi.id_lokasi=t_logger.lokasi_id')->join('kategori_logger','kategori_logger.id_katlogger=t_logger.katlog_id')->where('t_logger.user_id','4')->order_by('t_logger.katlog_id','asc')->get()->result_array();

		$select = "";
		$nama_pos = '';
		if(isset($idlogger) && isset($tgl_awal) && isset($tgl_akhir)){
			$nama_pos = $this->db->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.code_logger',$idlogger)->get('t_logger')->row()->nama_lokasi;
			$tabel = $this->session->userdata('data_tabel');
			$query_parameter=$this->db->query("SELECT * FROM t_logger INNER JOIN t_sensor ON t_logger.code_logger = t_sensor.logger_code where t_logger.code_logger = '".$idlogger."' and t_logger.user_id='4' order by cast(SUBSTRING(field_sensor,7) as unsigned)");
			foreach($query_parameter->result() as $parameter)
			{
				if($parameter->field_sensor == "sensor8" || $parameter->field_sensor == "sensor9" ){
					$select .= "sum(".$parameter->field_sensor.") as ".$parameter->field_sensor.",";
				}else
				{
					$select .= "avg(".$parameter->field_sensor.") as ".$parameter->field_sensor.",";
				}

			}
			$select_fix = substr($select, 0, -1);
			$query_data = $this->db->query('select waktu,'.$select_fix.' from '.$tabel.' use index(waktu) where code_logger = "' . $idlogger . '" and waktu >= "' . $tgl_awal . '" and waktu <= "' . $tgl_akhir . '" group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc ');
			if(!$query_data->result()){
				$query_data ="kosong";	
			}
		}else
		{
			$query_parameter="kosong";
			$query_data ="kosong";
		}
		$data['parameter']=$query_parameter;
		$data['datapos'] = $query_data;
		$data['nama_lokasi'] = $nama_pos;
		$data['konten']='konten/back/v_datapos';
		$this->load->view('template_admin/site',$data);
	}

	public function index()
	{
		$idlogger=$this->session->userdata('data_idlogger');
		$tgl_awal=$this->session->userdata('data_tglawal');
		$tgl_akhir=$this->session->userdata('data_tglakhir');

		$data['pilih_pos'] = $this->db->from('t_logger')->join('t_lokasi','t_lokasi.id_lokasi=t_logger.lokasi_id')->join('kategori_logger','kategori_logger.id_katlogger=t_logger.katlog_id')->where('t_logger.user_id','4')->order_by('t_logger.katlog_id','asc')->get()->result_array();

		$select = "";
		$nama_pos = '';
		if(isset($idlogger) && isset($tgl_awal) && isset($tgl_akhir)){
			$nama_pos = $this->db->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.code_logger',$idlogger)->get('t_logger')->row()->nama_lokasi;
			$tabel = $this->session->userdata('data_tabel');
			$query_parameter=$this->db->query("SELECT * FROM t_logger INNER JOIN t_sensor ON t_logger.code_logger = t_sensor.logger_code where t_logger.code_logger = '".$idlogger."' and t_logger.user_id='4' order by cast(SUBSTRING(field_sensor,7) as unsigned)");
			foreach($query_parameter->result() as $parameter)
			{
				if($parameter->field_sensor == "sensor8" || $parameter->field_sensor == "sensor9" ){
					$select .= "sum(".$parameter->field_sensor.") as ".$parameter->field_sensor.",";
				}else
				{
					$select .= "avg(".$parameter->field_sensor.") as ".$parameter->field_sensor.",";
				}

			}
			$select_fix = substr($select, 0, -1);
			$query_data = $this->db->query('select waktu,'.$select_fix.' from '.$tabel.' use index(waktu) where code_logger = "' . $idlogger . '" and waktu >= "' . $tgl_awal . '" and waktu <= "' . $tgl_akhir . '" group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc ');
			if(!$query_data->result()){
				$query_data ="kosong";	
			}
		}else
		{
			$query_parameter="kosong";
			$query_data ="kosong";
		}
		$data['parameter']=$query_parameter;
		$data['datapos'] = $query_data;
		$data['nama_lokasi'] = $nama_pos;
		$data['konten']='konten/back/v_datapos2';
		$this->load->view('template_admin/site',$data);
	}

	public function export($data) {
		echo json_encode($data);
	}

	public function tes_ajax(){
		echo json_encode($this->input->post('parameter'));
	}

	function export_excel (){

		include APPPATH.'third_party/PHPExcel/PHPExcel.php';

		// Panggil class PHPExcel nya
		$excel = new PHPExcel();
		// Settingan awal fil excel
		$title = $this->input->post('title');
		$excel->getProperties()->setCreator('Beacon Engineering')
			->setTitle("Data")
			->setDescription("Data Semua Parameter");

		$data = json_decode(htmlspecialchars_decode($this->input->post('data')));
		$parameter = json_decode(htmlspecialchars_decode($this->input->post('parameter')));


		$row = '2';
		$excel->setActiveSheetIndex(0)->setCellValue('A2', 'Waktu');
		$columns = 'B';
		foreach($parameter as $key=>$v){
			$cl = $columns ++;
			$excel->setActiveSheetIndex(0)->setCellValue($cl . $row, str_replace('_',' ', $v->alias_sensor) . ' (' . $v->satuan.')');
		}
		$row2 = 2;
		foreach($data as $k =>$vl){
			$rows = $row2 + 1 + $k ;
			$excel->setActiveSheetIndex(0)->setCellValue('A' . $rows, $vl->waktu);
			$column = 'B';
			foreach($parameter as $key=>$v){
				$cl = $column ++;
				$sensor =$v->field_sensor;
				$excel->setActiveSheetIndex(0)->setCellValue($cl . $rows, number_format($vl->$sensor,2,'.','') );
			}

		}
		foreach(range('A','O') as $columnID) {
			$excel->getActiveSheet()->getColumnDimension($columnID)
				->setAutoSize(true);
		}
		$excel->setActiveSheetIndex(0)->setCellValue('A1', $title);
		$excel->setActiveSheetIndex(0)->mergeCells('A1:'.$cl.'1');
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'.$title.'.xlsx"'); // Set nama file excel nya
		header('Cache-Control: max-age=0');
		$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$write->save('php://output');
	}

	public function excel_export()
	{
		// Matikan output buffer liar (hindari corrupt XLSX karena spasi/notice)
		if (ob_get_length()) { @ob_end_clean(); }
		@ini_set('memory_limit', '1024M');
		@set_time_limit(0);

		// Ambil input
		$title     = $this->input->post('title', true) ?: 'Data';
		$dataJson  = $this->input->post('data');      // encoded JSON string
		$paramJson = $this->input->post('parameter'); // encoded JSON string

		if (!$dataJson) {
			return $this->output
				->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'POST "data" kosong']));
		}

		// Decode (data bisa di-HTML-encode dari FE)
		$data      = json_decode(htmlspecialchars_decode($dataJson));
		$parameter = json_decode(htmlspecialchars_decode($paramJson));

		// Jika parameter tidak dikirim, buat otomatis dari keys data (kecuali waktu)
		if (!$parameter) {
			$parameter = [];
			if (is_array($data) && !empty($data)) {
				$first = (array)$data[0];
				foreach ($first as $k => $v) {
					if (strtolower($k) === 'waktu') continue;
					$parameter[] = (object)[
						'alias_sensor' => $k,
						'satuan'       => '',      // boleh kosong
						'field_sensor' => $k,
					];
				}
			}
		}

		// Load PHPExcel
		include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
		$excel = new PHPExcel();

		// Properti
		$excel->getProperties()
			->setCreator('Beacon Engineering')
			->setTitle($title)
			->setDescription('Data Semua Parameter');

		$sheet = $excel->setActiveSheetIndex(0);
		$rowHeader    = 2;   // baris header kolom
		$rowTitle     = 1;   // baris judul besar
		$colTime      = 'A';
		$sheet->setCellValue($colTime . $rowHeader, 'Waktu');

		// Tulis header parameter mulai kolom B
		$col = 'B';
		if (!is_array($parameter)) $parameter = [];
		foreach ($parameter as $p) {
			$alias  = isset($p->alias_sensor) ? $p->alias_sensor : '';
			$satuan = isset($p->satuan) ? $p->satuan : '';
			$text   = trim(str_replace('_', ' ', $alias) . ($satuan !== '' ? " ($satuan)" : ''));
			$sheet->setCellValue($col . $rowHeader, $text);
			$col++;
		}
		// Kolom terakhir untuk merge judul + autosize
		$lastCol = (isset($p) ? chr(ord($col)-1) : $colTime); // akan diperbaiki di bawah

		// PHP support untuk increment kolom string melewati Z -> AA, AB, ...
		// Di atas, $col++ sudah benar, tapi untuk mencari $lastCol yang akurat:
		$lastCol = $sheet->getHighestColumn(); // paling aman

		// Data rows
		$rowStart = $rowHeader + 1; // baris data pertama = 3
		if (is_array($data)) {
			$waktuKey = 'waktu';
			// cek apakah 'Waktu' kapital
			if (!empty($data)) {
				$first = (array)$data[0];
				if (array_key_exists('Waktu', $first)) $waktuKey = 'Waktu';
			}

			foreach ($data as $i => $item) {
				$itemArr = (array)$item;
				$r = $rowStart + $i;

				// Waktu
				$sheet->setCellValue('A' . $r, isset($itemArr[$waktuKey]) ? $itemArr[$waktuKey] : '');

				// Sensor columns
				$c = 'B';
				foreach ($parameter as $p) {
					$field = isset($p->field_sensor) ? $p->field_sensor : null;
					$val   = ($field !== null && isset($itemArr[$field])) ? $itemArr[$field] : '';
					// numeric format 2 desimal; jika bukan angka, tulis apa adanya
					if (is_numeric($val)) {
						$sheet->setCellValueExplicit($c . $r, number_format((float)$val, 2, '.', ''), PHPExcel_Cell_DataType::TYPE_STRING);
					} else {
						$sheet->setCellValue($c . $r, $val);
					}
					$c++;
				}
			}
		}

		// Autosize tiap kolom sampai kolom terakhir yang terpakai
		$firstColIndex = PHPExcel_Cell::columnIndexFromString('A'); // 1
		$lastColIndex  = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); // misal 'AD' -> 30
		for ($i = $firstColIndex; $i <= $lastColIndex; $i++) {
			$colLetter = PHPExcel_Cell::stringFromColumnIndex($i - 1);
			$sheet->getColumnDimension($colLetter)->setAutoSize(true);
		}

		// Judul besar + merge
		$sheet->setCellValue('A' . $rowTitle, $title);
		$sheet->mergeCells('A' . $rowTitle . ':' . $sheet->getHighestColumn() . $rowTitle);
		$sheet->getStyle('A' . $rowTitle)->getFont()->setBold(true)->setSize(14);

		// Freeze header (baris 3 jadi awal scroll)
		$sheet->freezePane('A3');

		// Border header
		$sheet->getStyle('A' . $rowHeader . ':' . $sheet->getHighestColumn() . $rowHeader)
			->getFont()->setBold(true);

		// Output ke client
		$filename = preg_replace('/[^A-Za-z0-9_\- ]/', '_', $title) . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
		$writer->save('php://output');
		exit;
	}
}
