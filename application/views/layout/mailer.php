<div style="width: auto; max-width: 600px; margin-left: auto; margin-right: auto; padding: 15px 10px; font-family: helvetica,'Open Sans',sans-serif; color: #484848; line-height: 22px;">
	<img src="<?php echo base_url(dir_upload('setting')).setting('logo'); ?>" alt="<?php echo setting('title'); ?>" style="width: 200px; margin: 10px auto; display: block;">
	<div style="min-height: 200px; padding: 10px;">
		<?php echo $content; ?>
	</div>
	<div style="margin-top: 10px; padding: 10px; border-top: 1px solid #ddd;">
		<img src="<?php echo base_url(dir_upload('setting')).setting('logo_perusahaan'); ?>" alt="<?php echo setting('title'); ?>" style="width: 100px; margin: 5px 0; display: block;">
		<div style="font-weight: 600;"><?php echo setting('company'); ?></div>
		<div><?php echo setting('alamat_perusahaan'); ?></div>
		<?php if(setting('telp_perusahaan')) { ?>
		<div>Telp. <?php echo setting('telp_perusahaan'); ?></div>
		<?php } if(setting('faks_perusahaan')) { ?>
		<div>Faks. <?php echo setting('faks_perusahaan'); ?></div>
		<?php } ?>
	</div>
	<div style="margin-top: 15px; background: #f0f0f0; border: 1px solid #eee; padding: 10px; text-align: center; font-size: 10px; color: #989898; border-radius: 2px; line-height: 14px">
		Email ini di kirim secara otomatis oleh system <?php echo setting('title'); ?>. Mohon untuk tidak membalas pesan yang dikirim oleh email ini.<br />
		Jika menemukan email ini di kotak <strong>Spam</strong> email anda, mohon untuk <strong>Laporkan Bukan Spam</strong> agar anda mendapatkan pemberitahuan <?php echo setting('title'); ?> secara realtime.
	</div>
</div>