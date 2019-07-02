<section class="content-header">
    <h1>
        Kelompok Belajar
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Kelompok Belajar</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Kelompok Belajar</h3>

            <div class="box-tools pull-right">
                <a data-toggle="tooltip" class="btn btn-sm btn-primary" href="<?php echo base_url('kelompok/add_data');?>" onclick="show_modal(this);return false" title="Tambah Kelompok"><i class="fa fa-plus"></i> Tambah Kelompok</a>
                <a data-toggle="tooltip" class="btn btn-sm btn-danger" href="javascript:;" onclick="delete_kel({'data-id':$('#klp_id').val()});return false"><i class="fa fa-trash"></i> Hapus Kelompok</a>
                <a data-toggle="tooltip" class="btn btn-sm btn-success disabled btn-add" href="javascript:;" onclick="show_modal({'href':base_url+'kelompok/add_member/'+$('#klp_id').val(),'title':'Tambah Anggota Kelompok'});return false"><i class="fa fa-plus-circle"></i> Tambah Anggota</a>
                <a data-toggle="tooltip" class="btn btn-sm btn-danger disabled btn-delete" href="javascript:;" onclick="bulk_delete();return false" title="Hapus anggota kelompok"><i class="fa fa-window-close-o"></i> Hapus Anggota Kelompok</a>
            </div>
        </div>
        <div class="box-body no-padding">
            <div class="" style="margin:10px auto">
                <div class="col-md-2" style="">
                    <select id="tapel" onchange="tapel_select()" class="form-control" style="width: 100%">
                        <?php
                        $min = $tapel;
                        for ($i = $min; $i <= date('Y'); $i++){
                            echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4" style="margin-bottom:10px">
                    <select id="klp_id" onchange="load_table();" style="width: 100%">
                        <option value="">Nama Kelompok</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="keyword form-control" placeholder="Cari ..." onkeyup="doSearch()">
                </div>
                <div class="col-md-2">
                    Jml : <strong class="jml">0</strong>
                </div>
                <div class="clearfix"></div>
            </div>

            <form id="formTable">
                <table id="dataTable" width="100%" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th width="50px"><input type="checkbox" onclick="icbxall(this)"></th>
                        <th width="100px">NIS</th>
                        <th width="200px">Nomor Peserta</th>
                        <th width="">Nama Peserta</th>
                        <th width="50px">L/ P</th>
                        <th width="80px">Kelas</th>
                        <th width="150px">Kelompok</th>
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

</section>
<script>
    $('#tapel,#klp_id').select2();
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
    function tapel_select() {
        var tapel   = $('#tapel').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'kelompok/tapel_select',
            type    : 'POST',
            data    : { tapel : tapel },
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    $('#klp_id').select2('destroy');
                    $('#klp_id').html(dt.msg);
                    $('#klp_id').select2();
                    $('.overlay').hide();
                    $('.btn-add,.btn-delete').addClass('disabled');
                    load_table();
                } else {
                    $('#klp_id').select2('destroy');
                    $('#klp_id').html('');
                    if (dt.data.length > 0){
                        $.each(dt.data,function (i,v) {
                            $('#klp_id').append('<option value="'+v.klp_id+'">'+v.klp_name+'</option>');
                            if (i + 1 >= dt.data.length){
                                $('#klp_id').val(dt.id).select2();
                                $('.overlay').hide();
                                $('.btn-add').removeClass('disabled');
                                $('.btn-delete').addClass('disabled');
                                load_table();
                            }
                        });
                    } else {
                        $('.btn-add,.btn-delete').addClass('disabled');
                        $('#klp_id').select2();
                        load_table();
                    }
                }
            }
        })
    }
    tapel_select();
    function delete_kel() {
        var kel_id  = $('#klp_id').val();
        var konf    = confirm('Anda yakin ingin menghapus kelompok ini ?');
        if (!kel_id){
            show_msg('Pilih Kelompok','error');
        } else if (konf){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'kelompok/delete_kel',
                type    : 'POST',
                dataType: 'JSON',
                data    : { kel_id : kel_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('.overlay').hide();
                        show_msg(dt.msg,'error');
                    } else {
                        $('.overlay').hide();
                        $('#klp_id').select2('destroy');
                        $('#klp_id').html('');
                        if (dt.data.length > 0){
                            $.each(dt.data,function (i,v) {
                                $('#klp_id').append('<option value="'+v.klp_id+'">'+v.klp_name+'</option>');
                                if (i + 1 >= dt.data.length){
                                    $('#klp_id').val(dt.id).select2();
                                    $('.overlay').hide();
                                    show_msg(dt.msg);
                                }
                            });
                            $('.btn-add').removeClass('disabled');
                        } else {
                            $('#klp_id').select2();
                            $('.btn-add,.btn-delete').addClass('disabled');
                        }
                    }
                }
            })
        }
    }
    var delayTimer;
    function doSearch() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function() {
            load_table();
        }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
    }
    function load_table() {
        var tapel       = $('#tapel').val();
        var klp_id      = $('#klp_id').val();
        var keyword     = $('.keyword').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'kelompok/data_home',
            type    : 'POST',
            dataType: 'JSON',
            data    : { tapel : tapel, klp_id : klp_id, keyword : keyword },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr><td colspan="7">'+dt.msg+'</td></tr>');
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
    load_table();
    function bulk_delete() {
        var dtlen   = $('#dataTable tbody input:checkbox:checked').length;
        var konf    = confirm('Anda yakin ingin menghapus data ini ?')
        if (dtlen == 0){
            show_msg('Pilih data lebih dulu','error');
        } else if (konf){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'kelompok/bulk_delete',
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
    function delete_data(ob) {
        var klm_id  = $(ob).attr('data-id');
        var konf    = confirm('Yakin ingin menghapus data?');
        if (!klm_id){
            show_msg('Pilih data lebih dulu','error');
        } else if (konf){
            $('.overlay').show();
            $.ajax({
                url     : base_url + 'kelompok/delete_data',
                type    : 'POST',
                dataType: 'JSON',
                data    : { klm_id : klm_id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay').hide();
                    } else {
                        show_msg(dt.msg);
                        $('.row_'+klm_id).remove();
                        $('.overlay').hide();
                    }
                }
            });
        }
    }
</script>