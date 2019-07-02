<?php
foreach ($data as $val){
    echo '<tr class="row_'.$val->jw_id.'">
            <td align="center"><input type="checkbox" name="jw_id[]" value="'.$val->jw_id.'"></td>
            <td>'.$this->conv->hariIndo(date('N',strtotime($val->jw_date_start))).', '.$this->conv->tglIndo(date('Y-m-d',strtotime($val->jw_date_start))).'</td>
            <td align="center">'.date('H:i',strtotime($val->jw_date_start)).'</td>
            <td>'.$val->klp_name.'</td>
            <td align="center">'.$val->cnt.'</td>
            <td>
                <a href="javascript:;" title="Edit data" onclick="show_modal({\'href\':base_url+\'jadwal/edit_data/'.$val->jw_id.'\',\'title\':$(this).attr(\'title\')});return false" data-id="'.$val->jw_id.'" class="btn-flat btn-block btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Edit</a>
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
    })
</script>
