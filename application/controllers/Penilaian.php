<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Penilaian extends CI_Controller {
	public function index(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']  = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $data['body']   = 'nilai/home';
            $data['menu']   = 'penilaian';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
	}
	function tapel_select(){
	    $json['t'] = 0; $json['msg'] = '';
	    $tapel  = $this->input->post('tapel');
	    $dtSis  = $this->dbase->sqlResult("
	        SELECT  u.user_nis,u.user_fullname,u.user_id
            FROM    tb_kelas_member AS km
            LEFT JOIN tb_user AS u ON km.user_id = u.user_id
            LEFT JOIN tb_kelas AS k ON km.kel_id = k.kel_id
            WHERE   km.km_tapel = '".$tapel."' AND km.km_status = 1 AND k.kk_id = '".$this->session->userdata('kk_id')."'
                    AND k.kel_tingkat = 12
            ORDER BY u.user_fullname ASC
	    ");
	    if (!$dtSis){
	        $json['msg'] = 'Tidak ada data';
        } else {
	        $json['t'] = 1;
	        $json['data']   = $dtSis;
        }
	    die(json_encode($json));
    }
    function data_penilaian(){
	    $json['t'] = 0; $json['msg'] = $json['html'] = '';
	    $tapel          = $this->input->post('tapel');
	    $jenis_nilai    = $this->input->post('jenis');
	    $sis_id         = $this->input->post('sis_id');
	    $dtSis          = $this->dbase->dataRow('user',array('user_id'=>$sis_id));
	    $penguji_tipe   = $this->session->userdata('user_in');
	    $penguji_id     = $this->session->userdata('user_id');
	    $dtPenguji      = $this->dbase->dataRow('user',array('user_id'=>$penguji_id),'user_id');
	    $kk_id          = $this->session->userdata('kk_id');
	    if (!$penguji_id || !$dtPenguji){
	        $json['msg'] = 'Invalid data penguji';
        } elseif (!$penguji_tipe){
	        $json['msg'] = 'Invalid jenis penguji';
        } elseif (!$jenis_nilai){
	        $json['msg'] = 'Pilih jenis penilaian';
        } elseif (!$sis_id || !$dtSis){
	        $json['msg'] = 'Invalid data siswa';
        } else {
	        if ($jenis_nilai == 'praktik'){
                $dtKomponen = $this->dbase->dataResult('k_komponen',array('kom_status'=>1),'kom_id,kom_name');
                if (!$dtKomponen){
                    $json['msg'] = 'Tidak ada komponen penilaian';
                } else {
                    $kom = 0;
                    foreach ($dtKomponen as $valKom){
                        $dtKomponen[$kom] = $valKom;
                        $dtSubKom   = $this->dbase->dataResult('k_sub_komponen',array(
                            'kom_id'=>$valKom->kom_id,  'kk_id'=>$kk_id,    'skom_status' => 1
                        ));
                        $skom = 0;
                        foreach ($dtSubKom as $valSkom){
                            $dtSubKom[$skom]  = $valSkom;
                            $dtInd  = $this->dbase->dataResult('k_indikator',array('skom_id'=>$valSkom->skom_id,'ind_status'=>1));
                            $ind = 0;
                            foreach ($dtInd as $valInd){
                                $dtInd[$ind] = $valInd;
                                //$dtInd[$ind]->jawab = NULL;
                                $chkJawab   = $this->dbase->dataRow('nilai_indikator',array(
                                    'user_id'=>$sis_id, 'ind_id'=>$valInd->ind_id, 'nind_uji_tipe' => $penguji_tipe
                                ));
                                if ($chkJawab){ $dtInd[$ind]->jawab = $chkJawab->nind_nilai; }
                                $ind++;
                            }
                            $dtSubKom[$skom]->indikator = $dtInd;
                            $skom++;
                        }
                        $dtKomponen[$kom]->sub_komponen = $dtSubKom;
                        $kom++;
                    }
                }
            } else {
                $dtKomponen = array();
            }
            if (!$dtKomponen){
	            $json['msg'] = 'Tidak ada data';
            } else {
	            $this->load->library('conv');
                $data['tipe']   = $jenis_nilai;
	            $data['data']   = $dtKomponen;
	            $data['siswa']  = $dtSis;
	            $json['t']      = 1;
	            $json['html']   = $this->load->view('nilai/data_penilaian_table',$data,TRUE);
            }
        }
	    die(json_encode($json));
    }
    function set_jawab(){
	    $json['t'] = 0; $json['msg'] = '';
	    $sis_id     = $this->input->post('sis_id');
	    $ind_id     = $this->input->post('ind_id');
	    $okno       = $this->input->post('okno');
        $penguji_id = $this->session->userdata('user_id');
        $penguji_tipe = $this->session->userdata('user_in');
        $dtPenguji      = $this->dbase->dataRow('user',array('user_id'=>$penguji_id));
        $dtSiswa        = $this->dbase->dataRow('user',array('user_id'=>$sis_id));
        $dtIndikator    = $this->dbase->dataRow('k_indikator',array('ind_id'=>$ind_id));
        if (!$sis_id || !$dtSiswa) {
            $json['msg'] = 'Invalid data siswa';
        } elseif (!$penguji_id || !$dtPenguji) {
            $json['msg'] = 'Invalid data penguji';
        } elseif (!$ind_id || !$dtIndikator) {
            $json['msg'] = 'Invalid data indikator';
        } else {
            $chkNilai = $this->dbase->dataRow('nilai_indikator',array(
                'user_id' => $sis_id, 'ind_id' => $ind_id, 'nind_uji_tipe' => $penguji_tipe
            ));
            if ($okno == 'ok'){ $nilai = 1; } else { $nilai = 0; }
            if ($chkNilai){
                $nind_id = $chkNilai->nind_id;
                $this->dbase->dataUpdate('nilai_indikator',array('nind_id'=>$chkNilai->nind_id),array('nind_nilai'=>$nilai));
            } else {
                $nind_id = $this->dbase->dataInsert('nilai_indikator',array(
                    'nind_nilai'=>$nilai, 'user_id' => $sis_id, 'ind_id' => $ind_id, 'nind_penguji' => $penguji_id,
                    'nind_uji_tipe' => $penguji_tipe
                ));
            }

            if (!$nind_id){
                $json['msg'] = 'DB Error';
            } else {
                //hitung nilai per sub komponen
                $hitskom = $this->dbase->sqlRow("SELECT  SUM(ni.nind_nilai) AS nilai
                            FROM  tb_nilai_indikator AS ni
                            LEFT JOIN tb_k_indikator AS i ON ni.ind_id = i.ind_id
                            WHERE ni.user_id = '".$sis_id."' AND i.skom_id = '".$dtIndikator->skom_id."' 
                                  AND ni.nind_uji_tipe = '".$penguji_tipe."' ");
                if ($hitskom){
                    $nilai = $this->kompeten($dtIndikator->skom_id,$hitskom->nilai);
                    $kom_id = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$dtIndikator->skom_id),'kom_id')->kom_id;
                    $chkNK = $this->dbase->dataRow('nilai_keterampilan',array(
                        'user_id'=>$sis_id, 'skom_id'=>$dtIndikator->skom_id, 'nk_tipe' => $penguji_tipe
                    ),'nk_id');
                    if (!$chkNK){
                        $this->dbase->dataInsert('nilai_keterampilan',array(
                            'user_id'=>$sis_id, 'skom_id'=>$dtIndikator->skom_id, 'nk_tipe' => $penguji_tipe, 'nk_nilai'=>$nilai,
                            'kom_id' => $kom_id
                        ));
                    } else {
                        $this->dbase->dataUpdate('nilai_keterampilan',array('nk_id'=>$chkNK->nk_id),array('nk_nilai'=>$nilai));
                    }
                }
                $json['t'] = 1;
            }
        }
	    die(json_encode($json));
    }
    function kompeten($skom_id,$nilai){
        $dtSkom = $this->dbase->dataRow('k_sub_komponen',array('skom_id'=>$skom_id),'skom_a,skom_b,skom_c,skom_d');
        if (!$dtSkom){
            return 0;
        } else {
            if ($nilai >= $dtSkom->skom_d && $nilai < $dtSkom->skom_c){
                return 0;
            } elseif ($nilai <= $dtSkom->skom_c && $nilai < $dtSkom->skom_b){
                return 1;
            } elseif ($nilai <= $dtSkom->skom_b && $nilai < $dtSkom->skom_a){
                return 2;
            } else {
                return 3;
            }
        }
    }
    function rekap(){
        if(!$this->session->userdata('login')){
            redirect(base_url('login'));
        } elseif (!$this->session->userdata('kk_id')){
            $data['body'] = 'errors/403';
        } else {
            $data['tapel']  = $this->dbase->dataRow('kelas',array('kel_status'=>1),'DISTINCT(kel_tapel) AS tapel')->tapel;
            $data['kk']     = $this->dbase->dataResult('keahlian_kompetensi',array('kk_status'=>1));
            $data['body']   = 'nilai/rekap';
            $data['menu']   = 'penilaian';
        }
        if ($this->input->is_ajax_request()){
            $this->load->view($data['body'],$data);
        } else {
            $this->load->view('home',$data);
        }
    }
    function rekap_data(){
	    $json['t'] = 0; $json['msg'] = '';
	    $tapel  = $this->input->post('tapel');
	    $jenis  = $this->input->post('jenis');
	    $aspek  = $this->input->post('aspek');
        $kk_id  = $this->input->post('kk_id');
        $dtSiswa = $this->dbase->sqlResult("
                SELECT    u.user_nis,u.user_fullname,u.user_sex,km.user_id
                FROM      tb_kelas_member AS km
                LEFT JOIN tb_kelas AS k ON km.kel_id = k.kel_id
                LEFT JOIN tb_user AS u ON km.user_id = u.user_id
                WHERE     k.kk_id = '".$kk_id."' AND km.km_status = 1 AND k.kel_tingkat = 12 AND k.kel_tapel = '".$tapel."'
                ORDER BY  u.user_fullname ASC
        ");
        if (!$dtSiswa){
            $json['msg'] = 'Tidak ada data siswa';
        } else {
            $s = 0;
            foreach ($dtSiswa as $valS){
                $dtSiswa[$s]    = $valS;
                $dtSiswa[$s]->k_siap = $dtSiswa[$s]->k_proses = $dtSiswa[$s]->k_hasil = $dtSiswa[$s]->nt =
                $dtSiswa[$s]->n_tt = $dtSiswa[$s]->n_tl = 0;
                $nk_siap    = $this->dbase->dataRow('nilai_keterampilan',array(
                    'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 1
                ),'AVG(nk_nilai) AS avg');
                if ($nk_siap){ $dtSiswa[$s]->k_siap = ( $nk_siap->avg * 45 ) / 100; }
                $nk_proses  = $this->dbase->dataRow('nilai_keterampilan',array(
                    'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 2
                ),'AVG(nk_nilai) AS avg');
                if ($nk_proses){ $dtSiswa[$s]->k_proses = ( $nk_proses->avg * 30 ) / 100; }
                $nk_hasil = $this->dbase->dataRow('nilai_keterampilan',array(
                    'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 3
                ),'AVG(nk_nilai) AS avg');
                if ($nk_hasil){ $dtSiswa[$s]->k_hasil = ( $nk_hasil->avg * 25 ) / 100; }
                $nt = $this->dbase->dataRow('nilai_k_tambah',array('user_id'=>$valS->user_id));
                if ($nt){ $dtSiswa[$s]->nt = $nt->nt_nilai; }
                $np_tt = $this->dbase->dataRow('nilai_pengetahuan',array('user_id'=>$valS->user_id,'np_type'=>'tt'),'np_nilai');
                if ($np_tt){ $dtSiswa[$s]->n_tt = $np_tt->np_nilai; }
                $np_tl = $this->dbase->dataRow('nilai_pengetahuan',array('user_id'=>$valS->user_id,'np_type'=>'tl'),'np_nilai');
                if ($np_tl){ $dtSiswa[$s]->n_tl = $np_tl->np_nilai; }
                $s++;
            }
            $this->load->library('conv');
            $json['t'] = 1;
            $data['data']   = $dtSiswa;
            $json['html']   = $this->load->view('nilai/rekap_data',$data,TRUE);

        }
	    die(json_encode($json));
    }
    function download(){
	    $tapel = $this->uri->segment(3);
	    $jenis = $this->uri->segment(4);
	    $kk_id = $this->uri->segment(5);
	    $dtKK  = $this->dbase->dataRow('keahlian_kompetensi',array('kk_id'=>$kk_id));
	    if (!$kk_id || !$dtKK){
	        die('Invalid Kompetensi Keahlian');
        } else {
	        $dtSiswa = $this->dbase->sqlResult("
	            SELECT    u.user_nis,u.user_fullname,u.user_sex,km.user_id,u.user_id,k.kel_name
                FROM      tb_kelas_member AS km
                LEFT JOIN tb_kelas AS k ON km.kel_id = k.kel_id
                LEFT JOIN tb_user AS u ON km.user_id = u.user_id
                WHERE     k.kk_id = '".$kk_id."' AND km.km_status = 1 AND k.kel_tingkat = 12 AND k.kel_tapel = '".$tapel."'
                ORDER BY  k.kel_name,u.user_fullname ASC
	        ");
	        if (!$dtSiswa){
	            die('Tidak ada data siswa');
            } else {
                $dtInd = $this->dbase->sqlResult("
                        SELECT    k.kom_id,sk.skom_id,sk.skom_urut,i.ind_id,i.ind_urut
                        FROM      tb_k_indikator AS i
                        LEFT JOIN tb_k_sub_komponen AS sk ON i.skom_id = sk.skom_id
                        LEFT JOIN tb_k_komponen AS k ON sk.kom_id = k.kom_id
                        WHERE     sk.kk_id = '".$kk_id."' AND sk.skom_status = 1 AND i.ind_status = 1
                        ORDER BY  k.kom_id,sk.skom_urut,i.ind_urut ASC
                ");
                if (!$dtInd){
                    die('Tidak ada indikator');
                } else {
                    //ini_set('max_execution_time', 0);
                    $this->load->library(array('PHPExcel','PHPExcel/IOFactory','conv'));
                    $objPHPExcel = new PHPExcel();
                    $sheet = $objPHPExcel->createSheet(0);
                    $sheet->setTitle("input");
                    $objPHPExcel->setActiveSheetIndex(0);
                    $sheet->setCellValue('A1', 'No')
                        ->setCellValue('B1', 'NIS')
                        ->setCellValue('C1', 'Nama Peserta')
                        ->setCellValue('D1', 'L/ P')
                        ->setCellValue('E1', 'Kelas');
                    $col = 6;
                    foreach ($dtInd as $valI){
                        $sheet->setCellValue($this->conv->toStr($col).'1', $valI->kom_id.'.'.$valI->skom_urut.'.'.$valI->ind_urut);
                        $objPHPExcel->getActiveSheet()->getColumnDimension($this->conv->toStr($col))->setWidth(6);
                        $col++;
                    }
                    $row = 2; $nomor = 1;
                    foreach ($dtSiswa as $valS){
                        $sheet->setCellValue('A'.$row, $nomor)
                            ->setCellValue('B'.$row, $valS->user_nis)
                            ->setCellValue('C'.$row, $valS->user_fullname)
                            ->setCellValue('D'.$row, $valS->user_sex)
                            ->setCellValue('E'.$row, $valS->kel_name);
                        $col = 6;
                        foreach ($dtInd as $valI){
                            $chkI = $this->dbase->dataRow('nilai_indikator',array(
                                'user_id'=>$valS->user_id, 'ind_id' => $valI->ind_id, 'nind_uji_tipe' => $jenis
                            ),'nind_nilai');
                            if (!$chkI){
                                $sheet->setCellValue($this->conv->toStr($col).$row, 0);
                            } else {
                                $sheet->setCellValue($this->conv->toStr($col).$row, $chkI->nind_nilai);
                            }
                            $col++;
                        }
                        $row++; $nomor++;
                    }
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);

                    $sheet = $objPHPExcel->setActiveSheetIndex(1);
                    $sheet->setTitle("nilai akhir");
                    $sheet->setCellValue('A1', 'No')
                        ->setCellValue('B1', 'NIS')
                        ->setCellValue('C1', 'Nama Peserta')
                        ->setCellValue('D1', 'L/ P')
                        ->setCellValue('E1', 'Kelas')

                        ->setCellValue('F1', 'Aspek Keterampilan')
                        ->setCellValue('F2', 'Tingkat Pencapaian Kompetensi')
                        ->setCellValue('F3', 'Persiapan 45%')
                        ->setCellValue('G3', 'Proses 30%')
                        ->setCellValue('H3', 'Hasil 25%')
                        ->setCellValue('I3', 'Skor Awal')
                        ->setCellValue('J3', 'Nilai Perolehan')
                        ->setCellValue('K3', 'Nilai Tambahan')
                        ->setCellValue('L3', 'Nilai Akhir K 70%')
                        ->setCellValue('M1', 'Aspek Pengetahuan')
                        ->setCellValue('M2', 'Tingkat Pencapaian Kompetensi')
                        ->setCellValue('M3', 'Tes Tulis')
                        ->setCellValue('N3', 'Tes Lisan')
                        ->setCellValue('O3', 'Nilai Akhir')
                        ->setCellValue('P3', 'Nilai Akhir Pengetahuan 30%')
                        ->setCellValue('Q1', 'Nilai Akhir UKK');
                    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(8);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(5);
                    $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(5);

                    $row = 4; $nomor = 1;
                    foreach ($dtSiswa as $valS){
                        $sheet->setCellValue('A'.$row, $nomor)
                            ->setCellValue('B'.$row, $valS->user_nis)
                            ->setCellValue('C'.$row, $valS->user_fullname)
                            ->setCellValue('D'.$row, $valS->user_sex)
                            ->setCellValue('E'.$row, $valS->kel_name);
                        $k_siap = $k_proses = $k_hasil = $nt = $n_tt = $n_tl = 0;

                        $nk_siap    = $this->dbase->dataRow('nilai_keterampilan',array(
                            'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 1
                        ),'AVG(nk_nilai) AS avg');
                        if ($nk_siap){ $k_siap = ( $nk_siap->avg * 45 ) / 100; }
                        $nk_proses  = $this->dbase->dataRow('nilai_keterampilan',array(
                            'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 2
                        ),'AVG(nk_nilai) AS avg');
                        if ($nk_proses){ $k_proses = ( $nk_proses->avg * 30 ) / 100; }
                        $nk_hasil = $this->dbase->dataRow('nilai_keterampilan',array(
                            'user_id'=>$valS->user_id, 'nk_tipe' => $jenis, 'kom_id' => 3
                        ),'AVG(nk_nilai) AS avg');
                        if ($nk_hasil){ $k_hasil = ( $nk_hasil->avg * 25 ) / 100; }
                        $nt = $this->dbase->dataRow('nilai_k_tambah',array('user_id'=>$valS->user_id));
                        if ($nt){ $nt = $nt->nt_nilai; }
                        $np_tt = $this->dbase->dataRow('nilai_pengetahuan',array('user_id'=>$valS->user_id,'np_type'=>'tt'),'np_nilai');
                        if ($np_tt){ $n_tt = $np_tt->np_nilai; }
                        $np_tl = $this->dbase->dataRow('nilai_pengetahuan',array('user_id'=>$valS->user_id,'np_type'=>'tl'),'np_nilai');
                        if ($np_tl){ $n_tl = $np_tl->np_nilai; }
                        $k_skor_awal = '=F'.$row.'+G'.$row.'+H'.$row;
                        $skor_koma   = '=I'.$row.'-INT(I'.$row.')';
                        $k_perolehan = '=IF(I'.$row.'=0,60,IF(I'.$row.'=1,70,IF(I'.$row.'=2,80,90)))';
                        $k_na        = '(J'.$row.'+K'.$row.')';
                        $k_na_kom    = '=('.$k_na.'*70)/100';
                        $n_pa        = '=N'.$row.'+M'.$row;
                        $na_p        = '=(O'.$row.'*30)/100';
                        $na_final    = '=P'.$row.'+L'.$row;
                        //$np_skor     = ( $n_tt + $n_tl ) / 2;
                        //$np_akhir    = ( $np_skor * 30 ) / 100;
                        //$n_ukk       = $k_na_kom + $np_akhir;
                        //$n_ukk       = round($n_ukk);

                        $sheet->setCellValue('F'.$row, $k_siap)
                            ->setCellValue('G'.$row, $k_proses)
                            ->setCellValue('H'.$row, $k_hasil)
                            ->setCellValue('I'.$row, $k_skor_awal)
                            ->setCellValue('J'.$row, $k_perolehan)
                            ->setCellValue('K'.$row, $nt)
                            ->setCellValue('L'.$row, $k_na_kom)
                            ->setCellValue('M'.$row, $n_tt)
                            ->setCellValue('N'.$row, $n_tl)
                            ->setCellValue('O'.$row, $n_pa)
                            ->setCellValue('P'.$row, $na_p)
                            ->setCellValue('Q'.$row, $na_final)
                            ->setCellValue('Z'.$row, $skor_koma);

                        $row++; $nomor++;
                    }


                    $objPHPExcel->setActiveSheetIndex(1);
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="UKK '.$tapel.' '.$dtKK->kk_sing.' '.$jenis.'.xlsx"');
                    header('Cache-Control: max-age=0');
                    $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save('php://output');
                    exit;
                }
            }
        }
    }
}
