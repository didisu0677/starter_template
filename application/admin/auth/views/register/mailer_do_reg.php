<p style="text-align: justify;">Pendaftaran rekanan <?php echo setting('company'); ?> atas nama <strong>"<?php echo $vendor['nama']; ?>"</strong> Berhasil. Selanjutnya silahkan lengkapi dokumen persyaratan baik secara fisik maupun secara sistem.</p>
<p style="text-align: justify;">Untuk masuk ke sistem <?php echo setting('title').' '.setting('company'); ?> lihat detail dibawah.</p>
<table style="border-collapse: collapse; width: 100%">
	<tr>
		<td style="width: 100px; background: #f7f7f7; padding: 5px;">URL</td>
		<td style="padding: 5px">
			<a href="<?php echo base_url('auth/login'); ?>"><?php echo base_url('auth/login'); ?></a>
		</td>
	</tr>
	<tr>
		<td style="width: 100px; background: #f7f7f7; padding: 5px;">Username</td>
		<td style="padding: 5px"><?php echo $username; ?></td>
	</tr>
	<tr>
		<td style="width: 100px; background: #f7f7f7; padding: 5px;">Password</td>
		<td style="padding: 5px"><?php echo $password; ?></td>
	</tr>
</table>
