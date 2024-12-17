<div class="text-center pt-2 pb-2">
	<a href="<?php echo base_url(); ?>">
		<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="<?php setting('title'); ?>" class="logo-auth">
	</a>
</div>
<form method="post" class="login-wrapper" action="<?php echo base_url('auth/login/do_login'); ?>" id="form">
	<div class="fieldset">
		<label class="inline-icon fa-user" for="username"><?php echo lang('nama_pengguna'); ?></label>
		<input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="<?php echo lang('nama_pengguna'); ?>" autocomplete="off" data-validation="required">
	</div>
	<div class="fieldset">
		<label class="inline-icon fa-lock" for="password"><?php echo lang('kata_sandi'); ?></label>
		<input type="password" class="form-control form-control-lg password" id="password" name="password" placeholder="<?php echo lang('kata_sandi'); ?>" autocomplete="off" data-validation="required">
		<button type="button" class="hide-password"><i class="fa-eye"></i></button>
	</div>
	<?php if(!setting('single_login')) { ?>
	<div class="fieldset">
		<label class="custom-control custom-checkbox">
			<input type="checkbox" class="custom-control-input" id="remember" name="remember">
	        <label class="custom-control-label" for="remember"><?php echo lang('ingatkan_saya'); ?></label>
		</label>
	</div>
	<?php } ?>
	<button type="submit" class="btn btn-primary btn-block"><?php echo lang('masuk'); ?></button>
	<div class="form-group text-center mt-4">
		<a href="<?php echo base_url('auth/forgot'); ?>"><?php echo lang('lupa_kata_sandi'); ?>?</a>
	</div>
</form>
<script type="text/javascript">
$(document).ready(function(){
	localStorage.clear();
});
$('.hide-password').click(function(){
	if( $('#password').attr('type') == 'text') {
		$('#password').attr('type','password').focus();
		$('.hide-password i').removeClass('fa-eye-slash').addClass('fa-eye');
	} else {
		$('#password').attr('type','text').focus();
		$('.hide-password i').removeClass('fa-eye').addClass('fa-eye-slash');
	}
});
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
					window.location = response.redirect;
				} else {
					cAlert.open(response.message,response.status);
				}
			}
		});
	}
});
</script>