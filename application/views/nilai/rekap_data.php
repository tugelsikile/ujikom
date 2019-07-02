<?php
foreach ($data as $valS){
    $k_skor_awal = $valS->k_siap + $valS->k_proses + $valS->k_hasil;
    $k_skor_awal = $k_skor_awal;
    $k_perolehan = $this->conv->conv_skor($k_skor_awal);
    $k_na        = $k_perolehan + $valS->nt;
    $k_na_kom    = round(( $k_na * 70 ) / 100);
    $k_na_komTxt = $k_na_kom;
    $np_skor     = ( $valS->n_tt + $valS->n_tl ) / 2;
    $np_akhir    = ( $np_skor * 30 ) / 100;
    $n_ukk       = $k_na_kom + $np_akhir;
    $n_ukk       = round($n_ukk);
    echo '<tr>
            <td><input type="checkbox" name="user_id[]" value="'.$valS->user_id.'"></td>
            <td align="center">'.$valS->user_nis.'</td>
            <td>'.$valS->user_fullname.'</td>
            <td align="center">'.$valS->user_sex.'</td>
            <td align="center">'.number_format($valS->k_siap,2,",",".").'</td>
            <td align="center">'.number_format($valS->k_proses,2,",",".").'</td>
            <td align="center">'.number_format($valS->k_hasil,2,",",".").'</td>
            <td align="center">'.number_format($k_skor_awal,2,",",".").'</td>
            <td align="center">'.$k_perolehan.'</td>
            <td align="center" class="tambah_'.$valS->user_id.'"><a href="'.base_url('nilai/nilai_tambah/'.$valS->user_id.'/'.$valS->jenis).'" class="btn btn-flat btn-block btn-xs btn-info" onclick="show_modal(this);return false">'.$valS->nt.'</a></td>
            <td align="center">'.$k_na.'</td>
            <td align="center">'.$k_na_komTxt.'</td>
            <td align="center">'.$valS->n_tt.'</td>
            <td align="center" class="lisan_'.$valS->user_id.'"><a href="'.base_url('nilai/nilai_lisan/'.$valS->user_id.'/'.$valS->jenis).'" class="btn btn-flat btn-block btn-xs btn-info" onclick="show_modal(this);return false">'.$valS->n_tl.'</a></td>
            <td align="center">'.$np_skor.'</td>
            <td align="center">'.$np_akhir.'</td>
            <td align="center">'.$n_ukk.'</td>
          </tr>';
}