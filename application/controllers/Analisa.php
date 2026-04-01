
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Analisa extends CI_Controller
{

	function __construct()
	{
		parent::__construct();

		$this->load->model('m_analisa');
	}
	
	function set_sensordash()
	{

		$idparam = $this->input->get('id_param');
		$param_data = $this->db->join('t_logger', 't_logger.code_logger = t_sensor.logger_code')->join('t_lokasi', 't_lokasi.id_lokasi = t_logger.lokasi_id')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_sensor.id', $idparam)->get('t_sensor')->row();
		if($idparam == '649'){
			$param_data->controller = 'awlr';
			$param_data->icon = 'awlr';
			$param_data->nama_lokasi = 'Pos AWLR Plataran';
		}
		$session_data = [
			'id_param'       => $idparam,
			'tabel'          => $param_data->tabel,
			'tanggal'        => date('Y-m-d'),
			'pada'     		 => date('Y-m-d'),
			'data'           => 'hari',
			'idlogger'       => $param_data->logger_code,
			'idparameter'    => $param_data->id,
			'nama_parameter' => $param_data->alias_sensor,
			'kolom'          => $param_data->field_sensor,
			'satuan'         => $param_data->satuan,
			'tipe_grafik'    => $param_data->satuan == 'mm' ? 'column' : 'spline',
			'namalokasi'     => $param_data->nama_lokasi,
			'controller'     => $param_data->controller,
		];

		$this->session->set_userdata($session_data);
		if ($param_data->icon == 'arr') {
			redirect('curah_hujan/analisa');
		} else {
			redirect($param_data->controller . '/analisa');
		}
	}

	public function index2()
	{
		if ($this->session->userdata('logged_in')) {
			$this->load->library('googlemaps');
			// BAru
			$kategori = array();
			$query_kategori = $this->db->query('select * from kategori_logger');

			//$klasifikasi
			$data_p = [];
			$data_p = [];
			foreach ($query_kategori->result()  as $kat) {
				$content = array();
				$query_lokasilogger = $this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where t_logger.katlog_id='$kat->id_katlogger' and t_logger.user_id='4' ");

				foreach ($query_lokasilogger->result() as $loklogger) {
					$id_logger = $loklogger->code_logger;
					$icon = $loklogger->icon;
					$parameter = array();

					$query_data = $this->db->query('select * from ' . $kat->temp_tabel . ' where code_logger="' . $id_logger . '"')->row();
					$waktu = $query_data->waktu;

					$awal = date('Y-m-d H:i', (mktime(date('H') - 1)));

					if ($icon == 'awlr') {
						$controller = $kat->controller;
						$kat_group = 'awlr';
						$data_p = $query_data->sensor1;
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/awlr-iri-coklat.png';
							$status = '<p style="color:brown;margin-bottom:0px">Perbaikan</p>';
							$statlog = 'th';
							$statuspantau = "-";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								$icon_marker = base_url() . 'pin_marker/awlr-iri-hijau.png';
								$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
								$statlog = 'th';
								$statuspantau = "-";
								$anim = "";
							} else {
								$icon_marker = base_url() . 'pin_marker/awlr-iri-hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'off';
								$statuspantau = "-";
								$anim = "BOUNCE";
							}
						}
					} else if ($icon == 'arr') {
						$controller = 'arr';
						$kat_group = 'arr';
						$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($sen) {
							$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor9;
						} else {
							$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor8;
						}

						$waktu = $query_data->waktu;
						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/arr-iri-coklat.png';
							$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
							$statlog = 'th';
							$statuspantau = "-";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								if ($data_p <= 0) {
									$icon_marker = base_url() . 'pin_marker/arr_hijau.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'th';
									$statuspantau = "Tidak Hujan";
									$anim = "";
								} elseif ($data_p >= 0.1 and $data_p < 1) {
									$icon_marker = base_url() . 'pin_marker/arr_biru.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sr';
									$statuspantau = "Hujan Sangat Ringan";
									$anim = "";
								} elseif ($data_p >= 1 and $data_p < 5) {
									$icon_marker = base_url() . 'pin_marker/arr_nila.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'r';
									$statuspantau = "Hujan Ringan";
									$anim = "";
								} elseif ($data_p >= 5 and $data_p < 10) {
									$icon_marker = base_url() . 'pin_marker/arr_kuning.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 's';
									$statuspantau = "Hujan Sedang";
									$anim = "";
								} elseif ($data_p >= 10 and $data_p < 20) {
									$icon_marker = base_url() . 'pin_marker/arr_oranye.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'l';
									$statuspantau = "Hujan Lebat";
									$anim = "BOUNCE";
								} elseif ($data_p >= 20) {
									$icon_marker = base_url() . 'pin_marker/arr_merah.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sl';
									$statuspantau = "Hujan Sangat Lebat";
									$anim = "BOUNCE";
								}
							} else {
								$icon_marker = base_url() . 'pin_marker/arr_hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'off';
								$statuspantau = "-";
								$anim = "BOUNCE";
							}
						}
					} else {
						$controller = $kat->controller;
						$kat_group = 'awr';
						$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
						$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
						if ($sen) {
							$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor9;
						} else {
							$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
							$data_p = $query_akumulasi->sensor8;
						}
						if ($perb) {
							$icon_marker = base_url() . 'pin_marker/awr_coklat.png';
							$status = '<p style="color:brown;margin-bottom:0px">Koneksi Terputus</p>';
							$statlog = 'th';
							$statuspantau = "-";
							$anim = "";
						} else {
							if ($waktu >= $awal) {
								if ($data_p >= 0 and $data_p < 0.1) {
									$icon_marker = base_url() . 'pin_marker/awr_hijau.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'th';
									$statuspantau = "Tidak Hujan";
									$anim = "";
								} elseif ($data_p >= 0.1 and $data_p < 1) {
									$icon_marker = base_url() . 'pin_marker/awr_biru.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sr';
									$statuspantau = "Hujan Sangat Ringan";
									$anim = "";
								} elseif ($data_p >= 1 and $data_p < 5) {
									$icon_marker = base_url() . 'pin_marker/awr_nila.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'r';
									$statuspantau = "Hujan Ringan";
									$anim = "";
								} elseif ($data_p >= 5 and $data_p < 10) {
									$icon_marker = base_url() . 'pin_marker/awr_kuning.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 's';
									$statuspantau = "Hujan Sedang";
									$anim = "";
								} elseif ($data_p >= 10 and $data_p < 20) {
									$icon_marker = base_url() . 'pin_marker/awr_oranye.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'l';
									$statuspantau = "Hujan Lebat";
									$anim = "BOUNCE";
								} elseif ($data_p >= 20) {
									$icon_marker = base_url() . 'pin_marker/awr_merah.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'sl';
									$statuspantau = "Hujan Sangat Lebat";
									$anim = "BOUNCE";
								}
							} else {
								$icon_marker = base_url() . 'pin_marker/awr_hitam.png';
								$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'off';
								$statuspantau = "-";
								$anim = "BOUNCE";
							}
						}
					}



					// create marker for each province


					if ($loklogger->icon == 'arr') {
						$controller = 'curah_hujan';
					}
					if($id_logger =='10114' and $kat->id_katlogger== '13'){
						$id_sensor = '649';
					}else{
						$id_sensor = $this->db->where('logger_code',$id_logger)->limit(1)->get('t_sensor')->row()->id;
					}
					

					$marker['position'] = $loklogger->latitude . ',' . $loklogger->longitude;
					$svg = "<svg xmlns='http://www.w3.org/2000/svg' class='icon icon-tabler icon-tabler-location-share ms-2' width='40' height='40' viewBox='0 0 24 24' stroke-width='2' stroke='currentColor' fill='none' stroke-linecap='round' stroke-linejoin='round'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M12 18l-2 -4l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5l-3.616 10.015'></path><path d='M16 22l5 -5'></path><path d='M21 21.5v-4.5h-4.5'></path></svg>";
					$content =
						"<h3 style='color:#333;' class='mt-2'><strong>" . $loklogger->nama_logger . "</strong></h3>" .
						"<center><img class='img-fluid mb-2' style='max-height:130px'  src='" . base_url() . "image/foto_pos/" . $loklogger->foto_pos . "' ></center>" .
						"<table style='color:#333;' class='table card-table table-striped'>" .
						"<tbody>" .
						"<tr>" .
						"<td>Nama Pos </td><td>: </td> <td>" . $loklogger->nama_lokasi . "</td>" .
						"</tr>" .
						"<tr>" .
						"<td>Status Logger</td><td>:</td> <td>" . $status . "</td>" .
						"</tr>" .
						"<tr>" .
						"<td style='white-space:nowrap'>Status Pemantauan </td><td>: </td> <td>" . $statuspantau . "</td>" .
						"</tr>" .
						"</tbody>" .
						"</table><br/>" .
						"<div class='col-md-12 d-flex justify-content-center align-items-center'><a href='".base_url()."analisa/set_sensordash?id_param=".$id_sensor."'>Lihat Data</a><a target='_blank' href='" . $loklogger->gmaps . "' class='ms-4 d-flex align-items-center'>Google Map " . $svg . " </a></div>";

					
					$marker['title'] = $loklogger->nama_lokasi;
					$marker['icon'] = $icon_marker;
					$marker['animation'] = $anim;
					$marker['category'] = $kat_group;
					$marker['category_group'] = $kat_group . '_' . $statlog;
					$marker['icon_scaledSize'] = '25,33';
					$markers[]= $marker;
					$this->googlemaps->add_marker($marker);
				}
			}
			
			//$data['dt_sensor']=$dataSensor;
			$config['center'] = '-7.8268087,110.3877723';
			//	$config['zoom'] = $this->session->userdata('zoom'); //zoom value
			$config['zoom'] = "10";
			$this->googlemaps->initialize($config);
			echo json_encode($markers);
			exit;
			$data['map'] = $this->googlemaps->create_map();
			$data['konten'] = "konten/back/v_analisa";
			$this->load->view('template_admin/site', $data);
		} else {
			redirect('login');
		}
	}

	public function index()
	{
		if($this->session->userdata('logged_in'))
		{

			$this->load->library('googlemaps');
			// BAru
			$id_kategori = $this->session->userdata('id_kategori');
			$ktg = $this->db->get('kategori_logger')->result_array();

			$data['ktg_all'] = $this->db->get('kategori_logger')->result_array();
			$das = $this->db->get('das_diy')->result_array();
			foreach($das as $key =>$ds){
				$das[$key]['logger'] = [];
				$data_logger = $this->db->join('kategori_logger', 't_logger.katlog_id = kategori_logger.id_katlogger')->join('t_lokasi','t_lokasi.id_lokasi = t_logger.lokasi_id')->where('t_lokasi.das',$ds['nama_das'])->where('t_logger.user_id','4')->order_by('code_logger')->get('t_logger')->result_array();
				foreach ($data_logger as $k=>$log){
					$tabel=$log['temp_tabel'];
					$id_logger=$log['code_logger'];
					$temp_data = $this->db->where('code_logger',$id_logger)->get($tabel)->row();
					$cek_perbaikan = $this->db->where('id_logger',$id_logger)->get('t_perbaikan')->row();
					
					$awal=date('Y-m-d H:i',(mktime(date('H')-1)));
					if($temp_data->waktu >= $awal)
					{
						$color="green";
						$status_logger="Koneksi Terhubung";
					}
					else{
						$color="red";
						$status_logger="Koneksi Terputus";			
					}
					if($cek_perbaikan){
						$color="#A16D28";
						$status_logger="Perbaikan";	
					}
					if($temp_data->sensor13 == '1' )
					{
						$sdcard='OK';
					}
					else{
						$sdcard='Bermasalah';
					}


					$param = $this->db->query("SELECT * FROM `t_sensor` WHERE logger_code = '$id_logger' ORDER BY CAST(SUBSTR(`field_sensor`,7) AS UNSIGNED)")->result_array();
					foreach($param as $ky => $val) {
						$get='&id_param='.$val['id'];
						$kolom = $val['field_sensor'];
						$param[$ky]['nilai'] = $temp_data->$kolom;
						$param[$ky]['link'] = base_url() .'analisa/set_sensordash?'.$get;
					}

					$das[$key]['logger'][$k] = [
						'id_logger'=>$id_logger,
						'nama_lokasi'=>$log['nama_lokasi'],
						'waktu'=>$temp_data->waktu,
						'color'=>$color,
						'status_logger'=>$status_logger,
						'status_sd'=>$sdcard,
						'param'=>$param,
					];
				}
			}
			$data['data_konten']=$das;

			$kategori=array();
			$query_kategori=$this->db->query('select * from kategori_logger');
			//$klasifikasi
			$marker = [];
			foreach ($query_kategori->result()  as $kat) {
				$tabel=$kat->tabel;
				$tabel_temp=$kat->temp_tabel;

				$content=array();
				$query_lokasilogger=$this->db->query("select * from t_logger inner join t_lokasi ON t_logger.lokasi_id=t_lokasi.id_lokasi where katlog_id='$kat->id_katlogger' and  t_logger.user_id = 4");

				
				foreach ($query_lokasilogger->result() as $loklogger){
					$id_logger=$loklogger->code_logger;
					$icon = $loklogger->icon;
					$parameter=array();
					$id_param = $this->db->where('logger_code',$id_logger)->limit(1)->get('t_sensor')->row();
					
					$query_data=$this->db->query('select * from '.$tabel_temp.' where code_logger="'.$id_logger.'"')->result();


					foreach ($query_data as $dt){
						$waktu=$dt->waktu;
						$awal=date('Y-m-d H:i',(mktime(date('H')-1)));

						if ($icon == 'awlr') {
							$controller = $kat->controller;
							$kat_group = 'awlr';
							$data_p = $dt->sensor1;
							$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
							if ($perb) {
								$icon_marker = base_url() . 'pin_marker/awlr-iri-coklat.png';
								$status = '<p style="color:brown;margin-bottom:0px">Perbaikan</p>';
								$statlog = 'Perbaikan';
								$statuspantau = "Perbaikan";
								$anim = "";
							} else {
								if ($waktu >= $awal) {
									$icon_marker = base_url() . 'pin_marker/awlr-iri-hijau.png';
									$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
									$statlog = 'Koneksi Terhubung';
									$statuspantau = "-";
									$anim = "";
								} else {
									$icon_marker = base_url() . 'pin_marker/awlr-iri-hitam.png';
									$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
									$statlog = 'Koneksi Terputus';
									$statuspantau = "-";
									$anim = "google.maps.Animation.BOUNCE";
								}
							}
						} else if ($icon == 'arr') {
							$controller = 'arr';
							$kat_group = 'arr';
							$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
							$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
							if ($sen) {
								$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
								$data_p = $query_akumulasi->sensor9;
							} else {
								$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
								$data_p = $query_akumulasi->sensor8;
							}
							
							if ($perb) {
								$icon_marker = base_url() . 'pin_marker/arr-iri-coklat.png';
								$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
								$statlog = 'Perbaikan';
								$statuspantau = "Perbaikan";
								$anim = "";
							} else {
								if ($waktu >= $awal) {
									if ($data_p <= 0) {
										$icon_marker = base_url() . 'pin_marker/arr_hijau.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'th';
										$statuspantau = "Tidak Hujan";
										$anim = "";
									} elseif ($data_p >= 0.1 and $data_p < 1) {
										$icon_marker = base_url() . 'pin_marker/arr_biru.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'sr';
										$statuspantau = "Hujan Sangat Ringan";
										$anim = "";
									} elseif ($data_p >= 1 and $data_p < 5) {
										$icon_marker = base_url() . 'pin_marker/arr_nila.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'r';
										$statuspantau = "Hujan Ringan";
										$anim = "";
									} elseif ($data_p >= 5 and $data_p < 10) {
										$icon_marker = base_url() . 'pin_marker/arr_kuning.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 's';
										$statuspantau = "Hujan Sedang";
										$anim = "";
									} elseif ($data_p >= 10 and $data_p < 20) {
										$icon_marker = base_url() . 'pin_marker/arr_oranye.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'l';
										$statuspantau = "Hujan Lebat";
										$anim = "google.maps.Animation.BOUNCE";
									} elseif ($data_p >= 20) {
										$icon_marker = base_url() . 'pin_marker/arr_merah.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'sl';
										$statuspantau = "Hujan Sangat Lebat";
										$anim = "google.maps.Animation.BOUNCE";
									}
									$statlog = 'Koneksi Terhubung';
								} else {
									$icon_marker = base_url() . 'pin_marker/arr_hitam.png';
									$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
									$statlog = 'Koneksi Terputus';
									$statuspantau = "Koneksi Terputus";
									$anim = "google.maps.Animation.BOUNCE";
								}
							}
						} else {
							$controller = $kat->controller;
							$kat_group = 'awr';
							$sen = $this->db->where('field_sensor', 'sensor9')->where('logger_code', $id_logger)->get('t_sensor')->row();
							$perb = $this->db->where('id_logger', $id_logger)->get('t_perbaikan')->row();
							if ($sen) {
								$query_akumulasi = $this->db->query('select sum(sensor9) as sensor9 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
								$data_p = $query_akumulasi->sensor9;
							} else {
								$query_akumulasi = $this->db->query('select sum(sensor8) as sensor8 from weather_station where code_logger = "' . $id_logger . '" and waktu >= "' . date('Y-m-d H') . ':00" ')->row();
								$data_p = $query_akumulasi->sensor8;
							}
							if ($perb) {
								$icon_marker = base_url() . 'pin_marker/awr_coklat.png';
								$status = '<p style="color:brown;margin-bottom:0px">Koneksi Terputus</p>';
								$statlog = 'Perbaikan';
								$statuspantau = "Perbaikan";
								$anim = "";
							} else {
								if ($waktu >= $awal) {
									if ($data_p >= 0 and $data_p < 0.1) {
										$icon_marker = base_url() . 'pin_marker/awr_hijau.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Tidak Hujan";
										$anim = "";
									} elseif ($data_p >= 0.1 and $data_p < 1) {
										$icon_marker = base_url() . 'pin_marker/awr_biru.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Hujan Sangat Ringan";
										$anim = "";
									} elseif ($data_p >= 1 and $data_p < 5) {
										$icon_marker = base_url() . 'pin_marker/awr_nila.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Hujan Ringan";
										$anim = "";
									} elseif ($data_p >= 5 and $data_p < 10) {
										$icon_marker = base_url() . 'pin_marker/awr_kuning.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Hujan Sedang";
										$anim = "";
									} elseif ($data_p >= 10 and $data_p < 20) {
										$icon_marker = base_url() . 'pin_marker/awr_oranye.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Hujan Lebat";
										$anim = "google.maps.Animation.BOUNCE";
									} elseif ($data_p >= 20) {
										$icon_marker = base_url() . 'pin_marker/awr_merah.png';
										$status = '<p style="color:green;margin-bottom:0px">Koneksi Terhubung</p>';
										$statlog = 'Koneksi Terhubung';
										$statuspantau = "Hujan Sangat Lebat";
										$anim = "google.maps.Animation.BOUNCE";
									}
								} else {
									$icon_marker = base_url() . 'pin_marker/awr_hitam.png';
									$status = '<p style="color:red;margin-bottom:0px">Koneksi Terputus</p>';
									$statlog = 'Koneksi Terputus';
									$statuspantau = "Koneksi Terputus";
									$anim = "google.maps.Animation.BOUNCE";
								}
							}
						}
						$status_sd = 'OK';

					}
					
					$id_sensor = $this->db->where('logger_code',$id_logger)->limit(1)->get('t_sensor')->row()->id;

					
					$get='id_param='.$id_sensor;
					$link =  base_url() .'analisa/set_sensordash?'.$get;
					$marker[] = [
						'nama_das'=>$loklogger->das,
						'id_kategori'=>$kat->id_katlogger,
						'id_logger'=>$loklogger->code_logger,
						'category'=>$kat_group,
						'category_group'=>$statuspantau,
						'koneksi'=>$statlog,
						'status_sd'=>$status_sd,
						'latitude' => $loklogger->latitude,
						'status_pantau' => $statuspantau,
						'longitude' => $loklogger->longitude,
						'nama_lokasi' => $loklogger->nama_lokasi,
						'icon' => $icon_marker,
						'id_param'=>$id_param->id,
						'link'=>$link,
						'anim'=>$anim
					];
				}
			}
			
			$data['das'] = $das;
			$data['marker'] = $marker;
			$config['center'] = '-0.910158,116.842700'; 
			$config['zoom'] = "15";

			$this->load->view('konten/back/analisa_geojson',$data);
		}
		else
		{
			redirect('login');
		}

	}

	function combologger()
	{
		$set = explode(',', $this->input->post('id_logger'));
		$idlogger = $set[0];
		$controller = $set[1];
		$tabel = $set[2];
		
		if($idlogger =='10114' and $controller == 'awlr' and $tabel == 'weather_station'){
			$id_sensor = '649';
		}else{
			$id_sensor = $this->db->where('logger_code',$idlogger)->limit(1)->get('t_sensor')->row()->id;
		}

		redirect('analisa/set_sensordash?id_param='.$id_sensor);
	}
}
