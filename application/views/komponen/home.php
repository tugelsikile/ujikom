<section class="content-header">
    <h1>
        Komponen, Sub Komponen, dan Indikator Penilaian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url('');?>" data-target="dashboard" onclick="load_page(this);return false"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Komponen Penilaian</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">

    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Tabel Komponen Penilaian</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body table-responsive no-padding">
            <table width="100%" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th>Nama Komponen</th>
                    <th width="100px">Sub Komponen</th>
                    <th width="100px">Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if (!$data){
                    echo '<tr><td colspan="3">Tidak ada data</td></tr>';
                } else {
                    foreach ($data as $val){
                        echo '<tr>
                                <td>'.$val->kom_name.'</td>
                                <td><a data-target="komponen" href="'.base_url('komponen/sub/'.$val->kom_id).'" onclick="load_page(this);return false" class="btn btn-primary btn-flat btn-block">Lihat</a> </td>
                                <td></td>
                              </tr>';
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            Footer
        </div>
        <!-- /.box-footer-->
    </div>
    <!-- /.box -->

</section>