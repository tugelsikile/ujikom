<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Quiz extends CI_Controller {
    function index(){
        redirect(base_url('quiz/landing'));
    }
	public function landing(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif ($this->session->userdata('user_level') != 1){
            $data['body'] = 'errors/403';
        } else {
            $quiz_id    = $this->session->userdata('quiz_id');
            $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id),'quiz_id,quiz_timer');
            if (!$quiz_id || !$dtQuiz){
                $data['body'] = 'errors/403';
            } else {
                $data['quiz']       = $dtQuiz;
                $user_id            = $this->session->userdata('user_id');
                $data['soal']       = $this->dbase->sqlResult("
                    SELECT      qs.soal_id,qj.qj_id,qs.quiz_id,qs.qs_nomor,sp.pg_nomor
                    FROM        tb_quiz_soal AS qs
                    LEFT JOIN   tb_quiz_jawab AS qj ON qj.soal_id = qs.soal_id AND qj.user_id = '".$user_id."'
                    LEFT JOIN   tb_soal_pg AS sp ON qj.pg_id = sp.pg_id
                    WHERE       qs.quiz_id = '".$quiz_id."' AND qs.qs_status = 1 AND qs.user_id = '".$user_id."'
                    GROUP BY    qs.qs_id,qj.qj_id
                    ORDER BY    qs.qs_nomor ASC
                ");
                $dtJawab      = $this->dbase->dataResult('quiz_jawab',array('quiz_id'=>$quiz_id,'user_id'=>$user_id),'soal_id,quiz_id','qj_date','DESC',1);
                if ($dtJawab){
                    $data['jawaban'] = $dtJawab[0];
                }
                $menit          = $this->session->userdata('ses_time_left')/60;
                $data['menit']  = ceil($menit);
                //die(var_dump($data['jawab']));
                $this->load->library('conv');
                $data['body']       = 'quiz/landing';
                $data['menu']       = 'tulis';
            }
        }
        $this->load->view($data['body'],$data);
	}
	function update_time(){
        $ses_id         = $this->session->userdata('ses_id');
        $ses_time_left  = $this->session->userdata('ses_time_left');
        $last_time      = $this->session->userdata('ses_last_time');
        $timeFirst      = strtotime($last_time);
        $timeSecond     = strtotime(date('Y-m-d H:i:s'));
        $time_sub       = $timeSecond - $timeFirst;
        $ses_time_left  = $ses_time_left - $time_sub;
        $this->session->set_userdata(array('ses_last_time'=>date('Y-m-d H:i:s'),'ses_time_left'=>$ses_time_left));
        $this->dbase->dataUpdate('quiz_session',array('ses_id'=>$ses_id),array('ses_time_left'=>$ses_time_left));
    }
	function load_soal(){
	    $json['t'] = 0; $json['msg'] = '';
	    $soal_id    = $this->input->post('soal_id');
	    $quiz_id    = $this->input->post('quiz_id');
	    $dtSoal     = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id),'soal_id,soal_content,soal_nomor');
	    $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id),'quiz_id');
	    if (!$soal_id || !$dtSoal){
	        $json['msg'] = 'Invalid SOAL';
        } elseif (!$quiz_id || !$dtQuiz) {
            $json['msg'] = 'Invalid QUIZ';
        } elseif (!$this->session->userdata('ses_id')){
	        $json['msg'] = 'Tes tidak valid. Silahkan login kembali';
        } else {
	        $user_id    = $this->session->userdata('user_id');
            $dtPG       = $this->dbase->dataResult('soal_pg',array('soal_id'=>$soal_id,'pg_status'=>1),'pg_id,pg_content,pg_nomor');
            $i = 0;
            foreach ($dtPG as $valPG){
                $dtPG[$i]   = $valPG;
                $dtPG[$i]->is_jawab = 0;
                $dtJawab    = $this->dbase->dataRow('quiz_jawab',array('quiz_id'=>$quiz_id,'pg_id'=>$valPG->pg_id,'user_id'=>$user_id),'qj_id');
                if ($dtJawab){ $dtPG[$i]->is_jawab = 1; }
                $i++;
            }
            $this->load->library('conv');
            $this->update_time();
            //die(var_dump($timer));


            $dtQS           = $this->dbase->dataRow('quiz_soal',array('soal_id'=>$soal_id,'quiz_id'=>$quiz_id,'user_id'=>$user_id),'qs_nomor');
            $dtSoal->pg     = $dtPG;
            $data['data']   = $dtSoal;
            $data['quiz']   = $dtQuiz;
            $json['t']      = 1;
            $json['id']     = $dtSoal->soal_id;
            $json['nomor']  = $dtQS->qs_nomor;
            $json['html']   = $this->load->view('quiz/load_soal',$data,true);
        }
	    die(json_encode($json));
    }
    function set_jawaban(){
	    $json['t'] = 0; $json['msg'] = '';
	    $soal_id        = $this->input->post('soal_id');
	    $quiz_id        = $this->input->post('quiz_id');
	    $pg_id          = $this->input->post('pg_id');
	    $user_id        = $this->session->userdata('user_id');
	    $dtSoal         = $this->dbase->dataRow('soal',array('soal_id'=>$soal_id),'soal_id');
	    $dtQuiz         = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id),'quiz_id');
	    $dtPG           = $this->dbase->dataRow('soal_pg',array('pg_id'=>$pg_id),'pg_id,pg_is_right,pg_nomor');
	    $dtUser         = $this->dbase->dataRow('user',array('user_id'=>$user_id),'user_id');
	    if (!$user_id || !$dtUser){
	        $json['msg'] = 'Sesi Habis. Silahkan login kembali';
        } elseif (!$soal_id || !$dtSoal){
	        $json['msg'] = 'Invalid parameter SOAL';
        } elseif (!$quiz_id || !$dtQuiz){
	        $json['msg'] = 'Invalid parameter TES';
        } elseif (!$pg_id || !$dtPG){
	        $json['msg'] = 'Invalid parameter Pilihan Ganda';
        } else {
	        $chkJawaban = $this->dbase->dataRow('quiz_jawab',array('quiz_id'=>$quiz_id,'user_id'=>$user_id,'soal_id'=>$soal_id),'qj_id');
	        if (!$chkJawaban){
                $qj_id = $this->dbase->dataInsert('quiz_jawab',array(
	                'quiz_id'=>$quiz_id,    'user_id'=>$user_id,    'soal_id'=>$soal_id, 'pg_id' => $pg_id, 'qj_is_right' => $dtPG->pg_is_right, 'qj_score' => $dtPG->pg_is_right
                ));
            } else {
                $qj_id = $chkJawaban->qj_id;
	            $this->dbase->dataUpdate('quiz_jawab',array('qj_id'=>$chkJawaban->qj_id),array('pg_id'=>$pg_id,'qj_is_right'=>$dtPG->pg_is_right,'qj_score'=>$dtPG->pg_is_right));
            }
            $this->update_time();
            $this->load->library('conv');
            $json['t']      = 1;
	        $json['nomor']  = $this->conv->toStr($dtPG->pg_nomor);
        }
	    die(json_encode($json));
    }
    function finish_tes(){
        $json['t']  = 0; $json['msg'] = '';
        $user_id    = $this->session->userdata('user_id');
        $quiz_id    = $this->session->userdata('quiz_id');
        $ses_id     = $this->session->userdata('ses_id');
        $dtUser     = $this->dbase->dataRow('user',array('user_id'=>$user_id),'user_id');
        $dtQuiz     = $this->dbase->dataRow('quiz',array('quiz_id'=>$quiz_id),'quiz_id,quiz_jml_soal');
        $dtSes      = $this->dbase->dataRow('quiz_session',array('ses_id'=>$ses_id),'ses_id');
        if (!$user_id || !$dtUser){
            $json['msg'] = 'Invalid parameter USER';
        } elseif (!$dtQuiz || !$quiz_id){
            $json['msg'] = 'Invalid parameter TES';
        } elseif (!$ses_id || !$dtSes){
            $json['msg'] = 'Invalid parameter SESSION';
        } else {
            $this->dbase->dataUpdate('quiz_session',array('ses_id'=>$ses_id),array('ses_active'=>99));
            $bobot  = ceil(100 / $dtQuiz->quiz_jml_soal);
            $nilai  = $this->dbase->dataRow('quiz_jawab',array('quiz_id'=>$quiz_id,'user_id'=>$user_id),'SUM(qj_score) AS nilai')->nilai;
            $nilai  = $nilai * $bobot;
            $nilai  = ceil($nilai);
            $chkNP  = $this->dbase->dataRow('nilai_pengetahuan',array('user_id'=>$user_id,'np_type'=>'tt'),'np_id');
            if ($chkNP){
                $this->dbase->dataUpdate('nilai_pengetahuan',array('np_id'=>$chkNP->np_id),array('np_nilai'=>$nilai));
            } else {
                $this->dbase->dataInsert('nilai_pengetahuan',array('user_id'=>$user_id,'np_type'=>'tt','np_nilai'=>$nilai));
            }
            $this->session->sess_destroy();
            $json['t']      = 1;
            $json['msg']    = '';
            $json['quiz_id']= $quiz_id;
        }
        die(json_encode($json));
    }
    function result(){

    }
}
