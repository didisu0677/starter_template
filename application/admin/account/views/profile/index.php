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
				<form id="form-command" action="<?php echo base_url('account/profile/save'); ?>" data-callback="reload" method="post" data-submit="ajax">
					<input type="hidden" name="id" value="<?php echo user('id'); ?>">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label required" for="nama"><?php echo lang('nama'); ?></label>
						<div class="col-sm-10">
							<input type="text" name="nama" id="nama" class="form-control" autocomplete="off" data-validation="required|min-length:3" value="<?php echo user('nama'); ?>">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label required" for="email"><?php echo lang('email');?></label>
						<div class="col-sm-10">
							<input type="text" name="email" id="email" class="form-control" autocomplete="off" data-validation="required|email" value="<?php echo user('email'); ?>">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label required" for="telepon"><?php echo lang('telepon'); ?></label>
						<div class="col-sm-10">
							<input type="text" name="telepon" id="telepon" class="form-control" autocomplete="off" data-validation="min-length:6" value="<?php echo user('telepon'); ?>">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="foto"><?php echo lang('foto'); ?></label>
						<div class="col-sm-10">
							<div class="image-upload">
								<div class="image-content">
									<img src="<?php echo user('foto'); ?>" alt="" data-action="<?php echo base_url('upload/image/200/200/force'); ?>">
									<input type="hidden" name="foto" data-validation="image">
								</div>
								<div class="image-description"><?php echo lang('rekomendasi_ukuran'); ?> 200 x 200 (px)</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-10 offset-sm-2">
							<button type="submit" class="btn btn-info"><?php echo lang('simpan_perubahan'); ?></button>
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