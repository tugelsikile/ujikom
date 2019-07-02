<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Komponen extends CI_Controller {
	public function index(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['data']   = $this->dbase->dataResult('k_komponen',array('kom_status'=>1));
            $data['body']   = 'komponen/home';
            $data['menu']   = 'komponen';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function sub(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $kom_id         = $this->uri->segment(3);
            $dtKom          = $this->dbase->dataRow('k_komponen',array('kom_id'=>$kom_id,'kom_status'=>1));
            if (!$kom_id || !$dtKom){
                $data['body'] = 'errors/404';
            } else {
                $data['data']   = $dtKom;
                $data['body']   = 'komponen/sub_home';
                $data['menu']   = 'komponen';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function sub_data(){
	    $json['t'] = 0; $json['msg'] = '';
	    $keyword    = $this->input->post('keyword');
	    $kom_id     = $this->input->post('kom_id');
	    $paket      = $this->input->post('paket');
	    $kk_id      = $this->session->userdata('kk_id');
	    $dtKom      = $this->dbase->dataRow('k_komponen',array('kom_id'=>$kom_id));
	    if (!$kom_id || !$dtKom){
	        $json['msg'] = 'Invalid data Komponen';
        } else {
	        $dtSub  = $this->dbase->sqlResult("
	            SELECT  * FROM tb_k_sub_komponen 
	            WHERE   (
	                      skom_content LIKE '%".$keyword."%'
	                    ) AND skom_status = 1 AND kk_id = '".$kk_id."' AND kom_id = '".$kom_id."' AND skom_paket = '".$paket."'
	            ORDER BY skom_urut ASC
	        ");
	        if (!$dtSub){
	            $json['msg'] = 'Tidak ada data';
            } else {
	            $json['t']      = 1;
	            $data['data']   = $dtSub;
	            $json['html']   = $this->load->view('komponen/sub_data',$data,TRUE);
            }
        }
	    die(json_encode($json));
    }
    function add_sub(){
        if(!$this->session->userdata('login')) {
            redirect(base_url('login'));
        } else {
            $kom_id     = $this->uri->segment(3);
            $dtKom      = $this->dbase->dataRow('k_komponen',array('kom_id'=>$kom_id));
            if (!$kom_id || !$dtKom){
                die('Invalid data Komponen');
            } else {
                $data['data']   = $dtKom;
                $this->load->view('komponen/add_sub',$data);
            }
        }
    }
    function add_sub_submit(){
	    $json['t'] = 1; $json['msg'] = '';
	    $kk_id          = $this->session->userdata('kk_id');
	    $kom_id         = $this->input->post('kom_id');
	    $dtKom          = $this->dbase->dataRow('k_komponen',array('kom_id'=>$kom_id));
        $skom_paket     = $this->input->post('skom_paket');
        $skom_content   = $this->input->post('skom_content');
        $skom_a          = $this->input->post('skom_a');
        $skom_b          = $this->input->post('skom_b');
        $skom_c          = $this->input->post('skom_c');
        $skom_d          = $this->input->post('skom_d');
        if (!$kk_id){
            $json['msg'] = 'Invalid kompetensi keahlian';
        } elseif (!$kom_id || !$dtKom){
            $json['msg'] = 'Invalid komponen';
        } elseif (strlen(trim($skom_content)) == 0){
            $json['msg'] = 'sub komponen belum diisi';
        } else {
            $urut = 0;
            $curut = $this->dbase->dataRow('k_sub_komponen',array('kom_id'=>$kom_id,'skom_status'=>1,'kk_id'=>$kk_id,'skom_paket'=>$skom_paket),'COUNT(skom_id) AS cnt');
            if ($curut){
                $urut = $curut->cnt + 1;
            }
            $arr = array(
                'kom_id' => $kom_id, 'kk_id' => $kk_id, 'skom_content' => $skom_content, 'skom_paket' => $skom_paket,
                'skom_a' => $skom_a, 'skom_b' => $skom_b, 'skom_c' => $skom_c, 'skom_d' => $skom_d, 'skom_urut' => $urut
            );
            $skom_id = $this->dbase->dataInsert('k_sub_komponen',$arr);
            if (!$skom_id){
                $json['msg'] = 'DB Error';
            } else {
                $data['data']   = $this->dbase->sqlResult("SELECT  * FROM tb_k_sub_komponen WHERE skom_id = '".$skom_id."'");
                $json['t'] = 1;
                $json['html'] = $this->load->view('komponen/sub_data',$data,TRUE);
                $json['msg'] = 'Sub komponen berhasil ditambahkan';
            }
        }
	    die(json_encode($json));
    }
    function edit_sub(){
	    $skom_id    = $this->uri->segment(3);
	    $dtSkom     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
	    if (!$skom_id || !$dtSkom){
	        die('Invalid data Sub Komponen');
        } else {
	        $data['kom']    = $this->dbase->dataRow('k_komponen',array('kom_id'=>$dtSkom->kom_id));
	        $data['data']   = $dtSkom;
	        $this->load->view('komponen/edit_sub',$data);
        }
    }
    function edit_sub_submit(){
        $json['t'] = 1; $json['msg'] = '';
        $kk_id          = $this->session->userdata('kk_id');
        $skom_id         = $this->input->post('skom_id');
        $dtKom          = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
        $skom_paket     = $this->input->post('skom_paket');
        $skom_content   = $this->input->post('skom_content');
        $skom_a          = $this->input->post('skom_a');
        $skom_b          = $this->input->post('skom_b');
        $skom_c          = $this->input->post('skom_c');
        $skom_d          = $this->input->post('skom_d');
        if (!$kk_id){
            $json['msg'] = 'Invalid kompetensi keahlian';
        } elseif (!$skom_id || !$dtKom){
            $json['msg'] = 'Invalid komponen';
        } elseif (strlen(trim($skom_content)) == 0){
            $json['msg'] = 'sub komponen belum diisi';
        } else {
            $arr = array(
                'skom_content' => $skom_content,
                'skom_a' => $skom_a, 'skom_b' => $skom_b, 'skom_c' => $skom_c, 'skom_d' => $skom_d
            );
            $this->dbase->dataUpdate('k_sub_komponen',array('skom_id'=>$skom_id),$arr);
            $json['t'] = 1;
            $json['msg'] = 'Sub komponen berhasil dirubah';
        }
        die(json_encode($json));
    }
    function delete_sub(){
	    $json['t'] = 0; $json['msg'] = '';
	    $skom_id    = $this->input->post('skom_id');
	    $dtSkom     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
	    if (!$skom_id || !$dtSkom){
	        $json['msg'] = 'Invalid sub komponen';
        } else {
	        $json['t'] = 1;
	        $this->dbase->dataUpdate('k_sub_komponen',array('skom_id'=>$skom_id),array('skom_status'=>0));
	        $json['msg'] = 'Sub Komponen berhasil dihapus';
        }
	    die(json_encode($json));
    }
    function kom_select(){
	    $json['t'] = 0; $json['msg'] = '';
	    $kom_id     = $this->input->post('kom_id');
	    $paket      = $this->input->post('paket');
	    $dtSKom     = $this->dbase->dataResult('k_sub_komponen',array('kk_id'=>$this->session->userdata('kk_id'),'kom_id'=>$kom_id,'skom_status'=>1,'skom_paket'=>$paket));
	    if (!$dtSKom){
	        $json['msg'] = 'Tidak ada data';
        } else {
	        $json['t'] = 1;
	        $json['data']   = $dtSKom;
        }
	    die(json_encode($json));
    }
    function indikator(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $skom_id         = $this->uri->segment(3);
            $dtSKom          = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id,'skom_status'=>1));
            if (!$skom_id || !$dtSKom){
                $data['body'] = 'errors/404';
            } else {
                $data['data']   = $dtSKom;
                $data['skom']   = $this->dbase->dataResult('k_sub_komponen',array('kom_id'=>$dtSKom->kom_id,'skom_status'=>1,'kk_id'=>$this->session->userdata('kk_id')));
                $data['body']   = 'komponen/indikator_home';
                $data['menu']   = 'komponen';
            }
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function indikator_data(){
        $json['t']  = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $skom_id    = $this->input->post('skom_id');
        $dtSKom     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
        if (!$skom_id || !$dtSKom){
            $json['msg'] = 'Invalid sub komponen';
        } else {
            $dtIndi = $this->dbase->sqlResult("
                SELECT    ksk.kom_id,ksk.skom_urut,ki.ind_id,ki.ind_content,ki.ind_urut
                FROM      tb_k_indikator AS ki
                LEFT JOIN tb_k_sub_komponen AS ksk ON ki.skom_id = ksk.skom_id
                WHERE   (
                        ki.ind_content LIKE '%".$keyword."%'
                        ) AND ki.ind_status = 1 AND ki.skom_id = '".$skom_id."'
                ORDER BY ki.ind_urut ASC 
            ");
            if (!$dtIndi){
                $json['msg'] = 'Tidak ada data';
            } else {
                $json['t'] = 1;
                $data['data']   = $dtIndi;
                $json['html']   = $this->load->view('komponen/indikator_data',$data,TRUE);
            }
        }
        die(json_encode($json));
    }
    function add_indikator(){
	    $skom_id    = $this->uri->segment(3);
	    $dtSKom     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
	    if (!$skom_id || !$dtSKom){
	        die('Invalid data Sub Komponen');
        } else {
	        $data['skom']   = $dtSKom;
	        $data['kom']    = $this->dbase->dataRow('k_komponen',array('kom_id'=>$dtSKom->kom_id));
	        $this->load->view('komponen/add_indikator',$data);
        }
    }
    function add_indikator_submit(){
	    $json['t'] = 0; $json['msg'] = '';
	    $skom_id    = $this->input->post('skom_id');
	    $ind_content= $this->input->post('ind_content');
	    $dtSKom     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id));
	    if (!$skom_id || !$dtSKom){
	        $json['msg'] = 'Invalid sub komponen';
        } elseif (strlen(trim($ind_content)) == 0){
	        $json['msg'] = 'Indikator belum diisi';
        } else {
	        $urut    = 0;
	        $curut   = $this->dbase->dataRow('k_indikator',array('skom_id'=>$skom_id,'ind_status'=>1),'COUNT(ind_id) AS cnt');
	        if ($curut){
	            $urut = $curut->cnt + 1;
            }
	        $ind_id = $this->dbase->dataInsert('k_indikator',array('skom_id'=>$skom_id,'ind_content'=>$ind_content,'ind_urut'=>$urut));
	        if (!$ind_id){
	            $json['msg'] = 'DB Error';
            } else {
	            $data['data'] = $this->dbase->sqlResult("SELECT    ksk.kom_id,ksk.skom_urut,ki.ind_id,ki.ind_content,ki.ind_urut
                FROM      tb_k_indikator AS ki
                LEFT JOIN tb_k_sub_komponen AS ksk ON ki.skom_id = ksk.skom_id
                WHERE  ki.ind_id = '".$ind_id."' ");
	            $json['t']  = 1;
	            $json['html'] = $this->load->view('komponen/indikator_data',$data,TRUE);
            }
        }
	    die(json_encode($json));
    }
    function edit_indikator(){
	    $ind_id     = $this->uri->segment(3);
	    $dtInd      = $this->dbase->dataRow('k_indikator',array('ind_id'=>$ind_id));
	    if (!$ind_id || !$dtInd){
	        die('Invalid data indikator');
        } else {
	        $data['data']   = $dtInd;
            $data['skom']   = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$dtInd->skom_id));
            $data['kom']    = $this->dbase->dataRow('k_komponen',array('kom_id'=>$data['skom']->kom_id));
            $this->load->view('komponen/edit_indikator',$data);
        }
    }
    function edit_indikator_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $ind_id     = $this->input->post('ind_id');
        $ind_content= $this->input->post('ind_content');
        $dtInd      = $this->dbase->dataRow('k_indikator',array('ind_id'=>$ind_id));
        if (!$ind_id || !$dtInd){
            $json['msg'] = 'Invalid indikator';
        } elseif (strlen(trim($ind_content)) == 0){
            $json['msg'] = 'Indikator belum diisi';
        } else {
            $this->dbase->dataUpdate('k_indikator',array('ind_id'=>$ind_id),array('ind_content'=>$ind_content));
            $json['t'] = 1;
            $json['msg'] = 'Indikator berhasil dirubah';
        }
        die(json_encode($json));
    }
    function delete_indikator(){
        $json['t'] = 0; $json['msg'] = '';
        $skom_id    = $this->input->post('skom_id');
        $dtSkom     = $this->dbase->dataRow('k_indikator',array('ind_id'=>$skom_id));
        if (!$skom_id || !$dtSkom){
            $json['msg'] = 'Invalid indikator';
        } else {
            $json['t'] = 1;
            $this->dbase->dataUpdate('k_indikator',array('ind_id'=>$skom_id),array('ind_status'=>0));
            $json['msg'] = 'Indikator berhasil dihapus';
        }
        die(json_encode($json));
    }
    function set_show(){
	    $json['t'] = 0; $json['msg'] = '';
	    $value      = $this->input->post('value');
	    $skom_id    = $this->input->post('skom_id');
	    $dtSKOM     = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id),'skom_id');
	    if (!$dtSKOM){
	        $json['msg'] = 'Invalid Sub Komponen';
        } else {
	        $this->dbase->dataUpdate('k_sub_komponen',array('skom_id'=>$skom_id),array('skom_show'=>$value));
	        $json['t'] = 1;
        }
	    die(json_encode($json));
    }
}
