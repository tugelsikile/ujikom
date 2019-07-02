<?php
foreach ($data as $val){
    echo '<tr class="row_'.$val->klm_id.'">
            <td align="center"><input type="checkbox" name="klm_id[]" value="'.$val->klm_id.'"></td>
            <td align="center">'.$val->user_nis.'</td>
            <td align="center">'.$val->user_nis.'</td>
            <td>'.$val->user_fullname.'</td>
            <td align="center">'.$val->user_sex.'</td>
            <td align="center">'.$val->kel_name.'</td>
            <td align="center">'.$val->klp_name.'</td>
            <td>
                <a href="javascript:;" onclick="delete_data(this);return false" data-id="'.$val->klm_id.'" class="btn-flat btn-block btn btn-danger btn-sm"><i class="fa fa-trash"></i> Hapus</a>
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
