<?php echo $data->soal_content; ?>
<div class="soal-pg">
    <?php
    if ($data->pg){
        foreach ($data->pg as $valPG){
            $btn = 'btn-default soalpg_'.$valPG->pg_id;
            if ($valPG->is_jawab == 1){
                $btn = 'btn-success soalpg_'.$valPG->pg_id;
            }
            ?>
            <div class="clearfix pg-items">
                <a class="btn btn-sm <?php echo $btn; ?>" href="javascript:;" onclick="set_jawaban(this);return false" pg-id="<?php echo $valPG->pg_id;?>" quiz-id="<?php echo $quiz->quiz_id;?>" soal-id="<?php echo $data->soal_id;?>"><?php echo $this->conv->toStr($valPG->pg_nomor);?></a>
                <?php echo $valPG->pg_content; ?>
            </div>
            <?php
        }
    }
    ?>
</div>
<script>
    var elem = $('.body-content pre');
    var text;
    $.each(elem,function (i,v) {
        text = $(this).html();
        text = text.replace('/</','&lt;');
        $(this).html(text);
    });
</script>