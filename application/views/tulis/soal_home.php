<section class="content-header">
    <h1>
        Bank Soal Tes Tulis
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('tulis');?>" data-target="tulis" onclick="load_page(this);return false">Tes Tulis</a></li>
        <li class="active">Bank Soal</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="printwrap">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Cetak Daftar Hadir</h3>

                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" class="btn btn-sm btn-success" href="javascript:;" onclick="print_now();return false" title="Cetak"><i class="fa fa-print"></i> Cetak</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-danger" href="javascript:;" onclick="cancel_print();return false" title="Batal Cetak"><i class="fa fa-close"></i> Batal Cetak</a>
                </div>
            </div>
            <div class="box-body no-padding">
                <iframe name="printframe" id="printframe" src="<?php echo base_url('home/cetak_loading');?>" style="width: 100%;border:solid 1px #CCC;height:450px"></iframe>
            </div>
        </div>
    </div>
    <div class="noprint">
        <?php
        ?>
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Bank Soal</h3>

                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" class="btn btn-sm btn-primary" href="javascript:;" onclick="show_modal({'href':base_url+'tulis/add_soal','title':$(this).attr('title')});return false" title="Tambah Soal"><i class="fa fa-plus"></i> Tambah Soal</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-success" href="javascript:;" onclick="show_modal({'href':base_url+'tulis/import_soal/','title':'Import Soal'});return false"><i class="fa fa-upload"></i> Import Soal</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-danger disabled btn-delete" href="javascript:;" onclick="bulk_delete();return false" title="Hapus soal"><i class="fa fa-trash"></i> Hapus Soal</a>
                </div>
            </div>
            <div class="box-body no-padding">
                <div class="" style="margin:10px auto">
                    <div class="col-md-3">
                        <input type="text" class="keyword form-control" placeholder="Cari ..." onkeyup="doSearch()">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <form id="formTable">
                    <table id="dataTable" width="100%" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="30px"><input type="checkbox" onclick="icbxall(this)"></th>
                            <th width="50px">Nomor</th>
                            <th width="40%">Isi Soal</th>
                            <th width="40%">Pilihan Ganda</th>
                            <th width="100px">Aksi</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>

            </div>
            <!-- /.box-body -->
            <div class="overlay"><i class="fa fa-spin fa-refresh"></i> </div>
        </div>
        <!-- /.box -->
    </div>


</section>
<script>
    //$('[data-toggle="tooltip"]').tooltip();
    $('.printwrap').hide();
    function print_now() {

    }
    function cancel_print() {
        $('.printwrap').hide();
        $('.noprint').show();
        $('#printframe').attr({'src':base_url+'home/cetak_loading'});
    }
    function print_data() {
        var tapel   = $('#tapel').val();
        var kel_id  = $('#klp_id').val();
        var jw_type = $('#jw_type').val();
        if (!tapel || !kel_id || !jw_type){
            show_msg('Parameter kurang','error');
        } else {
            $('#printframe').attr({'src':base_url+'jadwal/cetak/'+tapel+'/'+kel_id+'/'+jw_type});
            $('.printwrap').show();
            $('.noprint').hide();
        }
    }
    $('#tapel,#quiz_id').select2();
    $('.overlay').hide();
    function icbxall(ob) {
        if ($(ob).prop('checked') == true){
            $('#dataTable tbody input:checkbox').prop('checked',true);
            $('.btn-delete').removeClass('disabled');
        } else {
            $('#dataTable tbody input:checkbox').prop('checked',false);
            $('.btn-delete').addClass('disabled');
        }
    }
    var delayTimer;
    function doSearch() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function() {
            load_table();
        }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
    }
    load_table();
    function load_table() {
        var keyword     = $('.keyword').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'tulis/data_soal',
            type    : 'POST',
            dataType: 'JSON',
            data    : { keyword : keyword },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr><td colspan="5">'+dt.msg+'</td></tr>');
                    $('.btn-delete').addClass('disabled');
                    $('.overlay').hide();
                    $('.jml').html('0');
                } else {
                    $('#dataTable tbody').html(dt.html);
                    $('.btn-delete').addClass('disabled');
                    $('.overlay').hide();
                    $('.jml').html(dt.jml);
                }
            }
        })
    }
    function bulk_delete() {
        var dtlen   = $('#dataTable tbody input:checkbox:checked').length;
        var konf    = confirm('Anda yakin ingin menghapus data ini ?')
        if (dtlen == 0){
            show_msg('Pilih data lebih dulu','error');
        } else if (konf){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'tulis/bulk_delete_soal',
                type    : 'POST',
                dataType: 'JSON',
                data    : $('#formTable').serialize(),
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        show_msg(dt.msg);
                        $.each(dt.data,function (i,v) {
                            $('.row_'+v).remove();
                        });
                        $('.overlay').hide();
                    }
                }
            })
        }
    }
    function set_benar(id) {
        if (!id){
            show_msg('Invalid parameter','error');
        } else {
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'tulis/set_jawaban',
                type    : 'POST',
                data    : { pg_id : id },
                dataType: 'JSON',
                success : function (dt) {
                    if (dt.t == 0){
                        $('.overlay').hide();
                        show_msg(dt.msg,'error');
                    } else {
                        $('.overlay').hide();
                        $('.soal_'+dt.soal_id).find('.hidden').removeClass('hidden');
                        $('.right_'+id).addClass('hidden');
                    }
                }
            });
        }
    }
    function delete_pg(id) {
        var konf = confirm('Hapus data ?');
        if (!id){
            show_msg('Invalid parameter','error');
        } else if (konf){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'tulis/delete_pg',
                data    : { pg_id : id },
                type    : 'POST',
                dataType: 'JSON',
                success : function (dt) {
                    if (dt.t == 0){
                        $('.overlay').hide();
                        show_msg(dt.msg,'error');
                    } else {
                        $('.overlay').hide();
                        show_msg(dt.msg);
                        $('.pg_'+id).remove();
                    }
                }
            });
        }
    }
    function delete_data(ob) {
        var soal_id     = $(ob).attr('data-id');
        var konf        = confirm('Yakin ingin menghapus data?');
        if (!soal_id){
            show_msg('Pilih data','error');
        } else if (konf){
            $('.overlay').hide();
            $.ajax({
                url     : base_url + 'tulis/delete_soal',
                type    : 'POST',
                dataType: 'JSON',
                data    : { soal_id : soal_id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        $('.overlay').hide();
                        show_msg(dt.msg);
                        $('.row_'+soal_id).remove();
                    }
                }
            })
        }
    }
</script>