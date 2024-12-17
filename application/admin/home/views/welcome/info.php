<?php if(count($pengumuman) > 0) { foreach($pengumuman as $p) { ?>
<div class="card ">
	<div class="card-body">
		<div class="media">
			<div class="media-left pr-2">
				<img src="<?php echo $p->foto ? base_url(dir_upload('user')).$p->foto : base_url(dir_upload('user')).'default.png'; ?>" alt="avatar">
			</div>
			<div class="media-body overflow-h">
				<h4 class="pt-2"><?php echo $p->nama; ?></h4>
				<p class="text-grey"><i class="fa-calendar"></i> <?php echo date('d/m/Y H:i',strtotime($p->create_at)); ?></p>
				<h7 class="italic"><?php echo $p->pengumuman; ?></h7>
			</div>
		</div>
	</div>
</div>
<?php }} else { ?>
<p class="result-error">
	<i class="fa-info error-icon"></i>
	Tidak ada informasi
</p>
<?php } ?>
