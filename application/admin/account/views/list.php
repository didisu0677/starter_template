<div class="show-panel sticky-top">
	<div class="card">
		<div class="card-header">
			<?php echo lang('pengaturan_akun'); ?>
		</div>
		<div class="card-body dropdown-menu">
			<a class="dropdown-item<?php if($uri_string == 'account/profile') echo ' active'; ?>" href="<?php echo base_url('account/profile'); ?>"><i class="fa-user-edit"></i><?php echo lang('profil');?></a>
			<?php if(user('id_vendor')) { ?>
			<a class="dropdown-item<?php if($uri_string == 'account/dokumen') echo ' active'; ?>" href="<?php echo base_url('account/dokumen'); ?>"><i class="fa-file-alt"></i><?php echo lang('dokumen');?></a>
			<?php } ?>
			<a class="dropdown-item<?php if($uri_string == 'account/changepwd') echo ' active'; ?>" href="<?php echo base_url('account/changepwd'); ?>"><i class="fa-key"></i><?php echo lang('ubah_kata_sandi'); ?></a>
		</div>
	</div>
</div>