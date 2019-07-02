<div class="box box-solid">
    <!-- /.box-header -->
    <div class="box-body">
        <div class="box-group" id="accordion">
            <?php
            $sudah = $belum = 0;
            if ($data){
                $kom_1 = 0;
                foreach ($data as $valKom){
                    if ($kom_1 == 0){
                        $aria_expanded = 'true';
                        $class         = '';
                    } else {
                        $aria_expanded = 'false';
                        $class         = 'collapsed';
                    }
                    ?>
                    <!-- we are adding the .panel class so bootstrap.js collapse plugin detects it -->
                    <div class="panel box box-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $kom_1;?>" aria-expanded="<?php echo $aria_expanded;?>" class="<?php echo $class;?>">
                                    <?php echo $this->conv->romawi($valKom->kom_id).'. '.$valKom->kom_name; ?>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse<?php echo $kom_1;?>" class="panel-collapse collapse" aria-expanded="<?php echo $aria_expanded;?>" style="height: 0px;">
                            <div class="box-body">
                                <div class="box box-solid">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="box-group" id="accordion2">
                                    <?php
                                    $skom_1 = 0;
                                    foreach ($valKom->sub_komponen as $valSkom){
                                        ?>
                                            <div class="panel box box-primary">
                                                <div class="box-header with-border">
                                                    <h4 class="box-title">
                                                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse2<?php echo $skom_1;?>" aria-expanded="<?php echo $aria_expanded;?>" class="<?php echo $class;?>">
                                                            <?php echo $this->conv->romawi($valKom->kom_id).'.'.$valSkom->skom_urut.'. '.$valSkom->skom_content; ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapse2<?php echo $skom_1;?>" class="panel-collapse collapse" aria-expanded="<?php echo $aria_expanded;?>" style="height: 0px;">
                                                    <div class="box-body">
                                                        <ul class="list-group">
                                                        <?php
                                                        foreach ($valSkom->indikator as $valInd){
                                                            ?>
                                                            <li class="list-group-item">
                                                                <span class="badge">14</span>
                                                                Cras justo odio
                                                            </li>
                                                            <?php
                                                        }
                                                        ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
                                        $skom_1++;
                                    }
                                    ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $kom_1++;
                }
            }
            ?>
        </div>
    </div>
    <!-- /.box-body -->
</div>
<script>

</script>