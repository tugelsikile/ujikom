<section class="content-header">
    <h1>
        Jadwal UKK
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Jadwal UKK</li>
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
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Jadwal UKK</h3>

                <div class="box-tools pull-right">
                    <a data-toggle="tooltip" class="btn btn-sm btn-success" href="javascript:;" onclick="print_data();return false" title="Cetak Daftar Hadir"><i class="fa fa-print"></i> Cetak Daftar Hadir</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-success" href="javascript:;" onclick="print_ws();return false" title="Cetak Worsktation"><i class="fa fa-print"></i> Cetak WS</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-primary" href="javascript:;" onclick="show_modal({'href':base_url+'jadwal/add_data/'+$('#klp_id').val(),'title':$(this).attr('title')});return false" title="Tambah Jadwal"><i class="fa fa-plus"></i> Tambah Jadwal</a>
                    <a data-toggle="tooltip" class="btn btn-sm btn-danger disabled btn-delete" href="javascript:;" onclick="bulk_delete();return false" title="Hapus anggota kelompok"><i class="fa fa-trash"></i> Hapus Jadwal</a>
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
                    <div class="col-md-3" style="margin-bottom:10px">
                        <select id="jw_type" onchange="load_table();" style="width: 100%">
                            <option value="aya">Pengayaan</option>
                            <option value="pra">Pra UKK</option>
                            <option value="ukk">UKK Utama</option>
                        </select>
                    </div>
                    <div class="col-md-3" style="margin-bottom:10px">
                        <select id="klp_id" onchange="load_table();" style="width: 100%">
                            <option value="">Nama Kelompok</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="keyword form-control" placeholder="Cari ..." onkeyup="doSearch()">
                    </div>
                    <div class="clearfix"></div>
                </div>

                <form id="formTable">
                    <table id="dataTable" width="100%" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th width="50px"><input type="checkbox" onclick="icbxall(this)"></th>
                            <th width="200px">Hari / Tanggal</th>
                            <th width="100px">Jam</th>
                            <th width="">Kelompok</th>
                            <th width="50px">Jml Peserta</th>
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
    $('.printwrap').hide();
    function print_now() {
        window.frames["printframe"].focus();
        window.frames["printframe"].print();
    }
    function cancel_print() {
        $('.printwrap').hide();
        $('.noprint').show();
        $('#printframe').attr({'src':base_url+'home/cetak_loading'});
    }
    function print_ws() {
        var tapel       = $('#tapel').val();
        var kel_id      = $('#klp_id').val();
        $('#printframe').attr({'src':base_url+'jadwal/print_ws/'+tapel+'/'+kel_id});
        $('.printwrap').show();
        $('.noprint').hide();
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
    $('#tapel,#klp_id,#jw_type').select2();
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
        var jw_type     = $('#jw_type').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'jadwal/data_home',
            type    : 'POST',
            dataType: 'JSON',
            data    : { tapel : tapel, klp_id : klp_id, keyword : keyword, jw_type : jw_type },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr><td colspan="6">'+dt.msg+'</td></tr>');
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
                url     : base_url + 'jadwal/bulk_delete',
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
</script>