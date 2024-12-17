<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb(); ?>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body">
	<div class="main-container">
		<form id="form-command" action="<?php echo base_url('settings/web_setting/save'); ?>" data-callback="reload" method="post" data-submit="ajax" class="tab-app">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true"><?php echo lang('umum'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="true"><?php echo lang('smtp'); ?></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="template-tab" data-toggle="tab" href="#template" role="tab" aria-controls="template" aria-selected="true"><?php echo lang('templat'); ?></a>
				</li>
			</ul>
			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label required" for="title"><?php echo lang('nama_aplikasi'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="title" id="title" class="form-control" autocomplete="off" data-validation="required|min-length:3" value="<?php echo setting('title'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="deskripsi"><?php echo lang('deskripsi'); ?></label>
								<div class="col-sm-9">
									<textarea type="text" name="deskripsi" id="deskripsi" class="form-control" autocomplete="off" rows="4"><?php echo setting('deskripsi'); ?></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label required" for="company"><?php echo lang('nama_perusahaan'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="company" id="company" class="form-control" autocomplete="off" data-validation="required|min-length:3" value="<?php echo setting('company'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label required" for="alamat_perusahaan"><?php echo lang('alamat_perusahaan'); ?></label>
								<div class="col-sm-9">
									<textarea name="alamat_perusahaan" id="alamat_perusahaan" class="form-control" data-validation="required" rows="4"><?php echo setting('alamat_perusahaan'); ?></textarea>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="telp_perusahaan"><?php echo lang('no_telp_perusahaan'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="telp_perusahaan" id="telp_perusahaan" class="form-control" autocomplete="off" data-validation="phone" value="<?php echo setting('telp_perusahaan'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="faks_perusahaan"><?php echo lang('no_faks_perusahaan'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="faks_perusahaan" id="faks_perusahaan" class="form-control" autocomplete="off" data-validation="phone" value="<?php echo setting('faks_perusahaan'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="masa_aktif_password"><?php echo lang('masa_aktif_kata_sandi'); ?></label>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" name="masa_aktif_password" id="masa_aktif_password" class="form-control" autocomplete="off" data-validation="number" value="<?php echo setting('masa_aktif_password'); ?>">
										<div class="input-group-append">
											<span class="input-group-text"><?php echo lang('hari'); ?></span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="jumlah_history_password"><?php echo lang('batas_riwayat_kata_sandi'); ?></label>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" name="jumlah_history_password" id="jumlah_history_password" class="form-control" autocomplete="off" data-validation="number" value="<?php echo setting('jumlah_history_password'); ?>">
										<div class="input-group-append">
											<span class="input-group-text"><?php echo lang('kali'); ?></span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="jumlah_salah_password"><?php echo lang('batas_kesalahan_kata_sandi'); ?></label>
								<div class="col-sm-4">
									<div class="input-group">
										<input type="text" name="jumlah_salah_password" id="jumlah_salah_password" class="form-control" autocomplete="off" data-validation="number" value="<?php echo setting('jumlah_salah_password'); ?>">
										<div class="input-group-append">
											<span class="input-group-text"><?php echo lang('kali'); ?></span>
										</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('masuk_tunggal'); ?><small><?php echo lang('satu_akun_hanya_bisa_masuk_di_satu_perangkat'); ?></small></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="single_login" value="1"<?php if(setting('single_login') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('tampilkan_query_untuk_semua_pengguna'); ?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="query" value="1"<?php if(setting('query') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label">Web Socket</label>
								<div class="col-sm-2 col-4">
									<label class="switch">
										<input type="checkbox" name="websocket" value="1"<?php if(setting('websocket') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
								<div class="col-sm-7 col-8">
									<input type="text" name="ws_server" id="ws_server" class="form-control" autocomplete="off" data-validation="" placeholder="<?php echo lang('server'); ?>" value="<?php echo setting('ws_server'); ?>" aria-label="<?php echo lang('server'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('email_pemberitahuan'); ?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="email_notification" value="1"<?php if(setting('email_notification') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('log'); ?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="log_aktif" value="1"<?php if(setting('log_aktif') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="logo"><?php echo lang('logo_perusahaan'); ?></label>
								<div class="col-sm-9">
									<div class="image-upload">
										<div class="image-content">
											<img src="<?php echo base_url(dir_upload('setting').setting('logo_perusahaan')); ?>" alt="Logo Perusahaan" data-action="<?php echo base_url('upload/image/300/75'); ?>">
											<input type="hidden" name="logo_perusahaan" data-validation="image">
										</div>
										<div class="image-description"><?php echo lang('rekomendasi_ukuran');?> 300 x 75 (px)</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="logo"><?php echo lang('logo_aplikasi'); ?></label>
								<div class="col-sm-9">
									<div class="image-upload">
										<div class="image-content">
											<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="Logo Aplikasi" data-action="<?php echo base_url('upload/image/400/100'); ?>">
											<input type="hidden" name="logo" data-validation="image">
										</div>
										<div class="image-description"><?php echo lang('rekomendasi_ukuran');?> 400 x 100 (px)</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="favicon"><?php echo lang('ikon_aplikasi') ?></label>
								<div class="col-sm-9">
									<div class="image-upload small">
										<div class="image-content">
											<img src="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" alt="Favicon" data-action="<?php echo base_url('upload/image/100/100'); ?>">
											<input type="hidden" name="favicon" data-validation="image">
										</div>
										<div class="image-description">100 x 100 (px)</div>
									</div>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('tampilkan_warna_sebenarnya');?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="logo_true_color" value="1"<?php if(setting('logo_true_color') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="fileupload_mimes"><?php echo lang('unggahan_berkas_yang_diizinkan'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="fileupload_mimes" id="fileupload_mimes" class="form-control tags" autocomplete="off" value="<?php echo setting('fileupload_mimes'); ?>">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="email" role="tabpanel" aria-labelledby="email-tab">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="smtp_server"><?php echo lang('server'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="smtp_server" id="smtp_server" class="form-control" autocomplete="off" value="<?php echo setting('smtp_server'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="smtp_port"><?php echo lang('port'); ?></label>
								<div class="col-sm-4">
									<input type="text" name="smtp_port" id="smtp_port" class="form-control" autocomplete="off" data-validation="number" value="<?php echo setting('smtp_port'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label required" for="smtp_email"><?php echo lang('email'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="smtp_email" id="smtp_email" class="form-control" autocomplete="off" data-validation="required|email" value="<?php echo setting('smtp_email'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="smtp_password"><?php echo lang('kata_sandi'); ?></label>
								<div class="col-sm-9">
									<input type="password" name="smtp_password" id="smtp_password" class="form-control" autocomplete="off" data-validation="min-length:4" value="<?php echo setting('smtp_password'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="alias_email"><?php echo lang('email_alias'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="alias_email" id="alias_email" class="form-control" autocomplete="off" data-validation="email" value="<?php echo setting('alias_email'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="nama_alias_email"><?php echo lang('nama_alias'); ?></label>
								<div class="col-sm-9">
									<input type="text" name="nama_alias_email" id="nama_alias_email" class="form-control" autocomplete="off" value="<?php echo setting('nama_alias_email'); ?>">
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<p><?php echo lang('label_kirim_email'); ?></p>
							<button type="button" class="btn btn-success btn-input" data-id="0" aria-label="<?php echo lang('kirim_email'); ?>"><i class="fa-envelope"></i><?php echo lang('kirim_email'); ?></button>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" id="template" role="tabpanel" aria-labelledby="template-tab">
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="ukuran_tampilan"><?php echo lang('ukuran_tampilan'); ?></label>
								<div class="col-sm-9">
									<select name="ukuran_tampilan" id="ukuran_tampilan" class="custom-select select2 infinity">
										<option value="normal"<?php if(setting('ukuran_tampilan') == 'normal') echo ' selected'; ?>><?php echo lang('normal'); ?></option>
										<option value="small"<?php if(setting('ukuran_tampilan') == 'small') echo ' selected'; ?>><?php echo lang('kecil'); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="tipe_menu"><?php echo lang('model_menu'); ?></label>
								<div class="col-sm-9">
									<select name="tipe_menu" id="tipe_menu" class="custom-select select2 infinity">
										<option value="menubar"<?php if(setting('tipe_menu') == 'menubar') echo ' selected'; ?>>Menubar</option>
										<option value="sidebar"<?php if(setting('tipe_menu') == 'sidebar') echo ' selected'; ?>>Sidebar</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_dropdown"><?php echo lang('warna_dropdown'); ?></label>
								<div class="col-sm-9">
									<select name="warna_dropdown" id="warna_dropdown" class="custom-select select2 infinity">
										<option value="d-light-grey"<?php if(setting('warna_dropdown') == 'd-light-grey') echo ' selected'; ?>><?php echo lang('abu_abu'); ?></option>
										<option value="d-white"<?php if(setting('warna_dropdown') == 'd-white') echo ' selected'; ?>><?php echo lang('putih'); ?></option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="fa_icon"><?php echo lang('ikon'); ?></label>
								<div class="col-sm-9">
									<select name="fa_icon" id="fa_icon" class="custom-select select2 infinity">
										<option value="fontawesome.solid"<?php if(setting('fa_icon') == 'fontawesome.solid') echo ' selected'; ?>>Solid</option>
										<option value="fontawesome.regular"<?php if(setting('fa_icon') == 'fontawesome.regular') echo ' selected'; ?>>Regular</option>
										<option value="fontawesome.light"<?php if(setting('fa_icon') == 'fontawesome.light') echo ' selected'; ?>>Light</option>
										<option value="fontawesome.duotune"<?php if(setting('fa_icon') == 'fontawesome.duotune') echo ' selected'; ?>>Duotune</option>
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('kostum_templat'); ?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="custom_template" value="1"<?php if(setting('custom_template') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_primary"><?php echo lang('warna_primary'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_primary" id="warna_primary" class="form-control" autocomplete="off" value="<?php echo setting('warna_primary'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_primary_hover"><?php echo lang('warna_primary_hover'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_primary_hover" id="warna_primary_hover" class="form-control" autocomplete="off" value="<?php echo setting('warna_primary_hover'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_text_header"><?php echo lang('warna_teks_header'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_text_header" id="warna_text_header" class="form-control" autocomplete="off" value="<?php echo setting('warna_text_header'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_secondary"><?php echo lang('warna_secondary'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_secondary" id="warna_secondary" class="form-control" autocomplete="off" value="<?php echo setting('warna_secondary'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_border"><?php echo lang('warna_secondary_hover'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_border" id="warna_border" class="form-control" autocomplete="off" value="<?php echo setting('warna_border'); ?>">
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label" for="warna_notifikasi"><?php echo lang('warna_badge_pemberitahuan'); ?></label>
								<div class="col-sm-3">
									<input type="color" name="warna_notifikasi" id="warna_notifikasi" class="form-control" autocomplete="off" value="<?php echo setting('warna_notifikasi'); ?>">
								</div>
							</div>
							<?php if(ENVIRONMENT == 'development' && user('id_group') == 1) { ?>
							<div class="form-group row">
								<label class="col-sm-3 col-form-label"><?php echo lang('blur_data'); ?></label>
								<div class="col-sm-9">
									<label class="switch">
										<input type="checkbox" name="sensor_data" value="1"<?php if(setting('sensor_data') == '1') echo ' checked'; ?>>
										<span class="slider"></span>
									</label>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="col-sm-6 mt-3 mt-sm-0">
							<ul class="nav nav-tabs" id="cTab" role="tablist">
								<?php foreach(['primary','info','success','warning','danger'] as $k => $v) { ?>
								<li class="nav-item">
									<a class="nav-link<?php if($k == 0) echo ' active'; ?>" id="<?php  echo $v; ?>-tab" data-toggle="tab" href="#<?php  echo $v; ?>" role="tab" aria-controls="<?php  echo $v; ?>" aria-selected="true"><?php echo ucwords($v); ?></a>
								</li>
								<?php } ?>
							</ul>
							<div class="tab-content" id="cTabContent">
								<?php foreach(['primary','info','success','warning','danger'] as $k => $v) { ?>
								<div class="tab-pane fade<?php if($k == 0) echo ' show active'; ?>" id="<?php echo $v; ?>" role="tabpanel" aria-labelledby="<?php echo $v; ?>-tab">
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="bg_btn_<?php echo $v; ?>">Background</label>
										<div class="col-sm-4">
											<input type="color" name="bg_btn_<?php echo $v; ?>" id="bg_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('bg_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="border_btn_<?php echo $v; ?>">Border</label>
										<div class="col-sm-4">
											<input type="color" name="border_btn_<?php echo $v; ?>" id="border_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('border_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="color_btn_<?php echo $v; ?>">Color</label>
										<div class="col-sm-4">
											<input type="color" name="color_btn_<?php echo $v; ?>" id="color_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('color_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12 col-form-label font-weight-bold text-primary">On Hover</label>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="hover_bg_btn_<?php echo $v; ?>">Background</label>
										<div class="col-sm-4">
											<input type="color" name="hover_bg_btn_<?php echo $v; ?>" id="hover_bg_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('hover_bg_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="hover_border_btn_<?php echo $v; ?>">Border</label>
										<div class="col-sm-4">
											<input type="color" name="hover_border_btn_<?php echo $v; ?>" id="hover_border_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('hover_border_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="hover_color_btn_<?php echo $v; ?>">Color</label>
										<div class="col-sm-4">
											<input type="color" name="hover_color_btn_<?php echo $v; ?>" id="hover_color_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('hover_color_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-12 col-form-label font-weight-bold text-primary">On Focus</label>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="focus_bg_btn_<?php echo $v; ?>">Background</label>
										<div class="col-sm-4">
											<input type="color" name="focus_bg_btn_<?php echo $v; ?>" id="focus_bg_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('focus_bg_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="focus_border_btn_<?php echo $v; ?>">Border</label>
										<div class="col-sm-4">
											<input type="color" name="focus_border_btn_<?php echo $v; ?>" id="focus_border_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('focus_border_btn_'.$v); ?>">
										</div>
									</div>
									<div class="form-group row">
										<label class="col-sm-3 col-form-label" for="focus_color_btn_<?php echo $v; ?>">Color</label>
										<div class="col-sm-4">
											<input type="color" name="focus_color_btn_<?php echo $v; ?>" id="focus_color_btn_<?php echo $v; ?>" class="form-control" autocomplete="off" value="<?php echo setting('focus_color_btn_'.$v); ?>">
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-footer">
					<div class="row">
						<div class="col-12">
							<button type="submit" class="btn btn-info"><?php echo lang('simpan_perubahan'); ?></button>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true" id="modal-form">
	<div class="modal-dialog ">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"><?php echo lang('kirim_email'); ?></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<form method="post" action="<?php echo base_url('settings/web_setting/check_email'); ?>" id="form">
					<div class="form-group row">
						<div class="col-sm-12">
							<input type="text" name="email" id="email" autocomplete="off" class="form-control" data-validation="required|email" placeholder="<?php echo lang('email_tujuan'); ?>" aria-label="<?php echo lang('email_tujuan'); ?>">
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-12">
							<textarea rows="4" name="message" id="message" class="form-control" data-validation="required" placeholder="<?php echo lang('pesan'); ?>" aria-label="<?php echo lang('pesan'); ?>"></textarea>
						</div>
					</div>
					<div class="form-group row">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-info"><?php echo lang('kirim_email'); ?></button>
							<button type="reset" class="btn btn-secondary"><?php echo lang('batal'); ?></button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>