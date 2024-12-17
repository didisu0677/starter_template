<h3 style="word-break: break-all; overflow: hidden; text-overflow: ellipsis;">Hallo <?php echo $user->nama; ?></h3>
<p style="text-align: justify;">Untuk me-reset password akun anda silahkan klik link dibawah ini atau meng-copy link tersebut ke adressbar web browser anda.</p>
<a href="<?php echo base_url('auth/forgot/reset/'.$encode); ?>" style="display: block; color: #e83e8c; padding: 15px 0; text-align: center; font-family: SFMono-Regular,Menlo,Monaco,Consolas,'Liberation Mono','Courier New',monospace;">
	<?php echo base_url('auth/forgot/reset/'.$encode); ?>
</a>
<p>Link tersebut hanya berlaku hingga <?php echo $exp; ?></p>