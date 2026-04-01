<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	function __construct()
	{
		parent :: __construct();
		$this->load->model('mlogin');
		$this->load->model('m_analisa');
	}

	function data_menit(){ 
		$idlogger= $this->input->get('id_logger'); 
		$awal = $this->input->get('tgl') . ' 00:00'; 
		$akhir = $this->input->get('tgl') . ' 23:59'; 
		$data_logger = $this->db->join('kategori_logger','kategori_logger.id_katlogger=t_logger.katlog_id')->join('t_lokasi','t_lokasi.id_lokasi=t_logger.lokasi_id')->where('t_logger.code_logger',$idlogger)->get('t_logger')->row(); 
		$parameter_sensor = $this->db->where('logger_code',$idlogger)->get('t_sensor')->result_array();
		$dt = []; 
		if($data_logger){ 
			$data = $this->db->query('select * from '.$data_logger->tabel.' where code_logger = "'.$idlogger.'" and waktu >= "'.$awal.'" and waktu <= "'.$akhir.'"')->result_array(); 
			if($data){ 
				foreach($data as $key2=>$v){ 
					$dt[$key2]['waktu'] = $v['waktu'];
					foreach($parameter_sensor as $key=>$val){ 
						$field_sensor = $val['field_sensor']; 
						$nama_parameter = $val['alias_sensor'];
						$dt[$key2]['data'][] = [ 
							'namaParameter'=>$nama_parameter, 
							'nilai'=> number_format($v[$field_sensor],2,'.',''), 
							'satuan'=>$val['satuan'], 
						]; 
					} 
				} 
			} 
			$vt = [ 
				'status'=>true, 
				'namaPos'=>$data_logger->nama_lokasi, 
				'data'=>$dt 
			]; 
			echo json_encode($vt); 
		}else{ 
			$vt = [ 
				'status'=>false, 
				'message'=>'ID Logger Tidak Terdaftar' 
			]; 
			echo json_encode($vt); 
		} 
	}

	function get_data_first ($id_logger) {
		$tabel = $this->db->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->where('code_logger',$id_logger)->get('t_logger')->row();
		$data= $this->db->query("SELECT waktu FROM `".$tabel->tabel."` WHERE code_logger='".$id_logger."' ORDER by waktu ASC LIMIT 1;")->row();
		echo json_encode($data);
	}

	function login_app()
	{
		$username = $this->input->get('username');
		$password = md5($this->input->get('password'));
		$this->mlogin->apiambilPengguna($username, $password);
	}

	function daftar_pos(){
		$data2 = array();
		$data['awlr_stts'] = $data2;
		$kb = $this->db->group_by('kabupaten')->get('t_alamat')->result_array();

		$logger = $this->db->join('t_logger','t_alamat.id_logger=t_logger.code_logger')->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->get('t_alamat')->result_array();

		$s = [];
		foreach($kb as $key=>$val){
			$s[$key]['kabupaten'] = $val['kabupaten'];
			$sw = [];
			foreach($logger as $k=>$v){
				if($v['kabupaten'] == $val['kabupaten']){
					$sw[]= [
						'nama_lokasi'=>$v['nama_pos'],
						'alamat'=>$v['alamat'],
						'gmaps'=>$v['gmaps'],
					];
				}

			}
			$s[$key]['list'] = $sw;
		}


		echo json_encode($s);
	}

	function list_logger () {
		$data = $this->db->select('code_logger as id_logger,nama_lokasi,latitude,longitude,alamat,kabupaten,sensor,nosell as nomor_seluler, nama_pic as nama_penjaga, no_pic as no_penjaga,das')
			->join('t_alamat','t_alamat.id_logger = t_logger.code_logger')
			->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')
			->join('t_informasi', 't_logger.code_logger = t_informasi.logger_id')
			->where('t_logger.user_id', '4')
			->order_by('code_logger','asc')
			->get('t_logger')
			->result_array(); 

		foreach($data as &$logger) {
			// Ambil data logger lengkap untuk akses tabel temp_tabel
			$data_logger = $this->db->where('code_logger', $logger['id_logger'])
				->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')
				->get('t_logger')->row();

			if ($data_logger) {
				// Cek waktu update terakhir di tabel temp_tabel
				$temp_data = $this->db->where('code_logger', $logger['id_logger'])
					->order_by('waktu', 'desc')
					->get($data_logger->temp_tabel)
					->row();

				if ($temp_data) {
					$waktu = $temp_data->waktu;
					$awal2 = date('Y-m-d H:i', strtotime('-1 hour')); // waktu 1 jam lalu

					// Tentukan koneksi berdasar waktu update terakhir
					$logger['koneksi'] = ($awal2 < $waktu) ? 'Terhubung' : 'Terputus';
				} else {
					$logger['koneksi'] = 'Tidak Ada Data';
				}
			} else {
				$logger['koneksi'] = 'Logger Tidak Ditemukan';
			}
		}

		echo json_encode($data);
	}


	function data_range () {
		$id_logger = $this->input->get('id_logger');
		$awal = $this->input->get('awal');
		$akhir = $this->input->get('akhir');
		$interval = $this->input->get('interval');

		$nama_parameter = $this->input->post('parameter');

		if(!$id_logger){
			echo json_encode(['status'=>false]);
			exit;
		}

		$this->db->where('logger_code', $id_logger)
			->not_like('t_sensor.alias_sensor', 'Logger');

		if ($nama_parameter) {
			if (is_array($nama_parameter)) {
				$this->db->where_in('t_sensor.alias_sensor', $nama_parameter);
			} else {
				$this->db->where('t_sensor.alias_sensor', $nama_parameter);
			}
		}

		$param = $this->db->get('t_sensor')->result_array();

		$query = '';
		$last_key = array_key_last($param);



		$data_logger = $this->db->where('t_logger.code_logger',$id_logger)->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->get('t_logger')->row();

		if($interval == 'menit'){
			foreach($param as $k => $v) {
				if($k != $last_key){
					$query .= $v["field_sensor"].' as '.$v['alias_sensor'] . ', ';
				}else{
					$query .= $v["field_sensor"].' as '.$v['alias_sensor'];
				}
			}
			$query_data = $this->db->query("SELECT waktu as Waktu, ".$query." FROM ".$data_logger->tabel." where code_logger='".$id_logger."' and waktu >= '".$awal." 00:00' and waktu <= '".$akhir." 23:59'");
		}else{
			foreach($param as $k => $v) {
				if($k != $last_key){
					if($v['satuan'] == 'mm'){
						$query .= 'CONCAT('.'FORMAT(sum('.$v["field_sensor"].'), 2)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'] . ', ';

					}else{
						$query .= 'CONCAT('.'FORMAT(avg('.$v["field_sensor"].'), 3)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'] . ', ';
					}
				}else{
					if($v['satuan'] == 'mm'){
						$query .= 'CONCAT('.'FORMAT(sum('.$v["field_sensor"].'), 2)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'];
					}else{
						$query .= 'CONCAT('.'FORMAT(avg('.$v["field_sensor"].'), 3)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'];
						//$query .= 'FORMAT(avg('.$v["field_sensor"].'), 2) as '.$v['alias_sensor'];
					}
				}
			}
			$interval_num = $interval != 'hari' ? '60':'1440';
			if($interval == 'hari'){
				$query_data = $this->db->query("SELECT DATE_FORMAT(
    FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(waktu) / (5 * 60)) * (5 * 60)),
    '%Y-%m-%d'
) AS Waktu, ".$query." FROM ".$data_logger->tabel." where code_logger='".$id_logger."' and waktu >= '".$awal." 00:00' and waktu <= '".$akhir." 23:59' group by TIMESTAMPDIFF(MINUTE, '1970-01-01 00:00:00', waktu) DIV ".$interval_num)->result_array();
			}else{
				$query_data = $this->db->query("SELECT FROM_UNIXTIME(
        ROUND(UNIX_TIMESTAMP(waktu) / (5 * 60)) * (5 * 60)
    ) AS Waktu, ".$query." FROM ".$data_logger->tabel." where code_logger='".$id_logger."' and waktu >= '".$awal." 00:00' and waktu <= '".$akhir." 23:59' group by TIMESTAMPDIFF(MINUTE, '1970-01-01 00:00:00', waktu) DIV ".$interval_num)->result_array();
			}
		}

		$temp_data = $this->db->where('code_logger',$id_logger)->get($data_logger->temp_tabel)->row();
		$waktu=$temp_data->waktu;
		$awal2=date('Y-m-d H:i',(mktime(date('H')-1)));
		$koneksi = $awal2 < $waktu ? 'Terhubung' : 'Terputus';

		$query_data[0]['Koneksi'] = $koneksi;
		echo json_encode($query_data);
	}

	function data_range2 () {
		$id_logger = $this->input->post('id_logger');
		$awal = $this->input->post('awal');
		$akhir = $this->input->post('akhir');

		$nama_parameter = $this->input->post('parameter');

		if(!$id_logger){
			echo json_encode(['status'=>false]);
			exit;
		}

		$this->db->where('logger_code', $id_logger)
			->not_like('t_sensor.alias_sensor', 'Logger');

		if ($nama_parameter) {
			if (is_array($nama_parameter)) {
				$this->db->where_in('t_sensor.alias_sensor', $nama_parameter);
			} else {
				$this->db->where('t_sensor.alias_sensor', $nama_parameter);
			}
		}

		$param = $this->db->get('t_sensor')->result_array();

		$query = '';
		$last_key = array_key_last($param);

		$data_logger = $this->db->where('t_logger.code_logger',$id_logger)->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->get('t_logger')->row();


		foreach($param as $k => $v) {
			if($k != $last_key){
				if($v['satuan'] == 'mm'){
					$query .= 'CONCAT('.'FORMAT(sum('.$v["field_sensor"].'), 2)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'] . ', ';

				}else{
					$query .= 'CONCAT('.'FORMAT(avg('.$v["field_sensor"].'), 3)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'] . ', ';
				}
			}else{
				if($v['satuan'] == 'mm'){
					$query .= 'CONCAT('.'FORMAT(sum('.$v["field_sensor"].'), 2)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'];
				}else{
					$query .= 'CONCAT('.'FORMAT(avg('.$v["field_sensor"].'), 3)'.', " '.$v["satuan"].'") as '.$v['alias_sensor'];
				}
			}
		}
		$query_data = $this->db->query("SELECT DATE_FORMAT(
    FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(waktu) / (5 * 60)) * (5 * 60)),
    '%Y-%m-%d'
) AS Waktu, ".$query." FROM ".$data_logger->tabel." where code_logger='".$id_logger."' and waktu >= '".$awal." 00:00' and waktu <= '".$akhir." 23:59' group by TIMESTAMPDIFF(MINUTE, '1970-01-01 00:00:00', waktu) DIV 1440")->result_array();
		echo json_encode($query_data);
	}

	function compare_param () {
		$awal = $this->input->post('awal');
		$akhir = $this->input->post('akhir');

		$nama_parameter = $this->input->post('parameter');

		$query_first = $this->db->select('logger_code')->join('t_logger','t_logger.code_logger = t_sensor.logger_code')->where('t_logger.user_id','4')->not_like('t_sensor.alias_sensor', 'Logger');

		if ($nama_parameter) {
			if (is_array($nama_parameter)) {
				$query_sec = $query_first->where_in('t_sensor.alias_sensor', $nama_parameter);
			} else {
				$query_sec = $query_first->where('t_sensor.alias_sensor', $nama_parameter);
			}
		}
		$param_list = $query_sec->group_by('t_sensor.logger_code')->get('t_sensor')->result_array();
		//$logger_list = $query_sec->group_by('t_sensor.logger_code')->get('t_sensor')->result_array();

		foreach($param_list as $k=>$v){
			$id_logger = $v['logger_code'];
			$logger_data = $this->db->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('code_logger',$id_logger)->get('t_logger')->row();

			$query_first = $this->db->select('t_sensor.id as id_param, alias_sensor as nama_param, field_sensor as kolom,satuan')->not_like('t_sensor.alias_sensor', 'Logger');
			if ($nama_parameter) {
				if (is_array($nama_parameter)) {
					$query_sec = $query_first->where_in('t_sensor.alias_sensor', $nama_parameter);
				} else {
					$query_sec = $query_first->where('t_sensor.alias_sensor', $nama_parameter);
				}
			}
			$param_list2 = $query_sec->where('logger_code',$v['logger_code'])->get('t_sensor')->result_array();

			$query = '';
			$last_key = array_key_last($param_list2);
			//$param_list[$k]['last_key'] = $last_key;

			$param_list[$k]['nama_lokasi'] = $logger_data->nama_lokasi;
			foreach($param_list2 as $ps => $v) {
				if($ps != $last_key){
					if($v['satuan'] == 'mm'){
						$query .= 'CONCAT('.'FORMAT(sum('.$v["kolom"].'), 2)'.', " '.$v["satuan"].'") as '.$v['nama_param'] . ', ';

					}else{
						$query .= 'CONCAT('.'FORMAT(avg('.$v["kolom"].'), 3)'.', " '.$v["satuan"].'") as '.$v['nama_param'] . ', ';
					}
				}else{
					if($v['satuan'] == 'mm'){
						$query .= 'CONCAT('.'FORMAT(sum('.$v["kolom"].'), 2)'.', " '.$v["satuan"].'") as '.$v['nama_param'];
					}else{
						$query .= 'CONCAT('.'FORMAT(avg('.$v["kolom"].'), 3)'.', " '.$v["satuan"].'") as '.$v['nama_param'];
					}
				}
			}
			$query_data = $this->db->query("SELECT DATE_FORMAT(
    FROM_UNIXTIME(ROUND(UNIX_TIMESTAMP(waktu) / (5 * 60)) * (5 * 60)),
    '%Y-%m-%d'
) AS Waktu, ".$query." FROM ".$logger_data->tabel." where code_logger='".$id_logger."' and waktu >= '".$awal." 00:00' and waktu <= '".$akhir." 23:59'")->row_array();
			foreach($param_list2 as $s=>$pr){
				$alias_sensor = $pr['nama_param'];
				$param_list[$k][$alias_sensor] = $query_data[$alias_sensor];
			}	
		}
		echo json_encode($param_list);
	}

	function data_new () {
		$id_logger = $this->input->get('id_logger');
		$nama_parameter = $this->input->get('parameter');

		if(!$id_logger){
			echo json_encode(['status'=>false]);
			exit;
		}

		$param = $this->db->not_like('t_sensor.alias_sensor','Logger')->where('logger_code',$id_logger)->get('t_sensor')->result_array();

		$query = '';
		$last_key = array_key_last($param);
		$data_logger = $this->db->where('t_logger.code_logger',$id_logger)->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->get('t_logger')->row();
		$selectParts = [];
		$curah_hujan = [];
		foreach ($param as $k => $v) {
			if($v['satuan'] != 'mm'){
				$selectParts[] = 'CONCAT('.$v["field_sensor"].', " '.$v["satuan"].'") as '.$v['alias_sensor'];
			}else{
				$curah_hujan[] = 'CONCAT(format(sum('.$v["field_sensor"].'),2), " '.$v["satuan"].'") as '.$v['alias_sensor'];
			}
		}

		$query = implode(",\n", $selectParts);
		$query_data = [];
		if($selectParts){
			$query_data = $this->db->query("SELECT waktu as Waktu, ".$query." FROM ".$data_logger->temp_tabel." where code_logger='".$id_logger."'")->row_array();	
		}else{
			$query_data = $this->db->query("SELECT waktu as Waktu FROM ".$data_logger->temp_tabel." where code_logger='".$id_logger."'")->row_array();	
		}
		$query_hujan = implode(",\n", $curah_hujan);

		$hour_now = date('Y-m-d H');
		$query_data2 = [];
		if($curah_hujan){
			$query_data2 = $this->db->query("SELECT " . $query_hujan . " FROM " . $data_logger->tabel . " where code_logger='" . $id_logger . "' and waktu >= '".$hour_now.":00' and waktu <= '".$hour_now.":59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->row_array();
			if($query_data2){
				$new_array = array_merge($query_data, $query_data2);
			}else{
				$new_array = $query_data;
			}
		}else{
			$new_array = $query_data;
		}
		$waktu=$query_data['Waktu'];
		$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
		$koneksi = $awal < $waktu ? 'Terhubung' : 'Terputus';
		$new_array['Koneksi'] = $koneksi;
		echo json_encode([$new_array]);
	}

	function data_new2 () {
		$id_logger = $this->input->post('id_logger');
		$nama_parameter = $this->input->post('parameter');

		if(!$id_logger){
			echo json_encode(['status'=>false]);
			exit;
		}

		$this->db->where('logger_code', $id_logger)
			->not_like('t_sensor.alias_sensor', 'Logger');

		if ($nama_parameter) {
			if (is_array($nama_parameter)) {
				$this->db->where_in('t_sensor.alias_sensor', $nama_parameter);
			} else {
				$this->db->where('t_sensor.alias_sensor', $nama_parameter);
			}
		}

		$param = $this->db->get('t_sensor')->result_array();

		$query = '';
		$last_key = array_key_last($param);
		$data_logger = $this->db->where('t_logger.code_logger',$id_logger)->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->get('t_logger')->row();
		$selectParts = [];
		$curah_hujan = [];
		foreach ($param as $k => $v) {
			if($v['satuan'] != 'mm'){
				$selectParts[] = 'CONCAT('.$v["field_sensor"].', " '.$v["satuan"].'") as '.$v['alias_sensor'];
			}else{
				$curah_hujan[] = 'CONCAT(format(sum('.$v["field_sensor"].'),2), " '.$v["satuan"].'") as '.$v['alias_sensor'];
			}
		}

		$query = implode(",\n", $selectParts);
		$query_data = [];
		if($selectParts){
			$query_data = $this->db->query("SELECT waktu as Waktu, ".$query." FROM ".$data_logger->temp_tabel." where code_logger='".$id_logger."'")->row_array();	
		}else{
			$query_data = $this->db->query("SELECT waktu as Waktu FROM ".$data_logger->temp_tabel." where code_logger='".$id_logger."'")->row_array();	
		}
		$query_hujan = implode(",\n", $curah_hujan);

		$hour_now = date('Y-m-d H');
		$query_data2 = [];
		if($curah_hujan){
			$query_data2 = $this->db->query("SELECT " . $query_hujan . " FROM " . $data_logger->tabel . " where code_logger='" . $id_logger . "' and waktu >= '".$hour_now.":00' and waktu <= '".$hour_now.":59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->row_array();
			if($query_data2){
				$new_array = array_merge($query_data, $query_data2);
			}else{
				$new_array = $query_data;
			}
		}else{
			$new_array = $query_data;
		}
		$waktu=$query_data['Waktu'];
		$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
		$koneksi = $awal < $waktu ? 'Terhubung' : 'Terputus';
		$new_array['Koneksi'] = $koneksi;
		echo json_encode([$new_array]);
	}

	function get_rain () {
		$awal = $this->input->get('awal');
		$akhir = $this->input->get('akhir');
		$data = $this->db->select('code_logger as id_logger,t_lokasi.nama_lokasi')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_logger.katlog_id','1')->where('t_logger.user_id','4')->get('t_logger')->result_array();

		foreach($data as $k=> $v){
			$sensor_hujan = $this->db->select('field_sensor,alias_sensor,satuan')->where('logger_code',$v['id_logger'])->like('alias_sensor','Curah_Hujan')->get('t_sensor')->result_array();
			if(count($sensor_hujan) > 1){
				$sensor_hujan = $sensor_hujan[1];
			}else{
				$sensor_hujan = $sensor_hujan[0];
			}
			$hour_now = date('Y-m-d H');

			$query_hujan = 'format(sum('.$sensor_hujan['field_sensor'].'),2) as '.$sensor_hujan['alias_sensor'];
			$query = 'SELECT ' . $query_hujan . ' FROM weather_station where code_logger="' . $v['id_logger'] . '" and waktu >= "'.$hour_now.':00" and waktu <= "'.$hour_now.':59" group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;';

			$query_data2 = $this->db->query($query)->row_array();
			$nama_sensor = $sensor_hujan['alias_sensor'];
			$nilai_ch = $query_data2[$nama_sensor];
			if($nilai_ch >= 0 && $nilai_ch <= 0.1) 
			{
				$status_pantau = 'Tidak Hujan';
			}
			elseif($nilai_ch >= 0.1 && $nilai_ch < 1) 
			{
				$status_pantau = 'Hujan Sangat Ringan';
			}
			elseif($nilai_ch >= 1 && $nilai_ch < 5) 
			{
				$status_pantau = 'Hujan Ringan';
			}
			elseif($nilai_ch >= 5 && $nilai_ch < 10) 
			{
				$status_pantau = 'Hujan Sedang';
			}
			elseif($nilai_ch >= 10 && $nilai_ch < 20) 
			{
				$status_pantau = 'Hujan Lebat';
			}
			elseif($nilai_ch >= 20) 
			{
				$status_pantau = 'Hujan Sangat Lebat';
			}

			$data[$k]['curah_hujan'] = $nilai_ch. ' mm';
			$data[$k]['status'] = $status_pantau;
		}
		echo json_encode($data);
	}

	function get_logger() {
		$data = $this->db->select('alias_sensor')->join('t_logger','t_logger.code_logger = t_sensor.logger_code')->where('t_logger.user_id','4')->group_by('alias_sensor')->get('t_sensor')->result_array();
		$sw = [];
		foreach($data as $k => $s){
			array_push($sw, $s['alias_sensor']);
		}
		echo json_encode($sw);
	}

	function monitoring () {
		$id_kategori = $this->input->get('id_kategori');
		$tanggal_rekap = $this->input->get('tanggal_rekap');

		$data_rekap = array();
		if($id_kategori=='1'){
			$data['logger'] = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->select('t_lokasi.nama_lokasi,t_logger.code_logger, t_logger.nama_logger,t_logger.katlog_id')->where('t_logger.user_id', '4')->where('icon', 'ws')->order_by('code_logger','asc')->get('t_logger')->result_array();
		}elseif($id_kategori=='2'){
			$data['logger'] = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->select('t_lokasi.nama_lokasi,t_logger.code_logger, t_logger.nama_logger,t_logger.katlog_id')->where('t_logger.user_id', '4')->where('icon', 'arr')->order_by('code_logger','asc')->get('t_logger')->result_array();
		}else{
			$data['logger'] = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->select('t_lokasi.nama_lokasi,t_logger.code_logger, t_logger.nama_logger,t_logger.katlog_id')->where('t_logger.user_id', '4')->where('katlog_id', $id_kategori)->order_by('code_logger','asc')->get('t_logger')->result_array();
		}

		foreach($data['logger'] as $key=>$val){
			if($val['katlog_id']=='1' or $val['katlog_id']=='2'){
				$tabel = 'weather_station';
				$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor9'))->row();
				if($data_sensor){
					$select = 'sum(sensor9) as ' . 'nilai';
				}else{
					$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor8'))->row();
					$select = 'sum(sensor8) as ' . 'nilai';
				}

			}else{
				$tabel = 'awlr';
				$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor1'))->row();
				$select = 'avg(sensor1) as ' . 'nilai';
			}
			$query_data = $this->db->query("SELECT HOUR(waktu) as jam," . $select . " FROM " . $tabel . " where code_logger='" . $val['code_logger'] . "' and waktu >= '".$tanggal_rekap." 00:00' and waktu <= '".$tanggal_rekap." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();

			for ($i = 0; $i < 24; $i++) {
				if (array_search($i, array_column($query_data, 'jam')) !== false) {
				} else {
					array_push($query_data, array('jam' => ($i > 9) ? $i:'0'.$i , 'nilai' => '-'));					
				}
			}
			array_multisort(array_column($query_data, "jam"), SORT_ASC, $query_data);
			$data_rekap[] = array(
				'id_logger' => $val['code_logger'],
				'nama_logger' => $val['nama_lokasi'],

				'id_param'=>$data_sensor->id,
				'nama_param'=>$data_sensor->alias_sensor,

				'kolom_sensor'=>$data_sensor->field_sensor,

				'nama_lokasi' => $val['nama_lokasi'],
				'data' => $query_data,
			);
		}
		if($id_kategori == '1' or $id_kategori == '2'){
			foreach($data_rekap as $key=> $dtr){
				$data_rekap[$key]['tabel']= 'weather_station';
				$data_rekap[$key]['akumulasi'] = 0.0;
				foreach($dtr['data'] as $key2 => $dt_q){
					$nilai[] = $dt_q['nilai'];
					if($dt_q['nilai'] != '-'){

						$data_rekap[$key]['data'][$key2]['nilai'] = number_format($dt_q['nilai'],2);

						$data_rekap[$key]['akumulasi'] += $dt_q['nilai'];
						if($dt_q['nilai'] >= 0 && $dt_q['nilai'] < 0.1) {
							$data_rekap[$key]['data'][$key2]['warna'] = '0xffffffff';
						}
						elseif($dt_q['nilai'] >= 0.1 && $dt_q['nilai'] < 1) {
							$data_rekap[$key]['data'][$key2]['warna'] = '0xff70cddd';
						}
						elseif($dt_q['nilai'] >=  1 && $dt_q['nilai'] <  5){
							$data_rekap[$key]['data'][$key2]['warna'] = '0xff35549d';
						}
						elseif($dt_q['nilai'] >=  5 && $dt_q['nilai'] <  10) {
							$data_rekap[$key]['data'][$key2]['warna'] = '0xfffef216';
						}
						elseif($dt_q['nilai'] >=  10 && $dt_q['nilai'] <  20) {
							$data_rekap[$key]['data'][$key2]['warna'] = '0xfff47e2c';
						}
						elseif($dt_q['nilai'] >=  20) {
							$data_rekap[$key]['data'][$key2]['warna'] = '0xffed1c24';
						}
					}else{
						$data_rekap[$key]['data'][$key2]['warna'] = '0xffffffff';
					}

				}
				$data_rekap[$key]['akumulasi'] = number_format($data_rekap[$key]['akumulasi'],2);
				if($data_rekap[$key]['akumulasi'] >= 0 && $data_rekap[$key]['akumulasi'] < 0.1) {
					$data_rekap[$key]['warna_akum'] = '0xffffffff';
				}
				elseif($data_rekap[$key]['akumulasi'] >= 0.1 && $data_rekap[$key]['akumulasi'] < 5) {
					$data_rekap[$key]['warna_akum'] = '0xff70cddd';
				}
				elseif($data_rekap[$key]['akumulasi'] >=  5 && $data_rekap[$key]['akumulasi'] <  20){
					$data_rekap[$key]['warna_akum'] = '0xff35549d';
				}
				elseif($data_rekap[$key]['akumulasi'] >=  20 && $data_rekap[$key]['akumulasi'] <  50) {
					$data_rekap[$key]['warna_akum'] = '0xfffef216';
				}
				elseif($data_rekap[$key]['akumulasi'] >=  50 && $data_rekap[$key]['akumulasi'] <  100) {
					$data_rekap[$key]['warna_akum'] = '0xfff47e2c';
				}
				elseif($data_rekap[$key]['akumulasi'] >=  100) {
					$data_rekap[$key]['warna_akum'] = '0xffed1c24';
				}
			}
		}else{
			foreach($data_rekap as $key=> $dtr){
				$data_rekap[$key]['tabel']= 'awlr';
				$data_rekap[$key]['akumulasi'] = 0.0;
				$data_rekap[$key]['warna_akum'] = '0xffffffff';
				foreach($dtr['data'] as $key2 => $dt_q){
					if($dt_q['nilai'] != '-'){
						$data_rekap[$key]['data'][$key2]['nilai'] = number_format((float)$dt_q['nilai'],2);
					}
					$data_rekap[$key]['data'][$key2]['warna'] = '0xffffffff';
				}
			}
		}
		echo json_encode($data_rekap);
	}

	function pilih_pos1 () {
		$data = array();
		$id_logger =  $this->input->get('id_logger');
		$q_pos = $this->db->query("SELECT t_lokasi.nama_lokasi, t_logger.code_logger FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='8' and code_logger!='$id_logger' and t_logger.user_id = '4'");

		echo json_encode($q_pos->result_array());
	}

	function pilih_pos2 () {
		$data = array();

		$id_logger =  $this->input->get('id_logger');
		$q_pos = $this->db->query("SELECT t_lokasi.nama_lokasi, t_logger.code_logger FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='8' and code_logger!='$id_logger' and t_logger.user_id = '4'");

		echo json_encode($q_pos->result_array());
	}

	function pilih_pos3 () {
		$data = array();
		$bidang = $this->input->get('bidang');
		$leveluser = $this->input->get('level');
		$q_pos = $this->db->query("SELECT t_lokasi.nama_lokasi, t_logger.code_logger FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='1' and t_logger.user_id = '4'");

		echo json_encode($q_pos->result_array());
	}

	function login_app2()
	{
		$username = $this->input->get('username');
		$password = md5($this->input->get('password'));
		$this->mlogin->apiambilPengguna2($username, $password);

	}

	public function pilihparameter($idlogger)
	{
		$data=array();
		$q_parameter=$this->db->query("SELECT * FROM t_sensor where logger_code='".$idlogger."' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");
		foreach($q_parameter->result() as $param)
		{
			$data[]=array(
				'idParameter'=>$param->id,'namaParameter'=>$param->alias_sensor,'fieldParameter'=>$param->field_sensor,
				'icon'=>$param->alias_sensor
			);
		}
		echo json_encode($data);

	}

	function lokasi_new(){
		$kategori=array();
		$data = array();

		$query_kategori=$this->db->query('select * from kategori_logger where id_katlogger = "1" or id_katlogger="8"');

		//$klasifikasi
		foreach ($query_kategori->result()  as $kat) {
			$tabel=$kat->tabel;
			$tabel_temp=$kat->temp_tabel;
			$content=array();
			$query_lokasilogger=$this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where katlog_id='$kat->id_katlogger' and t_logger.user_id ='4' "); 
			foreach ($query_lokasilogger->result() as $loklogger){
				$id_logger=$loklogger->code_logger;
				$parameter=array();
				$query_data=$this->db->query('select * from '.$tabel_temp.' where code_logger="'.$id_logger.'"');

				if($kat->controller == 'station_cuaca')
				{
					$query_parameter=$this->db->query('select * from t_sensor where logger_code="'.$id_logger.'" and field_sensor="sensor9" ');
					if($query_parameter->result_array()){
						$sen = 'sensor9';

					}else{
						$sen = 'sensor8';
						$query_parameter=$this->db->query('select * from t_sensor where logger_code="'.$id_logger.'" and field_sensor="sensor8" ');
					}
					$query_akumulasi = $this->db->query('select sum('.$sen.') as '.$sen.' from '.$kat->tabel.' where code_logger = "'.$id_logger.'" and waktu >= "'.date('Y-m-d H').':00" ');

					foreach($query_akumulasi->result() as $akum)
					{
						$dtakum=$akum->$sen;	
						foreach ($query_data->result() as $dt){
							$waktu=$dt->waktu;
							$awal=date('Y-m-d H:i',(mktime(date('H')-1)));

							foreach ($query_parameter->result() as $param) {
								$kolom=$param->field_sensor;
								$dta=$dt->$kolom;
								$get='tabel='.$kat->tabel.'&id='.$param->id;	
							}
							$data_sensor = $query_parameter->row();
							######### cek status koneksi ######
							$dta=$dt->$kolom;
							$cek_perbaikan = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
							if($cek_perbaikan){

								$koneksi = 'Perbaikan';
								$icon_marker=$loklogger->icon.'_coklat.png';
								$status_pantau = '-';
							}else{
								if($waktu >= $awal)
								{
									$koneksi = 'Koneksi Terhubung';
									if($dtakum >= 0 && $dtakum <= 0.1) //Tidak Hujan
									{
										$icon_marker=$loklogger->icon.'_hijau.png';
										$status_pantau = 'Tidak Hujan';
									}
									elseif($dtakum >= 0.1 && $dtakum < 1) // Sangat Ringan
									{
										$icon_marker=$loklogger->icon.'_biru.png';
										$status_pantau = 'Hujan Sangat Ringan';
									}
									elseif($dtakum >= 1 && $dtakum < 5) // Ringan
									{
										$icon_marker=$loklogger->icon.'_nila.png';
										$status_pantau = 'Hujan Ringan';
									}
									elseif($dtakum >= 5 && $dtakum < 10) // Sedang
									{
										$icon_marker=$loklogger->icon.'_kuning.png';
										$status_pantau = 'Hujan Sedang';
									}
									elseif($dtakum >= 10 && $dtakum < 20) // Lebat
									{
										$icon_marker=$loklogger->icon.'_oranye.png';
										$status_pantau = 'Hujan Lebat';
									}
									elseif($dtakum >= 20) // Sangat Lebat
									{
										$icon_marker=$loklogger->icon.'_merah.png';
										$status_pantau = 'Hujan Sangat Lebat';
									}

								}
								else{
									$koneksi = 'Koneksi Terputus';
									$icon_marker=$loklogger->icon.'_hitam.png';
									$status_pantau = '-';
								}
							}

						}
					}}
				elseif($kat->controller == 'awlr')
				{
					foreach ($query_data->result() as $dt){
						$waktu=$dt->waktu;
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
						$query_parameter=$this->db->query('select * from t_sensor where logger_code="'.$id_logger.'" and field_sensor="sensor1"');
						foreach ($query_parameter->result() as $param) {
							$kolom=$param->field_sensor;
							$dta=$dt->$kolom;
							$get='tabel='.$kat->tabel.'&id='.$param->id;
							$link_parameter= anchor($kat->controller.'/set_sensordash?'.$get,$param->alias_sensor);
							$parameter[]='
								<td>'.$link_parameter.'</td><td>'.$dta.' '.$param->satuan.'</td>
								';	
						}
						$data_sensor = $query_parameter->row();
						$cek_perbaikan = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						######### cek status koneksi ######
						$dta=$dt->$kolom;
						$koneksi = '';
						if($cek_perbaikan){
							$koneksi = 'Perbaikan';
							$icon_marker='awlr_coklat.png';
							$status_pantau = '-';
						}else{
							if($waktu >= $awal)
							{
								$koneksi = 'Koneksi Terhubung';
								$icon_marker='awlr-hijau.png';
								$status_pantau = '-';
							}else{
								$status_pantau = '-';
								$koneksi = 'Koneksi Terputus';
								$icon_marker='awlr-hitam.png';
							}
						}


					}

				}
				$data[] = array(
					'tabel' => $tabel,
					'sensor'=>$data_sensor->id,
					'nama_param'=>$data_sensor->alias_sensor,
					'lokasi'=>$loklogger->nama_lokasi,
					'latitude'=>$loklogger->latitude,
					'longitude'=>$loklogger->longitude,
					'id_logger'=>$id_logger,
					'koneksi'=>$koneksi,
					'icon' => $icon_marker,
					'status_pantau'=> $status_pantau
				);
			}

		}
		echo json_encode($data);
	}

	function menu()
	{
		$dataMenu=array();
		$logger = $this->db->distinct('katlog_id')->select('katlog_id')->where('user_id', '4')->get('t_logger')->result();
		$cek_arr = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'arr' GROUP BY icon")->result_array();
		foreach($logger as $lg){
			$lgr = $this->db->get_where('kategori_logger', array('id_katlogger'=> $lg->katlog_id))->row();
			if($lgr->id_katlogger == '1'){
				$dataMenu[]= array(
					'id_kategori' =>$lgr->id_katlogger,
					'menu' =>$lgr->nama_kategori,
					'controller'=>$lgr->controller,
					'tabel'=>$lgr->tabel,
					'icon_app'=>'ws',
					'temp_tabel'=>$lgr->temp_tabel,	
				);
			}else{
				$dataMenu[]= array(
					'id_kategori' =>$lgr->id_katlogger,
					'menu' =>$lgr->nama_kategori,
					'controller'=>$lgr->controller,
					'tabel'=>$lgr->tabel,
					'icon_app'=>$lgr->icon_app,
					'temp_tabel'=>$lgr->temp_tabel,	
				);
			}

		}
		if($cek_arr){
			$dataMenu[]=array(
				'id_kategori' =>'2',
				'menu' =>'Curah Hujan',
				'nama_kategori' =>'Curah Hujan',
				'temp_tabel'=>'temp_weather_station',
				'controller'=>'curah_hujan',
				'icon_app'=>'arr',
				'tabel'=>'weather_station'
			);
		}
		array_multisort(array_column($dataMenu, "id_kategori"), SORT_ASC, $dataMenu);
		echo json_encode($dataMenu);
	}

	function logout() {
		$this->session->sess_destroy();
		redirect('login');
	}


	public function notif_versi(){
		$versi = '1.3.3';
		echo json_encode(array('versi'=> $versi, 'link'=>'https://dpupesdm.monitoring4system.com/unduh/go-hidro_1.3.3.apk'));
	}

	public function notif_versi_ios(){
		$versi = '1.3.3';
		echo json_encode(array('versi'=> $versi, 'link'=>'https://apps.apple.com/id/app/go-hidro/id6456266862'));
	}

	function lokasi_baru()
	{
		$kategori=$this->input->get('kategori_log');
		$tabel=$this->input->get('tabel');
		$dataLokasi=array();
		if($kategori == '2'){
			$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_id=t_lokasi.id_lokasi join t_informasi ON t_logger.code_logger=t_informasi.logger_id where t_logger.icon='arr' and t_logger.user_id = '4'");
		} elseif($kategori == '1'){
			$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_id=t_lokasi.id_lokasi join t_informasi ON t_logger.code_logger=t_informasi.logger_id where t_logger.icon='ws' and t_logger.user_id = '4'");
		}else{
			$query_lokasi = $this->db->query("SELECT * FROM t_logger join t_lokasi on t_logger.lokasi_id=t_lokasi.id_lokasi join t_informasi ON t_logger.code_logger=t_informasi.logger_id where t_logger.katlog_id='".$kategori."' and t_logger.user_id = '4'");
		}


		foreach($query_lokasi->result() as $lokasilog)
		{
			$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$lokasilog->code_logger.'" ');
			if($query_perbaikan->num_rows() == null) {
				$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
				$waktu_temp = $this->db->get_where($tabel,array('code_logger'=>$lokasilog->code_logger))->row();
				if($waktu_temp->waktu >= $awal){
					$status='On';}else{
					$status='Off';
				}


				$dataLokasi[]=array(
					'logger_id' =>$lokasilog->code_logger,
					'nama_logger' =>$lokasilog->nama_logger,
					'lokasi' =>$lokasilog->nama_lokasi,
					'latitude'=>$lokasilog->latitude,
					'longitude'=>$lokasilog->longitude,
					'status'=>$status,
				);
			}
			else {
				$dataLokasi[]=array(
					'logger_id' =>$lokasilog->code_logger,
					'nama_logger' =>$lokasilog->nama_logger,
					'lokasi' =>$lokasilog->nama_lokasi,
					'latitude'=>$lokasilog->latitude,
					'longitude'=>$lokasilog->longitude,
					'status'=>"Perbaikan",	
				);
			}
		}
		$lokasi_data = array(
			'lokasi_first' => $dataLokasi[0],
			'lokasi'=>$dataLokasi
		);
		echo json_encode($lokasi_data);
	}


	public function get_param(){
		$idlog = $this->input->get('id_logger1');
		$idlog2 = $this->input->get('id_logger2');
		$idlog3 = $this->input->get('id_logger3');
		$data = null;
		$data2 = null;
		$data3 = null;
		if($idlog != ''){
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' order by id limit 1");
			$data = $q_parameter->row();
		}
		if($idlog2 != ''){
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog2 . "' order by id limit 1");
			$data2 = $q_parameter->row();
		}
		if($idlog3 != ''){
			$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog3 . "' and field_sensor='sensor8'");
			$data3 = $q_parameter->row();
		}
		$data_param = array(
			'awlr1' =>$data,
			'awlr2' =>$data2,
			'arr' =>$data3,
		);
		echo json_encode($data_param);
	}

	public function get_komparasi(){

		$awlr1 = $this->input->get('awlr1');
		$awlr2 = $this->input->get('awlr2');
		$arr = $this->input->get('arr');
		$tanggal = $this->input->get('tanggal');

		$data1 = array();
		$data2 = array();
		$data3 = array();
		if ($awlr1 != '') {
			$data = array();
			$waktu = array();
			$satuan1 = $this->input->get('satuan1');
			$sensor = 'sensor1';
			$nama_sensor = "Rerata_Tinggi_Muka_Air";
			$kolom = 'sensor1';
			$select = 'avg(' . $kolom . ') as ' . $nama_sensor;

			$query_data = $this->db->query("SELECT waktu,avg(sensor1) as Rerata_Tinggi_Muka_Air FROM awlr where code_logger='" . $awlr1 . "' and waktu >= '".$tanggal." 00:00' and waktu <= '".$tanggal." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

			foreach ($query_data->result() as $datalog) {
				//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
				$data[] = array(
					'data'=>number_format($datalog->$nama_sensor, 2),
					'waktu'=> date("H", strtotime($datalog->waktu))
				);

			}

			$dataAnalisa = array(
				'namaSensor' => $nama_sensor,
				'satuan' => $satuan1,
				'data' => $data,

				'nosensor' => $kolom,
			);
			$data1 = $dataAnalisa;
		}
		if ($awlr2 != '') {
			$data = array();
			$waktu = array();
			$satuan1 = $this->input->get('satuan2');
			$sensor = 'sensor1';
			$nama_sensor = "Rerata_Tinggi_Muka_Air";
			$kolom = 'sensor1';
			$select = 'avg(' . $kolom . ') as ' . $nama_sensor;

			$query_data = $this->db->query("SELECT waktu,avg(sensor1) as Rerata_Tinggi_Muka_Air FROM awlr where code_logger='" . $awlr2 . "' and waktu >= '".$tanggal." 00:00' and waktu <= '".$tanggal." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

			foreach ($query_data->result() as $datalog) {
				//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
				$waktu[] = date("H", strtotime($datalog->waktu));
				$data[] = array(
					'data'=>number_format($datalog->$nama_sensor, 2),
					'waktu'=> date("H", strtotime($datalog->waktu))
				);
			}

			$dataAnalisa = array(
				'namaSensor' => $nama_sensor,
				'satuan' => $satuan1,
				'data' => $data,
				'nosensor' => $kolom,
			);
			$data2 = $dataAnalisa;
		}

		//echo json_encode($data2);
		//exit;

		if($arr != ''){
			$data_arr = array();
			$sensor = 'sensor8';
			$nama_sensor = "Akumulasi_Curah_Hujan";
			$select = 'sum(' . $sensor . ')as ' . $nama_sensor;


			$satuan = $this->input->get('satuan3');

			$query_data = $this->db->query("SELECT waktu," . $select . " FROM weather_station where code_logger='" . $arr. "' and  waktu >= '".$tanggal." 00:00' and waktu <= '".$tanggal." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

			foreach ($query_data->result() as $datalog) {
				//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
				$data_arr[] = array(
					'data'=>number_format($datalog->$nama_sensor, 2),
					'waktu'=> date("H", strtotime($datalog->waktu))
				);
			}
			$dataAnalisa2 = array(
				'namaSensor' => $nama_sensor,
				'satuan' => $satuan,
				'data' => $data_arr,
				'nosensor' => $sensor,
			);
			$data3 =$dataAnalisa2;
		}

		$tgl_now =  date('Y-m-d');
		if($tgl_now == $tanggal){
			if($awlr1 and $awlr2 and $arr){
				$maxLength = [count($data1['data']), count($data2['data']), count($data3['data'])];
				for ($i = 0; $i < max($maxLength); $i++) {
					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
				//echo json_encode(max($maxLength));
			}elseif($awlr1 and $awlr2 ){
				$maxLength = [count($data1['data']), count($data2['data'])];
				for ($i = 0; $i < max($maxLength); $i++) {
					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}

			}elseif($awlr1 and $arr){
				$maxLength = [count($data1['data']), count($data3['data'])];
				for ($i = 0; $i < max($maxLength); $i++) {
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
			}elseif($awlr2 and $arr){
				$maxLength = [count($data2['data']), count($data3['data'])];
				for ($i = 0; $i < max($maxLength); $i++) {

					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
			}
		}else{
			if($awlr1 and $awlr2 and $arr){
				for ($i = 0; $i < 24; $i++) {
					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
				//echo json_encode(max($maxLength));
			}elseif($awlr1 and $awlr2 ){
				for ($i = 0; $i < 24; $i++) {
					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}

			}elseif($awlr1 and $arr){
				for ($i = 0; $i < 24; $i++) {
					if (array_search($i, array_column($data1['data'], 'waktu')) !== false) {
					} else {
						array_push($data1['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
			}elseif($awlr2 and $arr){
				for ($i = 0; $i < 24; $i++) {

					if (array_search($i, array_column($data2['data'], 'waktu')) !== false) {
					} else {
						array_push($data2['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
					if (array_search($i, array_column($data3['data'], 'waktu')) !== false) {
					} else {
						array_push($data3['data'], array('data' => '-', 'waktu' => ($i > 9) ? $i:'0'.$i));
					}
				}
			}
		}

		if($awlr1 != ''){
			array_multisort(array_column($data1['data'], "waktu"), SORT_ASC, $data1['data']);	
		}
		if($awlr2 != ''){
			array_multisort(array_column($data2['data'], "waktu"), SORT_ASC, $data2['data']);
		}
		if($arr != ''){
			array_multisort(array_column($data3['data'], "waktu"), SORT_ASC, $data3['data']);
		}

		$data_akhir = array(
			'awlr1'=> $data1,
			'awlr2'=> $data2,
			'arr'=>$data3,
		);
		echo json_encode($data_akhir);
	}
	function get_logger_perbaikan () {
		$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_logger=t_lokasi.idlokasi')->get('t_logger')->result_array();
		$data = array();
		foreach($data_logger as $dt){
			$temp_data = $this->db->get_where('temp_'. $dt['tabel'], array('code_logger' => $dt['id_logger']))->row();
			$awal=date('Y-m-d H:i',(mktime(date('H')-1)));

			$perbaikan = $this->db->get_where('t_perbaikan', array('id_logger' => $dt['id_logger']))->row();
			if($perbaikan) {
				$data[] = array(
					'status' => 'Perbaikan',
					'nama_pos' => $dt['nama_lokasi'],
					'id_logger' => $dt['id_logger'],
				);
			}else{
				if($temp_data->waktu >= $awal){
					$data[] = array(
						'status' => '1',
						'nama_pos' => $dt['nama_lokasi'],
						'id_logger' => $dt['id_logger'],
					);
				}else{
					$data[] = array(
						'status' => '0',
						'nama_pos' => $dt['nama_lokasi'],
						'id_logger' => $dt['id_logger'],
					);
				}
			}
		}
		echo json_encode($data);
	}

	function tambah_perbaikan () {
		$id_logger = $this->input->post('id_logger');
		$data = array(
			'id_logger'=>$id_logger,
		);
		$insert = $this->db->insert('t_perbaikan', $data);
		if($insert){
			echo json_encode(array('body'=>'success'));
		}else{
			echo json_encode(array('body'=>'gagal'));
		}
	}

	function hapus_perbaikan () {
		$id_logger = $this->input->post('id_logger');
		$delete = $this->db->delete('t_perbaikan', array('id_logger' => $id_logger));
		if($delete){
			echo json_encode(array('body'=>'success'));
		}else{
			echo json_encode(array('body'=>'gagal'));
		}
	}

	function dtakhir()
	{
		$idlog = $this->input->get('idlogger');
		$tabel = $this->input->get('tabel');
		$data_terakhir=array();
		$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->where('t_logger.code_logger', $idlog)->get('t_logger')->row();
		$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$idlog.'" ');
		if($query_perbaikan->num_rows() == null){
			$qparam=$this->db->query("SELECT * FROM t_sensor where logger_code='".$idlog."' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");		
			foreach($qparam->result() as $sensor)
			{
				$kolom=$sensor->field_sensor;
				$qdataparam=$this->db->query("SELECT * FROM ".$tabel." where code_logger='".$idlog."' order by waktu desc limit 1")->row();
				$h=$qdataparam->$kolom;
				$waktu=$qdataparam->waktu;

				$h = number_format($h,2,'.','');
				$data_terakhir[]=array(
					'idsensor'=>$sensor->id,
					'sensor'=>$sensor->alias_sensor,
					'data'=>$h,
					'satuan'=>$sensor->satuan,
					'icon'=>$sensor->icon_sensor,
					'tipe_graf'=>$sensor->satuan == 'mm' ? 'column':'spline',
				);
			}
			$data_akhir=array(
				'nama_logger' => $data_logger->nama_lokasi,
				'waktu'=>$waktu,
				'tabel'=>$tabel,
				'data_terakhir'=>$data_terakhir);
			echo json_encode($data_akhir);
		}
		else {
			$qparam=$this->db->query("SELECT * FROM t_sensor where logger_code='".$idlog."' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");		
			foreach($qparam->result() as $sensor)
			{
				$kolom=$sensor->field_sensor;
				$qdataparam=$this->db->query("SELECT * FROM ".$tabel." where code_logger='".$idlog."' order by waktu desc limit 1")->row();
				$h=$qdataparam->$kolom;
				$waktu=$qdataparam->waktu;

				$h = number_format($h,2,'.','');
				$data_terakhir[]=array(
					'idsensor'=>$sensor->id,
					'sensor'=>$sensor->alias_sensor,
					'data'=>$h,
					'satuan'=>$sensor->satuan,
					'icon'=>$sensor->icon_sensor,
					'tipe_graf'=>$sensor->satuan == 'mm' ? 'column':'spline',
				);
			}
			$data_akhir=array(
				'nama_logger' => $data_logger->nama_lokasi,
				'waktu'=>$waktu,
				'tabel'=>$tabel,
				'status'=>'perbaikan',
				'data_terakhir'=>$data_terakhir);
			echo json_encode($data_akhir);
		}
	}

	function dtakhir2()
	{
		$idlog = $this->input->get('idlogger');
		$tabeldt = $this->input->get('tabel');
		$data_terakhir=array();
		$tabel = 'temp_'.$tabeldt;
		$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->where('t_logger.code_logger', $idlog)->get('t_logger')->row();
		$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$idlog.'" ');


		$qparam=$this->db->query("SELECT * FROM t_sensor where logger_code='".$idlog."' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)");		
		foreach($qparam->result() as $sensor)
		{
			$kolom=$sensor->field_sensor;
			$qdataparam=$this->db->query("SELECT * FROM ".$tabeldt." where code_logger='".$idlog."' order by waktu desc limit 1");

			foreach($qdataparam->result() as $data)
			{
				$datasensor=$data->$kolom;
				$waktu=$data->waktu;
			}
			$data_terakhir[]=array(
				'idsensor'=>$sensor->id,
				'sensor'=>$sensor->alias_sensor,
				'data'=>$datasensor,
				'satuan'=>$sensor->satuan,
			);

		}
		foreach($data_terakhir as $key => $dt3){
			if($dt3['sensor'] != 'Kelembaban_Logger' and $dt3['sensor'] != 'Baterai_Logger' and $dt3['sensor'] != 'Temperatur_Logger'){
				$data_terakhir[$key]['kat_data'] = '1';
			}else{
				$data_terakhir[$key]['kat_data'] = '2';
			}

		}	
		$data_akhir=array(
			'nama_logger' => $data_logger->nama_lokasi,
			'waktu'=>$waktu,
			'data_terakhir'=>$data_terakhir
		);


		echo json_encode($data_akhir);


	}


	function analisapertanggal2()
	{
		$idsensor=$this->input->get('idsensor');
		$tanggal=$this->input->get('tanggal');

		$data=array();
		$min=array();
		$max=array();

		$qparam=$this->db->join('t_logger','t_logger.code_logger = t_sensor.logger_code')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_sensor.id',$idsensor)->get('t_sensor')->row();	
		$id_logger = $qparam->code_logger;
		$tabel  = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter=$qparam->alias_sensor;

		if($sensor == 'sensor9' or $sensor == 'sensor8')
		{
			$tpg= 'column';
			$namaSensor='Akumulasi_'.$namaparameter;
			$select='sum('.$sensor.')as '.$namaSensor;
		}
		else{
			$tpg= 'spline';
			$namaSensor='Rerata_'.$namaparameter;
			$select='avg('.$sensor.')as '.$namaSensor;
		}

		$query_data = $this->db->query("SELECT waktu,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel."  USE INDEX (waktu) where code_logger='".$id_logger."' and waktu >= '".$tanggal." 00:00' and waktu <= '".$tanggal." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");
		$dbt = 0;
		$hsl = $query_data->result();

		foreach($hsl as $datalog)
		{
			$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
			$data[]= number_format($datalog->$namaSensor,3,'.', ''); 
			$min[]=number_format($datalog->min,3,'.', '');
			$max[]=number_format($datalog->max,3,'.', '');
		}

		if(!$hsl){
			$stts = 'error';
			$debit = 'error';
			$dataAnalisa = array(
				'status'=>'sukses',
				'idLogger' =>$id_logger,
				'nosensor'=>$sensor,
				'namaSensor' =>$namaSensor,
				'satuan'=>$satuan,
				'waktu' =>[],
				'tipegraf'=>$tpg,
				'data'=>[],
				'datamin'=>[],
				'datamax'=>[],
			);
		}else{
			if($dbt == 0){
				$stts = 'sukses';
				$debit = 'sukses';
				$dataAnalisa=array(
					'status'=>'sukses',
					'idLogger' =>$id_logger,
					'nosensor'=>$sensor,
					'namaSensor' =>$namaSensor,
					'satuan'=>$satuan,
					'waktu' =>$waktu,
					'tipegraf'=>$tpg,
					'data'=>$data,
					'datamin'=>$min,
					'datamax'=>$max,
				);
			}else{
				$stts = 'sukses';
				$debit = 'error';
				$dataAnalisa = 'Rumus Debit Belum Diatur';
			}

		}

		echo json_encode(
			array(
				'debit'=> $debit,
				'status' => $stts,
				'data'=>$dataAnalisa
			)
		);
	}

	function analisaperbulan2()
	{
		$idsensor=$this->input->get('idsensor');
		$tanggal=$this->input->get('tanggal');

		$data=array();
		$min=array();
		$max=array();

		$qparam=$this->db->join('t_logger','t_logger.code_logger = t_sensor.logger_code')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_sensor.id',$idsensor)->get('t_sensor')->row();	
		$idlogger = $qparam->code_logger;
		$tabel  = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter=$qparam->alias_sensor;

		if($sensor == 'sensor9' or $sensor == 'sensor8')
		{
			$tpg= 'column';
			$namaSensor='Akumulasi_'.$namaparameter;
			$select='sum('.$sensor.')as '.$namaSensor;
		}
		else{
			$tpg= 'spline';
			$namaSensor='Rerata_'.$namaparameter;
			$select='avg('.$sensor.')as '.$namaSensor;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel." USE INDEX (waktu) where code_logger='".$idlogger."' and waktu >= '".$tanggal."-01 00:00' and waktu <= '".$tanggal."-31 23:59' group by DAY(waktu),MONTH(waktu),YEAR(waktu);");
		$hsl = $query_data->result();

		foreach($hsl as $datalog)
		{
			$waktu[]= date('Y-m-d',strtotime($datalog->waktu));
			$data[]= number_format($datalog->$namaSensor,3); 
			$min[]=number_format($datalog->min,3);
			$max[]=number_format($datalog->max,3);
		}

		if($hsl){
			$stts = 'sukses';
			$debit = 'sukses';
		}else{
			$stts = 'error';
			$debit = 'error';
		}
		$dataAnalisa=array(
			'status'=>'sukses',
			'idLogger' =>$idlogger,
			'nosensor'=>$sensor,
			'namaSensor' =>$namaSensor,
			'satuan'=>$satuan,
			'waktu' =>$waktu,
			'tipegraf'=>$tpg,
			'data'=>$data,
			'datamin'=>$min,
			'datamax'=>$max,
		);
		echo json_encode(
			array(
				'debit' => $debit,
				'status' => $stts,
				'data'=>$dataAnalisa
			)
		);
	}


	function analisapertahun2()
	{
		$idsensor=$this->input->get('idsensor');
		$tanggal=$this->input->get('tahun');

		$data=array();
		$min=array();
		$max=array();

		$qparam=$this->db->join('t_logger','t_logger.code_logger = t_sensor.logger_code')->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_sensor.id',$idsensor)->get('t_sensor')->row();	
		$idlogger = $qparam->code_logger;
		$tabel  = $qparam->tabel;
		$sensor = $qparam->field_sensor;
		$satuan = $qparam->satuan;
		$namaparameter=$qparam->alias_sensor;

		if($sensor == 'sensor9' or $sensor == 'sensor8')
		{
			$tpg= 'column';
			$namaSensor='Akumulasi_'.$namaparameter;
			$select='sum('.$sensor.')as '.$namaSensor;
		}
		else{
			$tpg= 'spline';
			$namaSensor='Rerata_'.$namaparameter;
			$select='avg('.$sensor.')as '.$namaSensor;
		}
		$query_data = $this->db->query("SELECT waktu,DATE(waktu) as tanggal,MONTH(waktu) as bulan,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel." USE INDEX (waktu) where code_logger='".$idlogger."' and waktu >= '".$tanggal."-01-01 00:00' and waktu <= '".$tanggal."-12-31 23:59' group by MONTH(waktu),YEAR(waktu);");

		if($query_data->result_array()){
			foreach($query_data->result() as $datalog)
			{
				$waktu[]= date('Y-m',strtotime($datalog->waktu));
				$data2[]= number_format($datalog->$namaSensor,3); 
				$min2[]=number_format($datalog->min,3);
				$max2[]=number_format($datalog->max,3);
			}
			$stts = 'sukses';
			$debit = 'sukses';

		}else{
			$stts = 'error';
			$debit = 'error';
		}
		$dataAnalisa=array(
			'status'=>'sukses',
			'idLogger' =>$idlogger,
			'nosensor'=>$sensor,
			'namaSensor' =>$namaSensor,
			'satuan'=>$satuan,
			'waktu' =>$waktu,
			'tipegraf'=>$tpg,
			'data'=>$data2,
			'datamin'=>$min2,
			'datamax'=>$max2,
		);	
		echo json_encode(
			array(
				'debit'=>$debit,
				'status' => $stts,
				'data'=>$dataAnalisa
			)
		);
	}

	function analisaperrange2()
	{
		$idsensor = $this->input->get('idsensor', true);
		$dari     = $this->input->get('dari', true);
		$sampai   = $this->input->get('sampai', true);

		$waktu = [];
		$data  = [];
		$min   = [];
		$max   = [];

		$qparam = $this->db
			->join('t_logger','t_logger.code_logger = t_sensor.logger_code')
			->join('kategori_logger','kategori_logger.id_katlogger = t_logger.katlog_id')
			->where('t_sensor.id', $idsensor)
			->get('t_sensor')
			->row();

		if (!$qparam) {
			echo json_encode([
				'debit'  => 'error',
				'status' => 'error',
				'data'   => [
					'status'     => 'error',
					'idLogger'   => null,
					'nosensor'   => null,
					'namaSensor' => null,
					'satuan'     => null,
					'waktu'      => [],
					'tipegraf'   => null,
					'data'       => [],
					'datamin'    => [],
					'datamax'    => [],
				],
			], JSON_UNESCAPED_UNICODE);
			return;
		}

		$id_logger  = $qparam->code_logger;
		$tabel      = $qparam->tabel;
		$sensor     = $qparam->field_sensor;
		$satuan     = $qparam->satuan;
		$namaparam  = $qparam->alias_sensor;

		$isAkumulasi = in_array($sensor, ['sensor9','sensor8'], true);
		$tpg         = $isAkumulasi ? 'column' : 'spline';
		$namaSensor  = ($isAkumulasi ? 'Akumulasi_' : 'Rerata_') . $namaparam;
		$aggFunc     = $isAkumulasi ? 'SUM' : 'AVG';

		$start = $dari   . ' 00:00:00';
		$end   = $sampai . ' 23:59:59';

		$sql = "
    SELECT 
        DATE_FORMAT(waktu, '%Y-%m-%d %H:00') AS jam,
        {$aggFunc}($sensor) AS nilai,
        MIN($sensor) AS vmin,
        MAX($sensor) AS vmax
    FROM {$tabel} USE INDEX (waktu)
    WHERE code_logger = ? 
      AND waktu BETWEEN ? AND ?
    GROUP BY YEAR(waktu), MONTH(waktu), DAY(waktu), HOUR(waktu)
    ORDER BY jam ASC
";
		$rows = $this->db->query($sql, [$id_logger, $start, $end])->result();

		foreach ($rows as $r) {
			$waktu[] = $r->jam;
			$data[]  = number_format((float)$r->nilai, 3, '.', '');
			$min[]   = number_format((float)$r->vmin,  3, '.', '');
			$max[]   = number_format((float)$r->vmax,  3, '.', '');
		}

		$hasData = !empty($rows);
		$payload = [
			'status'     => 'sukses',
			'idLogger'   => $id_logger,
			'nosensor'   => $sensor,
			'namaSensor' => $namaSensor,
			'satuan'     => $satuan,
			'waktu'      => $waktu,
			'tipegraf'   => $tpg,
			'data'       => $data,
			'datamin'    => $min,
			'datamax'    => $max,
		];

		echo json_encode([
			'debit'  => $hasData ? 'sukses' : 'error',
			'status' => $hasData ? 'sukses' : 'error',
			'data'   => $payload,
		], JSON_UNESCAPED_UNICODE);
	}

	############################ APi User ##############
	function dataterakhir()
	{
		$idlog = $this->input->get('idlogger');
		$tabeldt = $this->input->get('kategori');

		if($tabeldt=='avw')	
		{
			$sensoravw=array();
			$qsensor=$this->db->query("SELECT * FROM sensor where logger_id='".$idlog."' ");
			foreach($qsensor->result() as $sensor)
			{
				$data_terakhir=array();
				$q_dataset=$this->db->query("select * from dataset_avw where avw_setid='".$sensor->id_avw."'");	
				if($q_dataset->num_rows() > 0)
				{
					foreach($q_dataset->result() as $dtset){	
						$a=$dtset->a;
						$b=$dtset->b;
						$r0=$dtset->L0;
						$t0=$dtset->t0;
						$tct=$dtset->tct;
						$c=-(($a*pow($r0,2))+($b*$r0));
						$elevasi=$dtset->elevasi;
					}
				}
				$qparam=$this->db->query("SELECT * FROM parameter_avw where avw_id='".$sensor->id_avw."' ");		
				foreach($qparam->result() as $param)
				{
					$kolom=explode(',',$param->kolom);
					if(count($kolom) > 1)
					{
						$kolom1=$kolom[0];
						$kolom2=$kolom[1];
						$qdataavw=$this->db->query("SELECT waktu,".$kolom1.",".$kolom2." FROM temp_avw where code_logger='".$idlog."' ");
					}
					else{

						$kolom1=$kolom[0];
						$qdataavw=$this->db->query("SELECT waktu,".$kolom1." FROM temp_avw where code_logger='".$idlog."' ");
					}

					foreach($qdataavw->result() as $dtvw){

						if(count($kolom) > 1)
						{
							$temp=$dtvw->$kolom2;
						}
						$waktu=$dtvw->waktu;
						$dataavw=$dtvw->$kolom1;
						$b_unit=$dataavw;
						$kpa=(($a*pow($b_unit,2))+($b*$b_unit)+$c)-($tct*($temp-$t0));
						$mh2o=$kpa*10.017;
						$elev=$elevasi+$mh2o;

					}
					if($param->jenis_parameter=='b_unit')
					{
						$dta=$b_unit;
					}elseif($param->jenis_parameter=='kpa'){
						$dta=number_format($kpa,3);
					}
					elseif($param->jenis_parameter=='mh2o'){
						$dta=number_format($mh2o,3);
					}
					elseif($param->jenis_parameter=='elevasi'){
						$dta=number_format($elev,3);
					}
					else{
						$dta=$dataavw;
					}


					$data_terakhir[]=array(
						'parameter'=>$param->nama_parameter,
						'data'=> $dta,
						'satuan'=>$param->satuan,
					);

				}
				$sensoravw[]=array(
					'nama_sensor'=>$sensor->nama_sensor,
					'data_terakhir'=>$data_terakhir
				);
			}

			$data_akhir=array(
				'waktu'=>$waktu,
				'sensor'=>$sensoravw
			);
			echo json_encode($data_akhir);

		}
		else{
			$data_terakhir=array();
			$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$idlog.'" ');
			if($query_perbaikan->num_rows() == null)
			{

				$qparam=$this->db->query("SELECT * FROM parameter_sensor where logger_id='".$idlog."'");		
				foreach($qparam->result() as $sensor)
				{
					$kolom=$sensor->kolom_sensor;
					$qdataparam=$this->db->query("SELECT * FROM ".$tabeldt." where code_logger='".$idlog."' order by waktu desc limit 1");

					if(preg_match("/debit/i", $sensor->nama_parameter)){
						foreach($qdataparam->result() as $data)
						{
							$datasensor=number_format(1380*(pow($data->$kolom/100,2.5)),3);
							$waktu=$data->waktu;
						}
					}
					else{
						foreach($qdataparam->result() as $data)
						{
							$datasensor=$data->$kolom;
							$waktu=$data->waktu;
						}
					}


					$data_terakhir[]=array(


						'sensor'=>$sensor->nama_parameter,
						'data'=>$datasensor,
						'satuan'=>$sensor->satuan,

					);

				}
				$data_akhir=array(
					'waktu'=>$waktu,
					'data_terakhir'=>$data_terakhir
				);
				echo json_encode($data_akhir);
			}
			else {
				foreach($query_perbaikan->result() as $data_perbaikan) {
					$d_per=	$data_perbaikan->data_terakhir;
					$data_per = json_decode($d_per);
					$data_akhir = $data_per->kolom;
					$data_terakhir[]=array(

						'idsensor'=>$data_per->id_param,
						'sensor'=>$data_per->nama_parameter,
						'data'=>$data_akhir,
						'satuan'=>$data_per->satuan,
						'icon'=>$data_per->icon_sensor
					);

				}
				$data_akhir=array(
					'waktu'=>$data_per->waktu,
					'data_terakhir'=>$data_terakhir
				);
				echo json_encode($data_akhir);

			}
		}

	}

	############################################### Sensor AVW ################################
	function sensoravw()
	{
		//$kategori=$this->input->get('kategori_log');
		//$tabel=$this->input->get('tabel');
		$dataSensor=array();
		$query_sensor = $this->db->query("SELECT * FROM sensor_avw ");
		foreach($query_sensor->result() as $sensor)
		{
			$query_informasi=$this->db->query('SELECT * FROM t_info where logger_id="'.$sensor->logger_id.'"');
			foreach($query_informasi->result() as $data)
			{
				$seri=$data->seri_logger;
				$sensor1=$data->sensor;
			}


			$query_ceksd=$this->db->query('SELECT sensor97 FROM avw where code_logger="'.$sensor->logger_id.'" order by waktu desc limit 1');
			foreach($query_ceksd->result() as $ceksd)
			{
				if($ceksd->sensor97 == '1')
				{
					$status_sd='OK';
				}
				else{
					$status_sd='Terjadi Kesalahan';
				}

			}



			$dataSensor[]=array(
				'logger_id' =>$sensor->logger_id,
				'id_avw' =>$sensor->id,
				'namasensor' =>$sensor->nama_sensor,
				'kolom'=>$sensor->kolom,
				'seri'=>$seri,
				'sensor'=>$sensor1,
				'status_sd'=>$status_sd,
				//'status'=>$status,


			);

		}
		echo json_encode($dataSensor);
	}

	function sensoravwbaru()
	{
		//$kategori=$this->input->get('kategori_log');
		//$this->session->userdata('id_log');
		$idlog = $this->input->get('idlogger');
		$tabel='avw';
		//$tabel=$this->input->get('tabel');
		$dataSensor=array();
		$query_sensor = $this->db->query("SELECT * FROM sensor_avw where logger_id='".$idlog."'");
		foreach($query_sensor->result() as $sensor)
		{
			$query_informasi=$this->db->query('SELECT * FROM t_info where logger_id="'.$sensor->logger_id.'"');
			foreach($query_informasi->result() as $data)
			{
				$seri=$data->seri_logger;
				$sensor1=$data->sensor;
			}


			$query_ceksd=$this->db->query('SELECT sensor97 FROM avw where code_logger="'.$sensor->logger_id.'" order by waktu desc limit 1');
			foreach($query_ceksd->result() as $ceksd)
			{
				if($ceksd->sensor97 == '1')
				{
					$status_sd='OK';
				}
				else{
					$status_sd='Terjadi Kesalahan';
				}

			}



			/*$dataSensor[]=array(
				'logger_id' =>$sensor->logger_id,
				'id_avw' =>$sensor->id,
				'namasensor' =>$sensor->nama_sensor,
				'kolom'=>$sensor->kolom,
				'seri'=>$seri,
				'sensor'=>$sensor1,
				'status_sd'=>$status_sd,
				'status'=>$status,


			);*/

			$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$sensor->logger_id.'" ');
			if($query_perbaikan->num_rows() == null) {

				if($this->m_analisa->cek_marker($sensor->logger_id,$tabel)->num_rows() > 0 )
				{
					//   $icon_marker='https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='.$loklogger->icon.'|00FF00|000000';
					$status='On';

				}
				else
				{
					//$icon_marker='https://chart.apis.google.com/chart?chst=d_map_pin_letter&chld='.$loklogger->icon.'|FF0000|000000';
					$status='Off';

				}

				$dataSensor[]=array(

					'logger_id' =>$sensor->logger_id,
					'id_avw' =>$sensor->id,
					'namasensor' =>$sensor->nama_sensor,
					'kolom'=>$sensor->kolom,
					'seri'=>$seri,
					'sensor'=>$sensor1,
					'status_sd'=>$status_sd,
					'status'=>$status,
				);
			}
			else {
				$dataSensor[]=array(
					'logger_id' =>$sensor->logger_id,
					'id_avw' =>$sensor->id,
					'namasensor' =>$sensor->nama_sensor,
					'kolom'=>$sensor->kolom,
					'seri'=>$seri,
					'sensor'=>$sensor1,
					'status_sd'=>$status_sd,
					'status'=>"perbaikan",		
				);
			}

		}
		echo json_encode($dataSensor);
	}

	function dtakhiravwv1()
	{
		$idlog = $this->input->get('idlogger');
		$idsen = $this->input->get('idsensor');
		$tabeldt = $this->input->get('tabel');
		//$data_akhir=array();
		$data_terakhir=array();
		$qsensor=$this->db->query("SELECT * FROM sensor_avw where logger_id='".$idlog."' and id='".$idsen."'");		
		foreach($qsensor->result() as $sensor)
		{


			$q_dataset=$this->db->query("select * from dataset_avw where avw_setid='".$sensor->id_avw."'");	
			if($q_dataset->num_rows() > 0)
			{
				foreach($q_dataset->result() as $dtset){	
					$a=$dtset->a;
					$b=$dtset->b;
					$r0=$dtset->r0;
					$t0=$dtset->t0;
					$tct=$dtset->tct;
					$elv_tim=$dtset->elevasi_tim;
					//$ax=$a*$r0;
					$c=-($a*pow($r0,2)+($b*$r0));
					$elevasi=$dtset->elevasi;
				}

			}
			else {
				$a='';
				$b='';
				$r0='';
				$t0='';
				$tct='';
				$elv_tim=0;
				//$ax=$a*$r0;
				$c='';
				$elevasi='';
			}



			$dtavwparameter=array();
			$q_paramavw=$this->db->query("select * from parameter_avw where avw_id='".$sensor->id_avw."'");
			foreach($q_paramavw->result() as $dtparam){	

				$kolom=explode(',',$dtparam->kolom);
				if(count($kolom) > 1)
				{
					$kolom1=$kolom[0];
					$kolom2=$kolom[1];
					$q_datavw=$this->db->query("select waktu,".$kolom1.",".$kolom2." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
				}
				else{
					$kolom1=$kolom[0];
					$q_datavw=$this->db->query("select waktu,".$kolom1." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
				}

				foreach($q_datavw->result() as $dtvw){


					if($a == '')
					{
						$waktuavw=$dtvw->waktu;
						$dataavw=$dtvw->$kolom1;

					}else{
						if(count($kolom) > 1)
						{
							$temp=$dtvw->$kolom2;
						}
						$waktuavw=$dtvw->waktu;
						$dataavw=$dtvw->$kolom1;
						$b_unit=$dataavw;
						$kpa=(($a*pow($b_unit,2))+($b*$b_unit)+$c)-($tct*($temp-$t0)); //kurang -Bcbx (Bc - Bi)
						$mh2o=$kpa*10.017;
						$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
						$elev=$elevasi+$mh2o;
					}

				}

				if($dtparam->jenis_parameter=='b_unit')
				{
					$dta=$b_unit;
				}elseif($dtparam->jenis_parameter=='kpa'){
					$dta=number_format($kpa,3);
				}
				elseif($dtparam->jenis_parameter=='mh2o'){
					$dta=number_format($mh2o,3);
				}
				elseif($dtparam->jenis_parameter=='ru'){
					$dta=number_format($ru,3);
				}
				elseif($dtparam->jenis_parameter=='elevasi'){
					$dta=number_format($elev,3);
				}
				else{
					$dta=$dataavw;
				}

				$dtavwparameter[]=array(

					'idsensor'=>$sensor->id,
					'idavw'=>$dtparam->avw_id,
					'idparameter'=>$dtparam->id,
					'parameter'=>$dtparam->nama_parameter,
					'data'=>$dta,
					'satuan'=>$dtparam->satuan,
					'icon'=>$dtparam->icon
				);
			}
			$data_terakhir=array(
				'waktu'=>$waktuavw,
				'data_terakhir'=>$dtavwparameter
			);

		}

		echo json_encode($data_terakhir);

	}

	function dtakhiravw() {
		$idlog = $this->input->get('idlogger');
		$idsen = $this->input->get('idsensor');
		$tabeldt = $this->input->get('tabel');
		//$data_akhir=array();
		$data_terakhir=array();


		$query_perbaikan=$this->db->query('select * from t_perbaikan where id_logger="'.$idlog.'" ');
		if($query_perbaikan->num_rows() == null)
		{ 
			$qsensor=$this->db->query("SELECT * FROM sensor_avw where logger_id='".$idlog."' and id='".$idsen."'");		
			foreach($qsensor->result() as $sensor)
			{


				$q_dataset=$this->db->query("select * from dataset_avw where avw_setid='".$sensor->id_avw."'");	
				if($q_dataset->num_rows() > 0)
				{
					foreach($q_dataset->result() as $dtset){	
						$a=$dtset->a;
						$b=$dtset->b;
						$r0=$dtset->L0;
						$t0=$dtset->t0;
						$tct=$dtset->tct;
						$elv_tim=$dtset->elevasi_tim;
						//$ax=$a*$r0;
						$c=-(($a*pow($r0,2))+($b*$r0));
						$elevasi=$dtset->elevasi;
					}

				}
				else {
					$a='';
					$b='';
					$r0='';
					$t0='';
					$tct='';
					$elv_tim=0;
					//$ax=$a*$r0;
					$c='';
					$elevasi='';
				}



				$dtavwparameter=array();
				$q_paramavw=$this->db->query("select * from parameter_avw where avw_id='".$sensor->id_avw."'");
				foreach($q_paramavw->result() as $dtparam){	

					$kolom=explode(',',$dtparam->kolom);
					if(count($kolom) > 1)
					{
						$kolom1=$kolom[0];
						$kolom2=$kolom[1];
						$q_datavw=$this->db->query("select waktu,".$kolom1.",".$kolom2." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
					}
					else{

						$kolom1=$kolom[0];

						$q_datavw=$this->db->query("select waktu,".$kolom1." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
					}

					foreach($q_datavw->result() as $dtvw){


						if($a == '')
						{
							$waktuavw=$dtvw->waktu;
							$dataavw=$dtvw->$kolom1;
							$b_unit=$dataavw;

						}else{
							if(count($kolom) > 1)
							{
								$temp=$dtvw->$kolom2;
							}
							$waktuavw=$dtvw->waktu;
							$dataavw=$dtvw->$kolom1;
							$b_unit=$dataavw;
							$kpa=(($a*pow($b_unit,2))+($b*$b_unit)+$c)-($tct*($temp-$t0));
							$mh2o=$kpa*10.017;
							$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
							$elev=$elevasi+$mh2o;


						}

					}

					if($dtparam->jenis_parameter=='b_unit')
					{
						$dta=$b_unit;
					}elseif($dtparam->jenis_parameter=='kpa'){
						$dta=number_format($kpa,2,".","");
					}
					elseif($dtparam->jenis_parameter=='mh2o'){
						$dta=number_format($mh2o,2,".","");
					}
					elseif($dtparam->jenis_parameter=='ru'){
						$dta=number_format($ru,2,".","");
					}
					elseif($dtparam->jenis_parameter=='elevasi'){
						$dta=number_format($elev,2,".","");
					}
					else{
						$dta=$dataavw;
					}

					$dtavwparameter[]=array(

						'idsensor'=>$sensor->id,
						'idavw'=>$dtparam->avw_id,
						'idparameter'=>$dtparam->id,
						'parameter'=>$dtparam->nama_parameter,
						'data'=>$dta,
						'satuan'=>$dtparam->satuan,
						'icon'=>$dtparam->icon
					);
				}
				$data_terakhir=array(
					'waktu'=>$waktuavw,
					'data_terakhir'=>$dtavwparameter
				);

			}

			echo json_encode($data_terakhir);
		}
		else {
			foreach($query_perbaikan->result() as $data_perbaikan) {
				$d_per=	$data_perbaikan->data_terakhir;
				$data_per = json_decode($d_per);

				$qsensor=$this->db->query("SELECT * FROM sensor where logger_id='".$idlog."' and id='".$idsen."'");		
				foreach($qsensor->result() as $sensor)
				{


					$q_dataset=$this->db->query("select * from dataset_avw where avw_setid='".$sensor->id_avw."'");	
					if($q_dataset->num_rows() > 0)
					{
						foreach($q_dataset->result() as $dtset){	
							$a=$dtset->a;
							$b=$dtset->b;
							$r0=$dtset->r0;
							$t0=$dtset->t0;
							$tct=$dtset->tct;
							$elv_tim=$dtset->elevasi_tim;
							//$ax=$a*$r0;
							$c=-($a*pow($r0,2)+($b*$r0));
							$elevasi=$dtset->elevasi;
						}

					}
					else {
						$a='';
						$b='';
						$r0='';
						$t0='';
						$tct='';
						$elv_tim=0;
						//$ax=$a*$r0;
						$c='';
						$elevasi='';
					}



					$dtavwparameter=array();
					$q_paramavw=$this->db->query("select * from parameter_avw where avw_id='".$sensor->id_avw."'");
					foreach($q_paramavw->result() as $dtparam){	

						$kolom=explode(',',$dtparam->kolom);
						if(count($kolom) > 1)
						{
							$kolom1=$kolom[0];
							$kolom2=$kolom[1];
							$q_datavw=array('waktu'=>$data_per->waktu,$kolom1=>$data_per->$kolom1,$kolom2=>$data_per->$kolom2);
							//$q_datavw=$this->db->query("select waktu,".$kolom1.",".$kolom2." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
						}
						else{
							$kolom1=$kolom[0];
							$q_datavw=array('waktu'=>$data_per->waktu,$kolom1=>$data_per->$kolom1);
							//$q_datavw=$this->db->query("select waktu,".$kolom1." from ".$tabeldt." where code_logger='".$sensor->logger_id."' order by waktu desc limit 1");
						}

						foreach($q_datavw as $dtvw){


							if($a == '')
							{
								$waktuavw=$data_per->waktu;
								$dataavw=$dtvw->$kolom1;
							}else{
								if(count($kolom) > 1)
								{
									$temp=$data_per->$kolom2;
								}
								$waktuavw=$data_per->waktu;
								$dataavw=$data_per->$kolom1;
								$b_unit=$dataavw;
								$kpa=(($a*pow($b_unit,2))+($b*$b_unit)+$c)-($tct*($temp-$t0));
								$mh2o=$kpa*10.017;
								$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
								$elev=$elevasi+$mh2o;
							}

						}

						if($dtparam->jenis_parameter=='b_unit')
						{
							$dta=$b_unit;
						}elseif($dtparam->jenis_parameter=='kpa'){
							$dta=number_format($kpa,3);
						}
						elseif($dtparam->jenis_parameter=='mh2o'){
							$dta=number_format($mh2o,3);
						}
						elseif($dtparam->jenis_parameter=='ru'){
							$dta=number_format($ru,3);
						}
						elseif($dtparam->jenis_parameter=='elevasi'){
							$dta=number_format($elev,3);
						}
						else{
							$dta=$dataavw;
						}

						$dtavwparameter[]=array(

							'idsensor'=>$sensor->id,
							'idavw'=>$dtparam->avw_id,
							'idparameter'=>$dtparam->id,
							'parameter'=>$dtparam->nama_parameter,
							'data'=>$dta,
							'satuan'=>$dtparam->satuan,
							'icon'=>$dtparam->icon
						);
					}
					$data_terakhir=array(
						'waktu'=>$waktuavw,
						'data_terakhir'=>$dtavwparameter
					);

				}
				echo json_encode($data_terakhir);
			}
		}

	}


	function analisapertanggalavw()
	{
		$idlogger=$this->input->get('idlogger');
		$idsensor=$this->input->get('idsensor');
		$idparameter=$this->input->get('idparameter');
		$tabel=$this->input->get('tabel');
		$tanggal=$this->input->get('tanggal');

		$data=array();
		$min=array();
		$max=array();
		//$waktu=array();

		$qsensor=$this->db->query("SELECT * FROM sensor_avw where id='".$idsensor."'");		
		foreach($qsensor->result() as $sensoravw)
		{


			$qparameter=$this->db->query("SELECT * FROM parameter_avw where id='".$idparameter."' and avw_id='".$sensoravw->id_avw."'");
			foreach($qparameter->result() as $parameter)
			{
				$namaSensor='Rerata_'.$parameter->nama_parameter.'_'.$sensoravw->nama_sensor;
				$jenis_parameter=$parameter->jenis_parameter;

				$sensor=$parameter->kolom;
				$satuan=$parameter->satuan;
			}	

			$qdataset=$this->db->query("SELECT * FROM dataset_avw where avw_setid='".$sensoravw->id_avw."'");
			if($qdataset->num_rows() > 0)
			{
				foreach($qdataset->result() as $dtset)
				{
					$a=$dtset->a;
					$b=$dtset->b;
					$r0=$dtset->L0;
					$t0=$dtset->t0;
					$tct=$dtset->tct;
					$elv_tim=$dtset->elevasi_tim;
					//$ax=$a*$r0;
					$c=-($a*pow($r0,2)+($b*$r0));
					$elevasi=$dtset->elevasi;
				}
			}else{
				$a=0;
				$b=0;
				$r0=0;
				$t0=0;
				$tct=0;
				$elv_tim=0;
				$c=0;
				$elevasi=0;
			}

		}

		$kolom=explode(',',$sensor);
		if(count($kolom) > 1)
		{
			$kolom1=$kolom[0];
			$kolom2=$kolom[1];
			$select='avg('.$kolom1.') as '.$jenis_parameter.','.$kolom2;
			//$select='avg('.$kolom1.') as '.$nama_sensor.','.$kolom2;
			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");
		}
		else{
			$kolom1=$kolom[0];
			$select='avg('.$kolom1.') as '.$jenis_parameter;

			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");

		}	


		//$query_data = $this->db->query("SELECT waktu,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu);");


		foreach($query_data->result() as $datalog)
		{
			if(count($kolom) > 1)
			{
				$temp=$datalog->$kolom2;

				$bunit=$datalog->$jenis_parameter;
				$kpa=(($a*pow($bunit,2))+($b*$bunit)+$c)-($tct*($temp-$t0));
				$mh2o=$kpa*10.017;
				$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elev=$elevasi+$mh2o;

				##### Min #########
				$bunitmin=$datalog->min;
				$kpamin=(($a*pow($bunitmin,2))+($b*$bunitmin)+$c)-($tct*($temp-$t0));
				$mh2omin=$kpamin*10.017;
				$rumin=(abs($kpamin)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmin=$elevasi+$mh2omin;
				##### Max #########
				$bunitmax=$datalog->max;
				$kpamax=(($a*pow($bunitmax,2))+($b*$bunitmax)+$c)-($tct*($temp-$t0));
				$mh2omax=$kpamax*10.017;
				$rumax=(abs($kpamax)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmax=$elevasi+$mh2omax;
			}
			else{
				$temp=0;
			}


			if($jenis_parameter == 'b_unit')
			{
				$dataavw=number_format($bunit,2,'.','');
				$mina=$bunitmin;
				$maxa=$bunitmax;
			}
			elseif($jenis_parameter == 'kpa')
			{
				$dataavw=number_format($kpa,2,'.','');
				$mina=$kpamax;
				$maxa=$kpamin;
			}
			elseif($jenis_parameter == 'mh2o')
			{
				$dataavw=number_format($mh2o,2,'.','');
				$mina=$mh2omax;
				$maxa=$mh2omin;
			}
			elseif($jenis_parameter == 'ru')
			{
				$dataavw=number_format($ru,2,'.','');
				$mina=$rumin;
				$maxa=$rumax;
			}
			elseif($jenis_parameter == 'elevasi')
			{
				$dataavw=number_format($elev,2,'.','');
				$mina=$elevmax;
				$maxa=$elevmin;
			}
			else {
				$dataavw=number_format($datalog->$jenis_parameter,2,'.','');
				$mina=$datalog->min;
				$maxa=$datalog->max;
			}
			$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
			$data[]= number_format($dataavw,2); 
			$min[]=number_format($mina,2);
			$max[]=number_format($maxa,2);
		}

		$dataAnalisa=array(

			'idLogger' =>$idlogger,
			'nosensor'=>$sensor,
			'namaSensor' =>$namaSensor,
			'satuan'=>$satuan,
			'waktu' =>$waktu,
			'data'=>$data,
			'datamin'=>$min,
			'datamax'=>$max,

		);
		echo json_encode($dataAnalisa);
	}

	function analisaperbulanavw()
	{
		$idlogger=$this->input->get('idlogger');
		$idsensor=$this->input->get('idsensor');
		$idparameter=$this->input->get('idparameter');
		$tabel=$this->input->get('tabel');
		$tanggal=$this->input->get('tanggal');

		$data=array();
		$min=array();
		$max=array();

		$qsensor=$this->db->query("SELECT * FROM sensor_avw where id='".$idsensor."'");		
		foreach($qsensor->result() as $sensoravw)
		{


			$qparameter=$this->db->query("SELECT * FROM parameter_avw where id='".$idparameter."' and avw_id='".$sensoravw->id_avw."'");
			foreach($qparameter->result() as $parameter)
			{
				$namaSensor='Rerata_'.$parameter->nama_parameter.'_'.$sensoravw->nama_sensor;
				$jenis_parameter=$parameter->jenis_parameter;
				//	$select='avg('.$parameter->kolom.') as '.$jenis_parameter;
				$sensor=$parameter->kolom;
				$satuan=$parameter->satuan;
			}	

			$qdataset=$this->db->query("SELECT * FROM dataset_avw where avw_setid='".$sensoravw->id_avw."'");
			if($qdataset->num_rows() > 0)
			{
				foreach($qdataset->result() as $dtset)
				{
					$a=$dtset->a;
					$b=$dtset->b;
					$r0=$dtset->L0;
					$t0=$dtset->t0;
					$tct=$dtset->tct;
					$elv_tim=$dtset->elevasi_tim;
					//$ax=$a*$r0;
					$c=-($a*pow($r0,2)+($b*$r0));
					$elevasi=$dtset->elevasi;
				}
			}else{
				$a=0;
				$b=0;
				$r0=0;
				$t0=0;
				$tct=0;
				$elv_tim=0;
				$c=0;
				$elevasi=0;
			}

		}

		$kolom=explode(',',$sensor);
		if(count($kolom) > 1)
		{
			$kolom1=$kolom[0];
			$kolom2=$kolom[1];
			$select='avg('.$kolom1.') as '.$jenis_parameter.','.$kolom2;
			//$select='avg('.$kolom1.') as '.$nama_sensor.','.$kolom2;
			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by DAY(waktu),MONTH(waktu),YEAR(waktu);");
		}
		else{
			$kolom1=$kolom[0];
			$select='avg('.$kolom1.') as '.$jenis_parameter;

			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by DAY(waktu),MONTH(waktu),YEAR(waktu);");

		}	



		//$query_data = $this->db->query("SELECT waktu,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by DAY(waktu),MONTH(waktu),YEAR(waktu);");


		foreach($query_data->result() as $datalog)
		{
			if(count($kolom) > 1)
			{
				$temp=$datalog->$kolom2;

				$bunit=$datalog->$jenis_parameter;
				$kpa=(($a*pow($bunit,2))+($b*$bunit)+$c)-($tct*($temp-$t0));
				$mh2o=$kpa*10.017;
				$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elev=$elevasi+$mh2o;

				##### Min #########
				$bunitmin=$datalog->min;
				$kpamin=(($a*pow($bunitmin,2))+($b*$bunitmin)+$c)-($tct*($temp-$t0));
				$mh2omin=$kpamin*10.017;	
				$rumin=(abs($kpamin)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmin=$elevasi+$mh2omin;
				##### Max #########
				$bunitmax=$datalog->max;
				$kpamax=(($a*pow($bunitmax,2))+($b*$bunitmax)+$c)-($tct*($temp-$t0));
				$mh2omax=$kpamax*10.017;
				$rumax=(abs($kpamax)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmax=$elevasi+$mh2omax;
			}
			else{
				$temp=0;
			}



			if($jenis_parameter == 'b_unit')
			{
				$dataavw=number_format($bunit,2,'.','');
				$mina=$bunitmin;
				$maxa=$bunitmax;
			}
			elseif($jenis_parameter == 'kpa')
			{
				$dataavw=number_format($kpa,2,'.','');
				$mina=$kpamax;
				$maxa=$kpamin;
			}
			elseif($jenis_parameter == 'mh2o')
			{
				$dataavw=number_format($mh2o,2,'.','');
				$mina=$mh2omax;
				$maxa=$mh2omin;
			}
			elseif($jenis_parameter == 'ru')
			{
				$dataavw=number_format($ru,2,'.','');
				$mina=number_format($rumax,2,'.','');
				$maxa=number_format($rumin,2,'.','');
			}
			elseif($jenis_parameter == 'elevasi')
			{
				$dataavw=number_format($elev,2,'.','');
				$mina=number_format($elevmax,2,'.','');
				$maxa=number_format($elevmin,2,'.','');
			}
			else {
				$dataavw=number_format($datalog->$jenis_parameter,2,'.','');
				$mina=number_format($datalog->min,2,'.','');
				$maxa=number_format($datalog->max,2,'.','');
			}
			$waktu[]= date('Y-m-d H',strtotime($datalog->waktu));
			$data[]= number_format($dataavw,2,'.',''); 
			$min[]=number_format($mina,2,'.','');
			$max[]=number_format($maxa,2,'.','');
		}

		$dataAnalisa=array(

			'idLogger' =>$idlogger,
			'nosensor'=>$sensor,
			'namaSensor' =>$namaSensor,
			'satuan'=>$satuan,
			'waktu' =>$waktu,
			'data'=>$data,
			'datamin'=>$min,
			'datamax'=>$max,

		);
		echo json_encode($dataAnalisa);
	}

	function analisapertahunavw()
	{
		$idlogger=$this->input->get('idlogger');
		$idsensor=$this->input->get('idsensor');
		$idparameter=$this->input->get('idparameter');
		$tabel=$this->input->get('tabel');
		$tanggal=$this->input->get('tahun');

		$data=array();
		$min=array();
		$max=array();

		$qsensor=$this->db->query("SELECT * FROM sensor_avw where id='".$idsensor."'");		
		foreach($qsensor->result() as $sensoravw)
		{


			$qparameter=$this->db->query("SELECT * FROM parameter_avw where id='".$idparameter."' and avw_id='".$sensoravw->id_avw."'");
			foreach($qparameter->result() as $parameter)
			{
				$namaSensor='Rerata_'.$parameter->nama_parameter.'_'.$sensoravw->nama_sensor;
				$jenis_parameter=$parameter->jenis_parameter;
				//$select='avg('.$parameter->kolom.') as '.$jenis_parameter;
				$sensor=$parameter->kolom;
				$satuan=$parameter->satuan;
			}	

			$qdataset=$this->db->query("SELECT * FROM dataset_avw where avw_setid='".$sensoravw->id_avw."'");
			if($qdataset->num_rows() > 0)
			{
				foreach($qdataset->result() as $dtset)
				{
					$a=$dtset->a;
					$b=$dtset->b;
					$r0=$dtset->L0;
					$t0=$dtset->t0;
					$tct=$dtset->tct;
					$elv_tim=$dtset->elevasi_tim;
					//$ax=$a*$r0;
					$c=-($a*pow($r0,2)+($b*$r0));
					$elevasi=$dtset->elevasi;
				}
			}else{
				$a=0;
				$b=0;
				$r0=0;
				$t0=0;
				$tct=0;
				$elv_tim=0;
				$c=0;
				$elevasi=0;
			}

		}

		$kolom=explode(',',$sensor);
		if(count($kolom) > 1)
		{
			$kolom1=$kolom[0];
			$kolom2=$kolom[1];
			$select='avg('.$kolom1.') as '.$jenis_parameter.','.$kolom2;
			//$select='avg('.$kolom1.') as '.$nama_sensor.','.$kolom2;
			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by MONTH(waktu),YEAR(waktu);");
		}
		else{
			$kolom1=$kolom[0];
			$select='avg('.$kolom1.') as '.$jenis_parameter;

			$query_data = $this->db->query("SELECT waktu,".$select.",min(".$kolom1.") as min,max(".$kolom1.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by MONTH(waktu),YEAR(waktu);");

		}	

		//$query_data = $this->db->query("SELECT waktu,".$select.",min(".$sensor.") as min,max(".$sensor.") as max FROM ".$tabel." where code_logger='".$idlogger."' and waktu like '".$tanggal."%'  group by MONTH(waktu),YEAR(waktu);");


		foreach($query_data->result() as $datalog)
		{
			if(count($kolom) > 1)
			{
				$temp=$datalog->$kolom2;
				$bunit=$datalog->$jenis_parameter;
				$kpa=(($a*pow($bunit,2))+($b*$bunit)+$c)-($tct*($temp-$t0));
				$mh2o=$kpa*10.017;
				$ru=(abs($kpa)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elev=$elevasi+$mh2o;

				##### Min #########
				$bunitmin=$datalog->min;
				$kpamin=(($a*pow($bunitmin,2))+($b*$bunitmin)+$c)-($tct*($temp-$t0));
				$mh2omin=$kpamin*10.017;
				$rumin=(abs($kpamin)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmin=$elevasi+$mh2omin;
				##### Max #########
				$bunitmax=$datalog->max;
				$kpamax=(($a*pow($bunitmax,2))+($b*$bunitmax)+$c)-($tct*($temp-$t0));
				$mh2omax=$kpamax*10.017;
				$rumax=(abs($kpamax)*100)/((($elv_tim-$elevasi)*19.02)/100);
				$elevmax=$elevasi+$mh2omax;
			}
			else{
				$temp=0;
			}


			if($jenis_parameter == 'b_unit')
			{
				$dataavw=number_format($bunit,2,'.','');
				$mina=$bunitmin;
				$maxa=$bunitmax;
			}
			elseif($jenis_parameter == 'kpa')
			{
				$dataavw=number_format($kpa,2,'.','');
				$mina=$kpamax;
				$maxa=$kpamin;
			}
			elseif($jenis_parameter == 'mh2o')
			{
				$dataavw=number_format($mh2o,2,'.','');
				$mina=$mh2omax;
				$maxa=$mh2omin;
			}
			elseif($jenis_parameter == 'ru')
			{
				$dataavw=number_format($ru,2,'.','');
				$mina=$rumin;
				$maxa=$rumax;
			}
			elseif($jenis_parameter == 'elevasi')
			{
				$dataavw=number_format($elev,2,'.','');
				$mina=$elevmax;
				$maxa=$elevmin;
			}
			else {
				$dataavw=number_format($datalog->$jenis_parameter,2,'.','');
				$mina=number_format($datalog->min,2,'.','');;
				$maxa=number_format($datalog->max,2,'.','');;
			}
			$waktu[]= date('Y-m-d H',strtotime($datalog->waktu));
			$data[]= number_format($dataavw,2,'.',''); 
			$min[]=number_format($mina,2,'.','');
			$max[]=number_format($maxa,2,'.','');
		}

		$dataAnalisa=array(

			'idLogger' =>$idlogger,
			'nosensor'=>$sensor,
			'namaSensor' =>$namaSensor,
			'satuan'=>$satuan,
			'waktu' =>$waktu,
			'data'=>$data,
			'datamin'=>$min,
			'datamax'=>$max,

		);
		echo json_encode($dataAnalisa);
	}

	function tampiljson() {
		$id='10080';

		$data=array();
		$query = $this->db->query("SELECT * FROM awlr where code_logger='".$id."'  ORDER BY waktu DESC  LIMIT 1;");
		foreach($query->result() as $data) {
			/*{	
		while($row = $data->fetch_assoc()) {
        echo "id: " . $row["id"]. "code_logger: " . $row["code_logger"];
    }
	}*/
			//file_put_contents("data.json", $data); 

			/*	if (file_put_contents("data.json", $data))
		echo "JSON file created successfully...";
	else 
		echo "Oops! Error creating json file...";

*/	//$data2=array('waktu'=>$data->waktu,'sensor1'=>$data->sensor1);
			echo json_encode($data);
			//echo json_encode($data2);
		}	


		//echo ".$current_data.<br>.  $array_data.";
	}

	function tambahjson(){
		//$url = 'https://bintangbano.monitoring4system.com/api/tampiljson';
		$url = $this->tampiljson;
		$json = file_get_contents($url);
		$jo = json_decode($json);
		echo $json;

		$query = $this->db->query('SELECT * from t_perbaikan;');
		foreach($query->result() as $code_l)
		{		


			//$idlog= $code_l->code_logger;
			//$tabel = $code_l->tabel;
			$data = array(

				// $idlogger;
				//echo $this->notif2($idlogger,$tabel);
				'data_terakhir' => $json,
				'id_logger' => '10080',


			);

			$this->db->insert('t_perbaikan', $data);



		}
	}

	function infov2() {
		$skr2 = date('Y-m-d H:i',mktime(0,0,0,date('m'),date('d')-1,date('Y')));

		$idlogger=$this->input->get('idlogger');
		$data_informasi=array();
		$data_terakhir=array();

		//		$nilai = array();

		$query = $this->db->query('SELECT * from kategori_logger INNER JOIN t_logger on t_logger.katlog_id = kategori_logger.id_katlogger;');


		foreach($query->result() as $code_l)
		{
			$tabel = $code_l->temp_tabel;
		}
		$status_sd='OK';
		$query_informasi=$this->db->query('SELECT * FROM t_informasi where logger_id="'.$idlogger.'"');
		//$alamat =  json_decode(file_get_contents('https://lolak.monitoring4system.com/welcome/alamat/'.$idlogger.''));
		foreach($query_informasi->result() as $data)
		{
			$query_logger=$this->db->query('SELECT * FROM t_logger join t_lokasi on t_lokasi.id_lokasi = t_logger.lokasi_id where t_logger.code_logger="'.$idlogger.'"');
			foreach($query_logger->result() as $logger)
			{
				$query_kategori=$this->db->query('SELECT * FROM kategori_logger where id_katlogger="'.$logger->katlog_id.'"');
				foreach($query_kategori->result() as $kategori)
				{
					$query_ceksd=$this->db->query('SELECT sensor13,sensor12 FROM '.$kategori->temp_tabel.' where code_logger="'.$idlogger.'" order by waktu desc limit 1');
					foreach($query_ceksd->result() as $ceksd)
					{
						if($ceksd->sensor13 == '1')
						{
							$status_sd='OK';
						}
						else{
							$status_sd='Terjadi Kesalahan';
						}

						if($ceksd->sensor12 == '1')
						{
							$status_sensor='OK';
						}
						else{
							$status_sensor='Terjadi Kesalahan';
						}
					}

				}
			}

			if (empty($data->elevasi)) {
				$data_informasi=array(
					'foto'=> base_url(). 'image/foto_pos/'.$logger->foto_pos,
					'maps'=> $logger->gmaps,
					'latitude'=> $logger->latitude,
					'longitude'=> $logger->longitude,
					'list'=>array(
						array(
							'nama'=>'ID Logger',
							'nilai'=>$data->logger_id),
						array('nama'=>
							  'Seri', 'nilai'=>$data->seri),
						array('nama'=>
							  'Sensor','nilai'=>$data->sensor),
						array('nama'=>
							  'Status SD','nilai'=>$status_sd),
						array('nama'=>
							  'No Seluler','nilai'=>$data->nosell),
						array('nama'=>
							  'Nama PIC','nilai'=>$data->nama_pic),
					),


				);
			}else {

				$data_informasi=array(
					'foto'=> base_url(). 'image/foto_pos/'.$logger->foto_pos,
					'maps'=> $logger->gmaps,
					'latitude'=> $logger->latitude,
					'longitude'=> $logger->longitude,
					'list'=>array(
						array(
							'nama'=>'ID Logger',
							'nilai'=>$data->logger_id),
						array('nama'=>
							  'Seri', 'nilai'=>$data->seri),
						array('nama'=>
							  'Sensor','nilai'=>$data->sensor),
						array('nama'=>
							  'Status SD','nilai'=>$status_sd),
						array('nama'=>'Elevasi','nilai'=>$data->elevasi),
						array('nama'=>
							  'No Seluler','nilai'=>$data->nosell),
						array('nama'=>
							  'Nama PIC','nilai'=>$data->nama_pic),
					),

				);
			}

		}




		$data_terakhir=array(
			'data'=>$data_informasi,
			//'elevasi'=>$data->elevasi
		);

		echo json_encode($data_terakhir);


	} 


	##################### end ##########################
}
