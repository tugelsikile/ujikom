<section class="content-header">
    <h1>
        Rekap Penilaian Pengetahuan dan Keterampilan
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Rekap Penilaian Pengetahuan dan Keterampilan</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="printwrap" style="display:none">
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
                <h3 class="box-title">Tabel Rekap Penilaian Pengetahuan dan Keterampilan</h3>

                <div class="box-tools pull-right">

                </div>
            </div>
            <div class="box-body no-padding">
                <div class="margin-bottom">
                    <div class="col-md-2" style="margin-bottom:10px">
                        <select id="tapel" onchange="load_table()" class="form-control" style="width: 100%">
                            <?php
                            $min = $tapel;
                            for ($i = $min; $i <= date('Y'); $i++){
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4" style="margin-bottom:10px">
                        <select id="kk_id" onchange="load_table();" style="width: 100%">
                            <?php
                            foreach ($kk as $val){
                                echo '<option value="'.$val->kk_id.'">'.$val->kk_name.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2" style="margin-bottom:10px">
                        <select id="jenis" onchange="load_table();" style="width: 100%">
                            <option value="in">Internal</option>
                            <option value="ex">Eksternal</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-12">
                        <a class="btn btn-primary btn-reload" href="javascript:;" onclick="load_table();return false"><i class="fa fa-refresh"></i> Reload</a>
                        <a class="btn btn-primary" href="javascript:;" onclick="download_data();return false"><i class="fa fa-download"></i> Download Data</a>
                        <a class="btn btn-primary" href="javascript:;" onclick="print_data();return false"><i class="fa fa-print"></i> Cetak Lembar Nilai</a>
                        <a class="btn btn-warning" href="javascript:;" onclick="auto_isi(this);return false"><i class="fa fa-check"></i> Auto Nilai Keterampilan</a>
                        <a class="btn btn-delete btn-danger" href="javascript:;" onclick="delete_nilai();return false"><i class="fa fa-trash"></i> Hapus Nilai</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="margin-bottom col-md-6">
                    <ul>
                        <li>NK 1 = Persiapan</li>
                        <li>NK 2 = Proses</li>
                        <li>NK 3 = Hasil</li>
                        <li>NP = Nilai Perolehan</li>
                        <li>NA = Nilai Akhir</li>
                        <li>NT = Nilai Tambahan</li>
                    </ul>
                </div>
                <div class="margin-bottom col-md-6">
                    <ul>
                        <li>NAK = Nilai Akhir Keterampilan</li>
                        <li>TT = Tes Tulis</li>
                        <li>TL = Tes Lisan</li>
                        <li>NAP = Nilai Akhir Pengetahuan</li>
                        <li>TPK = Tingkat Pencapaian Kompetensi</li>
                    </ul>
                </div>
                <div class="clearfix"></div>
                <table id="dataTable" width="100%" class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th rowspan="3" width="30px"><input type="checkbox"></th>
                        <th rowspan="3" width="100px">NIS</th>
                        <th rowspan="3" >Nama</th>
                        <th rowspan="3" width="50px">L/P</th>
                        <th colspan="8">Aspek Keterampilan 70%</th>
                        <th colspan="4">Aspek Pengetahuan 30%</th>
                        <th rowspan="3" width="50px">NA UKK</th>
                    </tr>
                    <tr>
                        <th colspan="8">TPK</th>
                        <th colspan="4">TPK</th>
                    </tr>
                    <tr>
                        <th width="50px">NK 1 45%</th>
                        <th width="50px">NK 2 30%</th>
                        <th width="50px">NK 3 25%</th>
                        <th width="50px">Skor Awal</th>
                        <th width="50px">NP</th>
                        <th width="50px">NT</th>
                        <th width="50px">NA</th>
                        <th width="50px">NAK</th>
                        <th width="50px">TT</th>
                        <th width="50px">TL</th>
                        <th width="50px">NA</th>
                        <th width="50px">NAP</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- /.box-body -->
            <div class="overlay"><i class="fa fa-spin fa-refresh"></i> </div>
        </div>
    </div>

    <!-- /.box -->

</section>
<script>
    function auto_isi(ob) {
        var tapel       = $('#tapel').val();
        var kk_id       = $('#kk_id').val();
        var jenis       = $('#jenis').val();
        var konfirmasi  = confirm('Yakin ingin auto nilai keterampilan ?');
        if (konfirmasi){
            $(ob).html('<i class="fa fa-spin fa-refresh"></i> Auto Nilai Keterampilan');
            $.ajax({
                url     : base_url + 'nilai/auto_isi',
                type    : 'POST',
                data    : { tapel : tapel, kk_id : kk_id, jenis : jenis },
                dataType: 'JSON',
                success : function (dt) {
                    if (dt.t == 0){
                        $(ob).html('<i class="fa fa-check"></i> Auto Nilai Keterampilan');
                        show_msg(dt.msg,'error');
                    } else {
                        $(ob).html('<i class="fa fa-check"></i> Auto Nilai Keterampilan');
                        load_table();
                    }
                }
            });
        }
    }
    function print_now() {
        window.frames["printframe"].focus();
        window.frames["printframe"].print();
    }
    function cancel_print() {
        $('.printwrap').hide();
        $('.noprint').show();
        $('#printframe').attr({'src':base_url+'home/cetak_loading'});
    }
    function print_data() {
        var tapel       = $('#tapel').val();
        var kk_id       = $('#kk_id').val();
        var jenis       = $('#jenis').val();
        var data        = '';
        $.each($('#dataTable tbody input:checkbox:checked'),function (i,v) {
            data    = data + $(this).val() + '-';
        });
        var url         = base_url + 'nilai/cetak_lembar/' + tapel + '/' + kk_id + '/' + jenis + '/' +  data ;
        $('#printframe').attr({ 'src' : url });
        $('.printwrap').show();
        $('.noprint').hide();
    }
    <?php
    if ($this->session->userdata('kk_id')){
        echo "$('#kk_id').val('".$this->session->userdata('kk_id')."');";
    }
    ?>
    $('.overlay').hide();
    $('#tapel,#jenis,#aspek,#kk_id').select2();
    function delete_nilai() {
        var tapel       = $('#tapel').val();
        var jenis       = $('#jenis').val();
        var kk_id       = $('#kk_id').val();
        var konfirm     = confirm('Hapus semua nilai?');
        if (konfirm){
            $('.btn-delete').html('<i class="fa fa-spin fa-refresh"></i> Hapus Nilai');
            $.ajax({
                url     : base_url + 'nilai/delete_nilai',
                type    : 'POST',
                dataType: 'JSON',
                data    : { tapel : tapel, jenis : jenis, kk_id : kk_id },
                success : function (dt) {
                    if (dt.t == 0){
                        $('.btn-delete').html('<i class="fa fa-trash"></i> Hapus Nilai');
                        show_msg(dt.msg,'error');
                    } else {
                        $('.btn-delete').html('<i class="fa fa-trash"></i> Hapus Nilai');
                        load_table();
                    }
                }
            });
        }
    }
    function download_data() {
        var tapel       = $('#tapel').val();
        var jenis       = $('#jenis').val();
        var kk_id       = $('#kk_id').val();
        var url = base_url + 'nilai/download/' + tapel + '/' + jenis + '/' + kk_id;
        window.open(url, '_blank');
    }
    function load_table() {
        var tapel       = $('#tapel').val();
        var jenis       = $('#jenis').val();
        var kk_id       = $('#kk_id').val();
        $('.btn-reload .fa').addClass('fa-spin');
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'nilai/rekap_data',
            type    : 'POST',
            dataType: 'JSON',
            data    : { tapel : tapel, jenis : jenis, kk_id : kk_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('.btn-reload .fa').removeClass('fa-spin');
                    $('#dataTable tbody').html('<tr><td colspan="3">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                } else {
                    $('.btn-reload .fa').removeClass('fa-spin');
                    $('#dataTable tbody').html(dt.html);
                    $('.overlay').hide();
                }
            }
        })
    }
    load_table();
</script>