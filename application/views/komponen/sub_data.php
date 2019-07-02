<?php
foreach ($data as $val){
    if ($val->skom_show == 1){
        $btn = '<a href="javascript:;" data-val="0" onclick="show_hide(this);return false" data-id="'.$val->skom_id.'" class="btn-flat btn-block btn btn-success btn-xs" title="Cara penilaian"><i class="fa-eye fa"></i> Manual</a>';
    } else {
        $btn = '<a href="javascript:;" data-val="1" onclick="show_hide(this);return false" data-id="'.$val->skom_id.'" class="btn-flat btn-block btn btn-warning btn-xs" title="Cara penilaian"><i class="fa-eye fa"></i> Auto</a>';
    }
    echo '<tr class="row_'.$val->skom_id.'">
            <td align="center">'.$val->kom_id.'.'.$val->skom_urut.'</td>
            <td>'.$val->skom_content.'</td>
            <td>Paket '.$val->skom_paket.'</td>
            <td>
                <a href="'.base_url('komponen/indikator/'.$val->skom_id).'" data-target="komponen" onclick="load_page(this);return false" class="btn btn-primary btn-flat btn-block">Lihat</a>
            </td>
            <td align="center">'.$val->skom_a.'</td>
            <td align="center">'.$val->skom_b.'</td>
            <td align="center">'.$val->skom_c.'</td>
            <td align="center">'.$val->skom_d.'</td>
            <td>
                <a href="'.base_url('komponen/edit_sub/'.$val->skom_id).'" class="btn btn-info btn-xs btn-flat btn-block" onclick="show_modal(this);return false"><i class="fa fa-pencil"></i> Edit</a>
                '.$btn.'
                <a href="javascript:;" onclick="delete_data(this);return false" data-id="'.$val->skom_id.'" class="btn-flat btn-block btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a>
            </td>
          </tr>';
}