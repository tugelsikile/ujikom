<?php
foreach ($data as $val){
    echo '<tr class="row_'.$val->ind_id.'">
            <td align="center">'.$val->kom_id.'.'.$val->skom_urut.'.'.$val->ind_urut.'</td>
            <td>'.$val->ind_content.'</td>
            <td>
                <a href="'.base_url('komponen/edit_indikator/'.$val->ind_id).'" class="btn btn-info btn-sm btn-flat btn-block" onclick="show_modal(this);return false"><i class="fa fa-pencil"></i> Edit</a>
                <a href="javascript:;" onclick="delete_data(this);return false" data-id="'.$val->ind_id.'" class="btn btn-danger btn-sm btn-flat btn-block"><i class="fa fa-trash"></i> Hapus</a>
            </td>
          </tr>';
}