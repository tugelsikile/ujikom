<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tulis extends CI_Controller {
	public function index(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']      = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $data['body']       = 'tulis/home';
            $data['menu']       = 'tulis';
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
        $kk_id      = $this->session->userdata('kk_id');
        $tapel      = $this->input->post('tapel');
        $dtQuiz     = $this->dbase->sqlResult("
            SELECT    Count(km.km_id) AS siswa_cnt,q.quiz_id,q.kk_id,q.mapel_id,q.quiz_jml_soal,kk.kk_name,q.quiz_start,q.quiz_timer,q.quiz_activate
            FROM      tb_quiz AS q
            LEFT JOIN tb_kelas AS k ON q.kk_id = k.kk_id AND k.kk_id = '".$kk_id."' AND k.kel_tingkat = 12 AND k.kel_tapel = '".$tapel."'
            LEFT JOIN tb_kelas_member AS km ON km.kel_id = k.kel_id AND km.km_status = 1
            LEFT JOIN tb_keahlian_kompetensi AS kk ON q.kk_id = kk.kk_id
            WHERE     q.quiz_status = 1 AND q.quiz_ujikom = 1 AND q.kk_id = '".$kk_id."' AND q.quiz_tapel = '".$tapel."'
                      AND q.jp_id = 5
            GROUP BY  q.quiz_id
        ");
        if (!$dtQuiz){
            $json['msg'] = 'Tidak ada data';
        } else {
            $i = 0;
            foreach ($dtQuiz as $val){
                $dtQuiz[$i]     = $val;
                $dtQuiz[$i]->dist = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$val->quiz_id,'qs_status'=>1),'COUNT(DISTINCT(soal_id)) AS cnt')->cnt;
                $dtQuiz[$i]->bank = $this->dbase->dataRow('soal',array('mapel_id'=>$val->mapel_id,'soal_status'=>1),'COUNT(soal_id) AS cnt')->cnt;
                $i++;
            }
            $this->load->library('conv');
            $data['data']   = $dtQuiz;
            $json['t']      = 1;
            $json['html']   = $this->load->view('tulis/data_home',$data,TRUE);
        }
        die(json_encode($json));
    }
    function add_data(){
	    $kk_id  = $this->session->userdata('kk_id');
	    $dtKK   = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$kk_id));
	    if (!$kk_id || !$dtKK){
	        die('Forbidden');
        } else {
	        $data['data']   = $dtKK;
            $this->load->view('tulis/add_data',$data);
        }
    }
    function add_data_submit(){
	    $json['t'] = 0; $json['msg'] = '';
	    $kk_id          = $this->session->userdata('kk_id');
	    $quiz_start     = $this->input->post('quiz_start');
        $date           = explode("-",$quiz_start);
        $tapel          = $this->input->post('tapel');
        $quiz_jml_soal  = $this->input->post('quiz_jml_soal');
        $quiz_timer     = $this->input->post('quiz_timer');
        $dtMapel        = $this->dbase->dataRow('mapel',array('kk_id'=>$kk_id,'mapel_status'=>1,'mapel_tingkat'=>12,'mapel_ukk'=>1));
        if (!$kk_id){
            $json['msg'] = 'Forbidden';
        } elseif (strlen($quiz_start) != 10){
            $json['msg'] = 'Tanggal pelaksanaan belum diisi';
        } elseif (count($date) != 3) {
            $json['msg'] = 'Format tanggal pelaksanaan tidak valid';
        } elseif (!$quiz_jml_soal || $quiz_jml_soal == 0) {
            $json['msg'] = ' Masukkan jumlah soal';
        } elseif (!$quiz_timer || $quiz_timer == 0){
            $json['msg'] = 'Masukkan batas waktu pengerjaan';
        } elseif (!$dtMapel) {
            $json['msg'] = 'Tidak ada Mapel UKK';
        } else {
            $chkQuiz     = $this->dbase->dataRow('quiz',array('quiz_start'=>$quiz_start.' 00:00:00','kk_id'=>$kk_id,'jp_id'=>5,'mapel_id'=>$dtMapel->mapel_id,'kk_id'=>$kk_id,'quiz_status'=>1,'quiz_tapel'=>$tapel,'quiz_ujikom'=>1));
            if ($chkQuiz){
                $json['msg'] = 'Jadwal UKK Tulis sudah ada';
            } else {
                $quiz_id = $this->dbase->dataInsert('quiz',array('quiz_timer'=>$quiz_timer,'quiz_start'=>$quiz_start.' 00:00:00','kk_id'=>$kk_id,'jp_id'=>5,'mapel_id'=>$dtMapel->mapel_id,'kk_id'=>$kk_id,'quiz_status'=>1,'quiz_tapel'=>$tapel,'quiz_ujikom'=>1,'quiz_tingkat'=>12,'quiz_end'=>$quiz_start.' 00:00:00','quiz_jml_soal'=>$quiz_jml_soal));
                if (!$quiz_id){
                    $json['msg'] = 'DB Error';
                } else {
                    $json['t'] = 1;
                    $data['data']   = $this->dbase->sqlResult("
                        SELECT    Count(km.km_id) AS siswa_cnt,q.quiz_id,q.kk_id,q.mapel_id,q.quiz_jml_soal,kk.kk_name,q.quiz_start,q.quiz_timer,q.quiz_activate
                        FROM      tb_quiz AS q
                        LEFT JOIN tb_kelas AS k ON q.kk_id = k.kk_id AND k.kk_id = '".$kk_id."' AND k.kel_tingkat = 12 AND k.kel_tapel = '".$tapel."'
                        LEFT JOIN tb_kelas_member AS km ON km.kel_id = k.kel_id AND km.km_status = 1
                        LEFT JOIN tb_keahlian_kompetensi AS kk ON q.kk_id = kk.kk_id
                        WHERE     q.quiz_id = '".$quiz_id."'
                        GROUP BY  q.quiz_id
                    ");
                    $data['data'][0]->bank = $data['data'][0]->dist = 0;
                    $this->load->library('conv');
                    $json['html']   = $this->load->view('tulis/data_home',$data,true);
                    $json['msg']    = 'Jadwal berhasil ditambahkan';
                }
            }
        }

	    die(json_encode($json));
    }
    function edit_data(){
	    $quiz_id    = $this->uri->segment(3);
	    $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id));
	    if (!$dtQuiz){
	        die('Invalid parameter');
        } else {
	        $data['data']   = $dtQuiz;
	        $data['kk']     = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$dtQuiz->kk_id));
	        $this->load->view('tulis/edit_data',$data);
        }
    }
    function edit_data_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $kk_id          = $this->session->userdata('kk_id');
        $quiz_id        = $this->input->post('quiz_id');
        $dtQuiz         = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id));
        $quiz_start     = $this->input->post('quiz_start');
        $date           = explode("-",$quiz_start);
        $tapel          = $this->input->post('tapel');
        $quiz_jml_soal  = $this->input->post('quiz_jml_soal');
        $quiz_timer     = $this->input->post('quiz_timer');
        $dtMapel        = $this->dbase->dataRow('mapel',array('kk_id'=>$kk_id,'mapel_status'=>1,'mapel_tingkat'=>12,'mapel_ukk'=>1));
        if (!$kk_id) {
            $json['msg'] = 'Forbidden';
        } elseif (!$quiz_id || !$dtQuiz){
            $json['msg'] = 'Invalid parameter';
        } elseif (strlen($quiz_start) != 10){
            $json['msg'] = 'Tanggal pelaksanaan belum diisi';
        } elseif (count($date) != 3) {
            $json['msg'] = 'Format tanggal pelaksanaan tidak valid';
        } elseif (!$quiz_jml_soal || $quiz_jml_soal == 0) {
            $json['msg'] = ' Masukkan jumlah soal';
        } elseif (!$quiz_timer || $quiz_timer == 0){
            $json['msg'] = 'Masukkan batas waktu pengerjaan';
        } elseif (!$dtMapel) {
            $json['msg'] = 'Tidak ada Mapel UKK';
        } else {
            $this->dbase->dataUpdate('quiz',array('quiz_id'=>$quiz_id),array('quiz_timer'=>$quiz_timer,'quiz_start'=>$quiz_start.' 00:00:00','quiz_end'=>$quiz_start.' 00:00:00','quiz_jml_soal'=>$quiz_jml_soal));
            $json['t']  = 1;
            $json['msg'] = 'Tes Tulis berhasil dirubah';
        }

        die(json_encode($json));
    }
    function bulk_delete(){
	    $json['t'] = 0; $json['msg'] = '';
	    $quiz_id    = $this->input->post('quiz_id');
	    if (!$quiz_id){
	        $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($quiz_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
	        foreach ($quiz_id as $val){
	            $this->dbase->dataUpdate('quiz',array('quiz_id'=>$val),array('quiz_status'=>0));
            }
            $json['t']      = 1;
	        $json['data']   = $quiz_id;
	        $json['msg']    = 'Data berhasil dihapus';
        }
	    die(json_encode($json));
    }
    function soal(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['body']       = 'tulis/soal_home';
            $data['menu']       = 'tulis';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_soal(){
	    $json['t'] = 0; $json['msg'] = '';
	    $keyword    = $this->input->post('keyword');
	    $kk_id      = $this->session->userdata('kk_id');
	    $dtMapel    = $this->dbase->dataRow('mapel',array('kk_id'=>$kk_id,'mapel_status'=>1,'mapel_ukk'=>1,'mapel_tingkat'=>12));
	    if (!$dtMapel){
	        $json['msg'] = 'Invalid quiz';
        } else {
            //$dtSoal     = $this->dbase->dataResult('soal',array('mapel_id'=>$dtMapel->mapel_id,'soal_status'=>1),'soal_id,soal_content,soal_nomor','soal_nomor','ASC');
            $dtSoal     = $this->dbase->sqlResult("
                SELECT    s.soal_id,s.soal_content,s.soal_nomor
                FROM      tb_soal AS s
                LEFT JOIN tb_soal_pg AS sp ON sp.soal_id = s.soal_id
                WHERE     (
                          s.soal_content LIKE '%".$keyword."%' OR
                          sp.pg_content LIKE '%".$keyword."%'
                          ) AND s.soal_status = 1 AND s.mapel_id = '".$dtMapel->mapel_id."'
                GROUP BY  s.soal_id
                ORDER BY  s.soal_nomor ASC
            ");
            if (!$dtSoal){
                $json['msg'] = 'Tidak ada data';
            } else {
                $i = 0;
                foreach ($dtSoal as $valSoal){
                    $dtSoal[$i]     = $valSoal;
                    $dtSoal[$i]->pg = $this->dbase->dataResult('soal_pg',array('soal_id'=>$valSoal->soal_id,'pg_status'=>1),'pg_id,pg_content,pg_is_right','pg_nomor','ASC');
                    $i++;
                }
                $data['data']   = $dtSoal;
                $json['t']      = 1;
                $json['html']   = $this->load->view('tulis/data_soal',$data,true);
            }
        }
	    die(json_encode($json));
    }
    function bulk_delete_soal(){
        $json['t']  = 0; $json['msg'] = '';
        $soal_id    = $this->input->post('soal_id');
        if (!$soal_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (count($soal_id) == 0){
            $json['msg'] = 'Pilih data lebih dulu';
        } else {
            $mapel_id = NULL;
            foreach ($soal_id as $val){
                $chk = $this->dbase->dataRow('soal',array('soal_id'=>$val),'mapel_id');
                if ($chk){
                    $this->dbase->dataUpdate('soal',array('soal_id'=>$val),array('soal_status'=>0));
                    $mapel_id = $chk->mapel_id;
                }
            }
            if ($mapel_id){
                $nomor = 1;
                $soal = $this->dbase->dataResult('soal',array('soal_status'=>1,'mapel_id'=>$mapel_id),'soal_id','soal_nomor','ASC');
                if ($soal){
                    foreach ($soal as $val){
                        $this->dbase->dataUpdate('soal',array('soal_id'=>$val->soal_id),array('soal_nomor'=>$nomor));
                        $nomor++;
                    }
                }
            }

            $json['t']      = 1;
            $json['data']   = $soal_id;
            $json['msg']    = 'Data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function add_soal(){
	    $kk_id      = $this->session->userdata('kk_id');
	    $dtMapel    = $this->dbase->dataRow('mapel',array('mapel_status'=>1,'kk_id'=>$kk_id,'mapel_tingkat'=>12,'mapel_ukk'=>1));
	    if (!$dtMapel){
	        die('Tidak ada mapel');
        } else {
            $soal_urut  = $this->dbase->dataRow('soal',array('mapel_id'=>$dtMapel->mapel_id,'soal_status'=>1),'COUNT(soal_id) AS cnt')->cnt;
            $soal_urut  = $soal_urut + 1;
            $data['nomor']  = $soal_urut;
	        $data['data']   = $dtMapel;
	        $this->load->view('tulis/add_soal',$data);
        }
    }
    function upload_img(){
        //var_dump($_FILES['file']['name']);
        $file_name  = $_FILES['file']['name'];
        $tmp_name   = $_FILES['file']['tmp_name'];
        $json['t']  = 0; $json['msg'] = '';
        if (!$file_name){
            $json['msg']    = 'Tidak ada file';
        } else {
            if (!file_exists(FCPATH.'assets/upload/')){
                mkdir(FCPATH.'assets/upload/',0777,true);
            }
            $tgt_path   = FCPATH . 'assets/upload/';
            $ext        = explode(".",$file_name);
            $ext        = end($ext);
            $ext        = strtolower($ext);
            $allow      = array('jpg','png','gif');
            if (!in_array($ext,$allow)){
                $json['msg']    = 'File not allowed';
            } else {
                $this->load->helper('string');
                $new_name       = date('ymdhis').time().random_string('alnum',10);
                $new_name       = md5($new_name).'.'.$ext;
                $tgt_file       = $tgt_path.$new_name;
                @move_uploaded_file($tmp_name,$tgt_file);
                @chmod($tgt_file,0777);
                $json['t']      = 1;
                $json['url']    = base_url('assets/upload/'.$new_name);
                $json['file_name'] = $new_name;
            }
        }
        die(json_encode($json));
    }
    function add_soal_submit(){
        $json['t']  = 0; $json['msg'] = '';
        $mapel_id       = $this->input->post('mapel_id');
        $soal_content   = $this->input->post('soal_content');
        $dtMapel        = $this->dbase->dataRow('mapel',array('mapel_id'=>$mapel_id));
        $soal_nomor     = $this->input->post('soal_nomor');
        if (!$mapel_id || !$dtMapel){
            $json['msg']    = 'Invalid parameter';
        } elseif (strlen(trim(strip_tags($soal_content))) == 0){
            $json['msg']    = 'Masukkan isi soal';
        } else {
            $soal_id    = $this->dbase->dataInsert('soal',array('mapel_id'=>$mapel_id,'soal_nomor'=>$soal_nomor,'soal_content'=>$soal_content));
            if (!$soal_id){
                $json['msg']    = 'DB Error';
            } else {
                $json['t']      = 1;
                $data['data']   = $this->dbase->dataResult('soal',array('soal_id'=>$soal_id),'soal_id,soal_content,soal_nomor','soal_nomor','ASC');
                $data['data'][0]->pg = array();
                $json['msg']    = 'Soal berhasil ditambahkan';
                $json['html']   = $this->load->view('tulis/data_soal',$data,true);
            }
        }
        die(json_encode($json));
    }
    function edit_soal(){
	    $soal_id    = $this->uri->segment(3);
	    $dtSoal     = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id));
	    if (!$soal_id || !$dtSoal){
	        die('Invalid parameter');
        } else {
	        $dtMapel        = $this->dbase->dataRow('mapel',array('mapel_id'=>$dtSoal->mapel_id));
	        $data['data']   = $dtSoal;
	        $data['mapel']  = $dtMapel;
	        $this->load->view('tulis/edit_soal',$data);
        }
    }
    function edit_soal_submit(){
        $json['t']  = 0; $json['msg'] = '';
        $soal_id        = $this->input->post('soal_id');
        $soal_content   = $this->input->post('soal_content');
        $dtSoal         = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id));
        $soal_nomor     = (int)$this->input->post('soal_nomor');
        $mapel_id       = $this->input->post('mapel_id');
        $chkNomor       = $this->dbase->dataRow('soal',array('soal_nomor'=>$soal_nomor,'mapel_id'=>$mapel_id,'soal_status'=>1,'soal_id !='=>$soal_id),'soal_id');
        if (!$dtSoal || !$soal_id) {
            $json['msg'] = 'Invalid parameter';
        } elseif (!$soal_nomor){
            $json['msg']    = 'Nomor soal belum diisi';
        } elseif ($chkNomor){
            $json['msg'] = 'Nomor soal sudah ada';
        } elseif (strlen(trim(strip_tags($soal_content))) == 0){
            $json['msg']    = 'Masukkan isi soal';
        } else {
            $this->dbase->dataUpdate('soal',array('soal_id'=>$soal_id),array('soal_content'=>$soal_content,'soal_nomor'=>$soal_nomor));
            $json['t']      = 1;
            $json['msg']    = 'Soal berhasil dirubah';
        }
        die(json_encode($json));
    }
    function add_pg(){
	    $soal_id    = $this->uri->segment(3);
	    $dtSoal     = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id));
	    if (!$soal_id || !$dtSoal){
	        die('Invalid parameter');
        } else {
	        $this->load->library('conv');
	        $data['data']   = $dtSoal;
	        $nomor          = $this->dbase->dataRow('soal_pg',array('soal_id'=>$soal_id,'pg_status'=>1),'COUNT(pg_id) AS cnt')->cnt;
	        $nomor          = $nomor + 1;
	        $data['nomor']  = $this->conv->toStr($nomor);
	        $data['pg']     = $nomor;
	        $this->load->view('tulis/add_pg',$data);
        }
    }
    function add_pg_submit(){
        $json['t']  = 0; $json['msg'] = '';
        $soal_id        = $this->input->post('soal_id');
        $pg_nomor       = (int)$this->input->post('pg_nomor');
        $is_right       = $this->input->post('is_right');
        $soal_content   = $this->input->post('soal_content');
        $dtSoal         = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id));
        $chkPG          = $this->dbase->dataRow('soal_pg',array('soal_id'=>$soal_id,'pg_status'=>1,'pg_nomor'=>$pg_nomor));
        if (!$soal_id || !$dtSoal){
            $json['msg'] = 'Invalid parameter';
        } elseif (!$pg_nomor){
            $json['msg'] = 'Invalid Pilihan Ganda';
        } elseif ($chkPG){
            $json['msg'] = 'Pilihan ganda sudah ada';
        } elseif (strlen(trim(strip_tags($soal_content))) == 0){
            $json['msg'] = 'Isi pilihan ganda belum diisi';
        } else {
            if ($is_right == 1){
                $this->dbase->dataUpdate('soal_pg',array('soal_id'=>$soal_id),array('pg_is_right'=>0));
            }
            $pg_id = $this->dbase->dataInsert('soal_pg',array('soal_id'=>$soal_id,'pg_content'=>$soal_content,'pg_nomor'=>$pg_nomor,'pg_is_right'=>$is_right));
            if (!$pg_id){
                $json['msg'] = 'DB Error';
            } else {
                $json['t']   = 1;
                $data['data']   = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id));
                $json['html']   = $this->load->view('tulis/pg_content',$data,true);
                $json['soal_id']= $soal_id;
                $json['msg']    = 'Pilihan ganda berhasil ditambahkan';
            }
        }
        die(json_encode($json));
    }
    function edit_pg(){
	    $pg_id      = $this->uri->segment(3);
	    $dtPG       = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id));
	    if (!$dtPG || !$pg_id){
	        die('Invalid parameter');
        } else {
	        $dtSoal = $this->dbase->dataRow('soal',array('soal_id'=>$dtPG->soal_id),'soal_nomor');
	        $data['data']   = $dtPG;
	        $data['soal']   = $dtSoal;
	        $this->load->library('conv');
	        $this->load->view('tulis/edit_pg',$data);
        }
    }
    function edit_pg_submit(){
        $json['t']  = 0; $json['msg'] = '';
        $pg_id         = $this->input->post('pg_id');
        $pg_nomor       = (int)$this->input->post('pg_nomor');
        $is_right       = $this->input->post('is_right');
        $soal_content   = $this->input->post('soal_content');
        $dtPG          = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id));

        if (!$pg_id || !$dtPG){
            $json['msg'] = 'Invalid parameter';
        } elseif (!$pg_nomor){
            $json['msg'] = 'Invalid Pilihan Ganda';
        } elseif (strlen(trim(strip_tags($soal_content))) == 0){
            $json['msg'] = 'Isi pilihan ganda belum diisi';
        } else {
            $chkPG          = $this->dbase->dataRow('soal_pg',array('pg_id !='=>$pg_id,'soal_id'=>$dtPG->soal_id,'pg_status'=>1,'pg_nomor'=>$pg_nomor));
            if ($chkPG) {
                $json['msg'] = 'Pilihan ganda sudah ada';
            } else {
                if ($is_right == 1){
                    $this->dbase->dataUpdate('soal_pg',array('pg_id !='=>$pg_id,'soal_id'=>$dtPG->soal_id),array('pg_is_right'=>0));
                }
                $this->dbase->dataUpdate('soal_pg',array('pg_id'=>$pg_id),array('pg_nomor'=>$pg_nomor,'pg_content'=>$soal_content,'pg_is_right'=>$is_right));
                $json['t']  = 1;
                $json['msg']        = 'Pilihan ganda berhasil dirubah';
            }
        }
        die(json_encode($json));
    }
    function set_jawaban(){
	    $json['t'] = 0; $json['msg'] = '';
	    $pg_id      = $this->input->post('pg_id');
	    $dtPG       = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id));
	    if (!$pg_id || !$dtPG){
	        $json['msg'] = 'Invalid parameter';
        } else {
	        $this->dbase->dataUpdate('soal_pg',array('pg_id !='=>$pg_id,'soal_id'=>$dtPG->soal_id),array('pg_is_right'=>0));
	        $this->dbase->dataUpdate('soal_pg',array('pg_id'=>$pg_id),array('pg_is_right'=>1));
	        $json['t']  = 1;
	        $json['soal_id']    = $dtPG->soal_id;
	        $json['msg']        = '';
        }
	    die(json_encode($json));
    }
    function delete_pg(){
	    $json['t'] = 0; $json['msg'] = '';
	    $pg_id      = $this->input->post('pg_id');
	    $dtPG       = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id));
	    if (!$pg_id || !$dtPG){
	        $json['msg']    = 'Invalid parameter';
        } else {
	        $this->dbase->dataUpdate('soal_pg',array('pg_id'=>$pg_id),array('pg_status'=>0));
	        $json['t']  = 1;
	        $json['msg']= 'Data berhasil dihapus';
        }
	    die(json_encode($json));
    }
    function distribusi(){
	    $quiz_id    = $this->input->post('id');
	    $json['t']  = 0; $json['msg'] = '';
	    $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id));

	    if (!$dtQuiz || !$quiz_id){
	        $json['msg'] = 'Invalid parameter';
        } else {
            $kk_id          = $dtQuiz->kk_id;
	        $mapel_id       = $dtQuiz->mapel_id;
	        $jml_bank_soal  = $this->dbase->dataRow('soal',array('mapel_id'=>$mapel_id,'soal_status'=>1),'COUNT(soal_id) AS cnt')->cnt;
            if ($jml_bank_soal < $dtQuiz->quiz_jml_soal){
                $json['msg'] = 'Jumlah soal didalam Bank Soal kurang memenuhi jumlah soal untuk tes ini.';
            } else {
                $dtSiswa = $this->dbase->sqlResult("
                    SELECT      km.user_id,u.user_fullname
                    FROM        tb_kelas_member AS km
                    LEFT JOIN   tb_kelas AS k ON km.kel_id = k.kel_id
                    LEFT JOIN   tb_user AS u ON km.user_id = u.user_id
                    WHERE       km.km_status = 1 AND k.kk_id = '".$kk_id."' AND k.kel_tapel = '".$dtQuiz->quiz_tapel."' AND k.kel_tingkat = 12
                    ORDER BY    u.user_fullname ASC
                ");
                if (!$dtSiswa){
                    $json['msg'] = 'Tidak ada data siswa';
                } else {
                    $json['data']       = $dtSiswa;
                    $json['mapel_id']   = $mapel_id;
                    $json['jml_soal']   = $dtQuiz->quiz_jml_soal;
                    $json['quiz_id']    = $quiz_id;
                    $json['t']          = 1;
                }
            }
        }
	    die(json_encode($json));
    }
    function distribusi_proses(){
	    $json['t'] = 0; $json['msg'] = '';
	    $user_id    = $this->input->post('user_id');
	    $mapel_id   = $this->input->post('mapel_id');
	    $jml_soal   = $this->input->post('jml_soal');
	    $quiz_id    = $this->input->post('quiz_id');
	    $dtSiswa    = $this->dbase->dataRow('user',array('user_id'=>$user_id),'user_fullname');
	    if (!$user_id || !$dtSiswa){
	        $json['msg'] = 'Invalid data siswa';
        } else {
            $chkSoalSiswa   = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$user_id,'qs_status'=>1),'COUNT(qs_id) AS cnt')->cnt;
            $limit          = $jml_soal - $chkSoalSiswa;
            if ($chkSoalSiswa == 0 || $chkSoalSiswa < $jml_soal){
                $soal   = $this->dbase->dataResult('soal',array('mapel_id'=>$mapel_id,'soal_status'=>1),'soal_id','RAND()','ASC',$limit);
                $nomor  = $chkSoalSiswa + 1;
                foreach ($soal as $valSoal){
                    $chkQS = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$user_id,'soal_id'=>$valSoal->soal_id),'qs_id');
                    if ($chkQS){
                        $this->dbase->dataUpdate('quiz_soal',array('qs_id'=>$chkQS->qs_id),array('qs_status'=>1,'qs_nomor'=>$nomor));
                    } else {
                        $this->dbase->dataInsert('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$user_id,'soal_id'=>$valSoal->soal_id,'qs_nomor'=>$nomor));
                    }
                    $nomor++;
                }
                $json['t'] = 1;
            } else {
                $json['t'] = 1;
            }
        }
	    die(json_encode($json));
        /*$dtCount    = $nomor = 0;
        foreach ($dtSiswa as $valSiswa){
            $chkSoalSiswa = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$valSiswa->user_id,'qs_status'=>1),'COUNT(qs_id) AS cnt')->cnt;
            $limit        = $dtQuiz->quiz_jml_soal - $chkSoalSiswa;
            if ($chkSoalSiswa == 0 || $chkSoalSiswa < $dtQuiz->quiz_jml_soal){
                $soal   = $this->dbase->dataResult('soal',array('mapel_id'=>$mapel_id,'soal_status'=>1),'soal_id','RAND()','ASC',$limit);
                $nomor  = $chkSoalSiswa + 1;
                foreach ($soal as $valSoal){
                    $chkQS = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$valSiswa->user_id,'soal_id'=>$valSoal->soal_id),'qs_id');
                    if ($chkQS){
                        $this->dbase->dataUpdate('quiz_soal',array('qs_id'=>$chkQS->qs_id),array('qs_status'=>1,'qs_nomor'=>$nomor));
                    } else {
                        $this->dbase->dataInsert('quiz_soal',array('quiz_id'=>$quiz_id,'user_id'=>$valSiswa->user_id,'soal_id'=>$valSoal->soal_id,'qs_nomor'=>$nomor));
                    }
                    $nomor++;
                }
            }
            $dtCount++;
        }*/
    }
    function distribusi_detail(){
	    $quiz_id    = $this->uri->segment(3);
	    $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id),'quiz_id,mapel_id,quiz_tapel,quiz_jml_soal,kk_id');
	    if (!$quiz_id || !$dtQuiz){
	        die('Invalid parameter');
        } else {
	        $kk_id      = $dtQuiz->kk_id;
            $dtSiswa    = $this->dbase->sqlResult("
                    SELECT      km.user_id,u.user_nis,u.user_fullname,u.user_sex
                    FROM        tb_kelas_member AS km
                    LEFT JOIN   tb_kelas AS k ON km.kel_id = k.kel_id
                    LEFT JOIN   tb_user AS u ON km.user_id = u.user_id
                    WHERE       km.km_status = 1 AND k.kk_id = '".$kk_id."' AND k.kel_tapel = '".$dtQuiz->quiz_tapel."' AND k.kel_tingkat = 12
                    ORDER BY    km.km_id ASC
                ");
            if ($dtSiswa){
                $i = 0;
                foreach ($dtSiswa as $valS){
                    $dtSiswa[$i]        = $valS;
                    $dtSiswa[$i]->jml_soal = $dtQuiz->quiz_jml_soal;
                    $dtSiswa[$i]->dist  = $this->dbase->dataRow('quiz_soal',array('user_id'=>$valS->user_id,'quiz_id'=>$quiz_id,'qs_status'=>1),'COUNT(qs_id) AS cnt')->cnt;
                    $i++;
                }
            }
            $data['data']   = $dtSiswa;
            $this->load->view('tulis/distribusi_detail',$data);
        }
    }
    function delete_soal(){
        $json['t']  = 0; $json['msg'] = '';
        $soal_id    = $this->input->post('soal_id');
        $dtSoal     = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id));
        if (!$soal_id){
            $json['msg'] = 'Pilih data lebih dulu';
        } elseif (!$dtSoal){
            $json['msg'] = 'Invalid parameter';
        } else {
            $this->dbase->dataUpdate('soal',array('soal_id'=>$soal_id),array('soal_status'=>0));
            $prev           = $this->dbase->dataResult('soal',array('mapel_id'=>$dtSoal->mapel_id,'soal_status'=>1,'soal_nomor <'=>$dtSoal->soal_nomor),'soal_id','soal_nomor','ASC');
            $next           = $this->dbase->dataResult('soal',array('mapel_id'=>$dtSoal->mapel_id,'soal_status'=>1,'soal_nomor >'=>$dtSoal->soal_nomor),'soal_id','soal_nomor','ASC');
            $nomor  = 1;
            if ($prev){
                foreach ($prev as $val){
                    $this->dbase->dataUpdate('soal',array('soal_id'=>$val->soal_id),array('soal_nomor'=>$nomor));
                    $nomor++;
                }
            }
            if ($next){
                foreach ($next as $val){
                    $this->dbase->dataUpdate('soal',array('soal_id'=>$val->soal_id),array('soal_nomor'=>$nomor));
                    $nomor++;
                }
            }

            $json['t']      = 1;
            $json['msg']    = 'Data berhasil dihapus';
        }
        die(json_encode($json));
    }
    function download_format(){
	    $mapel_id   = $this->uri->segment(3);
	    $dtMapel    = $this->dbase->dataRow('mapel',array('mapel_id'=>$mapel_id));
	    if (!$dtMapel || !$mapel_id){
	        die('Invalid parameter');
        } else {
            $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
            $excel2 = IOFactory::createReader('Excel2007');
            $excel2 = $excel2->load(FCPATH . 'assets/format/upload_soal.xlsx');
            $excel2->setActiveSheetIndex(0);
            $excel2->getActiveSheet()->setCellValue('A1',$mapel_id);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Import Soal '.$dtMapel->mapel_name.'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter = IOFactory::createWriter($excel2, 'Excel2007');
            $objWriter->save('php://output');
        }
    }
    function import_soal(){
	    $kk_id      = $this->session->userdata('kk_id');
        $dtMapel    = $this->dbase->dataRow('mapel',array('mapel_status'=>1,'kk_id'=>$kk_id,'mapel_tingkat'=>12,'mapel_ukk'=>1));
        if (!$kk_id || !$dtMapel){
            die('Invalid parameter');
        } else {
            $data['data']   = $dtMapel;
            $data['kk']     = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$kk_id),'kk_name');
            $this->load->view('tulis/import_soal',$data);
        }
    }
    function import_soal_submit(){
        $json['t'] = 0; $json['msg'] = '';
        $file_name  = $_FILES['file']['name'];
        $ext        = explode(".",$file_name);
        $ext        = end($ext);
        $ext        = strtolower($ext);
        if ($ext != 'xlsx'){
            $json['msg'] = 'File yang diizinkan adalah xlsx';
        } else {
            $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
            $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
            $cacheSettings = array( 'memoryCacheSize' => '2GB');
            PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
            $inputFileName  = $_FILES["file"]["tmp_name"];
            $inputFileType 	= IOFactory::identify($inputFileName);
            $objReader 		= IOFactory::createReader($inputFileType);
            $objPHPExcel 	= $objReader->load($inputFileName);
            try {
                $inputFileType = IOFactory::identify($inputFileName);
                $objReader = IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
                $json['msg'] = 'Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage();
            }//end try

            $sheet          = $objPHPExcel->getActiveSheet();
            $mapel_id       = $sheet->getCell('A1')->getValue();
            $dtMapel        = $this->dbase->dataRow('mapel',array('mapel_id'=>$mapel_id));
            if (!$dtMapel || !$mapel_id){
                $json['msg'] = 'Format file tidak valid';
            } else {
                $highestColumn 	= $sheet->getHighestColumn();
                $highestColumn  = $this->conv->toNum($highestColumn);
                $highestRow 	= $sheet->getHighestRow();
                $dataCount		= 0;
                $nomor          = $this->dbase->dataRow('soal',array('soal_status'=>1,'mapel_id'=>$mapel_id),'COUNT(soal_id) AS cnt')->cnt;
                $nomor          = $nomor + 1;
                for ($row = 4; $row <= $highestRow; $row++){
                    $isi_soal   = $sheet->getCell('B'.$row)->getValue();
                    $jawaban    = $sheet->getCell('C'.$row)->getValue();
                    if (strlen(trim($isi_soal)) > 0){
                        $soal_id = $this->dbase->dataInsert('soal',array('soal_content'=>$isi_soal,'mapel_id'=>$mapel_id,'soal_nomor'=>$nomor));
                        $nomor++; $dataCount++;
                        $nomor_pg = 1;
                        for ($col = 4; $col <= $highestColumn; $col++){
                            $pg_content = $sheet->getCell($this->conv->toStr($col).$row)->getValue();
                            if (strlen(trim($pg_content)) > 0){
                                if (strtolower($this->conv->toStr($nomor_pg)) == strtolower($jawaban)){
                                    $pg_is_right = 1;
                                } else {
                                    $pg_is_right = 0;
                                }
                                $pg_id = $this->dbase->dataInsert('soal_pg',array('pg_is_right'=>$pg_is_right,'soal_id'=>$soal_id,'pg_nomor'=>$nomor_pg,'pg_content'=>$pg_content));
                                $nomor_pg++;
                            }
                        }
                    }
                }
                if ($dataCount == 0){
                    $json['msg'] = 'Tidak ada data soal pada file ini';
                } else {
                    $json['t']      = 1;
                    $json['msg']    = $dataCount.' soal berhasil diupload';
                }
            }
        }
        die(json_encode($json));
    }
    function set_active(){
	    $json['t'] = 0; $json['msg'] = '';
	    $quiz_id    = $this->input->post('quiz_id');
	    $status     = $this->input->post('status');
	    $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id));
	    if (!$dtQuiz || !$quiz_id){
	        $json['msg'] = 'Invalid parameter';
        } else {
	        $this->dbase->dataUpdate('quiz',array('quiz_id'=>$quiz_id),array('quiz_activate'=>$status));
	        $json['t'] = 1;
	        $json['msg'] = 'Status tes berhasil dirubah';
        }
	    die(json_encode($json));
    }
    function status_tes(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') < 90){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']      = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $data['kk']         = $this->dbase->dataResult('keahlian_kompetensi',array('kk_status'=>1),'kk_id,kk_name');
            $data['body']       = 'tulis/status_tes';
            $data['menu']       = 'quiz';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function data_status_tes(){
        $json['t'] = 0; $json['msg'] = '';
        $keyword    = $this->input->post('keyword');
        $kk_id      = $this->input->post('kk_id');
        $sql_kk1 = $sql_kk2 = "";
        if ($kk_id){
            $sql_kk1 = " AND k.kk_id = '".$kk_id."' ";
            $sql_kk2 = " AND q.kk_id = '".$kk_id."' ";
        }
        $tapel      = $this->input->post('tapel');
        $dtQuiz     = $this->dbase->sqlResult("
            SELECT    Count(km.km_id) AS siswa_cnt,q.quiz_id,q.kk_id,q.mapel_id,q.quiz_jml_soal,kk.kk_name,q.quiz_start,q.quiz_timer,q.quiz_activate
            FROM      tb_quiz AS q
            LEFT JOIN tb_kelas AS k ON q.kk_id = k.kk_id ".$sql_kk1." AND k.kel_tingkat = 12 AND k.kel_tapel = '".$tapel."'
            LEFT JOIN tb_kelas_member AS km ON km.kel_id = k.kel_id AND km.km_status = 1
            LEFT JOIN tb_keahlian_kompetensi AS kk ON q.kk_id = kk.kk_id
            WHERE     q.quiz_status = 1 AND q.quiz_ujikom = 1 ".$sql_kk2." AND q.quiz_tapel = '".$tapel."'
                      AND q.jp_id = 5
            GROUP BY  q.quiz_id
        ");
        if (!$dtQuiz){
            $json['msg'] = 'Tidak ada data';
        } else {
            $i = 0;
            foreach ($dtQuiz as $val){
                $dtQuiz[$i]     = $val;
                $dtQuiz[$i]->dist = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$val->quiz_id,'qs_status'=>1),'COUNT(DISTINCT(soal_id)) AS cnt')->cnt;
                $dtQuiz[$i]->bank = $this->dbase->dataRow('soal',array('mapel_id'=>$val->mapel_id,'soal_status'=>1),'COUNT(soal_id) AS cnt')->cnt;
                $i++;
            }
            $this->load->library('conv');
            $data['data']   = $dtQuiz;
            $json['t']      = 1;
            $json['html']   = $this->load->view('tulis/data_home',$data,TRUE);
        }
        die(json_encode($json));
    }
}
