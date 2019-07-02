<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>UJIKOM</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/bootstrap/dist/css/bootstrap.min.css');?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bower_components/font-awesome/css/font-awesome.min.css');?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/client.css');?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- jQuery 3 -->
    <script src="<?php echo base_url('assets/bower_components/jquery/dist/jquery.min.js');?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo base_url('assets/bower_components/bootstrap/dist/js/bootstrap.min.js');?>"></script>

    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/sweetalert/sweetalert.css');?>">
    <script src="<?php echo base_url('assets/plugins/sweetalert/sweetalert.min.js');?>"></script>

    <script>
        $.fn.modal.Constructor.prototype.enforceFocus = function() {

        };
        var base_url = '<?php echo base_url('');?>';
    </script>
    <style>
        .modal {
            text-align: center;
            padding: 0!important;
        }

        .modal:before {
            content: '';
            display: inline-block;
            height: 100%;
            vertical-align: middle;
            margin-right: -4px;
        }

        .modal-dialog {
            display: inline-block;
            text-align: left;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="inner">
            <div class="pull-right header-status">
                SELAMAT DATANG<br>
                <?php echo $this->session->userdata('user_fullname'); ?><br>
                <a href="<?php echo base_url('logout');?>">LOGOUT</a>
            </div>
            <div class="logo"><img src="<?php echo base_url('assets/tutwuri_warna.png');?>" width="50px"></div>
        </div>
    </div>
    <div class="body">
        <div class="body-header">
            <strong class="nomor-soal"># 001</strong>
            <div class="timer-wrapper">
                <span class="timer-minute"><?php echo ceil($this->session->userdata('ses_time_left')/60);?></span>:<span class="timer-detik">00</span>
            </div>
        </div>
        <div class="body-content">
            <?php
            for($i = 1; $i <= 100; $i++){
                echo '<p>'.$i.'</p>';
            }
            ?>
        </div>
        <div class="text-center">
            <a href="javascript:;" onclick="show_hide_nomor();return false" class="btn-showhide btn-warning btn-flat btn-xs btn"><i class="fa fa-chevron-up"></i></a>
        </div>
        <div class="body-footer" id="nomor-soal">
            <?php
            if ($soal){
                foreach ($soal as $valSoal){
                    if ($valSoal->qj_id){
                        $btn    = 'btn-info';
                        $jawab  = $this->conv->toStr($valSoal->pg_nomor);
                    } else {
                        $btn    = 'btn-default';
                        $jawab  = '&nbsp;';
                    }
                    echo '<a href="javascript:;" class="btn btn-flat '.$btn.' soal_'.$valSoal->soal_id.'" onclick="load_soal(this);return false" soal-id="'.$valSoal->soal_id.'" quiz-id="'.$valSoal->quiz_id.'">
                            '.$valSoal->qs_nomor.'
                            <sup class="super pgno_'.$valSoal->soal_id.'">'.$jawab.'</sup>
                          </a> ';
                }
            }
            ?>
        </div>
        <div class="body-footer">
            <div class="col-md-3">
                <a href="javascript:;" class="btn-block btn btn-flat btn-primary btn-prev"><i class="fa fa-chevron-circle-left"></i> SOAL SEBELUMNYA</a>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-3">
                <a href="javascript:;" class="btn-block btn btn-flat btn-primary btn-next">SOAL SELANJUTNYA <i class="fa fa-chevron-circle-right"></i></a>
                <a href="<?php echo base_url('quiz/finish_tes');?>" onclick="show_modal(this);return false" class="btn-block btn btn-flat btn-success btn-finish"><i class="fa fa-check"></i> SELESAI TES</a>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="copyright">
        Copyright <i class="fa fa-copyright"></i> 2019 SMK Muhammadiyah Kandanghaur
    </div>
    <div class="overlay-loader">
        <div><i class="fa fa-spin fa-refresh"></i></div>
    </div>
    <div id="MyModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content btn-flat">
                <div class="modal-header bg-danger">
                    <h4 class="modal-title"><i class="fa fa-warning"></i> SELESAI TES</h4>
                </div>
                <div class="modal-body text-center">
                    <div class="col-md-12">
                        Anda yakin ingin menyelesaikan tes ini ?<br>
                        Setelah anda menekan tombol <strong class="text-danger">SELESAI TES</strong>, maka anda tidak dapat mengulangi TES ini lagi.
                    </div>
                    <div class="clearfix" style="margin-top:50px"></div>
                    <div class="col-md-5">
                        <a class="btn btn-danger btn-flat btn-block" href="javascript:;" onclick="finish_tes();return false">SELESAI TES</a>
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-5">
                        <a class="btn btn-success btn-flat btn-block" href="javascript:;" onclick="hide_modal();return false">BATAL</a>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php //var_dump($jawaban); ?>
<script>
    function finish_tes() {
        $('#MyModal .modal-body .btn').addClass('disabled');
        $.ajax({
            url     : base_url + 'quiz/finish_tes',
            type    : 'POST',
            dataType: 'JSON',
            success : function (dt) {
                if (dt.t == 0){
                    $('#MyModal .modal-body .btn').removeClass('disabled');
                    show_msg(dt.msg,'error');
                } else {
                    window.location.href = base_url + 'login';
                }
            }
        })
    }
    function show_modal(ob) {
        $('#MyModal').modal({ backdrop: 'static', keyboard: false });
    }
    function hide_modal() {
        $('#MyModal').modal('hide');
    }
    function pad (str, max) {
        str = str.toString();
        return str.length < max ? pad("0" + str, max) : str;
    }
    function start_timer() {
        var menit   = parseInt($('.timer-minute').text());
        var detik   = parseInt($('.timer-detik').text());
        window.setInterval(function () {
            if (menit <= 0){ finish_tes(); }
            if (detik <= 0){ detik = 60; menit = menit - 1; }
            detik   = detik - 1;
            $('.timer-detik').text(pad(detik,2));
            $('.timer-minute').text(menit);
        },1000)
    }
    start_timer();
    $('#nomor-soal,.overlay-loader,.btn-finish').hide();
    function show_hide_nomor() {
        $('#nomor-soal').slideToggle(500);
        if ($('.btn-showhide').find('.fa-chevron-up').length > 0){
            $('.btn-showhide').html('<i class="fa fa-chevron-down"></i>');
        } else {
            $('.btn-showhide').html('<i class="fa fa-chevron-up"></i>')
        }
    }
    function show_msg(msg,type) {
        header = 'Gagal';
        if (!type){
            type = 'success';
            header = 'Berhasil';
        }
        swal(header, msg, type);
    }
    function load_soal(ob) {
        var soal_id     = $(ob).attr('soal-id');
        var quiz_id     = $(ob).attr('quiz-id');
        if (!soal_id || !quiz_id){
            show_msg('Invalid parameter','error');
        } else {
            $('.overlay-loader').show();
            $.ajax({
                url     : base_url + 'quiz/load_soal',
                type    : 'POST',
                dataType: 'JSON',
                data    : { quiz_id : quiz_id, soal_id : soal_id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                        $('.overlay-loader').hide();
                    } else {
                        $('.nomor-soal').html('# '+dt.nomor);
                        $('#nomor-soal').find('.btn-primary').removeClass('btn-primary');
                        $('.soal_'+soal_id).addClass('btn-primary');
                        $('.body-content').html(dt.html);

                        //prev btn
                        if ($('.soal_'+soal_id).prev('a').length == 0){
                            $('.btn-prev').addClass('disabled');
                        } else {
                            $('.btn-prev').removeClass('disabled');
                        }
                        //next btn
                        if ($('.soal_'+soal_id).next('a').length == 0){
                            $('.btn-finish').show();
                            $('.btn-next').hide();
                        } else {
                            $('.btn-finish').hide();
                            $('.btn-next').show();
                        }
                        $('.overlay-loader').hide();
                    }
                }
            })
        }
    }
    function set_jawaban(ob) {
        var soal_id     = $(ob).attr('soal-id');
        var quiz_id     = $(ob).attr('quiz-id');
        var pg_id       = $(ob).attr('pg-id');
        if (!soal_id || !quiz_id || !pg_id){
            show_msg('Invalid parameter','msg');
        } else {
            $.ajax({
                url     : base_url + 'quiz/set_jawaban',
                type    : 'POST',
                dataType: 'JSON',
                data    : { soal_id : soal_id, quiz_id : quiz_id, pg_id : pg_id },
                success : function (dt) {
                    if (dt.t == 0){
                        show_msg(dt.msg,'error');
                    } else {
                        $('.soal-pg').find('.btn-success').removeClass('btn-success').addClass('btn-default');
                        $('.soalpg_'+pg_id).addClass('btn-success');

                        $('.soal_'+soal_id).removeClass('btn-default').addClass('btn-info');
                        $('.pgno_'+soal_id).html(dt.nomor);
                    }
                }
            });
        }
    }
    $('.btn-next').click(function () {
        var elem    = $('#nomor-soal').find('.btn-primary').next('a');
        load_soal({'soal-id':elem.attr('soal-id'),'quiz-id':elem.attr('quiz-id')});
        return false;
    });
    $('.btn-prev').click(function () {
        var elem    = $('#nomor-soal').find('.btn-primary').prev('a');
        load_soal({'soal-id':elem.attr('soal-id'),'quiz-id':elem.attr('quiz-id')});
        return false;
    });
    <?php
    if (!isset($jawaban)){
        ?>
        var elem = $('#nomor-soal').find('a').eq(0);
        load_soal({'soal-id':elem.attr('soal-id'),'quiz-id':elem.attr('quiz-id')});
        <?php
    } else {
        ?>
        load_soal({'soal-id':'<?php echo $jawaban->soal_id;?>','quiz-id':'<?php echo $jawaban->quiz_id;?>'});
        <?php
    }
    ?>
</script>
</body>
</html>
