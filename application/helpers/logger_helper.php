<?php
function loggercombo(){

	$ci=& get_instance();
	$ci->load->database('default');
	$ci->db->from('t_logger');
	$ci->db->join('t_lokasi','t_lokasi.id_lokasi=t_logger.lokasi_id');
	$ci->db->join('kategori_logger','kategori_logger.id_katlogger=t_logger.katlog_id');
	
	$ci->db->where('t_logger.user_id','4');
	$ci->db->order_by('t_logger.katlog_id','asc');

	//$ci->db->where('logger_code',$ci->session->userdata('code_logger'));
	$data=$ci->db->get();
	if(  $data->num_rows() > 0){
		$drop=array();
		foreach( $data->result() as $row)

		{
			$drop['']=  'Pilih Lokasi';
			if($row->icon == 'arr'){
				$drop[$row->code_logger.',curah_hujan,'.$row->tabel] = $row->nama_lokasi;
			}else{
				$drop[$row->code_logger.','.$row->controller.','.$row->tabel] = $row->nama_lokasi;
			}
			
		}
		return ($drop);
	}
	else 
	{
		$drop=array(
			''=>"Tidak Ada Logger"
		);
		return $drop;
	}
}

function lokasilogger()
{
	$url="https://api.beacontelemetry.com/lokasi/weblokasi?iduser=12&leveluser=User";
	$get_url = file_get_contents($url);
	$data = json_decode($get_url);
	return $data;

}

