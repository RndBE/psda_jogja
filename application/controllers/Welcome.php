<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function save_answer() {
		$rawData = json_decode(file_get_contents("php://input"));
		$last_key = array_key_last($rawData->messages);
		$history = [
			'uuid' =>$rawData->uuid,
			'type' =>'answer',
			'content'=>$rawData->answer
		];
		$sql = $this->db->insert('t_history',$history);
	}
	public function get_api() {
		$rawData = file_get_contents("php://input");
		
		// Kirim ulang ke backend LLaMA HTTP server
		$backend_url = "http://31.58.158.182:5000/chat";
		$sendData = json_decode(file_get_contents("php://input"));
		
		$last_key = array_key_last($sendData->messages);
		$history = [
			'uuid' =>$sendData->uuid,
			'type' =>'question',
			'content'=>$sendData->messages[$last_key]->content
		];
		$sql = $this->db->insert('t_history',$history);

		// Setup context dengan header dan JSON body yang sama
		$options = [
			'http' => [
				'method'  => 'POST',
				'header'  => "Content-Type: application/json\r\n" .
				"Content-Length: " . strlen($rawData) . "\r\n",
				'content' => $rawData,
				'ignore_errors' => true
			]
		];

		$context = stream_context_create($options);

		// Kirim permintaan ke model server
		$response = file_get_contents($backend_url, false, $context);

		// Forward response ke frontend
		http_response_code(http_response_code()); // optional
		header("Content-Type: application/json");
		echo $response;
	}
	
	public function index()
	{
		$data = $this->db->join('t_lokasi', 't_logger.lokasi_logger=t_lokasi.idlokasi')->join('kategori_logger', 't_logger.kategori_log=kategori_logger.id_katlogger')->get('t_logger')->result_array();
		$date_now = date('Y:m:d H:i:s');
		$date = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($date_now)));

		$lebih2=date('Y-m-d H:i', strtotime($date_now. ' +5 minutes'));
		$kurang2=date('Y-m-d H:i', strtotime($date_now. ' -5 minutes'));

		foreach($data as $key => $val){
			$data[$key]['sumber'] = 'PUSDA JATIM';
			$data[$key]['status'] = 'aktif';
			$data[$key]['web'] = base_url()."editinfo/edit/".$val['id'];
			if($val['kategori_log'] == '1'){
				$waktu = $this->db->get_where('temp_arr', array('code_logger'=> $val['id_logger']))->row();
				$data[$key]['waktu'] = $waktu->waktu;
				if($waktu->waktu < $date ){
					$data[$key]['status'] = 'nonaktif';
				}

				if($waktu->waktu < $kurang2){
					if($waktu->waktu < $date ){
						$data[$key]['srtc'] = 'nonaktif';
					}else {
						$data[$key]['srtc'] = '< toleransi'; }
				} elseif($waktu->waktu > $lebih2){
					$data[$key]['srtc'] = '> toleransi';
				} else{
					$data[$key]['srtc'] = 'Normal';
				}

			}elseif($val['kategori_log'] == '2'){
				$waktu = $this->db->get_where('temp_awlr', array('code_logger'=> $val['id_logger']))->row();
				$data[$key]['waktu'] = $waktu->waktu;
				if($waktu->waktu < $date ){
					$data[$key]['status'] = 'nonaktif';
				}

				if($waktu->waktu < $kurang2){
					if($waktu->waktu < $date ){
						$data[$key]['srtc'] = 'nonaktif';
					}else {
						$data[$key]['srtc'] = '< toleransi'; }
				} elseif($waktu->waktu > $lebih2){
					$data[$key]['srtc'] = '> toleransi';
				} else{
					$data[$key]['srtc'] = 'Normal';
				}

			}else{
				$data[$key]['status'] = 'belum terdapat data';
			}
		}
		echo json_encode($data);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */