<?php

$btn_edit   = '<a data-toggle="tooltip" href="'.base_url('tulis/edit_pg/'.$data->pg_id).'" onclick="show_modal(this);return false" title="Edit PG" class="btn btn-xs btn-primary"><i class="fa fa-pencil"></i></a>';
$btn_delete = '<a data-toggle="tooltip" href="javascript:;" onclick="delete_pg('.$data->pg_id.');return false" title="Hapus pilihan ganda" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></a>';
if ($data->pg_is_right == 1){
    $btn_benar  = '<a data-toggle="tooltip" href="javascript:;" onclick="set_benar('.$data->pg_id.');return false" title="Set sebagai jawaban yang benar" class="btn btn-xs btn-success hidden right_'.$data->pg_id.'"><i class="fa fa-check"></i></a>';
} else {
    $btn_benar  = '<a data-toggle="tooltip" href="javascript:;" onclick="set_benar('.$data->pg_id.');return false" title="Set sebagai jawaban yang benar" class="btn btn-xs btn-success right_'.$data->pg_id.'"><i class="fa fa-check"></i></a>';
}
$all_btn    = $btn_benar.' '.$btn_edit.' '.$btn_delete;
echo '<li class="pg_'.$data->pg_id.'" style="margin-bottom:5px;border-bottom:solid 1px #ccc"><span class="pull-right">'.$all_btn.'</span> <span class="content_'.$data->pg_id.'">'.$data->pg_content.'</span><span class="clearfix"></span> </li>';