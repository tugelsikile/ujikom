<form id="modalForm">
    <input type="hidden" name="quiz_id" value="<?php echo $data->quiz_id;?>">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="frtapel">Tahun Pelajaran</label>
            <input type="text" class="form-control frtapel" disabled value="<?php echo $data->quiz_tapel;?>">
            <input type="hidden" name="tapel" id="frtapel" value="<?php echo $data->quiz_tapel;?>">
        </div>
    </div>
    <div class="col-md-10">
        <div class="form-group">
            <label class="control-label" for="klp_name">Kompetensi Keahlian</label>
            <input type="text" class="form-control" disabled value="<?php echo $kk->kk_name;?>">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label" for="quiz_start">Tanggal Pelaksanaan</label>
            <input type="text" name="quiz_start" id="quiz_start" class="form-control" value="<?php echo date('Y-m-d',strtotime($data->quiz_start));?>">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label" for="quiz_jml_soal">Jml Soal</label>
            <input type="number" name="quiz_jml_soal" id="quiz_jml_soal" class="form-control" value="<?php echo $data->quiz_jml_soal;?>" min="0" max="999">
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label" for="quiz_timer">Batas Waktu (Menit)</label>
            <input type="number" name="quiz_timer" id="quiz_timer" class="form-control" value="<?php echo $data->quiz_timer;?>" min="0" max="999">
        </div>
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
    $('#quiz_start').datepicker({
        format      : 'yyyy-mm-dd',
        autoclose   : true,
        startDate   : '<?php echo date('Y');?>-02-01'
    });
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'tulis/edit_data_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('.btn-submit').prop({'disabled':false}).html('Submit');
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