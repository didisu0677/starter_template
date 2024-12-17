<div class="row mt-lg-4 pt-2 pb-2 mt-0">
    <div class="col-lg-7">
        <h2 class="text-center page-title">Frequently Asked Questions</h2>
        <div class="accordion">
            <?php foreach($faq as $k => $f) { ?>
            <div class="card <?php if($k > 0) echo ' border-top-0'; ?>">
                <div class="card-header" id="heading<?php echo $f['id']; ?>" data-toggle="collapse" data-target="#collapse<?php echo $f['id']; ?>" aria-expanded="true" aria-controls="collapse<?php echo $f['id']; ?>"><?php echo $f['pertanyaan']; ?></div>
                <div id="collapse<?php echo $f['id']; ?>" class="collapse <?php if($k == 0) echo 'show'; ?>" aria-labelledby="heading<?php echo $f['id']; ?>" data-parent="#accordion">
                    <div class="card-body">
                        <?php echo html_entity_decode($f['jawaban']); ?> 
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="sticky-image sticky-top">
            <img class="img-fluid" src="<?php echo base_url('assets/public/images/question.svg'); ?>" alt="">
        </div>
    </div>
</div>