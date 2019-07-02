<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jadwal extends CI_Controller {
	public function index(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']      = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $kk_id              = $this->session->userdata('kk_id');
            $data['kelas']      = $this->dbase->dataResult('kelompok_siswa',array('kk_id'=>$kk_id,'klp_status'=>1,'klp_tapel'=>$data['tapel']));
            $data['body']       = 'jadwal/home';
            $data['menu']       = 'jadwal';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
    function data_home(){
        $json['t'] = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $klp_id     = $this->input->post('klp_id');
        $kk_id      = $this->session->userdata('kk_id');
        $jw_type    = $this->input->post('jw_type');
        $dtJadwal   = $this->dbase->sqlResult("
            SELECT      ju.jw_id,ju.jw_date_start,Count(km.klm_id) AS cnt,ks.klp_name
            FROM        tb_jadwal_ukk AS ju
            LEFT JOIN   tb_kelompok_siswa AS ks ON ju.klp_id = ks.klp_id
            LEFT JOIN   tb_kelompok_member AS km ON km.klp_id = ks.klp_id AND km.klm_status = 1
            WHERE       (
                          ks.klp_name LIKE '%".$keyword."%' OR
                          ju.jw_date_start  LIKE '%".$keyword."%'
                        )
                        AND ju.jw_status = 1 AND ks.klp_id = '".$klp_id."' AND ju.jw_type = '".$jw_type."'
            GROUP BY    ju.jw_id
            ORDER BY    ju.jw_date_start ASC
        ");
        if (!$dtJadwal){
            $json['msg'] = 'Tidak ada data';
        } else {
            $this->load->library('conv');
            $json['jml']    = count($dtJadwal);
            $json['t']      = 1;
            $data['data']   = $dtJadwal;
            $json['html']   = $this->load->view('jadwal/data_home',$data,TRUE);
        }
        die(json_encode($json));
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
	function add_data(){
        if(!$this->session->userdata('login')){
            die('Forbidden');
        } elseif (!$this->session->userdata('kk_id')){
            die('Bukan Kaprodi');
        } else {
            $kk_id  = $this->session->userdata('kk_id');
            $dtKK   = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$kk_id));
            $klp_id = $this->uri->segment(3);
            $dtKlp  = $this->dbase->dataRow('kelompok_siswa',array('klp_id'=>$klp_id));
            if (!$kk_id || !$dtKK) {
                die('Invalid Kompetensi');
            } elseif (!$klp_id || !$dtKlp){
                die('Invalid kelompok');
            } else {
                $data['data']   = $dtKlp;
                $this->load->view('jadwal/add_data',$data);
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
            $frjw_type  = $this->input->post('frjw_type');
            $klp_id     = $this->input->post('klp_id');
            $jw_date    = $this->input->post('jw_date');
            $jw_time    = $this->input->post('jw_time');
            $date       = explode("-",$jw_date);
            $jam        = explode(":",$jw_time);
            $chkJad     = $this->dbase->dataRow('jadwal_ukk',array('kk_id'=>$kk_id,'klp_id'=>$klp_id,'jw_tapel'=>$frtapel,'jw_status'=>1,'jw_date_start'=>$jw_date.' '.$jw_time.':00'));
            if (strlen($jw_date) != 10){
                $json['msg'] = 'Tanggal belum diisi';
            } elseif (count($date) != 3) {
                $json['msg'] = 'Format tanggal tidak valid';
            } elseif (strlen($jw_time) != 5) {
                $json['msg'] = 'Jam belum diisi';
            } elseif (count($jam) != 2) {
                $json['msg'] = 'Format Jam tidak valid';
            } elseif ($chkJad){
                $json['msg'] = 'Jadwal sudah ada';
            } else {
                $jw_id = $this->dbase->dataInsert('jadwal_ukk',array('kk_id'=>$kk_id,'klp_id'=>$klp_id,'jw_tapel'=>$frtapel,'jw_date_start'=>$jw_date.' '.$jw_time,'jw_type'=>$frjw_type));
                if (!$jw_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $this->load->library('conv');
                    $json['t']      = 1;
                    $data['data']   = $this->dbase->sqlResult("
                        SELECT      ju.jw_id,ju.jw_date_start,Count(km.klm_id) AS cnt,ks.klp_name
                        FROM        tb_jadwal_ukk AS ju
                        LEFT JOIN   tb_kelompok_siswa AS ks ON ju.klp_id = ks.klp_id
                        LEFT JOIN   tb_kelompok_member AS km ON km.klp_id = ks.klp_id AND km.klm_status = 1
                        WHERE       ju.jw_id = '".$jw_id."'  
                        GROUP BY    ju.jw_id
                    ");
                    $json['html']   = $this->load->view('jadwal/data_home',$data,true);
                }
            }
        }
        die(json_encode($json));
    }
    function bulk_delete(){
        $json['t'] = 0; $json['msg'] = '';
        $angg       = $this->input->post('jw_id');
        if (!$angg){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($angg) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            foreach ($angg as $val){
                $this->dbase->dataUpdate('jadwal_ukk',array('jw_id'=>$val),array('jw_status'=>0));
            }
            $json['t'] = 1;
            $json['data'] = $angg;
            $json['msg'] = count($angg).' jadwal berhasil dihapus';
        }
        die(json_encode($json));
    }
    function edit_data(){
	    $jw_id      = $this->uri->segment(3);
	    $dtJW       = $this->dbase->dataRow('jadwal_ukk',array('jw_id'=>$jw_id));
	    if (!$jw_id || !$dtJW){
	        die('Invalid parameter');
        } else {
	        $data['data']   = $dtJW;
	        $data['kelas']  = $this->dbase->dataRow('kelompok_siswa',array('klp_id'=>$dtJW->klp_id),'klp_name');
	        $this->load->view('jadwal/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t']  = 0; $json['msg'] = '';
        if(!$this->session->userdata('login')){
            $json['msg'] = 'Forbidden';
        } elseif (!$this->session->userdata('kk_id')){
            $json['msg'] = 'Bukan Kaprodi';
        } else {
            $jw_id      = $this->input->post('jw_id');
            $kk_id      = $this->session->userdata('kk_id');
            $frtapel    = $this->input->post('frtapel');
            $frjw_type  = $this->input->post('frjw_type');
            $klp_id     = $this->input->post('klp_id');
            $jw_date    = $this->input->post('jw_date');
            $jw_time    = $this->input->post('jw_time');
            $date       = explode("-",$jw_date);
            $jam        = explode(":",$jw_time);
            $chkJW      = $this->dbase->dataRow('jadwal_ukk',array('jw_id'=>$jw_id));
            $chkJad     = $this->dbase->dataRow('jadwal_ukk',array('jw_id !='=>$jw_id,'kk_id'=>$kk_id,'klp_id'=>$klp_id,'jw_tapel'=>$frtapel,'jw_status'=>1,'jw_date_start'=>$jw_date.' '.$jw_time.':00'));
            if (!$jw_id || !$chkJW){
                $json['msg'] = 'Parameter invalid';
            } elseif (strlen($jw_date) != 10){
                $json['msg'] = 'Tanggal belum diisi';
            } elseif (count($date) != 3) {
                $json['msg'] = 'Format tanggal tidak valid';
            } elseif (strlen($jw_time) != 5) {
                $json['msg'] = 'Jam belum diisi';
            } elseif (count($jam) != 2) {
                $json['msg'] = 'Format Jam tidak valid';
            } elseif ($chkJad){
                $json['msg'] = 'Jadwal sudah ada';
            } else {
                $this->dbase->dataUpdate('jadwal_ukk',array('jw_id'=>$jw_id),array('jw_date_start'=>$jw_date.' '.$jw_time));
                $json['t']      = 1;
                $json['msg']    = 'Jadwal berhasil dirubah';
            }
        }
        die(json_encode($json));
    }
    function cetak(){
	    $kk_id      = $this->session->userdata('kk_id');
	    $tapel      = $this->uri->segment(3);
	    $klp_id     = $this->uri->segment(4);
	    $jw_type    = $this->uri->segment(5);
	    $dtKelas    = $this->dbase->dataRow('kelompok_siswa',array('klp_tapel'=>$tapel,'klp_id'=>$klp_id,'klp_status'=>1));
	    if (!$dtKelas){
	        die('Invalid Kelompok');
        } else {
            $dtJadwal   = $this->dbase->dataResult('jadwal_ukk',array('jw_status'=>1,'klp_id'=>$klp_id,'kk_id'=>$kk_id,'jw_tapel'=>$tapel,'jw_type'=>$jw_type),'*','jw_date_start','ASC');
            if (!$dtJadwal){
                die('Tidak ada jadwal');
            } else {
                $i = 0;
                foreach ($dtJadwal as $valJ){
                    $dtJadwal[$i]           = $valJ;
                    $dtJadwal[$i]->peserta  = $this->dbase->sqlResult("
                        SELECT      u.user_nis,u.user_fullname,u.user_sex,u.user_nopes
                        FROM        tb_kelompok_member AS km
                        LEFT JOIN   tb_user AS u ON km.user_id = u.user_id
                        WHERE       km.klm_status = 1 AND km.klp_id = '".$valJ->klp_id."'
                        ORDER BY    u.user_fullname ASC
                    ");
                    $i++;
                }
                $this->load->library('conv');
                $dtKelas->kk        = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$dtKelas->kk_id));
                $dtKelas->jadwal    = $dtJadwal;
                $data['data']       = $dtKelas;
                $this->load->view('jadwal/cetak',$data);
            }
        }
    }
    function print_ws(){
        $kk_id      = $this->session->userdata('kk_id');
        $tapel      = $this->uri->segment(3);
        $dtKelas    = $this->dbase->dataRow('kelompok_siswa',array('klp_tapel'=>$tapel));
        if (!$dtKelas){
            die('Tidak ada kelas');
        } else {
            $ws = array();
            for($i = 1; $i < 19; $i++){
                $ws[$i] = array();
                $ws[$i][0]  = new stdClass();
                $grupA  = '';
            }
        }
    }
}
