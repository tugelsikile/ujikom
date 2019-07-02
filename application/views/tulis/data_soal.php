<?php
$nomor = 1;
foreach ($data as $val){
    echo '<tr class="row_'.$val->soal_id.'">
            <td align="center"><input type="checkbox" name="soal_id[]" value="'.$val->soal_id.'"></td>
            <td align="center">'.$val->soal_nomor.'</td>
            <td>'.$val->soal_content.'</td>
            <td>';
    echo '<ol type="a" class="soal_'.$val->soal_id.'">';
    if ($val->pg){
        foreach ($val->pg as $valPG){
            $dataPG['data'] = $valPG;
            $this->load->view('tulis/pg_content',$dataPG);
        }
    }
    echo '</ol>';
    echo    '</td>
             <td>
                <a data-toggle="tooltip" title="Rubah soal" href="'.base_url('tulis/edit_soal/'.$val->soal_id).'" onclick="show_modal(this);return false" class="btn-flat btn-block btn btn-xs btn-primary"><i class="fa fa-pencil"></i> Edit Soal</a> 
                <a data-toggle="toolip" title="Hapus soal" data-id="'.$val->soal_id.'" href="javascript:;" onclick="delete_data(this);return false" class="btn-flat btn-block btn btn-xs btn-danger"><i class="fa fa-trash"></i> Hapus Soal</a> 
                <a data-toggle="tooltip" title="Tambah pilihan ganda" href="'.base_url('tulis/add_pg/'.$val->soal_id).'" onclick="show_modal(this);return false" class="btn-flat btn-block btn btn-xs btn-info"><i class="fa fa-plus-circle"></i> Tambah PG</a>
            </td>';
    echo '</tr>';
    $nomor++;
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
    //$('[data-toggle="tooltip"]').tooltip();
    var elem = $('#dataTable tbody pre');
    var text;
    $.each(elem,function (i,v) {
        text = $(this).html();
        text = text.replace(/</,'&lt;');
        text = text.replace(/>/,'&gt;');
        $(this).html(text);
    });
</script>
