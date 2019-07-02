<form id="modalForm">
    <input type="hidden" name="ind_id" value="<?php echo $data->ind_id;?>">
    <div class="col-md-10">
        <div class="form-group">
            <label class="control-label" for="kom_name">Komponen</label>
            <input type="text" class="form-control" disabled value="<?php echo $kom->kom_name;?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="skom_paket">Paket Soal</label>
            <input type="text" class="form-control skom_paket" disabled value="">
            <input type="hidden" name="skom_paket" id="skom_paket" value="">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="skom_content">Sub Komponen</label>
            <input type="text" class="form-control" disabled value="<?php echo $skom->skom_content;?>">
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label" for="ind_content">Isi Indikator</label>
            <textarea name="ind_content" id="ind_content" class="form-control"><?php echo $data->ind_content;?></textarea>
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
            url     : base_url + 'komponen/edit_indikator_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    if ($('.row_zero').length > 0){
                        $('.row_zero').remove();
                    }
                    load_table();
                    show_msg(dt.msg);
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>