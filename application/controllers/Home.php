<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {


	public function index()
	{
		$data['konten']='konten/hal_home';
		$this->load->view('template/site',$data);
	}
}
