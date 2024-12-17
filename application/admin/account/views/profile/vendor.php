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

				<form id="form-command" action="<?php echo base_url('account/profile/save_vendor'); ?>" data-callback="reload" method="post" data-submit="ajax" class="tab-app">

					<div class="alert alert-info">

						<?php echo lang('info_edit_vendor'); ?>

					</div>

					<?php

					if($laporan_kunjungan == 9) alert(lang('setelah_dilakukan_kunjungan_anda_dinyatakan_tidak_lolos_verifikasi'),'danger');
					else if($status_drm == 9) alert(lang('perusahaan_anda_tidak_lolos_drm'),'danger');

					col_init(3,9);

					label(strtoupper(lang('informasi_umum')),'mb-2 mt-2');

					input('hidden','id','id','',$id);

					input('text',lang('kode_rekanan'),'kode_rekanan','required|unique|max-length:30',$kode_rekanan,'disabled');

					if($jenis_rekanan == 1) {

						input('text',lang('nama_perusahaan'),'nama','required|max-length:100',$nama,'disabled');

						input('text',lang('npwp_perusahaan'),'npwp','required|max-length:30',$npwp,'disabled');

					} else {

						input('text',lang('nama_lengkap'),'nama','required|max-length:100',$nama,'disabled');

						input('text',lang('npwp'),'npwp','required|max-length:30',$npwp,'disabled');								

					}

					select2(lang('kategori_rekanan'),'id_kategori_rekanan[]','required',$kategori_rekanan,'id','kategori',json_decode($id_kategori_rekanan,true),'multiple');

					if($jenis_rekanan == 1) {

						select2(lang('bentuk_badan_usaha'),'id_bentuk_badan_usaha','required',$bentuk_badan_usaha,'id','bentuk_badan_usaha',$id_bentuk_badan_usaha);

						select2(lang('status_perusahaan'),'id_status_perusahaan','required',$status_perusahaan,'id','status_perusahaan',$id_status_perusahaan);

					} else {

						?>

						<div class="form-group row">

							<label class="col-form-label col-sm-3 required" for="no_identitas"><?php echo lang('no_identitas'); ?></label>

							<div class="col-sm-5">

								<input type="text" name="no_identitas" id="no_identitas" class="form-control" autocomplete="off" data-validation="required|max-length:50" value="<?php echo $no_identitas; ?>" />

							</div>

							<div class="col-sm-4 mt-2 mt-sm-0">

								<input type="text" name="tanggal_berakhir_identitas" id="tanggal_berakhir_identitas" autocomplete="off" class="form-control dp" data-validation="required" placeholder="<?php echo lang('berlaku_sampai'); ?>" value="<?php echo c_date($tanggal_berakhir_identitas); ?>">

							</div>

						</div>

						<?php

					}

					select2(lang('kualifikasi'),'id_kualifikasi','required',$kualifikasi,'id','kualifikasi',$id_kualifikasi);

					select2(lang('asosiasi'),'id_asosiasi','required',$asosiasi,'id','asosiasi',$id_asosiasi);

					select2(lang('mendaftar_di_unit'),'id_unit_daftar','required',$unit,'id','unit',$id_unit_daftar);



					label(strtoupper(lang('alamat_lengkap')),'mb-2 mt-2');

					textarea(lang('alamat'),'alamat','required',$alamat);

					col_init(3,5);

					select2(lang('negara'),'id_negara','required',$negara,'id','nama',$id_negara);

					?>

					<div class="form-group row">

						<label class="col-form-label col-sm-3 required" for="id_provinsi"><?php echo lang('provinsi'); ?></label>

						<div class="col-sm-5">

							<select name="id_provinsi" id="id_provinsi" class="form-control select2" data-validation="required">

								<option value=""></option>

								<?php foreach($provinsi as $p) { ?>

								<option value="<?php echo $p['id']; ?>"<?php if($p['id'] == $id_provinsi) echo ' selected'; ?>><?php echo $p['nama']; ?></option>

								<?php } ?>

								<option value="999"<?php if($id_provinsi == 999) echo ' selected'; ?>><?php echo lang('lainnya'); ?></option>

							</select>

						</div>

						<div class="col-sm-4 mt-2 mt-sm-0<?php if($id_provinsi != 999) echo ' hidden'; ?>">

							<input type="text" name="nama_provinsi" id="nama_provinsi" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="<?php echo $nama_provinsi; ?>" placeholder="<?php echo lang('nama_provinsi'); ?>">

						</div>

					</div>

					<div class="form-group row">

						<label class="col-form-label col-sm-3 required" for="id_kota"><?php echo lang('kota'); ?></label>

						<div class="col-sm-5">

							<select name="id_kota" id="id_kota" class="form-control select2" data-validation="required">

								<option value=""></option>

								<?php foreach($kota as $p) { ?>

								<option value="<?php echo $p['id']; ?>"<?php if($p['id'] == $id_kota) echo ' selected'; ?>><?php echo $p['nama']; ?></option>

								<?php } ?>

								<option value="999"<?php if($id_kota == 999) echo ' selected'; ?>><?php echo lang('lainnya'); ?></option>

							</select>

						</div>

						<div class="col-sm-4 mt-2 mt-sm-0<?php if($id_kota != 999) echo ' hidden'; ?>">

							<input type="text" name="nama_kota" id="nama_kota" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="<?php echo $nama_kota; ?>" placeholder="<?php echo lang('nama_kota'); ?>">

						</div>

					</div>

					<div class="form-group row">

						<label class="col-form-label col-sm-3 required" for="id_kecamatan"><?php echo lang('kecamatan'); ?></label>

						<div class="col-sm-5">

							<select name="id_kecamatan" id="id_kecamatan" class="form-control select2" data-validation="required">

								<option value=""></option>

								<?php foreach($kecamatan as $p) { ?>

								<option value="<?php echo $p['id']; ?>"<?php if($p['id'] == $id_kecamatan) echo ' selected'; ?>><?php echo $p['nama']; ?></option>

								<?php } ?>

								<option value="999"<?php if($id_kecamatan == 999) echo ' selected'; ?>><?php echo lang('lainnya'); ?></option>

							</select>

						</div>

						<div class="col-sm-4 mt-2 mt-sm-0<?php if($id_kecamatan != 999) echo ' hidden'; ?>">

							<input type="text" name="nama_kecamatan" id="nama_kecamatan" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="<?php echo $nama_kecamatan; ?>" placeholder="<?php echo lang('nama_kecamatan'); ?>">

						</div>

					</div>

					<div class="form-group row">

						<label class="col-form-label col-sm-3 required" for="id_kelurahan"><?php echo lang('kelurahan'); ?></label>

						<div class="col-sm-5">

							<select name="id_kelurahan" id="id_kelurahan" class="form-control select2" data-validation="required">

								<option value=""></option>

								<?php foreach($kelurahan as $p) { ?>

								<option value="<?php echo $p['id']; ?>"<?php if($p['id'] == $id_kelurahan) echo ' selected'; ?>><?php echo $p['nama']; ?></option>

								<?php } ?>

								<option value="999"<?php if($id_kelurahan == 999) echo ' selected'; ?>><?php echo lang('lainnya'); ?></option>

							</select>

						</div>

						<div class="col-sm-4 mt-2 mt-sm-0<?php if($id_kelurahan != 999) echo ' hidden'; ?>">

							<input type="text" name="nama_kelurahan" id="nama_kelurahan" autocomplete="off" class="form-control" data-validation="required|max-length:50" value="<?php echo $nama_kelurahan; ?>" placeholder="<?php echo lang('nama_kelurahan'); ?>">

						</div>

					</div>

					<?php

					input('text',lang('kode_pos'),'kode_pos','required|length:5|number',$kode_pos);

					col_init(3,9);

					input('text',lang('no_telepon'),'no_telepon','required|phone|max-length:30',$no_telepon);

					input('text',lang('no_fax'),'no_fax','required|phone|max-length:30',$no_fax);

					input('text',lang('email'),'email','required|email|max-length:50',$email);



					label(strtoupper(lang('kontak_person')),'mb-2 mt-2');

					input('text',lang('nama'),'nama_cp','required|max-length:100',$nama_cp);

					input('text',lang('hp'),'hp_cp','required|phone|max-length:30',$hp_cp);

					input('text',lang('email'),'email_cp','required|email|unique|max-length:50',$email_cp,'disabled');

					if($laporan_kunjungan == 9) {
						?>
					<div class="form-group row">
						<div class="col-sm-9 offset-sm-3">
							<div class="custom-checkbox custom-control custom-control-inline">
								<input class="custom-control-input" type="checkbox" id="kunjungan_ulang" name="kunjungan_ulang" value="1">
								<label class="custom-control-label" for="kunjungan_ulang"><?php echo lang('ajukan_kunjungan_ulang'); ?></label>
							</div>
						</div>
					</div>
						<?php
					} else if($status_drm == 9) {
						?>
					<div class="form-group row">
						<div class="col-sm-9 offset-sm-3">
							<div class="custom-checkbox custom-control custom-control-inline">
								<input class="custom-control-input" type="checkbox" id="drm_ulang" name="drm_ulang" value="1">
								<label class="custom-control-label" for="drm_ulang"><?php echo lang('ajukan_ulang'); ?></label>
							</div>
						</div>
					</div>
						<?php
					}

					form_button(lang('simpan'));

					?>

				</form>

			</div>

			<div class="col-sm-3 d-none d-sm-block">

				<?php echo include_view('account/list'); ?> 

			</div>

		</div>

	</div>

