<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Monitoring extends CI_Controller
{
	function __construct() {
		parent::__construct();
		if(!$this->session->userdata('logged_in'))
		{
			redirect('login');
		}
	}
	public function index (){
		if ($this->session->userdata('tanggal_rekap') == '') {
			$this->session->set_userdata('tanggal_rekap', date('Y-m-d'));
		};
		$data['kategori'] = $this->db->select('t_logger.katlog_id,kategori_logger.nama_kategori')->from('t_logger')->join('kategori_logger', 'kategori_logger.id_katlogger = t_logger.katlog_id')->where('t_logger.user_id','4')->group_by('t_logger.katlog_id')->get()->result_array();
		$data['kategori'][] = array(
			'katlog_id'=>'2',
			'kategori_logger'=>'2',
			'nama_kategori'=>'Curah Hujan',
		);
		array_multisort(array_column($data['kategori'], "katlog_id"), SORT_ASC, $data['kategori']);
		$id_kategori = $this->session->userdata('id_kategori_rekap');
		$tanggal_rekap = $this->session->userdata('tanggal_rekap');

		if($id_kategori){
			$data['data_rekap'] = array();
			if($id_kategori == '2'){
				$data['logger'] =$this->db->select('t_logger.code_logger, t_logger.nama_logger, t_logger.icon,t_lokasi.nama_lokasi')->join('t_lokasi', 't_logger.lokasi_id=t_lokasi.id_lokasi')->where('t_logger.icon', 'arr')->where('t_logger.user_id', '4')->order_by('code_logger','asc')->get('t_logger')->result_array();
			}elseif($id_kategori == '1'){
				$data['logger'] =$this->db->select('t_logger.code_logger, t_logger.nama_logger, t_logger.icon,t_lokasi.nama_lokasi')->join('t_lokasi', 't_logger.lokasi_id=t_lokasi.id_lokasi')->where('katlog_id', $id_kategori)->where('icon', 'ws')->where('t_logger.user_id', '4')->order_by('code_logger','asc')->get('t_logger')->result_array();
			}else{
				$data['logger'] =$this->db->select('t_logger.code_logger, t_logger.nama_logger, t_logger.icon,t_lokasi.nama_lokasi')->join('t_lokasi', 't_logger.lokasi_id=t_lokasi.id_lokasi')->where('katlog_id', $id_kategori)->where('t_logger.user_id', '4')->order_by('code_logger','asc')->get('t_logger')->result_array();
			}
			foreach($data['logger'] as $key=>$val){

				if($id_kategori == '1' or $id_kategori == '2'){
					$tbl = 'weather_station';
					$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor9'))->row();
					if(!$data_sensor){
						$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor8'))->row();	
					}
					$select = 'sum(' . $data_sensor->field_sensor . ') as nilai';
				}else{
					$data_sensor = $this->db->get_where('t_sensor',array('logger_code'=>$val['code_logger'], 'field_sensor'=>'sensor1'))->row();
					$select = 'avg(' . $data_sensor->field_sensor . ') as nilai';
					$tbl = 'awlr';
				}

				$query_data = $this->db->query("SELECT HOUR(waktu) as jam," . $select . " FROM " . $tbl . " where code_logger='" . $val['code_logger'] . "' and waktu >= '".$tanggal_rekap." 00:00' and waktu <= '".$tanggal_rekap." 23:59' group by HOUR(waktu),DAY(waktu),MONTH(waktu),YEAR(waktu) order by waktu asc;")->result_array();

				for ($i = 0; $i < 24; $i++) {
					if (array_search($i, array_column($query_data, 'jam')) !== false) {
					} else {
						array_push($query_data, array('jam' => ($i > 9) ? $i:'0'.$i , 'nilai' => '-'));					
					}
				}
				array_multisort(array_column($query_data, "jam"), SORT_ASC, $query_data);
				$data['data_rekap'][] = array(
					'id_logger' => $val['code_logger'],
					'icon' => $val['icon'],
					'nama_logger' => $val['nama_lokasi'],
					'data' => $query_data,
					'id_param'=>$data_sensor->id
				);
			}

			$kat = $this->db->get_where('kategori_logger',array('id_katlogger'=>$id_kategori))->row();
			$data['nama_logger'] = $kat->nama_kategori;

			if($id_kategori == '1' or $id_kategori == '2'){
				foreach($data['data_rekap'] as $key=> $dtr){
					$data['data_rekap'][$key]['tabel']= 'weather_station';
					$data['data_rekap'][$key]['controller']= $kat->controller;		

					foreach($dtr['data'] as $key2 => $dt_q){
						if($dt_q['nilai'] != '-'){
							$dt_q['nilai'] = number_format($dt_q['nilai'],2);
							if($dt_q['nilai'] < 0.1) {
								$data['data_rekap'][$key]['data'][$key2]['warna'] = 'white';
							}
							elseif($dt_q['nilai'] >= 0.1 && $dt_q['nilai'] < 1) {
								$data['data_rekap'][$key]['data'][$key2]['warna'] = '#70cddd';
							}
							elseif($dt_q['nilai'] >=  1 && $dt_q['nilai'] < 5){
								$data['data_rekap'][$key]['data'][$key2]['warna'] = '#35549d';
							}
							elseif($dt_q['nilai'] >=  5 && $dt_q['nilai'] <  10) {
								$data['data_rekap'][$key]['data'][$key2]['warna'] = '#fef216';
							}
							elseif($dt_q['nilai'] >=  10 && $dt_q['nilai'] <  20) {
								$data['data_rekap'][$key]['data'][$key2]['warna'] = '#f47e2c';
							}
							elseif($dt_q['nilai'] >=  20) {
								$data['data_rekap'][$key]['data'][$key2]['warna'] = '#ed1c24';
							}
						}else{
							$data['data_rekap'][$key]['data'][$key2]['warna'] = 'white';
						}
					}
				}
			}else{
				foreach($data['data_rekap'] as $key=> $dtr){
					$data['data_rekap'][$key]['tabel']= 'awlr';
					$data['data_rekap'][$key]['controller']= $kat->controller;

					foreach($dtr['data'] as $key2 => $dt_q){
						$data['data_rekap'][$key]['data'][$key2]['warna'] = 'white';
					}
				}
			}

		}
		$data['konten'] = 'konten/back/v_rekapitulasi';
		$this->load->view('template_admin/site', $data);
	}

	function set_kategori(){
		$id_kategori = $this->input->post('id_kategori');
		$this->session->set_userdata('id_kategori_rekap', $id_kategori);
		redirect('monitoring');
	}

	function set_tanggal(){
		$tanggal = $this->input->post('tgl');
		$this->session->set_userdata('tanggal_rekap', $tanggal);
		redirect('monitoring');
	}
}