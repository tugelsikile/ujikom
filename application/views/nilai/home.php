<section class="content-header">
    <h1>
        Penilaian Praktik, &amp; Lisan
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Penilaian Praktik, &amp; Lisan</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Tabel Penilaian Praktik, &amp; Lisan</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body no-padding">
            <div class="margin-bottom">
                <div class="col-md-2" style="margin-bottom:10px">
                    <select id="tapel" onchange="tapel_select()" class="form-control" style="width: 100%">
                        <?php
                        $min = $tapel;
                        for ($i = $min; $i <= date('Y'); $i++){
                            echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2" style="margin-bottom:10px">
                    <select id="jenis" onchange="load_table();" style="width: 100%">
                        <option value="praktik">Praktik</option>
                        <option value="lisan">Lisan</option>
                    </select>
                </div>
                <div class="col-md-8" style="">
                    <select id="sis_id" onchange="load_table()" style="width: 100%">
                        <option value="">Nama Siswa</option>
                    </select>
                </div>
                <div class="clearfix"></div>
            </div>


            <table id="dataTable" width="100%" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="50px">No</th>
                    <th>Komponen, Sub Komponen &amp; Indikator Penilaian</th>
                    <th width="">Aksi</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="overlay"><i class="fa fa-spin fa-refresh"></i> </div>
    </div>
    <!-- /.box -->

</section>
<div class="foot">
    <a href="javascript:;" onclick="$(document).scrollTop(0);return false" class="btn btn-md btn-flat btn-info pull-right"><i class="fa fa-arrow-circle-up"></i></a>
    <a href="javascript:;" title="Nilai Tambah" onclick="nilai_tambah();return false" class="btn btn-md btn-flat btn-warning pull-right"><i class="fa fa-plus-circle"></i></a>
    <a href="javascript:;" title="Nilai Lisan" onclick="nilai_lisan();return false" class="btn btn-md btn-flat btn-primary pull-right"><i class="fa fa-mortar-board"></i></a>
    <a href="javascript:;" onclick="show_qr();return false" class="btn btn-md btn-flat btn-default pull-right"><i class="fa fa-qrcode"></i></a>
    <div style="margin-top:5px;margin-left:5px">
        <strong>Butir Penilaian</strong> : <strong class="nilaisudah text-danger"></strong> / <strong class="nilaiBelum text-primary"></strong>
    </div>
</div>
<div class="qrwrapper">
    <div class="qrinner">
        <div style="border-bottom: solid 1px #000;padding:5px">
            <div class="col-xs-9">
                <select class="form-control flat" id="camera-select"></select>
            </div>
            <div class="col-xs-2">
                <a href="javascript:;" onclick="hide_qr();return false" class="flat btn btn-flat btn-danger">Batal</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="videoWrap" style="background:#ccc">
            <canvas id="webcodecam-canvas"></canvas>
            <div class="top right"></div>
            <div class="top left"></div>
            <div class="bottom right"></div>
            <div class="bottom left"></div>
        </div>
    </div>
</div>
<script>
    hide_qr();
    function hide_qr() {
        $('.qrwrapper').hide();
    }
    function show_qr() {
        $('.qrwrapper').show();
        var elem_width  = $('.videoWrap').width();
        var elem_height = parseInt(elem_width / 2);
        elem_height     = Math.round(elem_height);
        var arg = {
            DecodeQRCodeRate    : 1,
            DecodeBarCodeRate   : 1,
            codeRepetition      : true,
            tryVertical         : true,
            successTimeout      : 500,
            frameRate           : 25,
            beep                : base_url + 'assets/bower_components/webcodecamjs-master/audio/beep.mp3',
            decoderWorker       : base_url + 'assets/bower_components/webcodecamjs-master/js/DecoderWorker.js',
            width               : elem_width,
            height              : 200,
            resultFunction      : function(result) {
                //alert(result.code);
                $('#sis_id').select2('destroy');
                $('#sis_id').val(result.code);
                $('#sis_id').select2();
                hide_qr();
                decoder.stop();
                //select_peserta(result.code);
                //$('body').append($('<li>' + result.format + ': ' + result.code + '</li>'));
            }
        };
        var decoder = $("#webcodecam-canvas").WebCodeCamJQuery(arg).data().plugin_WebCodeCamJQuery;
        decoder.buildSelectMenu("#camera-select");
        decoder.play();

        /*  Without visible select menu
            decoder.buildSelectMenu(document.createElement('select'), 'environment|back').init(arg).play();
        */
        $('#camera-select').on('change', function(){
            decoder.stop().play();
        });
    }
    function nilai_lisan() {
        var user_id  = $('#sis_id').val();
        var ob       = {'title':'Nilai Lisan','href':base_url+'nilai/nilai_lisan/'+user_id+'/xx'};
        show_modal(ob);
    }
    function nilai_tambah() {
        var user_id  = $('#sis_id').val();
        var ob       = {'title':'Nilai Tambah','href':base_url+'nilai/nilai_tambah/'+user_id+'/xx'};
        show_modal(ob);
    }
    function set_jawab(ob) {
        var sis_id  = $(ob).attr('data-sisid');
        var ind_id  = $(ob).attr('data-indid');
        var n_tipe  = $(ob).attr('data-tipe');
        var okno    = $(ob).attr('data-no');
        $.ajax({
            url     : base_url + 'nilai/set_jawab',
            type    : 'POST',
            dataType: 'JSON',
            data    : { sis_id : sis_id, ind_id : ind_id, n_tipe : n_tipe, okno : okno },
            success : function (dt) {
                if (dt.t > 0){
                    if (okno == 'ok'){
                        $('.no_'+ind_id).removeClass('btn-danger').addClass('btn-default');
                        $('.'+okno+'_'+ind_id).removeClass('btn-default').addClass('btn-success');
                    } else {
                        $('.ok_'+ind_id).removeClass('btn-success').addClass('btn-default');
                        $('.'+okno+'_'+ind_id).removeClass('btn-default').addClass('btn-danger');
                    }
                    var sudah   = $('.nilaisudah').text();
                    sudah       = parseInt(sudah);
                    var belum   = $('.nilaiBelum').text();
                    belum       = parseInt(belum);
                    if (dt.insert == 1){
                        sudah   = sudah + 1;
                        $('.nilaisudah').html(sudah);
                    }
                    if (sudah == belum){
                        $('.nilaisudah').removeClass('text-danger').removeClass('text-info').addClass('text-success');
                    }
                }
            }
        })
    }
    $('.overlay').hide();
    $('#tapel,#jenis,#sis_id,#uji').select2();
    function tapel_select() {
        var tapel   = $('#tapel').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'nilai/tapel_select',
            type    : 'POST',
            data    : { tapel : tapel },
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    $('#sis_id').html('<option value="">'+dt.msg+'</option>');
                    $('#dataTable tbody').html('<tr><td colspan="3">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                } else {
                    $('#sis_id').html('');
                    $.each(dt.data,function (i,v) {
                        $('#sis_id').append('<option value="'+v.user_id+'">'+v.user_nis+' - '+v.user_fullname+'</option>');
                        if (i + 1 >= dt.data.length){ load_table(); $('.overlay').hide(); }
                    });
                }
            }
        })
    }
    tapel_select();
    function load_table() {
        var tapel       = $('#tapel').val();
        var jenis       = $('#jenis').val();
        var sis_id      = $('#sis_id').val();
        $('.overlay').show();
        $.ajax({
            url     : base_url + 'nilai/data_penilaian',
            type    : 'POST',
            dataType: 'JSON',
            data    : { tapel : tapel, jenis : jenis, sis_id : sis_id },
            success : function (dt) {
                if (dt.t == 0){
                    $('#dataTable tbody').html('<tr><td colspan="3">'+dt.msg+'</td></tr>');
                    $('.overlay').hide();
                    hide_qr();
                } else {
                    $('.nama_siswa').html(dt.nama_siswa);
                    $('#dataTable tbody').html(dt.html);
                    $('.overlay').hide();
                    hide_qr();
                }
            }
        })
    }
</script>