</div>

<script>

$('#id_negara').change(function(){

	if($(this).val() != '101') {

		$('#id_provinsi').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');

	} else {

		$('#id_provinsi').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');

		readonly_ajax = false;

		$.getJSON(base_url + 'ajax/json/wilayah', function(data){

			var konten = '<option value=""></option>';

			$.each(data,function(d,v){

				konten += '<option value="'+v.id+'">'+v.nama+'</option>';

			});

			konten += '<option value="999">'+lang.lainnya+'</option>';

			$('#id_provinsi').html(konten).trigger('change');

			readonly_ajax = true;

		});

	}

});

$('#id_provinsi').change(function(){

	if($(this).val() != '' && $(this).val() != '0') {

		if($(this).val() == '999') {

			$('#nama_provinsi').parent().removeClass('hidden');

			$('#nama_provinsi').val('');

			$('#id_kota').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');

		} else {

			$('#nama_provinsi').parent().addClass('hidden');

			$('#nama_provinsi').val($(this).find(':selected').text());

			$('#id_kota').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');

			readonly_ajax = false;

			$.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){

				var konten = '<option value=""></option>';

				$.each(data,function(d,v){

					konten += '<option value="'+v.id+'">'+v.nama+'</option>';

				});

				konten += '<option value="999">'+lang.lainnya+'</option>';

				$('#id_kota').html(konten).trigger('change');

				readonly_ajax = true;

			});

		}

	} else {

		$('#nama_provinsi').parent().addClass('hidden');

		$('#nama_provinsi').val($(this).find(':selected').text());

		$('#id_kota').html('<option value=""></option>').trigger('change');

	}

});

