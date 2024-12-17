<div class="text-center pt-2 pb-4">
	<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php setting('title'); ?>" class="logo-auth">
</div>
<p class="text-center"><?php echo lang('label_lupa_kata_sandi'); ?></p>
<form method="post" class="login-wrapper" action="<?php echo base_url('auth/forgot/do_forgot'); ?>" id="form">
	<div class="fieldset">
		<label class="inline-icon fa-envelope" for="email"><?php echo lang('email'); ?></label>
		<input type="text" class="form-control form-control-lg" id="email" name="email" placeholder="<?php echo lang('email'); ?>" autocomplete="off" data-validation="required|email">
	</div>
	<button type="submit" class="btn btn-primary btn-block"><?php echo lang('kirim_link'); ?></button>
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