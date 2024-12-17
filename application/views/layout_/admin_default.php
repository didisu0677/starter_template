<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="appname" content="<?php echo setting('title'); ?>">
<meta name="applang" content="<?php echo setting('language'); ?>">
<meta name="description" content="<?php echo setting('deskripsi'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?php echo csrf_token(); ?>">
<title><?php echo setting('title') . ' &raquo; ' . $title; ?></title>
<link rel="shortcut icon" href="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" />
<?php
Asset::css('bootstrap.min.css', true);
Asset::css('bootstrap.color.min.css', true);
Asset::css('bootstrap.tagsinput.css', true);
Asset::css('daterangepicker.css', true);
Asset::css('roboto.css', true);
if(setting('fa_icon')) {
	Asset::css(setting('fa_icon').'.css', true);
} else {
	Asset::css('fontawesome.solid.css', true);
}
Asset::css('select2.min.css', true);
Asset::css('iconpicker.css', true);
Asset::css('jquery.contextMenu.min.css', true);
Asset::css('jquery.toast.min.css', true);
Asset::css('ion.rangeSlider.css', true);
Asset::css('style.css', true);
if(setting('ukuran_tampilan') == 'small') {
	Asset::css('small-style.css', true);
}
if(setting('custom_template') && file_exists(FCPATH . 'assets/css/template.css')) {
	Asset::css('template.css', true);
}
echo Asset::render();
?>
<?php echo $css_content; ?>
</head>
<body class="<?php
	if(setting('tipe_menu') == 'menubar') echo 'app-menubar ';
	else echo 'app-sidebar ';
	if(setting('sensor_data') == '1') echo 'censored-data ';
	echo setting('warna_dropdown').' ';
	if((get_cookie('menu-minimize') && get_cookie('menu-minimize') == 'minimize') || setting('tipe_menu') == 'menubar') echo 'body-minimize';
	?>"<?php echo flash_body(); ?> data-size="<?php echo setting('ukuran_tampilan'); ?>">
	<nav class="navbar header-navbar">
		<div class="navbar-wrapper">
			<div class="navbar-header">
				<ul class="nav navbar-nav">
					<li class="nav-item mobile-menu hidden-md-up float-xs-left">
						<a href="#" class="nav-link nav-menu-main menu-toggle is-active">
							<i class="fa-bars font-large-1"></i>
						</a>
					</li>
					<li class="nav-item">
						<a href="<?php echo base_url('home'); ?>" class="navbar-brand<?php if(setting('logo_true_color')) echo ' true-color'; ?>">
							<img alt="<?php echo setting('title'); ?>" src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" class="brand-logo">
							<img alt="<?php echo setting('title'); ?>" src="<?php echo base_url(dir_upload('setting').setting('favicon')); ?>" class="brand-logo-min">
						</a>
					</li>
					<li class="nav-item hidden-md-up float-xs-right">
						<?php if(setting('chatting')) { ?>
						<a href="#" class="btn-nav-chat nav-link open-navbar-container d-inline-block">
							<i class="fa-comment-alt font-large-1"></i>
							<?php if($jml_pesan) { ?>
								<span class="tag tag-pill tag-up tag-notification"><?php echo $jml_pesan < 10 ? $jml_pesan : '9+'; ?></span>
							<?php } ?>
						</a>
						<?php } ?>
						<a href="#" id="nav-menu-more" class="nav-link open-navbar-container d-inline-block">
							<i class="fa-ellipsis-v font-large-1"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="navbar-container">
				<div id="navbar-mobile" class="navbar-toggleable-sm collapse">
					<ul class="nav navbar-nav navbar-container-left" id="menu-bar">
						<li class="nav-item nav-toggle<?php if(setting('tipe_menu') == 'menubar') echo ' hidden'; ?>">
							<a href="#" class="nav-link nav-menu-main menu-toggle" id="btn-minimize">
								<i class="fa-bars font-large-1"></i>
							</a>
						</li>
						<?php if(setting('tipe_menu') == 'menubar') { ?>
						<?php foreach($menu_access['menu'][0] as $m) { ?>
						<li class="nav-item menu-bar">
							<?php if(isset($menu_access['menu'][$m->id]) && count($menu_access['menu'][$m->id]) > 0) { ?>
							<a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle<?php if($m->id == $menu_access['active_l1']) echo ' active'; ?>"><?php echo lang($m->target,$m->nama); ?></a>
							<ul class="dropdown-menu">
								<?php foreach($menu_access['menu'][$m->id] as $mc1) { ?>
								<?php if(isset($menu_access['menu'][$mc1->id]) && count($menu_access['menu'][$mc1->id]) > 0) { ?>
								<li class="dropdown-submenu">
									<a href="#" class="dropdown-item dropdown-toggle<?php if($mc1->id == $menu_access['active_l2']) echo ' active'; ?>"><?php echo lang($mc1->target,$mc1->nama); ?></a>
									<ul class="dropdown-menu">
										<?php foreach($menu_access['menu'][$mc1->id] as $mc2) { ?>
										<?php if(isset($menu_access['menu'][$mc2->id]) && count($menu_access['menu'][$mc2->id]) > 0) { ?>
										<li class="dropdown-submenu">
											<a href="#" class="dropdown-item dropdown-toggle<?php if($mc2->id == $menu_access['active_l3']) echo ' active'; ?>"><?php echo lang($mc2->target,$mc2->nama); ?></a>
											<ul class="dropdown-menu">
												<?php foreach($menu_access['menu'][$mc2->id] as $mc3) { ?>
												<a href="<?php echo base_url($m->target.'/'.$mc3->target); ?>" class="dropdown-item<?php if($mc3->id == $menu_access['active_l4']) echo ' active'; ?>" data-shortcut="<?php echo $mc3->shortcut; ?>"><?php echo lang($mc3->target,$mc3->nama); ?></a>
												<?php } ?>
											</ul>
										</li>
										<?php } else { ?>
										<li>
											<a href="<?php echo base_url($m->target.'/'.$mc2->target); ?>" class="dropdown-item<?php if($mc2->id == $menu_access['active_l3']) echo ' active'; ?>" data-shortcut="<?php echo $mc2->shortcut; ?>"><?php echo lang($mc2->target,$mc2->nama); ?></a>
										</li>
										<?php } ?>
										<?php } ?>
									</ul>
								</li>
								<?php } else { ?>
								<li>
									<a href="<?php echo base_url($m->target.'/'.$mc1->target); ?>" class="dropdown-item<?php if($mc1->id == $menu_access['active_l2']) echo ' active'; ?>" data-shortcut="<?php echo $mc1->shortcut; ?>"><?php echo lang($mc1->target,$mc1->nama); ?></a>
								</li>
								<?php } ?>
								<?php } ?>
							</ul>
							<?php } else { ?>
							<a href="<?php echo base_url($m->target); ?>" class="nav-link<?php if($m->id == $menu_access['active_l1']) echo ' active'; ?>" data-shortcut="<?php echo $m->shortcut; ?>"><?php echo lang($m->target,$m->nama); ?></a>
							<?php } ?>
						</li>
						<?php } ?>
						<?php } ?>
					</ul>
					<ul class="nav navbar-nav float-xs-right pr-md-2">
						<li class="nav-item nav-btn">
							<div class="btn-group">
								<button type="button" class="btn btn-sm btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									<img src="<?php echo base_url('assets/lang/'.setting('language').'/_flag.png'); ?>" width="16" class="mr-2 mt--1"> <?php echo strtoupper(setting('language')); ?> 
								</button>
								<div class="dropdown-menu dropdown-menu-right dropdown-auto">
									<?php foreach($bahasa as $b) { ?>
									<button class="dropdown-item border-0 change-language<?php if($b == setting('language')) echo ' current'; ?>" type="button" data-value="<?php echo $b; ?>">
										<img src="<?php echo base_url('assets/lang/'.$b.'/_flag.png'); ?>" width="16" class="mr-2 mt--1"> <?php echo strtoupper($b); ?> 
									</button>
									<?php } ?>
								</div>
							</div>
						</li>
						<?php if(setting('query') || user('id_group') == 1) { ?>
						<li class="nav-item">
							<a href="<?php echo base_url('query'); ?>" class="nav-link nav-link-label" data-toggle="tooltip" data-placement="bottom" title="Query">
								<i class="fa-database"></i>
							</a>
						</li>
						<?php } if(ENVIRONMENT == 'development' && user('id_group') == 1) { ?>
						<li class="nav-item">
							<a href="<?php echo base_url('command'); ?>" class="nav-link nav-link-label" data-toggle="tooltip" data-placement="bottom" title="Command">
								<i class="fa-terminal"></i>
							</a>
						</li>
						<?php } if(setting('chatting')) { ?>
						<li class="nav-item">
							<a href="#" class="nav-link nav-link-label btn-nav-chat" data-toggle="tooltip" data-placement="bottom" title="<?php echo lang('obrolan'); ?>">
								<i class="fa-comment-alt"></i>
								<?php if($jml_pesan) { ?>
								<span class="tag tag-pill tag-up tag-notification"><?php echo $jml_pesan < 10 ? $jml_pesan : '9+'; ?></span>
								<?php } ?>
							</a>
						</li>
						<?php } ?>
						<li class="dropdown dropdown-notification nav-item">
							<a href="#" data-toggle="dropdown" class="nav-link nav-link-label" title="<?php echo lang('pemberitahuan'); ?>">
								<i class="fa-bell"></i>
								<?php if($notifikasi['count'] > 0) { ?>
								<span class="tag tag-pill tag-up tag-notification"><?php echo $notifikasi['count'] < 10 ? $notifikasi['count'] : '9+'; ?></span>
								<?php } ?>
							</a>
							<ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
								<li class="dropdown-menu-header">
									<h6 class="dropdown-header m-0 clearfix">
										<span class="grey darken-2 float-left"><?php echo lang('pemberitahuan'); ?></span>
										<?php if($notifikasi['count'] > 0) { ?>
										<span class="notification-tag tag float-xs-right m-0"><?php echo $notifikasi['count'].' '.lang('baru'); ?></span>
										<?php } ?>
									</h6>
								</li>
								<?php if(count($notifikasi['list']) == 0) { ?>
								<li class="dropdown-empty-notification">
									<i class="fa-bell icon"></i>
									<span><?php echo lang('tidak_ada_pemberitahuan'); ?></span>
								</li>
								<?php } else { ?>
								<div class="notification-list">
									<?php foreach($notifikasi['list'] as $l) { ?>
									<li>
										<a href="<?php echo base_url('home/notification/read?i='.$l['id'].'&l='.encode_string($l['notif_link'])); ?>" class="dropdown-item<?php if(!$l['is_read']) echo ' dark'; ?>">
											<div class="media">
												<div class="media-left">
													<i class="<?php echo $l['notif_icon']; ?> bg-<?php echo $l['notif_type']; ?>"></i>
												</div>
												<div class="media-body">
													<h4 class="<?php echo $l['notif_type']; ?>"><?php echo $l['title']; ?></h4>
													<p><?php echo $l['description']; ?></p>
													<small><?php echo timeago($l['notif_date']); ?></small>
												</div>
											</div>
										</a>
									</li>
									<?php } ?>
								</div>
								<li class="dropdown-menu-footer">
									<a href="<?php echo base_url('home/notification'); ?>" class="dropdown-item text-muted text-center link-notification"><?php echo lang('lihat_semua'); ?></a>
								</li>
								<?php } ?>
							</ul>
						</li>
						<li class="dropdown dropdown-user nav-item">
							<a href="#" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link">
								<span class="avatar">
									<img src="<?php echo user('foto'); ?>" alt="avatar">
								</span>
								<span class="user-name"><?php echo user('nama'); ?></span>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<h6 class="dropdown-header m-0"><?php echo lang('pengaturan_akun'); ?></h6>
								<a href="<?php echo base_url('account/profile'); ?>" class="dropdown-item">
									<i class="fa-user-edit"></i> <?php echo lang('profil'); ?> 
								</a>
								<?php if(user('id_vendor')) { ?>
								<a href="<?php echo base_url('account/dokumen'); ?>" class="dropdown-item">
									<i class="fa-file-alt"></i> <?php echo lang('dokumen'); ?> 
								</a>
								<?php } ?>
								<a href="<?php echo base_url('account/changepwd'); ?>" class="dropdown-item">
									<i class="fa-key"></i> <?php echo lang('ubah_kata_sandi'); ?> 
								</a>
								<div class="dropdown-divider"></div>
								<a href="<?php echo base_url('auth/logout'); ?>" class="dropdown-item">
									<i class="fa-sign-out-alt"></i> <?php echo lang('keluar'); ?> 
								</a>
							</div>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
	<div class="sidebar-panel" id="sidebar-panel"<?php if(get_cookie('sidebar_pos')) echo ' data-pos="'.get_cookie('sidebar_pos').'"'; ?>>
		<div class="sidebar-container">
			<ul class="sidebar-menu main-menu">
				<?php foreach($menu_access['menu'][0] as $m) { ?>
				<?php if(isset($menu_access['menu'][$m->id]) && count($menu_access['menu'][$m->id]) > 0) { ?>
					<li class="header">
						<span><?php echo lang($m->target,$m->nama); ?></span>
						<i class="fa-minus" data-toggle="tooltip" data-placement="right" title="<?php echo lang($m->target,$m->nama); ?>"></i>
					</li>
					<?php foreach($menu_access['menu'][$m->id] as $mc1) { ?>
						<?php if(isset($menu_access['menu'][$mc1->id]) && count($menu_access['menu'][$mc1->id]) > 0) { ?>
						<li class="has-sub<?php if($mc1->id == $menu_access['active_l2']) echo ' active'; ?>">
							<a href="#" class="<?php if($mc1->id == $menu_access['active_l2']) echo 'active'; ?>" title="<?php echo lang($mc1->target,$mc1->nama); ?>"><i class="<?php echo $mc1->icon; ?>"></i><span><?php echo lang($mc1->target,$mc1->nama); ?></span></a>
							<ul class="treeview-menu">
								<?php foreach($menu_access['menu'][$mc1->id] as $mc2) { ?>
									<?php if(isset($menu_access['menu'][$mc2->id]) && count($menu_access['menu'][$mc2->id]) > 0) { ?>
									<li class="has-sub<?php if($mc2->id == $menu_access['active_l3']) echo ' active'; ?>">
										<a href="#" class="<?php if($mc2->id == $menu_access['active_l3']) echo 'active'; ?>" title="<?php echo lang($mc2->target,$mc2->nama); ?>"><?php echo lang($mc2->target,$mc2->nama); ?></a>
										<ul class="treeview-menu">
											<?php foreach($menu_access['menu'][$mc2->id] as $mc3) { ?>
												<li><a href="<?php echo base_url($m->target.'/'.$mc3->target); ?>" class="<?php if($mc3->id == $menu_access['active_l4']) echo 'active'; ?>" data-shortcut="<?php echo $mc3->shortcut; ?>" title="<?php echo lang($mc3->target,$mc3->nama); ?>"><?php echo lang($mc3->target,$mc3->nama); ?></a></li>
											<?php } ?>
										</ul>
									</li>
									<?php } else { ?>
									<li><a href="<?php echo base_url($m->target.'/'.$mc2->target); ?>" class="<?php if($mc2->id == $menu_access['active_l3']) echo 'active'; ?>" data-shortcut="<?php echo $mc2->shortcut; ?>" title="<?php echo lang($mc2->target,$mc2->nama); ?>"><?php echo lang($mc2->target,$mc2->nama); ?></a></li>
									<?php } ?>
								<?php } ?>
							</ul>
						</li>
						<?php } else { ?>
						<li><a href="<?php echo base_url($m->target.'/'.$mc1->target); ?>" class="<?php if($mc1->id == $menu_access['active_l2']) echo 'active'; ?>" data-shortcut="<?php echo $mc1->shortcut; ?>" title="<?php echo lang($mc1->target,$mc1->nama); ?>"><i class="<?php echo $mc1->icon; ?>"></i><span><?php echo lang($mc1->target,$mc1->nama); ?></span></a></li>
						<?php } ?>
					<?php } ?>
				<?php } else { ?>
				<li><a href="<?php echo base_url($m->target); ?>" class="<?php if($m->id == $menu_access['active_l1']) echo 'active'; ?>" data-shortcut="<?php echo $m->shortcut; ?>" title="<?php echo lang($m->target,$m->nama); ?>"><i class="<?php echo $m->icon; ?>"></i><span><?php echo lang($m->target,$m->nama); ?></span></a></li>
				<?php } ?>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="app-content fixed-content" id="content">
		<?php echo $view_content; ?>
	</div>
	<?php if(setting('chatting')) { ?>
	<div class="chat-box">
		<div class="chat-header">
			<div id="chat-list-title">
				<div class="chat-header-title">
					<?php echo lang('obrolan'); ?> 
					<a href="javascript:;" class="ic-close"><i class="fa-times"></i></a>
				</div>
			</div>
			<div id="chat-active-title" class="hidden">
				<img src="<?php echo base_url(dir_upload('user').'default.png'); ?>" alt="">
				<div class="chat-header-title">
					<a href="javascript:;" class="ic-back"><i class="fa-arrow-left"></i></a>
					<span></span>
				</div>
			</div>
		</div>
		<div class="chat-body">
			<div id="chatMenu">
				<div class="chat-container pt-2">
					<ul class="nav nav-tabs chat-tabs" id="chatTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="chat-online-tab" data-toggle="tab" href="#chat-online" role="tab" aria-controls="chat-online" aria-selected="true"><?php echo lang('online'); ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="chat-obrolan-tab" data-toggle="tab" href="#chat-obrolan" role="tab" aria-controls="chat-obrolan" aria-selected="true"><?php echo lang('obrolan'); ?></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="chat-grup-tab" data-toggle="tab" href="#chat-grup" role="tab" aria-controls="chat-grup" aria-selected="true"><?php echo lang('grup'); ?></a>
						</li>
					</ul>
				</div>
				<div class="tab-content chat-container" id="chatTabContent">
					<div class="tab-pane show active pt-3" id="chat-online" role="tabpanel" aria-labelledby="chat-online-tab"></div>
					<div class="tab-pane pt-3" id="chat-obrolan" role="tabpanel" aria-labelledby="chat-obrolan-tab"></div>
					<div class="tab-pane pt-3" id="chat-grup" role="tabpanel" aria-labelledby="chat-grup-tab"></div>
				</div>
				<div class="chat-search-user">
					<div class="chat-container position-relative">
						<div class="border-top">
							<i class="fa-search chat-input-icon"></i>
							<input type="text" autocomplete="off" id="chat-search-user" class="form-control" placeholder="<?php echo lang('cari'); ?>" />
						</div>
					</div>
				</div>
			</div>
			<div class="chat-container hidden" id="chatContent">
				<div class="chat-message" id="chat-message">
				</div>
				<div class="chat-input-message">
					<div class="chat-container">
						<div class="border-top">
							<textarea id="chat-input" class="form-control" rows="1" placeholder="<?php echo lang('ketik_pesan'); ?>" data-emoji-picker="true"></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
<script type="text/javascript">
var base_url = '<?php echo base_url(); ?>';
var user_key = '<?php echo user('key_id'); ?>';
var ws_server = '<?php if(setting('chatting') && setting('ws_server')) echo base64_encode(setting('ws_server')); ?>';
var upl_flsz = <?php echo $file_upload_max_size; ?>;
var upl_alw = '<?php echo setting('fileupload_mimes') ? base64_encode(str_replace(',', '|', setting('fileupload_mimes'))) : base64_encode(ALLOWED_FILE_UPLOAD); ?>';
<?php
	if(isset($varjs) && is_array($varjs)) {
		foreach($varjs as $kv => $vv) {
			echo 'var '.$kv.' = '.$vv;
		}
	}
?>
</script>
<?php
Asset::js('jquery.min.js', true);
Asset::js('jquery.hotkeys.js', true);
Asset::js('jquery.browser.min.js', true);
Asset::js('jquery.mousewheel.min.js', true);
Asset::js('jquery.inputmask.js', true);
Asset::js('jquery.fileupload.js', true);
Asset::js('jquery.contextMenu.min.js', true);
Asset::js('jquery.autocomplete.js', true);
Asset::js('jquery.redirect.js', true);
Asset::js('jquery.toast.min.js', true);
Asset::js('ion.rangeSlider.js', true);
Asset::js('push.min.js', true);
Asset::js('hashids.min.js', true);
Asset::js('moment.min.js', true);
Asset::js('other.bundle.js', true);
Asset::js('popper.min.js', true);
Asset::js('bootstrap.min.js', true);
Asset::js('bootstrap.tagsinput.js', true);
Asset::js('daterangepicker.js', true);
Asset::js('sweetalert.min.js', true);
Asset::js('select2.min.js', true);
Asset::js('iconpicker.js', true);
Asset::js('_'.setting('language').'.js', true);
Asset::js('app.fn.js', true);
Asset::js('app.js', true);
if(setting('chatting')) {
	Asset::js('linkify.min.js', true);
	Asset::js('linkify-jquery.min.js', true);
	Asset::js('realtime.js', true);
	Asset::js('chatbox.js', true);
}
Asset::js('main.js', true);
Asset::js('table.js', true);
echo Asset::render();
?>
<?php echo $js_content; ?>
<?php if(setting('realtime_notification')) { ?>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
	var OneSignal = window.OneSignal || [];
	OneSignal.push(function() {
		OneSignal.init({
			appId: "<?php echo setting('onesignal_app_id'); ?>",
			autoRegister: true,
			notifyButton: {
				enable: false,
			},
			welcomeNotification: {
				title: "Terima Kasih Telah Mengaktifkan Notifikasi",
				message: "Anda akan mendapatkan pemberitahuan secara realtime ketika anda login."
			}
		});
		if(!$.cookie('osuid')) {
			OneSignal.getUserId(function(userId) {
				$.cookie('osuid', userId, { path: '/' });
			});
		}
	});
</script>
<?php } ?>
</body>
</html>
