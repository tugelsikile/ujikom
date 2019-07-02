<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
	public function index(){
	    if(!$this->session->userdata('login')){
	        redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') == 1){
	        redirect(base_url('quiz/landing'));
        }
        $data['body']   = 'dashboard';
	    $data['menu']   = 'dashboard';
	    if ($this->input->is_ajax_request()){
	        $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function cetak_loading(){
	    $this->load->view('cetak_loading');
    }
}
