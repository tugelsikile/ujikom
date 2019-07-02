<section class="content-header">
    <h1>
        Indikator Penilaian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?php echo base_url('komponen');?>" data-target="komponen" onclick="load_page(this);return false">Komponen</a></li>
        <li><a href="<?php echo base_url('komponen/sub/'.$data->kom_id);?>" data-target="komponen" onclick="load_page(this);return false">Sub Komponen</a></li>
        <li class="active">Indikator Penilaian</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Tabel Indikator Penilaian</h3>

            <div class="box-tools pull-right">
                <a title="Tambah Sub Komponen" onclick="add_data();return false" href="javascript:;" class="btn btn-primary margin-r-5"><i class="fa fa-plus"></i> Tambah Indikator</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="box-body table-responsive no-padding">
            <div class="margin-bottom">
                <div class="col-md-3">
                    <select id="paket" class="form-control" onchange="paket_select()">
                        <option value="1">Paket 1</option>
                        <option value="2">Paket 2</option>
                        <option value="3">Paket 3</option>
                        <option value="4">Paket 4</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="kom_id" class="form-control" onchange="kom_select()">
                        <option value="1">1. Persiapan</option>
                        <option value="2">2. Pelaksanaan</option>
                        <option value="3">3. Hasil</option>
                        <option value="4">4. Sikap</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="skom_id" class="form-control" onchange="load_table()">
                        <?php
                        foreach ($skom as $val){
                            echo '<option value="'.$val->skom_id.'">'.$val->kom_id.'.'.$val->skom_urut.'. '.$val->skom_content.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="keyword form-control" placeholder="Cari ..." onkeyup="doSearch()">
                </div>
                <div class="clearfix"></div>
            </div>
            <table id="dataTable" width="100%" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="100px">No.</th>
                    <th>Indikator</th>
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
    var ftime = 0;
    $('#kom_id').val('<?php echo $data->kom_id;?>');
    $('#skom_id').val('<?php echo $data->skom_id;?>');
    $('.overlay').hide();
    $('#kom_id,#skom_id,#paket').select2();
    function paket_select() {
        kom_select();
    }
    function kom_select() {
        var kom_id  = $('#kom_id').val();
        var paket   = $('#paket').val();
        $.ajax({
            url     : base_url + 'komponen/kom_select',
            type    : 'POST',
            dataType: 'JSON',
            data    : { kom_id : kom_id, paket : paket },
            success : function (dt) {
                if (dt.t == 0){
                    $('#skom_id').html('<option value="">'+dt.msg+'</option>');
                    load_table();
                } else {
                    $('#skom_id').html('');
                    $.each(dt.data,function (i,v) {
                        $('#skom_id').append('<option value="'+v.skom_id+'">'+v.kom_id+'.'+v.skom_urut+'. '+v.skom_content+'</option>');
                        if (i + 1 >= dt.data.length){
                            if (ftime == 0){
                                $('#skom_id').val('<?php echo $data->skom_id;?>');
                            }
                            ftime = ftime + 1;
                            load_table();
                        }
                    });
                }
            }
        })
    }
    function add_data() {
        var skom_id = $('#skom_id').val();
        var url     = base_url + 'komponen/add_indikator/' + skom_id;
        var ob      = { 'href':url, 'title':'Tambah indikator'};
        show_modal(ob);
    }
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
                url     : base_url + 'komponen/delete_indikator',
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
                            $('#dataTable tbody tr').append('<tr class="row_zero"><td colspan="3">Tidak ada data</td></tr>');
                        }
                    }
                }
            });
        }
    }
    load_table();
    function load_table() {
        var keyword = $('.keyword').val();
        var skom_id  = $('#skom_id').val();
        $.ajax({
            url     : base_url + 'komponen/indikator_data',
            type    : 'POST',
            dataType: 'JSON',
            data    : { keyword : keyword, skom_id : skom_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr class="row_zero"><td colspan="3">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                } else {
                    $('#dataTable tbody').html(dt.html);
                    $('.overlay').hide();
                }
            }
        })
    }
</script>