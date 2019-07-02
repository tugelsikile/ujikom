<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {
	public function index(){
	    if($this->session->userdata('login')){
	        redirect(base_url(''));
        }
		$this->load->view('login');
	}
	function submit(){
	    $json['t']  = 0; $json['msg'] = '';
	    $username   = $this->input->post('username');
	    $password   = $this->input->post('password');
	    $dtUser     = $this->dbase->dataRow('user',array('user_name'=>$username,'user_status'=>1));
	    if (strlen(trim($username)) == 0){
	        $json['msg'] = 'Isikan username';
        } elseif (!$dtUser){
	        $json['msg'] = 'Username tidak ditemukan';
        } elseif (strlen(trim($password)) == 0){
	        $json['msg'] = 'Isikan password';
        } elseif ($password != $dtUser->user_password){
	        $json['msg'] = 'Password salah';
        } else {
            $arr = array();
	        if ($dtUser->user_level >= 40){
                $arr = array(
                    'login' => true, 'user_id' => $dtUser->user_id,
                    'user_fullname' => $dtUser->user_fullname,'user_level'=>$dtUser->user_level,
                    'kk_id' => $dtUser->kk_id, 'user_in' => $dtUser->user_in
                );
                $json['t'] = 1;
            } else {
	            $dtKM = $this->dbase->sqlRow("
                    SELECT    k.kk_id,k.kel_tapel
                    FROM      tb_kelas_member AS km
                    LEFT JOIN tb_kelas AS k ON km.kel_id = k.kel_id
                    WHERE     km.user_id = '".$dtUser->user_id."' AND km.km_status = 1 AND k.kel_tingkat = 12
	            ");
	            if (!$dtKM){
	                $json['msg'] = 'Pengguna tidak valid';
                } else {
	                $dtQuiz = $this->dbase->dataRow('quiz',array('quiz_activate'=>1,'quiz_tingkat'=>12,'kk_id'=>$dtKM->kk_id,'quiz_tapel'=>$dtKM->kel_tapel,'quiz_status'=>1,'quiz_ujikom'=>1),'quiz_id,quiz_timer');
	                if (!$dtQuiz){
	                    $json['msg'] = 'Tidak ada data Tes yang valid. Silahkan hubungi Proktor';
                    } else {
	                    $dtSoal = $this->dbase->dataRow('quiz_soal',array('quiz_id'=>$dtQuiz->quiz_id,'qs_status'=>1,'user_id'=>$dtUser->user_id));
	                    if (!$dtSoal){
	                        $json['msg'] = 'Anda belum memiliki soal pada tes ini. Silahkan hubungi Panitia';
                        } else {
	                        $dtSes = $this->dbase->dataRow('quiz_session',array('quiz_id'=>$dtQuiz->quiz_id,'user_id'=>$dtUser->user_id));
	                        if ($dtSes){
	                            if ($dtSes->ses_active > 1){
	                                $json['msg'] = 'Anda sudah mengerjakan tes ini';
                                } else {
                                    if ($dtSes->ses_time_left > 0){
                                        $json['t'] = 1;
                                        $arr = array(
                                            'login' => true, 'user_id' => $dtUser->user_id, 'user_nis' => $dtUser->user_nis,
                                            'user_fullname' => $dtUser->user_fullname,'user_level'=>$dtUser->user_level,
                                            'kk_id' => $dtKM->kk_id, 'quiz_id' => $dtQuiz->quiz_id,
                                            'ses_id' => $dtSes->ses_id, 'ses_time_left' => $dtSes->ses_time_left, 'ses_start' => date('Y-m-d H:i:s'),
                                            'ses_last_time' => date('Y-m-d H:i:s')
                                        );
                                        $this->dbase->dataUpdate('quiz_session',array('ses_id'=>$dtSes->ses_id),array('ses_active'=>1,'ses_start'=>date('Y-m-d H:i:s')));
                                    } else {
                                        $json['msg'] = 'Waktu anda sudah habis untuk sesi tes ini';
                                    }
                                }
                            } else {
                                $ses_id = $this->dbase->dataInsert('quiz_session',array('quiz_id'=>$dtQuiz->quiz_id,'user_id'=>$dtUser->user_id,'ses_start'=>date('Y-m-d H:i:s'),'ses_time_left'=>($dtQuiz->quiz_timer*60)));
                                $arr = array(
                                    'login' => true, 'user_id' => $dtUser->user_id, 'user_nis' => $dtUser->user_nis,
                                    'user_fullname' => $dtUser->user_fullname,'user_level'=>$dtUser->user_level,
                                    'kk_id' => $dtKM->kk_id, 'quiz_id' => $dtQuiz->quiz_id,
                                    'ses_id' => $ses_id, 'ses_time_left' => ($dtQuiz->quiz_timer*60), 'ses_start' => date('Y-m-d H:i:s'),
                                    'ses_last_time' => date('Y-m-d H:i:s')
                                );
                                $json['t'] = 1;
                            }
                        }
                    }
                }
            }
	        $this->session->set_userdata($arr);
	        $json['lvl'] = $dtUser->user_level;
        }
	    die(json_encode($json));
    }
}
