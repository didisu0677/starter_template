<div class="text-center pt-2 pb-4">
	<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php setting('title'); ?>" class="logo-auth">
</div>
<?php if($exp) { ?>
<p class="text-center"><?php echo lang('label_expired_page'); ?></p>
<?php } else { ?>
<p class="text-center"><?php echo lang('halo'); ?>, <strong><?php echo $user->nama; ?></strong>. <br /><?php echo lang('label_reset_kata_sandi'); ?></p>
<form method="post" class="login-wrapper" action="<?php echo base_url('auth/forgot/do_reset'); ?>" id="form">
	<input type="hidden" name="id" value="<?php echo $id; ?>">
	<div class="fieldset">
		<label class="inline-icon fa-lock" for="password"><?php echo lang('kata_sandi_baru'); ?></label>
		<input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="<?php echo lang('kata_sandi_baru'); ?>" data-validation="required|min-length:6">
	</div>
	<div class="fieldset">
		<label class="inline-icon fa-lock" for="konfirmasi"><?php echo lang('konfirmasi_kata_sandi'); ?></label>
		<input type="password" class="form-control form-control-lg" id="konfirmasi" name="konfirmasi" placeholder="<?php echo lang('konfirmasi_kata_sandi'); ?>" data-validation="required|equal:password">
	</div>
	<button type="submit" class="btn btn-primary btn-block"><?php echo lang('reset_kata_sandi'); ?></button>
	<div class="form-group text-center mt-4">
		<a href="<?php echo base_url('auth/login'); ?>" id="login-page"><?php echo lang('halaman_masuk'); ?></a>
	</div>
</form>
<script type="text/javascript">
function toLogin() {
	window.location = $('#login-page').attr('href');
}
$('#form').submit(function(e){
	e.preventDefault();
	if(validation()){
		$.ajax({
			url : $(this).attr('action'),
			data : $(this).serialize(),
			type : 'POST',
			dataType : 'json',
			success : function(response) {
				if(response.status == 'success') {
					cAlert.open(response.message,response.status,'toLogin');
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	}
});
</script>
<?php } ?>