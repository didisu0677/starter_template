<div class="mt-lg-4 pt-2 pb-2 mt-0">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="text-center page-title"><?php echo $title; ?></h2>
            <div class="text-justify">
                <?php echo html_entity_decode($informasi->informasi); ?>
            </div>
            <div class="mt-4 pt-4 text-right information-date">Oleh : <?php echo $informasi->create_by.' pada '.date_indo($informasi->create_at); ?></div>
        </div>
    </div>
</div>
