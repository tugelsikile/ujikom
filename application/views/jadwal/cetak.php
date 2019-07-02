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
            margin:50px;
        }
        *{
            font-size: 10pt !important;
        }
    </style>

</head>
<body>
<?php
if (!$data){
    echo 'tidak ada data';
} else {
    //var_dump($data);
    foreach ($data->jadwal as $valJ){
        switch ($valJ->jw_type){
            case 'aya'  : $name = 'PENGAYAAN UJI KOMPETENSI KEAHLIAN (UKK)'; break;
            case 'pra'  : $name = 'PRA UJI KOMPETENSI KEAHLIAN (UKK)'; break;
            case 'ukk'  : $name = 'UJI KOMPETENSI KEAHLIAN (UKK)'; break;
        }
        ?>
        <div class="page">
            <table width="100%">
                <tr>
                    <td width="80px">
                        <img src="<?php echo base_url('assets/tutwuri.jpg');?>" width="100%">
                    </td>
                    <td align="center" valign="middle" style="font-weight: bold; font-size: 12pt !important;">
                        DAFTAR HADIR PESERTA<br>
                        <?php echo $name;?><br>
                        TAHUN PELAJARAN <?php echo $data->klp_tapel.'/'.($data->klp_tapel + 1); ?>
                    </td>
                    <td width="80px">
                        <img src="<?php echo base_url('assets/bsnp.jpg');?>" width="100%">
                    </td>
                </tr>
            </table>
            <table width="90%" style="margin-top:10px" class="detail">
                <tr>
                    <td width="170px">KOTA/KABUPATEN</td>
                    <td width="10px">:</td>
                    <td><span style="width: 100%">KABUPATEN INDRAMAYU</span></td>
                    <td width="50px">KODE</td>
                    <td width="10px">:</td>
                    <td width="90px"><span style="width: 100%">18</span></td>
                </tr>
                <tr>
                    <td>SEKOLAH</td>
                    <td>:</td>
                    <td><span style="width: 100%">SMK MUHAMMADIYAH KANDANGHAUR</span></td>
                    <td>KODE</td>
                    <td>:</td>
                    <td><span style="width: 100%">0134</span></td>
                </tr>
                <tr>
                    <td>KOMPETENSI KEAHLIAN</td>
                    <td>:</td>
                    <td><span style="width: 100%"><?php echo strtoupper($data->kk->kk_name);?></span></td>
                    <td>KODE</td>
                    <td>:</td>
                    <td><span style="width: 100%"><?php echo $data->kk->kk_kode;?></span></td>
                </tr>
                <tr>
                    <td>HARI/TANGGAL</td>
                    <td>:</td>
                    <td>
                        <span style="width: 100%">
                            <?php
                            echo strtoupper($this->conv->hariIndo(date('N',strtotime($valJ->jw_date_start))).', '.
                                 $this->conv->tglIndo(date('Y-m-d',strtotime($valJ->jw_date_start))));
                            ?>
                        </span>
                    </td>
                    <td>PUKUL</td>
                    <td>:</td>
                    <td>
                        <span style="width: 100%">
                            <?php echo date('H:i',strtotime($valJ->jw_date_start)); ?>
                        </span>
                    </td>
                </tr>
            </table>
            <table width="100%" class="it-grid" style="margin-top: 10px">
                <thead>
                <tr>
                    <th width="40px">NO</th>
                    <th width="150px">NOMOR PESERTA</th>
                    <th width="">NAMA PESERTA</th>
                    <th width="20px">L/ P</th>
                    <th width="100px" colspan="2">TANDA TANGAN</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if ($valJ->peserta){
                    $nomor = 1;
                    foreach ($valJ->peserta as $valPes){
                        $mods = ($nomor % 2);
                        echo '<tr>
                                <td align="center">'.$nomor.'</td>
                                <td align="center">'.$valPes->user_nopes.'</td>
                                <td>'.$valPes->user_fullname.'</td>
                                <td align="center">'.$valPes->user_sex.'</td>';
                        if ($mods == 1){
                            echo '<td valign="top" rowspan="2" style="font-size:8pt !important;">'.$nomor.'</td>';
                            echo '<td valign="bottom" rowspan="2" style="font-size:8pt !important;">'.($nomor+1).'</td>';
                        }
                        echo '</tr>';
                        $nomor++;
                    }
                    if ($nomor - 1 < 26){
                        for ($i = $nomor; $i <= 30; $i++){
                            $mods = ($nomor % 2);
                            echo '<tr>
                                <td align="center">'.$nomor.'</td>
                                <td align="center"></td>
                                <td></td><td></td>';
                            if ($mods == 1){
                                echo '<td valign="top" rowspan="2" style="font-size:8pt !important;">'.$nomor.'</td>';
                                echo '<td valign="bottom" rowspan="2" style="font-size:8pt !important;">'.($nomor+1).'</td>';
                            }
                            echo '</tr>';
                            $nomor++;
                        }
                    }
                }
                ?>
                </tbody>
            </table>
            <div style="margin-top: 20px;width: 200px;float: right;text-align: center">
                Kandanghaur, <?php echo $this->conv->tglIndo(date('Y-m-d',strtotime($valJ->jw_date_start)));?><br>
                Penguji 1 / Penguji 2
                <div style="height:70px"></div>
                (...............................................)
            </div>
        </div>
        <?php
    }
}
?>

</body>
</html>