<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>ADMIN</title>


    <!-- Custom styles for this template -->
    <link href="<?php echo base_url('assets/cetak.min.css');?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        .page{
            margin:50px; position:relative;
        }
        *{
            font-size: 10pt !important;font-family: 'Times New Roman', Times, serif !important;
        }
        .nopes{
            border:solid 1px #000; width:80%;
        }
        .clearfix{ clear: both }
        .it-grid tr{
            background:none !important;
        }
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    //var_dump($skom);

    foreach ($data as $valS){
        $nopage = 1;
        $hasiltr = 0;
        //var_dump($valS->skom);
        $nopes       = explode("-",$valS->user_nopes);
        $k_skor_awal = (( $valS->k_siap * 45 )/100) + (($valS->k_proses * 30)/100) + (($valS->k_hasil * 25)/100);
        $k_perolehan = $this->conv->conv_skor($k_skor_awal);
        $k_na        = $k_perolehan + $valS->nt;
        $k_na_kom    = round(( $k_na * 70 ) / 100);
        $k_na_komTxt = $k_na_kom;
        $np_skor     = ( $valS->n_tt + $valS->n_tl ) / 2;
        $np_akhir    = ( $np_skor * 30 ) / 100;
        $n_ukk       = $k_na_kom + $np_akhir;
        $n_ukk       = round($n_ukk);
        ?>
        <div class="page">
            <table width="50%" class="it-grid">
                <tr>
                    <td>No. Peserta</td>
                    <?php
                    foreach ($nopes as $valnopes){
                        $ns = str_split($valnopes);
                        foreach ($ns as $valns){
                            echo '<td align="center">'.$valns.'</td>';
                        }
                    }
                    ?>
                </tr>
            </table>
            <div style="height:10px"></div>
            <div style="width:20%;float:left">
                <div style="border:solid 1px #000;padding:10px;font-weight:bold;text-align:center">
                    DOKUMEN NEGARA
                </div>
            </div>
            <div class="text-center" style="float:left;width:60%;text-align:center;font-weight:bold;font-size:16px !important;">
                UJI KOMPETENSI KEAHLIAN<br>
                TAHUN PELAJARAN <?php echo $tapel.' / '.($tapel + 1); ?><br><br>
                LEMBAR PENILAIAN<br>
                UJIAN PRAKTIK KEJURUAN
            </div>
            <div style="width:20%;float:right">
                <div style="border:solid 1px #000;width:100px;float:right;text-align:center;padding:10px;">
                    <strong>PAKET<br><?php echo $kk->kk_paket_nomor;?></strong>
                </div>
            </div>
            <table width="100%">
                <tr>
                    <td width="100px"></td>
                    <td width="150px">Satuan Pendidikan</td>
                    <td width="10px">:</td>
                    <td>SMK Muhammadiyah Kandanghaur</td>
                    <td width="100px"></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Kompetensi Keahlian</td>
                    <td>:</td>
                    <td><?php echo $kk->kk_name;?></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>Kode</td>
                    <td>:</td>
                    <td><?php echo $kk->kk_kode;?></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td valign="top">Judul Tugas</td>
                    <td valign="top">:</td>
                    <td valign="top"><?php echo $kk->kk_paket_name;?></td>
                    <td></td>
                </tr>
            </table>
            <div style="height:5px;border-top:solid 3px #000;border-bottom:solid 1px #000;margin-bottom:20px"></div>
            <span style="display:inline-block;width:150px;margin-bottom:10px">Nomor Peserta</span> : <strong><?php echo $valS->user_nopes;?></strong><br>
            <span style="display:inline-block;width:150px">Nama Peserta</span> : <strong><?php echo $valS->user_fullname;?></strong>
            <div style="height:10px" class="clearfix"></div>
            <strong style="margin-bottom:10px;display:inline-block"><u>FORM PENILAIAN ASPEK KETERAMPILAN</u></strong>
            <table width="100%" class="it-grid">
                <thead>
                    <tr>
                        <th rowspan="4" width="50px">No</th>
                        <th rowspan="4">Komponen / Sub Komponen</th>
                        <th colspan="4">Kompeten</th>
                        <th rowspan="4" width="100px">Catatan</th>
                    </tr>
                    <tr>
                        <th width="50px" rowspan="2">Belum</th>
                        <th colspan="3">Ya</th>
                    </tr>
                    <tr>
                        <th width="50px">Cukup</th>
                        <th width="50px">Baik</th>
                        <th width="50px">Sangat Baik</th>
                    </tr>
                    <tr>
                        <th>0</th><th>1</th><th>2</th><th>3</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td align="center"><strong>I</strong></td>
                        <td colspan="6"><strong>Persiapan</strong></td>
                    </tr>
                    <?php
                    $persiapan = $valS->skom[0];
                    $persiapan = array_chunk($persiapan,10,true);
                    $nokom = 1;
                    foreach ($persiapan as $valPersiapan){
                        foreach ($valPersiapan as $valKom){
                            $belum = $cukup = $baik = $sangbaik = '';
                            if ($valKom->nilai == 3){
                                $sangbaik = '&#x2713;';
                            } elseif ($valKom->nilai == 2){
                                $baik = '&#x2713;';
                            } elseif ($valKom->nilai == 1){
                                $cukup = '&#x2713;';
                            } else {
                                $belum = '&#x2713;';
                            }
                            echo '<tr>
                                <td align="center">1.'.$nokom.'</td>
                                <td>'.$valKom->skom_content.'</td>
                                <td align="center">'.$belum.'</td>
                                <td align="center">'.$cukup.'</td>
                                <td align="center">'.$baik.'</td>
                                <td align="center">'.$sangbaik.'</td>
                                <td></td>
                              </tr>';
                            $nokom++;
                        }
                    }
                    $size = 26 - $nokom ;
                    $proses = $valS->skom[1];
                    $proses = array_chunk($proses,$size,true);
                    ?>
                    <tr>
                        <td align="center"><strong>II</strong></td>
                        <td colspan="6"><strong>Pelaksanaan</strong></td>
                    </tr>
                    <?php
                    $proses1 = $proses[0];
                    $nokom = 1;
                    foreach ($proses1 as $valKom){
                        $belum = $cukup = $baik = $sangbaik = '';
                        if ($valKom->nilai == 3){
                            $sangbaik = '&#x2713;';
                        } elseif ($valKom->nilai == 2){
                            $baik = '&#x2713;';
                        } elseif ($valKom->nilai == 1){
                            $cukup = '&#x2713;';
                        } else {
                            $belum = '&#x2713;';
                        }
                        echo '<tr>
                                <td align="center">2.'.$nokom.'</td>
                                <td>'.$valKom->skom_content.'</td>
                                <td align="center">'.$belum.'</td>
                                <td align="center">'.$cukup.'</td>
                                <td align="center">'.$baik.'</td>
                                <td align="center">'.$sangbaik.'</td>
                                <td></td>
                              </tr>';
                        $nokom++;
                    }
                    ?>
                </tbody>
            </table>
            <div style="left:10px;position:absolute;bottom:10px;right:10px;font-size:10px !important;">
                Lembar Penilaian UKK - <?php echo $valS->user_nopes.' - '.$valS->user_fullname;?>
                <div style="float:right;padding:5px;background:#CCC;font-weight:bold;font-size:10px !important;width:50px;text-align:center;border-bottom:solid 2px #000;border-right:solid 2px #000">
                    <?php echo $nopage; ?>
                </div>
            </div>
        </div>
        <?php
        $nopage++;
        if (count($proses) > 1){
            if ($proses[1]){
                ?>
                <div class="page">
                    <table width="50%" class="it-grid">
                        <tr>
                            <td>No. Peserta</td>
                            <?php
                            foreach ($nopes as $valnopes){
                                $ns = str_split($valnopes);
                                foreach ($ns as $valns){
                                    echo '<td align="center">'.$valns.'</td>';
                                }
                            }
                            ?>
                        </tr>
                    </table>
                    <table width="100%" class="it-grid" style="margin-top:10px">
                        <thead>
                        <tr>
                            <th rowspan="4" width="50px">No</th>
                            <th rowspan="4">Komponen / Sub Komponen</th>
                            <th colspan="4">Kompeten</th>
                            <th rowspan="4" width="100px">Catatan</th>
                        </tr>
                        <tr>
                            <th width="50px" rowspan="2">Belum</th>
                            <th colspan="3">Ya</th>
                        </tr>
                        <tr>
                            <th width="50px">Cukup</th>
                            <th width="50px">Baik</th>
                            <th width="50px">Sangat Baik</th>
                        </tr>
                        <tr>
                            <th>0</th><th>1</th><th>2</th><th>3</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $nokom2 = 0;
                        $proses2 = $proses[1];
                        if ($proses2){
                            foreach ($proses2 as $valKom){
                                $belum = $cukup = $baik = $sangbaik = '';
                                if ($valKom->nilai == 3){
                                    $sangbaik = '&#x2713;';
                                } elseif ($valKom->nilai == 2){
                                    $baik = '&#x2713;';
                                } elseif ($valKom->nilai == 1){
                                    $cukup = '&#x2713;';
                                } else {
                                    $belum = '&#x2713;';
                                }
                                echo '<tr>
                                <td align="center">2.'.$nokom.'</td>
                                <td>'.$valKom->skom_content.'</td>
                                <td align="center">'.$belum.'</td>
                                <td align="center">'.$cukup.'</td>
                                <td align="center">'.$baik.'</td>
                                <td align="center">'.$sangbaik.'</td>
                                <td></td>
                              </tr>';
                                $nokom++; $nokom2++;
                            }
                        }
                        if (count($proses) > 2){
                            $proses3 = $proses[2];
                            if ($proses3){
                                foreach ($proses3 as $valKom){
                                    $belum = $cukup = $baik = $sangbaik = '';
                                    if ($valKom->nilai == 3){
                                        $sangbaik = '&#x2713;';
                                    } elseif ($valKom->nilai == 2){
                                        $baik = '&#x2713;';
                                    } elseif ($valKom->nilai == 1){
                                        $cukup = '&#x2713;';
                                    } else {
                                        $belum = '&#x2713;';
                                    }
                                    echo '<tr>
                                <td align="center">2.'.$nokom.'</td>
                                <td>'.$valKom->skom_content.'</td>
                                <td align="center">'.$belum.'</td>
                                <td align="center">'.$cukup.'</td>
                                <td align="center">'.$baik.'</td>
                                <td align="center">'.$sangbaik.'</td>
                                <td></td>
                              </tr>';
                                    $nokom++; $nokom2++;
                                }
                            }
                        }
                        if (count($proses) > 3){
                            $proses4 = $proses[3];
                            if ($proses4){
                                foreach ($proses4 as $valKom){
                                    $belum = $cukup = $baik = $sangbaik = '';
                                    if ($valKom->nilai == 3){
                                        $sangbaik = '&#x2713;';
                                    } elseif ($valKom->nilai == 2){
                                        $baik = '&#x2713;';
                                    } elseif ($valKom->nilai == 1){
                                        $cukup = '&#x2713;';
                                    } else {
                                        $belum = '&#x2713;';
                                    }
                                    echo '<tr>
                                <td align="center">2.'.$nokom.'</td>
                                <td>'.$valKom->skom_content.'</td>
                                <td align="center">'.$belum.'</td>
                                <td align="center">'.$cukup.'</td>
                                <td align="center">'.$baik.'</td>
                                <td align="center">'.$sangbaik.'</td>
                                <td></td>
                              </tr>';
                                    $nokom++; $nokom2++;
                                }
                            }
                        }
                        if ($nokom2 < 10){
                            $hasiltr++;
                            echo '<tr>
                                    <td align="center"><strong>III</strong></td>
                                    <td colspan="6"><strong>Hasil</strong></td>
                                </tr>';
                            $hasil  = $valS->skom[2];
                            $nokom = 1;
                            foreach ($hasil as $valKom){
                                $belum = $cukup = $baik = $sangbaik = '';
                                if ($valKom->nilai == 3){
                                    $sangbaik = '&#x2713;';
                                } elseif ($valKom->nilai == 2){
                                    $baik = '&#x2713;';
                                } elseif ($valKom->nilai == 1){
                                    $cukup = '&#x2713;';
                                } else {
                                    $belum = '&#x2713;';
                                }
                                echo '<tr>
                            <td align="center">3.'.$nokom.'</td>
                            <td>'.$valKom->skom_content.'</td>
                            <td align="center">'.$belum.'</td>
                            <td align="center">'.$cukup.'</td>
                            <td align="center">'.$baik.'</td>
                            <td align="center">'.$sangbaik.'</td>
                            <td></td>
                          </tr>';
                                $nokom++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <div style="left:10px;position:absolute;bottom:10px;right:10px;font-size:10px !important;">
                        Lembar Penilaian UKK - <?php echo $valS->user_nopes.' - '.$valS->user_fullname;?>
                        <div style="float:right;padding:5px;background:#CCC;font-weight:bold;font-size:10px !important;width:50px;text-align:center;border-bottom:solid 2px #000;border-right:solid 2px #000">
                            <?php echo $nopage; ?>
                        </div>
                    </div>
                </div>
                <?php
                $nopage++;
            }
        }
        if ($hasiltr == 0) {
            ?>
            <div class="page">
                <table width="50%" class="it-grid">
                    <tr>
                        <td>No. Peserta</td>
                        <?php
                        foreach ($nopes as $valnopes) {
                            $ns = str_split($valnopes);
                            foreach ($ns as $valns) {
                                echo '<td align="center">' . $valns . '</td>';
                            }
                        }
                        ?>
                    </tr>
                </table>
                <table width="100%" class="it-grid" style="margin-top:10px">
                    <thead>
                    <tr>
                        <th rowspan="4" width="50px">No</th>
                        <th rowspan="4">Komponen / Sub Komponen</th>
                        <th colspan="4">Kompeten</th>
                        <th rowspan="4" width="100px">Catatan</th>
                    </tr>
                    <tr>
                        <th width="50px" rowspan="2">Belum</th>
                        <th colspan="3">Ya</th>
                    </tr>
                    <tr>
                        <th width="50px">Cukup</th>
                        <th width="50px">Baik</th>
                        <th width="50px">Sangat Baik</th>
                    </tr>
                    <tr>
                        <th>0</th>
                        <th>1</th>
                        <th>2</th>
                        <th>3</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td align="center"><strong>III</strong></td>
                        <td colspan="6"><strong>Hasil</strong></td>
                    </tr>
                    <?php
                    $hasil = $valS->skom[2];
                    $nokom = 1;
                    foreach ($hasil as $valKom) {
                        $belum = $cukup = $baik = $sangbaik = '';
                        if ($valKom->nilai == 3) {
                            $sangbaik = '&#x2713;';
                        } elseif ($valKom->nilai == 2) {
                            $baik = '&#x2713;';
                        } elseif ($valKom->nilai == 1) {
                            $cukup = '&#x2713;';
                        } else {
                            $belum = '&#x2713;';
                        }
                        echo '<tr>
                                <td align="center">3.' . $nokom . '</td>
                                <td>' . $valKom->skom_content . '</td>
                                <td align="center">' . $belum . '</td>
                                <td align="center">' . $cukup . '</td>
                                <td align="center">' . $baik . '</td>
                                <td align="center">' . $sangbaik . '</td>
                                <td></td>
                              </tr>';
                        $nokom++;
                    }
                    ?>
                    </tbody>
                </table>
                <div style="left:10px;position:absolute;bottom:10px;right:10px;font-size:10px !important;">
                    Lembar Penilaian UKK - <?php echo $valS->user_nopes . ' - ' . $valS->user_fullname; ?>
                    <div style="float:right;padding:5px;background:#CCC;font-weight:bold;font-size:10px !important;width:50px;text-align:center;border-bottom:solid 2px #000;border-right:solid 2px #000">
                        <?php echo $nopage; ?>
                    </div>
                </div>
            </div>
            <?php
        }
        $nopage++;
        ?>

        <div class="page">
            <table width="50%" class="it-grid">
                <tr>
                    <td>No. Peserta</td>
                    <?php
                    foreach ($nopes as $valnopes){
                        $ns = str_split($valnopes);
                        foreach ($ns as $valns){
                            echo '<td align="center">'.$valns.'</td>';
                        }
                    }
                    ?>
                </tr>
            </table>
            <strong style="margin:10px auto;display:inline-block"><u>REKAPITULASI PENILAIAN ASPEK KETERAMPILAN</u></strong>
            <table width="100%" class="it-grid">
                <tr>
                    <th rowspan="3"></th>
                    <th colspan="4">Tingkat Pencapaian Kompetensi</th>
                    <th rowspan="3">Nilai Perolehan</th>
                    <th rowspan="3">Nilai Tambahan</th>
                    <th rowspan="3">Nilai Akhir Aspek Keterampilan</th>
                </tr>
                <tr>
                    <th colspan="3">Keterampilan</th>
                    <th rowspan="2" width="80px">Skor Awal</th>
                </tr>
                <tr>
                    <th width="80px">Persiapan</th>
                    <th width="80px">Pelaksanaan</th>
                    <th width="80px">Hasil</th>
                </tr>
                <tr>
                    <td>Nilai Rata-rata</td>
                    <td align="center"><?php echo number_format($valS->k_siap,2,",","."); ?></td>
                    <td align="center"><?php echo number_format($valS->k_proses,2,",","."); ?></td>
                    <td align="center"><?php echo number_format($valS->k_hasil,2,",","."); ?></td>
                    <td rowspan="3" width="80px" align="center"><?php echo round($k_skor_awal);?></td>
                    <td rowspan="3" width="80px" align="center"><?php echo $k_perolehan;?></td>
                    <td rowspan="3" width="80px" align="center"><?php echo $valS->nt; ?></td>
                    <td rowspan="3" width="80px" align="center"><?php echo $k_na; ?></td>
                </tr>
                <tr>
                    <td>Bobot</td>
                    <td align="center">45%</td><td align="center">30%</td><td align="center">25%</td>
                </tr>
                <tr>
                    <td>Nilai Komponen</td>
                    <td align="center"><?php echo number_format(($valS->k_siap * 45)/100,2,",","."); ?></td>
                    <td align="center"><?php echo number_format(($valS->k_proses * 30)/100,2,",","."); ?></td>
                    <td align="center"><?php echo number_format(($valS->k_hasil * 25)/100,2,",","."); ?></td>
                </tr>
            </table>
            <strong style="margin:10px auto;display:inline-block"><u>NILAI AKHIR</u></strong>
            <table width="70%" class="it-grid">
                <tr>
                    <th></th>
                    <th>Aspek Pengetahuan</th>
                    <th>Aspek Keterampilan</th>
                    <th>Nilai Akhir</th>
                </tr>
                <tr>
                    <td>Nilai Perolehan</td>
                    <td width="100px" align="center"><?php echo $np_skor; ?></td>
                    <td width="100px" align="center"><?php echo $k_na; ?></td>
                    <td rowspan="3" width="100px" align="center">
                        <?php echo round((($np_skor * 30)/100) + (($k_na * 70)/100)); ?>
                    </td>
                </tr>
                <tr>
                    <td>Bobot</td>
                    <td align="center">30%</td>
                    <td align="center">70%</td>
                </tr>
                <tr>
                    <td>Nilai Komponen</td>
                    <td align="center"><?php echo ($np_skor*30)/100;?></td>
                    <td align="center"><?php echo ($k_na*70)/100;?></td>
                </tr>
            </table>
            <?php
            if (count($valS->skom)>3) {
                if (count($valS->skom[3]) > 0){
                    ?>
                    <strong style="margin:10px auto;display:inline-block"><u>Form Penilaian Aspek Sikap (Sikap
                            Kerja)</u></strong>
                    <table width="100%" class="it-grid">
                        <thead>
                        <tr>
                            <th rowspan="2" width="200px">Indikator Pencapaian Kompetensi</th>
                            <th colspan="4">Tingkat Pencapaian Kompetensi</th>
                        </tr>
                        <tr>
                            <th width="70px">Kurang</th>
                            <th width="70px">Cukup</th>
                            <th width="70px">Baik</th>
                            <th width="70px">Sangat Baik</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php

                        foreach ($valS->skom[3] as $valKom) {
                            $belum = $cukup = $baik = $sangbaik = '';
                            if ($valKom->nilai == 3) {
                                $sangbaik = '&#x2713;';
                            } elseif ($valKom->nilai == 2) {
                                $baik = '&#x2713;';
                            } elseif ($valKom->nilai == 1) {
                                $cukup = '&#x2713;';
                            } else {
                                $belum = '&#x2713;';
                            }
                            echo '<tr>
                                <td>' . $valKom->skom_content . '</td>
                                <td align="center">' . $belum . '</td>
                                <td align="center">' . $cukup . '</td>
                                <td align="center">' . $baik . '</td>
                                <td align="center">' . $sangbaik . '</td>
                              </tr>';
                        }


                        ?>
                        </tbody>
                    </table>
                    <?php
                }

            }
                ?>
            <div style="width:30%;float:right;margin-top:20px;text-align:center">
                Kandanghaur, <?php echo $this->conv->tglIndo(date("Y-m-d"));?><br>
                Penilai 1 / Penilai 2
                <div style="height:80px;border-bottom:solid 1px #000;"></div>

            </div>
            <div style="left:10px;position:absolute;bottom:10px;right:10px;font-size:10px !important;">
                Lembar Penilaian UKK - <?php echo $valS->user_nopes.' - '.$valS->user_fullname;?>
                <div style="float:right;padding:5px;background:#CCC;font-weight:bold;font-size:10px !important;width:50px;text-align:center;border-bottom:solid 2px #000;border-right:solid 2px #000">
                    <?php echo $nopage; ?>
                </div>
            </div>
        </div>
        <?php
        $nopage++;
    }
}
?>

</body>
</html>