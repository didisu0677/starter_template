<div class="mt-lg-4 pt-2 pb-2 mt-0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if(count($informasi) > 0) { ?>
            <div class="text-justify">
                <?php foreach($informasi as $i) { ?>
                <div class="information">
                    <a href="<?php echo base_url('informasi/read/'.encode_id([$i['id'],strtotime('now')])); ?>" class="information-title"><?php echo $i['judul'];  ?></a>
                    <div class="information-date"><?php echo date_indo($i['create_at']); ?></div>
                    <p class="information-desc"><?php echo word_limiter(strip_tags(html_entity_decode($i['informasi'])),50); ?></p>
                </div>
                <?php } ?>
            </div>
            <?php } else { ?>
            <div class="image-center">
                <img class="img-fluid mt-0 mt-lg-4" src="<?php echo base_url('assets/public/images/file-searching.svg'); ?>" alt="">
            </div>
            <h6 class="text-center">Tidak ada informasi</h6>
            <?php } ?>
        </div>
    </div>
</div>
