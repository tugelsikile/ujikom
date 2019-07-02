<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelompok extends CI_Controller {
	public function index(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']      = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $data['body']       = 'kelompok/home';
            $data['menu']       = 'kelompok';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function tapel_select(){
	    $json['t']  = 0; $json['msg'] = '';
	    $tapel      = $this->input->post('tapel');
	    $kk_id      = $this->session->userdata('kk_id');
	    if (!$tapel || !$kk_id){
	        $json['msg'] = 'Wrong parameter';
        } else {
	        $dtKel  = $this->dbase->dataResult('kelompok_siswa',array('kk_id'=>$kk_id,'klp_tapel'=>$tapel,'klp_status'=>1),'klp_id,klp_name');
	        if (!$dtKel){
	            $json['msg'] = 'Tidak ada data';
            } else {
	            $json['t'] = 1;
	            $json['data']   = $dtKel;
	            $json['id']     = $dtKel[0]->klp_id;
            }
        }
	    die(json_encode($json));
    }
    function delete_kel(){
        $json['t']  = 0; $json['msg'] = '';
        $klp_id      = $this->input->post('kel_id');
        $dtKel       = $this->dbase->dataRow('kelompok_siswa',array('klp_id'=>$klp_id));
        if (!$klp_id || !$dtKel){
            $json['msg'] = 'Wrong parameter';
        } else {
            $this->dbase->dataUpdate('kelompok_siswa',array('klp_id'=>$klp_id),array('klp_status'=>0));
            $dtKel  = $this->dbase->dataResult('kelompok_siswa',array('kk_id'=>$dtKel->kk_id,'klp_tapel'=>$dtKel->klp_tapel,'klp_status'=>1),'klp_id,klp_name');
            $json['t']      = 1;
            $json['data']   = $dtKel;
            if ($dtKel){
                $json['id']     = $dtKel[0]->klp_id;
            } else {
                $json['id']     = NULL;
            }

        }
        die(json_encode($json));
    }
	function add_data(){
        if(!$this->session->userdata('login')){
            die('Forbidden');
        } elseif (!$this->session->userdata('kk_id')){
            die('Bukan Kaprodi');
        } else {
            $kk_id  = $this->session->userdata('kk_id');
            $dtKK   = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$kk_id));
            if (!$kk_id || !$dtKK){
                die('Invalid Kompetensi');
            } else {
                $this->load->library('conv');
                $tapel      = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
                $klpnum     = $this->dbase->dataRow('kelompok_siswa',array('kk_id'=>$kk_id,'klp_tapel'=>$tapel,'klp_status'=>1),'COUNT(klp_id) AS cnt')->cnt;
                $klpnum     = $klpnum + 1;
                $data['tapel']  = $tapel;
                $data['klpnum'] = $this->conv->toStr($klpnum);
                $this->load->view('kelompok/add_data',$data);
            }
        }
    }
    function add_data_submit(){
        $json['t']  = 0; $json['msg'] = '';
        if(!$this->session->userdata('login')){
            $json['msg'] = 'Forbidden';
        } elseif (!$this->session->userdata('kk_id')){
            $json['msg'] = 'Bukan Kaprodi';
        } else {
            $kk_id      = $this->session->userdata('kk_id');
            $frtapel    = $this->input->post('frtapel');
            $klp_name   = $this->input->post('klp_name');
            $chkName    = $this->dbase->dataRow('kelompok_siswa',array('kk_id'=>$kk_id,'klp_name'=>$klp_name,'klp_status'=>1),'klp_id');
            if (strlen($klp_name) == 0){
                $json['msg'] = 'Isikan nama kelompok';
            } elseif ($chkName){
                $json['msg']    = 'Nama kelompok sudah ada';
            } else {
                $klp_id = $this->dbase->dataInsert('kelompok_siswa',array('kk_id'=>$kk_id,'klp_tapel'=>$frtapel,'klp_name'=>$klp_name));
                if (!$kk_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t']  = 1;
                    $json['data']   = $this->dbase->dataResult('kelompok_siswa',array('kk_id'=>$kk_id,'klp_status'=>1),'klp_id,klp_name');
                    $json['id']     = $klp_id;
                }
            }
        }
        die(json_encode($json));
    }
    function data_home(){
	    $json['t'] = 0; $json['msg'] = '';
	    $keyword    = $this->input->post('keyword');
	    $klp_id     = $this->input->post('klp_id');
	    $kk_id      = $this->session->userdata('kk_id');
	    $sql_klp    = "";
	    if ($klp_id){ $sql_klp = ""; }
	    $dtKlp      = $this->dbase->sqlResult("
	        SELECT      km.*,ks.klp_name,u.user_nis,u.user_fullname,u.user_sex,k.kel_name
            FROM        tb_kelompok_member AS km
            LEFT JOIN   tb_kelompok_siswa AS ks ON km.klp_id = ks.klp_id
            LEFT JOIN   tb_user AS u ON km.user_id = u.user_id
            LEFT JOIN   tb_kelas_member AS kme ON kme.user_id = u.user_id
            LEFT JOIN   tb_kelas AS k ON kme.kel_id = k.kel_id
            WHERE       (
                        u.user_nis LIKE '%".$keyword."%' OR  
                        u.user_fullname LIKE '%".$keyword."%' OR 
                        ks.klp_name LIKE '%".$keyword."%'
                        ) AND km.klm_status = 1 AND km.klp_id = '".$klp_id."'
            ORDER BY    k.kel_name,u.user_fullname ASC
	    ");
	    if (!$dtKlp){
	        $json['msg'] = 'Tidak ada data';
        } else {
	        $json['jml']    = count($dtKlp);
	        $json['t']      = 1;
	        $data['data']   = $dtKlp;
	        $json['html']   = $this->load->view('kelompok/data_home',$data,TRUE);
        }
	    die(json_encode($json));
    }
    function add_member(){
        if(!$this->session->userdata('login')){
            die('Forbidden');
        } elseif (!$this->session->userdata('kk_id')){
            die('Bukan Kaprodi');
        } else {
            $klp_id     = $this->uri->segment(3);
            $dtKel      = $this->dbase->dataRow('kelompok_siswa',array('klp_id'=>$klp_id));
            if (!$klp_id || !$dtKel){
                die('Invalid parameter');
            } else {
                $dtSiswa = $this->dbase->sqlResult("
                    SELECT      us.user_nis,us.user_fullname,us.user_sex,us.user_id,k.kel_name
                    FROM        tb_kelas_member AS km
                    LEFT JOIN   tb_user AS us ON km.user_id = us.user_id
                    LEFT JOIN   tb_kelas AS k ON km.kel_id = k.kel_id
                    WHERE       us.user_level = 1 AND km.km_tapel = '".$dtKel->klp_tapel."' AND k.kk_id = '".$dtKel->kk_id."'
                                AND k.kel_tingkat = 12 AND km.km_status = 1
                                AND us.user_id NOT IN (
                                  SELECT      klm.user_id
                                  FROM        tb_kelompok_member AS klm
                                  LEFT JOIN   tb_kelompok_siswa AS ks ON klm.klp_id = ks.klp_id
                                  WHERE       klm.klm_status = 1 AND ks.kk_id = '".$dtKel->kk_id."'
                                              AND ks.klp_tapel = '".$dtKel->klp_tapel."'
                                )
                    ORDER BY    k.kel_name,us.user_fullname ASC
                ");
                if (!$dtSiswa){
                    die('Tidak ada data siswa');
                } else {
                    $data['data']   = $dtSiswa;
                    $data['kelas']  = $dtKel;
                    $this->load->view('kelompok/add_member',$data);
                }
            }
        }
    }
    function add_member_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $klp_id     = $this->input->post('klp_id');
        $angg       = $this->input->post('user_id');
        $dtKel      = $this->dbase->dataRow('kelompok_siswa',array('klp_id'=>$klp_id));
        if (!$klp_id || !$dtKel) {
            $json['msg'] = 'Invalid parameter';
        } elseif (!$angg){
            $json['msg'] = 'Pilih siswa lebih dulu';
        } elseif (count($angg) == 0){
            $json['msg'] = 'Pilih siswa lebih dulu';
        } else {
            foreach ($angg as $val){
                $chk = $this->dbase->dataRow('kelompok_member',array('user_id'=>$val));
                if ($chk){
                    $this->dbase->dataUpdate('kelompok_member',array('klm_id'=>$chk->klm_id),array('klm_status'=>1,'klp_id'=>$klp_id));
                } else {
                    $this->dbase->dataInsert('kelompok_member',array('klp_id'=>$klp_id,'user_id'=>$val));
                }
            }
            $json['t'] = 1;
            $json['msg'] = count($angg).' siswa berhasil ditambahkan kedalam kelompok';
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 0; $json['msg'] = '';
        $angg       = $this->input->post('klm_id');
        if (!$angg){
            $json['msg'] = 'Pilih siswa lebih dulu';
        } elseif (count($angg) == 0){
            $json['msg'] = 'Pilih siswa lebih dulu';
        } else {
            foreach ($angg as $val){
                $this->dbase->dataUpdate('kelompok_member',array('klm_id'=>$val),array('klm_status'=>0));
            }
            $json['t'] = 1;
            $json['data'] = $angg;
            $json['msg'] = count($angg).' siswa berhasil dihapus dari kelompok';
        }
        die(json_encode($json));
    }
    function delete_data(){
	    $json['t'] = 0; $json['msg'] = '';
	    $klm_id = $this->input->post('klm_id');
	    $dtKlm  = $this->dbase->dataRow('kelompok_member',array('klm_id'=>$klm_id));
	    if (!$klm_id || !$dtKlm){
	        $json['msg'] = 'Invalid parameter';
        } else {
	        $this->dbase->dataUpdate('kelompok_member',array('klm_id'=>$klm_id),array('klm_status'=>0));
	        $json['t'] = 1;
	        $json['msg'] = 'Data berhasil dihapus';
        }
	    die(json_encode($json));
    }
}
