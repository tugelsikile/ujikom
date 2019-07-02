<table width="100%" class="table table-responsive table-bordered">
    <thead>
    <tr>
        <th width="50px">No</th>
        <th width="120px">NIS</th>
        <th width="">Nama Peserta</th>
        <th width="50px">L/ P</th>
        <th width="50px">Jml Soal Tes</th>
        <th width="50px">Jml Soal Didapat</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $nomor = 1;
    if (!$data){
        echo '<tr><td colspan="6">Tidak ada data peserta</td></tr>';
    } else {
        foreach ($data as $val){
            echo '<tr>
                    <td align="center">'.$nomor.'</td>
                    <td align="center">'.$val->user_nis.'</td>
                    <td>'.$val->user_fullname.'</td>
                    <td>'.$val->user_sex.'</td>
                    <td align="center">'.$val->jml_soal.'</td>
                    <td align="center">'.$val->dist.'</td>
                 </tr>';
            $nomor++;
        }
    }
    ?>
    </tbody>
</table>
<div class="col-md-12">
    <div class="pull-right">
        <a href="javascript:;" onclick="$('#MyModal').modal('hide');return false" class="btn btn-warning"><i class="fa fa-close"></i> Tutup</a>
    </div>
</div>