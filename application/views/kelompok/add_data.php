<form id="modalForm">
    <div class="col-md-2">
        <div class="form-group">
            <label class="control-label" for="frtapel">Tahun Pelajaran</label>
            <select name="frtapel" id="frtapel" class="form-control" style="width: 100%">
                <?php
                $min = $tapel;
                for ($i = $min; $i <= date('Y'); $i++){
                    echo '<option value="'.$i.'">'.$i.'</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-10">
        <div class="form-group">
            <label class="control-label" for="klp_name">Nama Kelompok</label>
            <input type="text" name="klp_name" class="form-control" value="Kelompok <?php echo $klpnum;?>">
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
    $('#frtapel').val($('#tapel').val());
    $('#frtapel').select2();
    $('#modalForm').submit(function () {
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'kelompok/add_data_submit',
            type    : 'POST',
            data    : $(this).serialize(),
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    $('#klp_id').select2('destroy');
                    $('#klp_id').html('');
                    $.each(dt.data,function (i,v) {
                        $('#klp_id').append('<option value="'+v.klp_id+'">'+v.klp_name+'</option>');
                        if (i + 1 >= dt.data.length){
                            $('#klp_id').val(dt.id).select2();
                        }
                    })
                    show_msg(dt.msg);
                    $('.btn-add').removeClass('disabled');
                    $('#MyModal').modal('hide');
                }
            }
        });
        return false;
    })
</script>