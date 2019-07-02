<form id="modalForm">
    <input type="hidden" name="mapel_id" value="<?php echo $data->mapel_id;?>">
    <div class="inputnya">
        <div class="col-md-12">
            <div class="form-group">
                <div class="input-group">
                    <label data-toggle="tooltip" title="Browse File" class="input-group-addon" for="file"><i class="fa fa-folder"></i></label>
                    <input type="text" class="file-label form-control" placeholder="" aria-describedby="basic-addon1" disabled>
                    <span class="input-group-btn">
                        <button class="btn-cancel btn btn-success btn-flat disabled btn-upload" disabled type="submit"><i class="fa fa-upload"></i> Upload</button>
                    </span>
                    <span class="input-group-btn">
                        <button onclick="hide_modal();return false" class="btn-cancel btn btn-danger btn-flat" type="button"><i class="fa fa-close"></i> Batal</button>
                    </span>
                </div>
                <input style="display:none" type="file" name="file" id="file" class="form-control">
            </div>
        </div>
    </div>
    <div class="col-md-12 progressnya">
        <div class="progress">
            <div id="progressBar" class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                <span class="sr-only">45% Complete</span>
            </div>
        </div>
    </div>
    <div class="clearfix text-center">
        Mohon hanya gunakan format file yang diambil dari tombol download dibawah ini.
    </div>
    <div class="col-md-12">
        <a data-toggle="tooltip" target="_blank" class="btn btn-primary btn-sm" href="<?php echo base_url('tulis/download_format/'.$data->mapel_id);?>"><i class="fa fa-download"></i> Download Format</a>
    </div>
    <div class="clearfix"></div>
</form>
<script>
    $('.progressnya').hide();
    $('#file').change(function () {
        var file_name   = $('#file').val();
        $('.file-label').val(file_name);
        $('.btn-upload').removeClass('disabled');
        $('.btn-upload').prop({'disabled':false});
    })
    $('[data-toggle="tooltip"]').tooltip();
    $('#modalForm').submit(function () {
        $('.btn-upload,.btn-cancel').addClass('disabled');
        $('.inputnya,.progressnya').toggle();
        $('.btn-cancel').prop({'disabled':true});
        $('#progressBar').attr('aria-valuenow', 0).css('width', '0%').text('0%');
        var formdata    = new FormData($('#modalForm')[0]);
        $.ajax({
            xhr     : function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e){
                    if(e.lengthComputable){
                        console.log('Bytes Loaded : ' + e.loaded);
                        console.log('Total Size : ' + e.total);
                        console.log('Persen : ' + (e.loaded / e.total));

                        var percent = Math.round((e.loaded / e.total) * 100);

                        $('#progressBar').attr('aria-valuenow', percent).css('width', percent + '%').text(percent + '%');
                    }
                });
                return xhr;
            },
            type        : 'POST',
            url         : base_url + 'tulis/import_soal_submit',
            data        : formdata,
            processData : false,
            contentType : false,
            dataType    : 'JSON',
            success     : function(dt){
                if (dt.t == 0){
                    $('#modalForm')[0].reset();
                    $('.inputnya,.progressnya').toggle();
                    show_msg(dt.msg,'error');
                    $('.btn-cancel').prop({'disabled':false});
                    $('.btn-upload').removeClass('disabled');
                    $('.btn-upload').prop({'disabled':true});
                    $('#progressBar').attr('aria-valuenow', 0).css('width', '0%').text('0%');
                } else {
                    $('#modalForm')[0].reset();
                    $('.inputnya,.progressnya').toggle();
                    hide_modal();
                    load_table();
                    show_msg(dt.msg);
                    $('.btn-upload').removeClass('disabled');
                    $('.btn-cancel').prop({'disabled':false});
                    $('.btn-upload').prop({'disabled':true});
                    $('#progressBar').attr('aria-valuenow', 0).css('width', '0%').text('0%');
                }
            }
        });
        return false;
    })
</script>