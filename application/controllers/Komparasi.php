<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Komparasi extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->load->library('csvimport');
		$this->load->model('m_awlr');
	}

	public function hapus_awlr()
	{
		$this->session->unset_userdata('id_logger_komparasi_1');
		$this->session->unset_userdata('kolom_komparasi_1');
		redirect('komparasi');
	}
	public function hapus_awlr2()
	{
		$this->session->unset_userdata('id_logger_komparasi_3');
		$this->session->unset_userdata('kolom_komparasi_3');
		redirect('komparasi');
	}

	public function hapus_arr()
	{
		$this->session->unset_userdata('namalokasi_komparasi_2');
		$this->session->unset_userdata('id_logger_komparasi_2');
		$this->session->unset_userdata('kolom_komparasi_2');
		redirect('komparasi');
	}
	public function pilihposawlr()
	{
		$data = array();
		
		$id_logger =  $this->session->userdata('id_logger_komparasi_3');
		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='8' AND t_logger.code_logger != '$id_logger' and t_logger.user_id = '4'");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	public function pilihposawlr2()
	{
		$data = array();
		$id_logger =  $this->session->userdata('id_logger_komparasi_1');

		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='8' AND t_logger.code_logger != '$id_logger' and t_logger.user_id = '4'");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	public function pilihposarr()
	{
		$data = array();

		$q_pos = $this->db->query("SELECT * FROM t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id = t_lokasi.id_lokasi where katlog_id='1' and t_logger.user_id = '4'");

		foreach ($q_pos->result() as $pos) {
			$data[] = array(
				'idLogger' => $pos->code_logger, 'namaPos' => $pos->nama_lokasi
			);
		}

		$data_pos = json_encode($data);
		return json_decode($data_pos);
	}

	function settgl2()
	{
		$tgl = str_replace('/', '-', $this->input->post('tgl'));
		$this->session->set_userdata('tanggal_komparasi', $tgl);
		$this->session->set_userdata('pada_komparasi', $tgl);
		redirect('komparasi');
	}

	function set_pos2()
	{
		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where code_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $idlog;
		$this->session->set_userdata('namalokasi_komparasi_1', $lokasilog);
		$this->session->set_userdata('id_logger_komparasi_1', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' order by id limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger_komparasi_1' => $parameter->logger_code,
				'idparameter_komparasi_1' => $parameter->id,
				'nama_parameter_komparasi_1' => $parameter->nama_parameter,
				'kolom_komparasi_1' => $parameter->field_sensor,
				'satuan_komparasi_1' => $parameter->satuan,
				'tipe_grafik_komparasi_1' => 'spline',
				'kolom_acuan_komparasi_1' => 'sensor1'
			);
			$this->session->set_userdata('id_param_komparasi_1', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('komparasi');
	}

	function set_pos4()
	{
		$idlog = $this->input->post('pilihpos');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where t_logger.code_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger = $idlog;
		$this->session->set_userdata('namalokasi_komparasi_3', $lokasilog);
		$this->session->set_userdata('id_logger_komparasi_3', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' order by id limit 1");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger_komparasi_3' => $parameter->logger_code,
				'idparameter_komparasi_3' => $parameter->id,
				'nama_parameter_komparasi_3' => $parameter->nama_parameter,
				'kolom_komparasi_3' => $parameter->field_sensor,
				'satuan_komparasi_3' => $parameter->satuan,
				'tipe_grafik_komparasi_3' =>'spline',
				'kolom_acuan_komparasi_3' => 'sensor1'
			);
			$this->session->set_userdata('id_param_komparasi_3', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}

		redirect('komparasi');
	}

	function set_pos3()
	{
		$idlog = $this->input->post('pilihpos2');
		$querylogger = $this->db->query('select * from t_logger INNER JOIN t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where t_logger.code_logger="' . $idlog . '";');
		$log = $querylogger->row();
		$lokasilog = $log->nama_lokasi;
		$id_logger =$idlog;

		$this->session->set_userdata('namalokasi_komparasi_2', $lokasilog);
		$this->session->set_userdata('id_logger_komparasi_2', $id_logger);

		$q_parameter = $this->db->query("SELECT * FROM t_sensor where logger_code='" . $idlog . "' and field_sensor = 'sensor8'");
		if ($q_parameter->num_rows() > 0) {
			$parameter = $q_parameter->row();
			//data hasil seleksi dimasukkan ke dalam $session
			$session = array(
				'idlogger_komparasi_2' => $parameter->logger_code,
				'idparameter_komparasi_2' => $parameter->id,
				'nama_parameter_komparasi_2' => $parameter->nama_parameter,
				'kolom_komparasi_2' => $parameter->field_sensor,
				'satuan_komparasi_2' => $parameter->satuan,
				'tipe_grafik_komparasi_2' => 'column',
				'kolom_acuan_komparasi_2' => 'sensor8'
			);
			$this->session->set_userdata('id_param_komparasi_2', $parameter->id_param);
			//data dari $session akhirnya dimasukkan ke dalam session
			$this->session->set_userdata($session);
		}
		redirect('komparasi');
	}

	function index()
	{

		if ($this->session->userdata('logged_in')) {
			if ($this->session->userdata('pada_komparasi') == '') {
				$this->session->set_userdata('pada_komparasi', date('Y-m-d'));
			};
			if ($this->session->userdata('kolom_komparasi_1')) {
				$data = array();
				$sensor = 'sensor1';
				$nama_sensor = "Rerata_Tinggi_Muka_Air";
				$kolom = 'sensor1';
				$select = 'avg(' . $kolom . ') as ' . $nama_sensor;
				$satuan = $this->session->userdata('satuan_komparasi_1');


				$query_data = $this->db->query("SELECT HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . ",min(" . $kolom . ") as min,max(" . $kolom . ") as max FROM awlr where code_logger='" . $this->session->userdata('id_logger_komparasi_1') . "' and waktu >= '".$this->session->userdata('pada_komparasi')." 00:00' and waktu <= '".$this->session->userdata('pada_komparasi')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

				foreach ($query_data->result() as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($datalog->$nama_sensor, 3) . "]";
				}

				$dataAnalisa = array(
					'idLogger' => $this->session->userdata('id_logger_komparasi_1'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik_komparasi_1'),
					'data' => $data,
					'nosensor' => $kolom,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam = json_encode($dataAnalisa);
			} else {
				$dataparam = null;
			}



			if ($this->session->userdata('kolom_komparasi_2')) {
				$data2 = array();
				$sensor = $this->session->userdata('kolom_komparasi_2');
				$nama_sensor = "Akumulasi_" . $this->session->userdata('nama_parameter_komparasi_2');
				$sen = $this->db->where('logger_code',$this->session->userdata('id_logger_komparasi_2'))->where('field_sensor','sensor9')->get('t_sensor')->row();
				if($sen){
					$select = 'sum(sensor9)as ' . $nama_sensor;
				}else{
					$select = 'sum(sensor8)as ' . $nama_sensor;
				}
				

				
				$satuan = $this->session->userdata('satuan_komparasi_2');

				$query_data = $this->db->query("SELECT HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select . "  FROM weather_station where code_logger='" . $this->session->userdata('idlogger_komparasi_2') . "' and waktu >= '".$this->session->userdata('pada_komparasi')." 00:00' and waktu <= '".$this->session->userdata('pada_komparasi')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");

				foreach ($query_data->result() as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data2[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($datalog->$nama_sensor, 3) . "]";
				}
				$dataAnalisa2 = array(
					'idLogger' => $this->session->userdata('id_logger_komparasi_2'),
					'namaSensor' => $nama_sensor,
					'satuan' => $satuan,
					'tipe_grafik' => $this->session->userdata('tipe_grafik_komparasi_2'),
					'data' => $data2,
					'nosensor' => $sensor,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam2 = json_encode($dataAnalisa2);
			} else {
				$dataparam2 = null;
			}

			if ($this->session->userdata('kolom_komparasi_3')) {
				$data3 = array();
				$sensor3 = 'sensor1';
				$nama_sensor3 = "Rerata_Tinggi_Muka_Air";
				$kolom3 = 'sensor1';
				$select3 = 'avg(' . $kolom3 . ') as ' . $nama_sensor3;
				$satuan3 = $this->session->userdata('satuan_komparasi_3');

				$query_data = $this->db->query("SELECT HOUR(waktu) as jam,DAY(waktu) as hari,MONTH(waktu) as bulan,YEAR(waktu) as tahun," . $select3 . ",min(" . $kolom3 . ") as min,max(" . $kolom3 . ") as max FROM awlr where code_logger='" . $this->session->userdata('idlogger_komparasi_3') . "' and waktu >= '".$this->session->userdata('pada_komparasi')." 00:00' and waktu <= '".$this->session->userdata('pada_komparasi')." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;");


				foreach ($query_data->result() as $datalog) {
					//$waktu[]= date('Y-m-d H',strtotime($datalog->waktu)).":00";
					$data3[] = "[ Date.UTC(" . $datalog->tahun . "," . $datalog->bulan . "-1," . $datalog->hari . "," . $datalog->jam . ")," . number_format($datalog->$nama_sensor3, 3) . "]";
				}

				$dataAnalisa3 = array(
					'idLogger' => $this->session->userdata('id_logger_komparasi_3'),
					'namaSensor' => $nama_sensor3,
					'satuan' => $satuan3,
					'tipe_grafik' => $this->session->userdata('tipe_grafik_komparasi_3'),
					'data' => $data3,
					'nosensor' => $kolom3,
					'tooltip' => "Waktu %d-%m-%Y %H:%M"
				);
				$dataparam3 = json_encode($dataAnalisa3);
			} else {
				$dataparam3 = null;
			}
			//exit;
			$data['data_sensor'] = json_decode($dataparam);
			$data['data_sensor2'] = json_decode($dataparam2);
			$data['data_sensor3'] = json_decode($dataparam3);
			$data['pilih_pos'] = $this->pilihposawlr();
			$data['pilih_pos2'] = $this->pilihposawlr2();
			$data['pilih_pos_arr'] = $this->pilihposarr();
			$data['konten'] = 'konten/back/awlr/komparasi_awlr';
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}
}
