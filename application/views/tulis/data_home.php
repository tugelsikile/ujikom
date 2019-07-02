<?php
foreach ($data as $val){
    if ($val->dist == 0){
        $dist   = 0;
    } else {
        $dist   = '<a data-toggle="tooltip" title="Detail distribusi soal" href="'.base_url('tulis/distribusi_detail/'.$val->quiz_id).'" onclick="show_modal(this);return false" class="btn btn-info btn-flat btn-sm btn-block">'.$val->dist.'</a>';
    }
    if ($val->quiz_activate == 1){
        $btn = '<a data-toggle="tooltip" title="Non aktifkan Tes" href="javascript:;" onclick="set_active(this);return false" data-id="'.$val->quiz_id.'" data-value="0" class="act_'.$val->quiz_id.' btn btn-success btn-sm btn-block btn-flat"><i class="fa fa-check-circle"></i> Tes Aktif</a>';
    } else {
        $btn = '<a data-toggle="tooltip" title="aktifkan Tes" href="javascript:;" onclick="set_active(this);return false" data-id="'.$val->quiz_id.'" data-value="1" class="act_'.$val->quiz_id.' btn btn-warning btn-sm btn-block btn-flat"><i class="fa fa-close"></i> Tes Tidak Aktif</a>';
    }
    echo '<tr class="row_'.$val->quiz_id.'">
            <td align="center"><input type="checkbox" name="quiz_id[]" value="'.$val->quiz_id.'"></td>
            <td>'.$this->conv->hariIndo(date('N',strtotime($val->quiz_start))).', '.$this->conv->tglIndo(date('Y-m-d',strtotime($val->quiz_start))).'</td>
            <td>'.$val->kk_name.'</td>
            <td align="center">'.$val->quiz_timer.' menit</td>
            <td align="center">'.$val->quiz_jml_soal.'</td>
            <td align="center">'.$val->bank.'</td>
            <td align="center">'.$dist.'</td>
            <td align="center">'.$val->siswa_cnt.'</td>
            <td>
                <a data-toggle="tooltip" title="Edit Tes" href="'.base_url('tulis/edit_data/'.$val->quiz_id).'" onclick="show_modal(this);return false" class="btn-flat btn-block btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Edit Data</a>
                <a data-toggle="tooltip" title="Hapus Tes" href="javascript:;" data-id="'.$val->quiz_id.'" onclick="distribusi(this);return false" class="btn-flat btn-block btn btn-success btn-sm"><i class="fa fa-sign-in"></i> Distribusi Soal</a>
                '.$btn.'
            </td>
          </tr>';
}
?>
<script>
    $('#dataTable tbody input:checkbox').click(function () {
        var dtlen = $('#dataTable tbody input:checkbox:checked').length;
        if (dtlen > 0){
            $('.btn-delete').removeClass('disabled');
        } else {
            $('.btn-delete').addClass('disabled');
        }
    });
    $('[data-toggle="tooltip"]').tooltip();
</script>
