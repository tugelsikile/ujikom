<section class="content-header">
    <h1>
        Sub Komponen, dan Indikator Penilaian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('komponen');?>" data-target="komponen" onclick="load_page(this);return false">Komponen</a></li>
        <li class="active">Sub Komponen Penilaian</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Tabel Sub Komponen Penilaian</h3>

            <div class="box-tools pull-right">
                <a title="Tambah Sub Komponen" onclick="add_data();return false" href="javascript:;" class="btn btn-primary margin-r-5"><i class="fa fa-plus"></i> Tambah Sub Komponen</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="margin-bottom">
                <div class="col-md-4">
                    <select id="paket" class="form-control" onchange="load_table()">
                        <option value="1">Paket 1</option>
                        <option value="2">Paket 2</option>
                        <option value="3">Paket 3</option>
                        <option value="4">Paket 4</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <select id="kom_id" class="form-control" onchange="load_table()">
                        <option value="1">1. Persiapan</option>
                        <option value="2">2. Pelaksanaan</option>
                        <option value="3">3. Hasil</option>
                        <option value="4">4. Sikap</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="keyword form-control" placeholder="Cari ..." onkeyup="doSearch()">
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="dataTable" width="100%" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="40px">No.</th>
                    <th>Nama Sub Komponen</th>
                    <th width="80px">Paket Soal</th>
                    <th width="50px">Indikator Penilaian</th>
                    <th width="50px">Sangat Baik</th>
                    <th width="50px">Baik</th>
                    <th width="50px">Cukup Baik</th>
                    <th width="50px">Belum</th>
                    <th width="100px">Aksi</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            Footer
        </div>
        <div class="overlay"><i class="fa fa-spin fa-refresh"></i></div>
        <!-- /.box-footer-->
    </div>
    <!-- /.box -->

</section>
<script>
    function add_data() {
        var kom_id  = $('#kom_id').val();
        var url     = base_url + 'komponen/add_sub/' + kom_id;
        var ob      = { 'href':url, 'title':'Tambah sub komponen'};
        show_modal(ob);
    }
    $('#kom_id').val('<?php echo $data->kom_id;?>');
    $('#paket,#kom_id').select2();
    $('.overlay').hide();
    var delayTimer;
    function doSearch() {
        clearTimeout(delayTimer);
        delayTimer = setTimeout(function() {
            load_table();
        }, 1000); // Will do the ajax stuff after 1000 ms, or 1 s
    }
    function delete_data(ob) {
        var skom_id = $(ob).attr('data-id');
        var kon     = confirm('Anda yakin ingin menghapus data ini ?');
        if (!skom_id){
            show_msg('Invalid data');
        } else if (kon){
            $.ajax({
                url     : base_url + 'komponen/delete_sub',
                type    : 'POST',
                dataType: 'JSON',
                data    : { skom_id : skom_id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                    } else {
                        show_msg(dt.msg);
                        $('.row_'+skom_id).remove();
                        if ($('#dataTable tbody tr').length == 0){
                            $('#dataTable tbody tr').append('<tr class="row_zero"><td colspan="9">Tidak ada data</td></tr>');
                        }
                    }
                }
            });
        }
    }
    load_table();
    function load_table() {
        var keyword = $('.keyword').val();
        var kom_id  = $('#kom_id').val();
        var paket   = $('#paket').val();
        $.ajax({
            url     : base_url + 'komponen/sub_data',
            type    : 'POST',
            dataType: 'JSON',
            data    : { keyword : keyword, paket : paket, kom_id : kom_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr class="row_zero"><td colspan="9">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                } else {
                    $('#dataTable tbody').html(dt.html);
                    $('.overlay').hide();
                }
            }
        })
    }
    function show_hide(ob) {
        var value   = $(ob).attr('data-val');
        var skom_id = $(ob).attr('data-id');
        $(ob).html('<i class="fa fa-spin fa-refresh"></i> Loading');
        $.ajax({
            url     : base_url + 'komponen/set_show',
            type    : 'POST',
            dataType: 'JSON',
            data    : { value : value, skom_id : skom_id },
            success : function (dt) {
                if (dt.t == 0){
                    show_msg(dt.msg,'error');
                    if (value == 1){
                        $(ob).html('<i class="fa fa-eye"></i> Manual').removeClass('btn-warning').addClass('btn-success').attr('data-val',0);
                    } else {
                        $(ob).html('<i class="fa fa-eye"></i> Auto').removeClass('btn-success').addClass('btn-warning').attr('data-val',1);
                    }
                } else {
                    if (value == 1){
                        $(ob).html('<i class="fa fa-eye"></i> Manual').removeClass('btn-warning').addClass('btn-success').attr('data-val',0);
                    } else {
                        $(ob).html('<i class="fa fa-eye"></i> Auto').removeClass('btn-success').addClass('btn-warning').attr('data-val',1);
                    }
                }
            }
        });
    }
</script>