$('#id_kota').change(function(){

	if($(this).val() != '' && $(this).val() != '0') {

		if($(this).val() == '999') {

			$('#nama_kota').parent().removeClass('hidden');

			$('#nama_kota').val('');

			$('#id_kecamatan').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');

		} else {

			$('#nama_kota').parent().addClass('hidden');

			$('#nama_kota').val($(this).find(':selected').text());

			$('#id_kecamatan').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');

			readonly_ajax = false;

			$.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){

				var konten = '<option value=""></option>';

				$.each(data,function(d,v){

					konten += '<option value="'+v.id+'">'+v.nama+'</option>';

				});

				konten += '<option value="999">'+lang.lainnya+'</option>';

				$('#id_kecamatan').html(konten).trigger('change');

				readonly_ajax = true;

			});

		}

	} else {

		$('#nama_kota').parent().addClass('hidden');

		$('#nama_kota').val($(this).find(':selected').text());

		$('#id_kecamatan').html('<option value=""></option>').trigger('change');

	}

});

$('#id_kecamatan').change(function(){

	if($(this).val() != '' && $(this).val() != '0') {

		if($(this).val() == '999') {

			$('#nama_kecamatan').parent().removeClass('hidden');

			$('#nama_kecamatan').val('');

			$('#id_kelurahan').html('<option value=""></option><option value="999">'+lang.lainnya+'</option>').trigger('change');

		} else {

			$('#nama_kecamatan').parent().addClass('hidden');

			$('#nama_kecamatan').val($(this).find(':selected').text());

			$('#id_kelurahan').html('<option value="0">'+lang.mohon_tunggu+'</option>').trigger('change');

			readonly_ajax = false;

			$.getJSON(base_url + 'ajax/json/wilayah/' + $(this).val(), function(data){

				var konten = '<option value=""></option>';

				$.each(data,function(d,v){

					konten += '<option value="'+v.id+'">'+v.nama+'</option>';

				});

				konten += '<option value="999">'+lang.lainnya+'</option>';

				$('#id_kelurahan').html(konten).trigger('change');

				readonly_ajax = true;

			});

		}

	} else {

		$('#nama_kecamatan').parent().addClass('hidden');

		$('#nama_kecamatan').val($(this).find(':selected').text());

		$('#id_kelurahan').html('<option value=""></option>').trigger('change');

	}

});

$('#id_kelurahan').change(function(){

	if($(this).val() == '999') {

		$('#nama_kelurahan').parent().removeClass('hidden');

		$('#nama_kelurahan').val('');

	} else {

		$('#nama_kelurahan').parent().addClass('hidden');

		$('#nama_kelurahan').val($(this).find(':selected').text());

	}

});

$(document).on('click','.btn-remove',function(){

	$(this).closest('.form-group').remove();

});

</script>