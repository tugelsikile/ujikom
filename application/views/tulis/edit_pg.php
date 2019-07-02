<form id="modalForm">
    <input type="hidden" name="pg_id" value="<?php echo $data->pg_id;?>">
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label">Nomor Soal</label>
            <input type="text" disabled class="form-control" value="<?php echo $soal->soal_nomor; ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label class="control-label" for="pg_nomor">Nomor Pilihan Ganda</label>
            <select name="pg_nomor" id="pg_nomor" class="form-control" style="width: 100%">
                <?php
                for($i = 1; $i <= 10; $i++){
                    echo '<option value="'.$i.'">'.$this->conv->toStr($i).'</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label">Jawaban Benar ?</label>
            <div class="">
                <label for="is_right_ya"><input type="radio" name="is_right" id="is_right_ya" value="1" <?php if ($data->pg_is_right == 1){ echo 'checked';}?> > Ya</label>&nbsp;&nbsp;&nbsp;
                <label for="is_right_no"><input type="radio" name="is_right" id="is_right_no" value="0" <?php if ($data->pg_is_right == 0){ echo 'checked';}?> > Tidak</label>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="form-group">
            <label class="control-label">Isi Pilihan Ganda</label>
            <textarea name="soal_content" id="soal_content" class="form-control" placeholder="Place some text here">
                <?php echo $data->pg_content;?>
            </textarea>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="pull-right">
            <button type="submit" class="btn btn-success btn-submit"><i class="fa fa-floppy-o"></i> Submit</button>
            <a href="javascript:;" onclick="hide_modal();return false" class="btn btn-danger"><i class="fa fa-close"></i> Batal</a>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<div id="ModalForm" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Sisipkan Gambar</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>-->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $('#pg_nomor').val('<?php echo $data->pg_nomor; ?>');
    $('#pg_nomor').select2();
    var editor = $('#soal_content').summernote({
        dialogsInBody   : true,
        height          : 300,
        shortcuts       : false,
        callbacks       : {
            onImageUpload: function(files) {
                //console.log(editor);
                sendFile(files[0]);
            }
        }

    });
    function sendFile(file) {
        data = new FormData();
        data.append("file", file);
        $.ajax({
            dataType    : 'JSON',
            data        : data,
            type        : "POST",
            url         : base_url + 'tulis/upload_img',
            cache       : false,
            contentType : false,
            processData : false,
            success     : function(dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                } else {
                    var uri = dt.url;
                    editor.summernote('insertImage', uri, dt.file_name);
                }
            }
        });
    }

    $('#modalForm').submit(function () {
        //CKupdate();
        $('.btn-submit').prop({'disabled':true}).html('<i class="fa fa-spin fa-refresh"></i> Submit');
        $.ajax({
            url     : base_url + 'tulis/edit_pg_submit',
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
                    hide_modal();
                }
            }
        });
        return false;
    })
</script>