<form id="modalForm">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="frtapel">Tahun Pelajaran</label>
            <input type="text" id="frtapel" value="" class="form-control frtapel" disabled>
            <input type="hidden" name="frtapel" id="frtapel" value="">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label" for="frjw_type">Jenis Jadwal</label>
            <input type="text" class="form-control frjw_type" disabled>
            <input type="hidden" name="frjw_type" id="frjw_type">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label" for="klp_name">Nama Kelompok</label>
            <input type="text" name="klp_name" id="klp_name" disabled class="form-control" value="<?php echo $data->klp_name;?>">
            <input type="hidden" name="klp_id" id="frklp_id" value="">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label" for="jw_date">Tanggal</label>
            <input type="text" name="jw_date" id="jw_date" class="form-control" placeholder="YYYY-MM-DD">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="jw_time">Pukul</label>
            <input type="text" name="jw_time" id="jw_time" class="form-control" placeholder="HH:II">
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
    $('#frtapel,.frtapel').val($('#tapel').val());
    var jenis = $('#jw_type').val();
    if (jenis == 'aya'){
        $('.frjw_type').val('Pengayaan');
    } else if (jenis == 'pra'){
        $('.frjw_type').val('Pra UKK');
    } else {
        $('.frjw_type').val('UKK');
    }
    $('#frjw_type').val($('#jw_type').val());
    $('#frklp_id').val($('#klp_id').val());
    $("#jw_date").datepicker({
        format      : 'yyyy-mm-dd',
        autoclose   : true,
        startDate   : '2019-04-01'
    });
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'jadwal/add_data_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    $('.btn-submit').prop({'disabled':false}).html('Submit');
                } else {
                    $('#dataTable tbody').append(dt.html);
                    show_msg(dt.msg);
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>