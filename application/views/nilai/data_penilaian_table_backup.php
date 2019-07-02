<?php
if ($tipe == 'praktik'){
    //var_dump($data);
    $sudah = $belum = 0;
    foreach ($data as $valKom){
        echo '<tr><td colspan="3"><strong>'.$this->conv->romawi($valKom->kom_id).'. '.$valKom->kom_name.'</strong></td></tr>';
        foreach ($valKom->sub_komponen as $valSkom){
            echo '<tr>
                    <td align="left">'.$this->conv->romawi($valKom->kom_id).'.'.$valSkom->skom_urut.'</td>
                    <td colspan="2"><strong>'.$valSkom->skom_content.'</strong></td>
                 </tr>';
            foreach ($valSkom->indikator as $valInd){
                $btn_ok = '<a href="javascript:;" data-no="ok" data-indid="'.$valInd->ind_id.'" data-sisid="'.$siswa->user_id.'" class="ok_'.$valInd->ind_id.' btn btn-default btn-flat btn-block" onclick="set_jawab(this);return false"><i class="fa fa-check"></i></a>';
                $btn_no = '<a href="javascript:;" data-no="no" data-indid="'.$valInd->ind_id.'" data-sisid="'.$siswa->user_id.'" class="no_'.$valInd->ind_id.' btn btn-default btn-flat btn-block" onclick="set_jawab(this);return false"><i class="fa fa-close"></i></a>';
                if (isset($valInd->jawab)){
                    if ($valInd->jawab == 1){
                        $btn_ok = '<a href="javascript:;" data-no="ok" data-indid="'.$valInd->ind_id.'" data-sisid="'.$siswa->user_id.'" class="ok_'.$valInd->ind_id.' btn btn-success btn-flat btn-block" onclick="set_jawab(this);return false"><i class="fa fa-check"></i></a>';
                    } else {
                        $btn_no = '<a href="javascript:;" data-no="no" data-indid="'.$valInd->ind_id.'" data-sisid="'.$siswa->user_id.'" class="no_'.$valInd->ind_id.' btn btn-danger btn-flat btn-block" onclick="set_jawab(this);return false"><i class="fa fa-close"></i></a>';
                    }
                    $sudah++;
                }
                echo '<tr>
                        <td align="left">'.$this->conv->romawi($valKom->kom_id).'.'.$valSkom->skom_urut.'.'.$valInd->ind_urut.'</td>
                        <td>'.$valInd->ind_content.'</td>
                        <td>'.$btn_ok.$btn_no.'</td>
                      </tr>';
                $belum++;
            }
        }
    }
}
?>
<script>
    <?php
        if ($sudah > 0 && $sudah < $belum){
            echo "$('.nilaisudah').removeClass('text-danger').addClass('text-info');";
        } elseif ($sudah == $belum){
            echo "$('.nilaisudah').removeClass('text-danger').addClass('text-success');";
        }
    ?>
$('.nilaisudah').html('<?php echo $sudah;?>');
$('.nilaiBelum').html('<?php echo $belum;?>');
</script>
