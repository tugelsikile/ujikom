<form id="modalForm">
    <input type="hidden" name="jw_id" value="<?php echo $data->jw_id;?>">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="frtapel">Tahun Pelajaran</label>
            <input type="text" id="frtapel" value="<?php echo $data->jw_tapel;?>" class="form-control frtapel" disabled>
            <input type="hidden" name="frtapel" id="frtapel" value="<?php echo $data->jw_tapel;?>">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <?php
            switch($data->jw_type){
                case 'aya'  : $name = 'Pengayaan'; break;
                case 'pra'  : $name = 'Pra UKK'; break;
                case 'ukk'  : $name = 'UKK Utama'; break;
            }
            ?>
            <label class="control-label" for="frjw_type">Jenis Jadwal</label>
            <input type="text" class="form-control frjw_type" disabled value="<?php echo $name;?>">
            <input type="hidden" name="frjw_type" id="frjw_type" value="<?php echo $data->jw_type;?>">
        </div>
    </div>
    <div class="col-md-5">
        <div class="form-group">
            <label class="control-label" for="klp_name">Nama Kelompok</label>
            <input type="text" name="klp_name" id="klp_name" disabled class="form-control" value="<?php echo $kelas->klp_name;?>">
            <input type="hidden" name="klp_id" id="frklp_id" value="<?php echo $data->klp_id;?>">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4">
        <div class="form-group">
            <label class="control-label" for="jw_date">Tanggal</label>
            <input type="text" name="jw_date" id="jw_date" class="form-control" placeholder="YYYY-MM-DD" value="<?php echo date('Y-m-d',strtotime($data->jw_date_start));?>">
        </div>
    </div>
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="jw_time">Pukul</label>
            <input type="text" name="jw_time" id="jw_time" class="form-control" placeholder="HH:II" value="<?php echo date('H:i',strtotime($data->jw_date_start));?>">
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
    $("#jw_date").datepicker({
        format      : 'yyyy-mm-dd',
        autoclose   : true,
        startDate   : '2019-04-01'
    });
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'jadwal/edit_data_submit',
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
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>