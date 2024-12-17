<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="float-right">
			<button type="button" class="btn btn-info btn-sm cInfo" data-target="<?php echo base_url('command/help'); ?>"><i class="fa-question-circle"></i>Bantuan</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
		<div class="alert alert-info alert-dismissible fade show" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
			<div class="alert-icon">
				<i class="fa-info"></i>
			</div>
			<div class="alert-description">
				Menu ini untuk men-generate module, menu standar, dan table di database, dengan catatan permission folder module/backend harus 777. Menu ini hanya bisa di akses dengan role root atau user group yang ber-ID 1 saja. Untuk menghilangkan menu ini silahkan ganti <strong>ENVIRONMENT</strong> di index.php dari development menjadi production.
			</div>
		</div>
		<form id="form-command" action="<?php echo base_url('command/process'); ?>" method="post" data-submit="ajax">
			<div class="form-group row">
				<div class="col-12">
					<textarea class="form-control code" rows="12" name="command" data-validation="required"></textarea>
				</div>
			</div>
			<div class="form-group row">
				<div class="col-12">
					<button type="submit" class="btn btn-info">Proses</button>
				</div>
			</div>
		</form>
	</div>
</div>