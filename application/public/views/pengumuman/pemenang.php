<div class="mt-lg-4 pt-2 pb-2 mt-0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if(count($pengadaan) > 0) { ?>
            <div class="text-justify">
                <?php foreach($pengadaan as $i) { ?>
                <a href="<?php echo base_url('pengadaan/penetapan_pemenang/cetak/'.encode_id([$i['id'],rand()])); ?>" class="link-block" target="_blank">
                    Pengumuman Pemenang <?php echo $i['nama_pengadaan']; ?>
                </a>
                <?php } ?>
            </div>
            <?php } else { ?>
            <div class="image-center">
                <img class="img-fluid mt-0 mt-lg-4" src="<?php echo base_url('assets/public/images/logistics.svg'); ?>" alt="">
            </div>
            <h6 class="text-center">Tidak ada data</h6>
            <?php } ?>
        </div>
    </div>
</div>
