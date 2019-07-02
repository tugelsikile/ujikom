<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Logout extends CI_Controller {
	public function index(){
	    if(!$this->session->userdata('login')){
	        redirect(base_url('login'));
        }
		$this->session->sess_destroy();
	    redirect(base_url(''));
	}
}
