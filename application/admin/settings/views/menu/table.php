<?php foreach($menu[0] as $m0) { ?>
	<tr>
		<td><?php echo $m0->nama; ?></td>
		<td>{base_url}/<?php echo $m0->target; ?></td>
		<td class="text-center"><?php echo $m0->shortcut; ?></td>
		<td class="text-center"><?php echo $m0->urutan; ?></td>
		<td class="text-center"><?php echo $m0->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td>
		<td class="button">
			<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
			<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
			<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
			<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m0->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
			<?php } ?>
		</td>
	</tr>
	<?php foreach($menu[$m0->id] as $m1) { ?>
		<tr>
			<td class="sub-1"><?php echo $m1->nama; ?></td>
			<td>{base_url}/<?php echo $m0->target.'/'.$m1->target; ?></td>
			<td class="text-center"><?php echo $m1->shortcut; ?></td>
			<td class="text-center"><?php echo $m1->urutan; ?></td>
			<td class="text-center"><?php echo $m1->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td>
			<td class="button">
				<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
				<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
				<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
				<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m1->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
				<?php } ?>
			</td>
		</tr>
		<?php foreach($menu[$m1->id] as $m2) { ?>
			<tr>
				<td class="sub-2"><?php echo $m2->nama; ?></td>
				<td>{base_url}/<?php echo $m0->target.'/'.$m2->target; ?></td>
				<td class="text-center"><?php echo $m2->shortcut; ?></td>
				<td class="text-center"><?php echo $m2->urutan; ?></td>
				<td class="text-center"><?php echo $m2->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td>
				<td class="button">
					<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
					<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m2->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
					<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
					<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m2->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
					<?php } ?>
				</td>
			</tr>
			<?php foreach($menu[$m2->id] as $m3) { ?>
				<tr>
					<td class="sub-3"><?php echo $m3->nama; ?></td>
					<td>{base_url}/<?php echo $m0->target.'/'.$m3->target; ?></td>
					<td class="text-center"><?php echo $m3->shortcut; ?></td>
					<td class="text-center"><?php echo $m3->urutan; ?></td>
					<td class="text-center"><?php echo $m3->is_active ? '<span class="badge badge-success">TRUE</span>' : '<span class="badge badge-danger">FALSE</span>' ; ?></td>
					<td class="button">
						<?php if($access_edit && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
						<button type="button" class="btn btn-warning btn-input" data-key="edit" data-id="<?php echo $m3->id; ?>" title="<?php echo lang('ubah'); ?>"><i class="fa-edit"></i></button>
						<?php } if($access_delete && user('id_group') == 1 && ENVIRONMENT == 'development') { ?>
						<button type="button" class="btn btn-danger btn-delete" data-key="delete" data-id="<?php echo $m3->id; ?>" title="<?php echo lang('hapus'); ?>"><i class="fa-trash-alt"></i></button>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		<?php } ?>
	<?php } ?>
<?php } ?>