<form id="modalForm">
    <input type="hidden" name="user_id" value="<?php echo $data->user_id;?>">
    <input type="hidden" name="jenis" value="<?php echo $jenis;?>">
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="nt_nilai">Nilai Tambah (min 0 max 10)</label>
            <input id="nt_nilai" name="nt_nilai" type="number" min="0" max="10" step="1" class="form-control" value="<?php echo $data->nt_nilai;?>">
        </div>
    </div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-floppy-o"></i> Submit</button>
            <a href="javascript:;" onclick="$('#MyModal').modal('hide');return false" class="btn btn-danger"><i class="fa fa-close"></i> Batal</a>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('#skom_paket').val($('#paket').val());
    $('.skom_paket').val('Paket '+$('#paket').val());
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'nilai/nilai_tambah_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    load_table();
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>