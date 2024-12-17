<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container container">
		<div class="row">
			<div class="col-sm-9">
				<?php if(setting('masa_aktif_password') && $expired) { ?>
				<div class="alert alert-warning">
					<?php echo lang('kata_sandi_anda_terakhir_diperbaharui_pada_tanggal').' <strong>'.date_lang(user('change_password_at')).'</strong>. '.lang('untuk_keamanan_kata_sandi_anda_harus_diperbaharui_minimal_setiap').' '.setting('masa_aktif_password').' '.lang('hari_sekali'); ?>
				</div>
				<?php } ?>
				<form id="form-command" action="<?php echo base_url('account/changepwd/save'); ?>" data-callback="toHome" method="post" data-submit="ajax">
					<div class="form-group row">
						<label class="col-sm-3 col-form-label required" for="password_lama"><?php echo lang('kata_sandi_lama'); ?></label>
						<div class="col-sm-9">
							<input type="password" name="password_lama" id="password_lama" class="form-control" data-validation="required">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label required" for="password"><?php echo lang('kata_sandi_baru'); ?></label>
						<div class="col-sm-9">
							<input type="password" name="password" id="password" class="form-control" data-validation="required|min-length:6">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-3 col-form-label required" for="konfirmasi"><?php echo lang('konfirmasi_kata_sandi_baru'); ?></label>
						<div class="col-sm-9">
							<input type="password" name="konfirmasi" id="konfirmasi" class="form-control" data-validation="required|equal:password">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-9 offset-sm-3">
							<button type="submit" class="btn btn-info"><?php echo lang('ubah_kata_sandi'); ?></button>
						</div>
					</div>
				</form>
			</div>
			<div class="col-sm-3 d-none d-sm-block">
				<?php echo include_view('account/list'); ?> 
			</div>
		</div>
	</div>
</div> 
<script type="text/javascript">
function toHome() {
	window.location = base_url + 'home';
}
</script>