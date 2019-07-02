<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kocok extends CI_Controller {
	public function index(){
	    $this->load->view('kocok/home');
	}
	function submit(){
	    $json['t'] = 0; $json['msg'] = '';
	    $user_name      = $this->input->post('username');
	    $dtUser         = $this->dbase->dataRow('user',array('user_name'=>$user_name));
	    if (strlen($user_name) == 0){
	        $json['msg'] = 'Masukkan Username UNBK';
        } elseif (!$dtUser){
	        $json['msg'] = 'Username UNBK tidak terdaftar';
        } else {
	        $dtKLPMem   = $this->dbase->sqlRow("
	            SELECT    ks.klp_name
                FROM      tb_kelompok_member AS km
                LEFT JOIN tb_kelompok_siswa AS ks ON km.klp_id = ks.klp_id
                WHERE     km.user_id = '".$dtUser->user_id."'
	        ");
	        if ($dtKLPMem){
	            $json['msg'] = 'Anda sudah masuk ke dalam kelompok : '.$dtKLPMem->klp_name;
            } else {
	            $dtKK = $this->dbase->sqlRow("
	              SELECT    k.kel_name,k.kk_id
                  FROM      tb_kelas_member AS km
                  LEFT JOIN tb_kelas AS k ON km.kel_id = k.kel_id
                  WHERE     km.user_id = '".$dtUser->user_id."'
                ");
	            if (!$dtKK){
	                $json['msg'] = 'Tidak ada data kelas';
                } else {
                    $dtKelompok = $this->dbase->dataResult('kelompok_siswa',array('kk_id'=>$dtKK->kk_id,'klp_status'=>1),'klp_name,klp_id,klp_max_mem','RAND()');
                    //var_dump($dtUser);
                    if (!$dtKelompok){
                        $json['msg'] = 'Tidak ada Kelompok pada Kejuruan anda, hubungi kepala kompetensi keahlian';
                    } else {
                        $i = 0; $klp_name = '';
                        foreach ($dtKelompok as $valKlp){
                            $jmlMem = $this->dbase->dataRow('kelompok_member',array('klm_status'=>1),'COUNT(klm_id) AS cnt')->cnt;
                            //die($jmlMem);
                            if ($jmlMem <= $valKlp->klp_max_mem){
                                $this->dbase->dataInsert('kelompok_member',array('user_id'=>$dtUser->user_id,'klp_id'=>$valKlp->klp_id));
                                $klp_name = $valKlp->klp_name;
                                break;
                            }
                            $i++;
                        }
                        $json['t'] = 1;
                        $json['msg'] = 'berhasil';
                        $json['fullname'] = $dtUser->user_fullname;
                        $json['klp_name'] = $klp_name;
                    }
                }
            }
	        //$dtKelompok = $this->dbase->dataResult('')
        }
	    die(json_encode($json));
    }
}
