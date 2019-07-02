<form id="modalForm">
    <input type="hidden" name="klp_id" value="<?php echo $kelas->klp_id;?>">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="frtapel">Tahun Pelajaran</label>
            <input type="text" class="form-control" disabled value="<?php echo $kelas->klp_tapel;?>">
        </div>
    </div>
    <div class="col-md-10">
        <div class="form-group">
            <label class="control-label" for="klp_name">Nama Kelompok</label>
            <input type="text" name="klp_name" class="form-control" disabled value="<?php echo $kelas->klp_name;?>">
        </div>
    </div>
    <div class="col-md-12 no-padding">
        <table id="tableForm" class="table table-bordered table-responsive">
            <thead>
            <tr>
                <th width="50px"><input type="checkbox" onclick="formicbxall(this)"></th>
                <th width="100px">NIS</th>
                <th width="">Nama Siswa</th>
                <th width="50px">L/ P</th>
                <th width="100px">Kelas</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if ($data){
                foreach ($data as $val){
                    echo '<tr>
                            <td align="center"><input type="checkbox" name="user_id[]" value="'.$val->user_id.'"></td>
                            <td align="center">'.$val->user_nis.'</td>
                            <td>'.$val->user_fullname.'</td>
                            <td align="center">'.$val->user_sex.'</td>
                            <td align="center">'.$val->kel_name.'</td>
                         </tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-floppy-o"></i> Submit</button>
            <a href="javascript:;" onclick="$('#MyModal').modal('hide');return false" class="btn btn-danger"><i class="fa fa-close"></i> Batal</a>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    function formicbxall(ob) {
        if ($(ob).prop('checked') == true){
            $('#tableForm tbody input:checkbox').prop({'checked':true});
        } else {
            $('#tableForm tbody input:checkbox').prop({'checked':false});
        }
    }
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'kelompok/add_member_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    load_table();
                    show_msg(dt.msg);
                    $('.btn-add').removeClass('disabled');
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>