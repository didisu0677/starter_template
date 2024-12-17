<div class="mt-lg-4 pt-2 pb-2 mt-0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <?php if($valid) { ?>
                <h1 class="text-center mt-2 mb-4 text-success"><i class="fa-check-circle"></i></h1>
                <div class="text-center"><strong><?php echo $vendor['nama']; ?></strong> terverifikasi sebagai rekanan PT. Pegadaian (Persero)</div>
            <?php } else { ?>
                <h1 class="text-center mt-2 mb-4 text-danger"><i class="fa-times-circle"></i></h1>
                <div class="text-center">Link tidak valid</div>
            <?php } ?>
        </div>
    </div>
</div>