<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Beranda extends CI_Controller {
	function __construct() {
		parent::__construct();

		$this->load->model('m_dashboard');
	}
	public function index(){

		if($this->session->userdata('logged_in'))
		{
			//echo json_encode($this->session->userdata['leveluser']);
			//exit;
			$data = $this->db->from('menu_logger')->join('kategori_logger', 'kategori_logger.id_katlogger = menu_logger.katlog_id')->where('user_id','4')->order_by('id_katlogger')->get()->result();
			$cek = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'ws' GROUP BY icon")->result_array();
			$cek_arr = $this->db->query("SELECT * FROM t_logger WHERE user_id = '4' and icon = 'arr' GROUP BY icon")->result_array();

			foreach($data as $key =>$row)
			{
				if($row->temp_tabel){
					$dataMenu[]=array(
						'id_katlogger' =>$row->id_katlogger,
						'menu' =>$row->nama_kategori,
						'nama_kategori' =>$row->nama_kategori,
						'tabel'=>$row->temp_tabel,
						'controller'=>$row->controller,
						'tabel_besar'=>$row->tabel
					);
				}else{
					$dataMenu[]=array(
						'id_katlogger' =>$row->id_katlogger,
						'menu' =>$row->nama_kategori,
						'nama_kategori' =>$row->nama_kategori,
						'tabel'=>$row->tabel,
						'controller'=>$row->controller,
						'tabel_besar'=>$row->tabel,
					);
				}
			}
			if($cek_arr){
				$dataMenu[]=array(
					'id_katlogger' =>'2',
					'menu' =>'Curah Hujan',
					'nama_kategori' =>'Curah Hujan',
					'tabel'=>'temp_weather_station',
					'controller'=>'curah_hujan',
					'tabel_besar'=>'weather_station'
				);
			}
			array_multisort(array_column($dataMenu, "id_katlogger"), SORT_ASC, $dataMenu);
			foreach ($dataMenu  as  $key => $kat) {
				$tabel = $kat['tabel'];
				$kategori[$key]['nama_kategori'] = $kat['nama_kategori'];
				if($kat['id_katlogger'] == '2'){
					$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger','t_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.user_id', '4')->where('t_logger.icon', 'arr')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
				}elseif($kat['id_katlogger'] == '1'){
					$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger','t_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->where('t_logger.icon', 'ws')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
				}else{
					$data_logger = $this->db->join('t_lokasi', 't_logger.lokasi_id = t_lokasi.id_lokasi')->join('kategori_logger','t_logger.katlog_id = kategori_logger.id_katlogger')->where('t_logger.katlog_id', $kat['id_katlogger'])->where('t_logger.user_id', '4')->order_by('t_logger.code_logger', 'asc')->get('t_logger')->result_array();
				}

				$kategori[$key]['logger'] = $data_logger;
				foreach($data_logger as $k2=>$lg){
					$waktu = $this->db->where('code_logger', $lg['code_logger'])->limit(1)->get($tabel)->row();
					if($waktu){
						$kategori[$key]['logger'][$k2]['waktu']= $waktu->waktu;

						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
						$id_logger= $lg['code_logger'];
						$cek_perbaikan = $this->db->get_where('t_perbaikan', array('id_logger'=>$id_logger))->row();
						if($cek_perbaikan){
							$kategori[$key]['logger'][$k2]['status'] = 'aktif';
							$kategori[$key]['logger'][$k2]['warna'] = '#7e6126';
						}else{
							if($waktu->waktu > $awal)
							{
								$kategori[$key]['logger'][$k2]['status'] = 'aktif';
								$kategori[$key]['logger'][$k2]['warna'] = '#2fb344';
							}
							else{
								$kategori[$key]['logger'][$k2]['status'] = 'terputus';
								$kategori[$key]['logger'][$k2]['warna'] = '#181823';
							}
						}

						if($id_logger == '10114' and $kat['id_katlogger'] == '2'){
							$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Kedalaman_Air_Sumur' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
						}
						elseif($id_logger == '10114' and $kat['id_katlogger'] == '13'){
							$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' and alias_sensor != 'Curah_Hujan' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
						}else{

							$parameter_sensor = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
						}

						$kategori[$key]['logger'][$k2]['parameter'] = $parameter_sensor;
						foreach($parameter_sensor as $k3 => $param){
							if($id_logger == '10114' and $kat['id_katlogger'] == '13'){
								$get='id_param='.$param['id'];	
							}else{
								$get='id_param='.$param['id'];	
							}
							
							$kolom = $param['field_sensor'];
							if($param['set_debit'] == '1')
							{
								$q_datasheet = $this->db->get_where('datasheet_debit', array('id_logger'=>$id_logger))->row();

								$datadebit= $q_datasheet->c * pow(($waktu->$kolom + $q_datasheet->a),$q_datasheet->b); 
								$kategori[$key]['logger'][$k2]['parameter'][$k3]['nilai']=number_format($datadebit,3);

							}else{
								$kategori[$key]['logger'][$k2]['parameter'][$k3]['nilai'] = number_format($waktu->$kolom, 3);
							}

							$kategori[$key]['logger'][$k2]['parameter'][$k3]['link'] = base_url() . 'analisa/set_sensordash?' . $get;
						}
					}else{
						$kategori[$key]['logger'][$k2]['waktu']='';
						$kategori[$key]['logger'][$k2]['parameter'] = [];
					}
				}
			}
			$data['data_konten']=$kategori;
			$data['konten']='konten/back/v_beranda';
			$this->load->view('template_admin/site',$data);
		}
		else{
			redirect('login');
		}
		//echo json_encode($this->session->userdata);
	}



}
