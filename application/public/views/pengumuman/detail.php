<div class="mt-lg-4 pt-2 pb-2 mt-0">
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">Data Pelelangan</div>
				<div class="card-body table-responsoive">
					<table class="table table-bordered table-detail mb-0">
						<tr>
							<th width="200">Unit Kerja</th>
							<td><?php echo $unit_kerja; ?></td>
						</tr>
						<tr>
							<th>Metode Pengadaan</th>
							<td><?php echo $metode_pengadaan; ?></td>
						</tr>
						<tr>
							<th>Nama Pekerjaan</th>
							<td><?php echo $nama_pengadaan; ?></td>
						</tr>
						<tr>
							<th>Nomor Pengumuman</th>
							<td><?php echo $nomor_pengumuman; ?></td>
						</tr>
						<tr>
							<th>Bidang - Sub Bidang Usaha</th>
							<td><?php 
							if($bidang_usaha) {
								echo '<table>';
								$i = 1;
								foreach(json_decode($bidang_usaha,true) AS $v) {
									echo '<tr>';
									echo '<td width="20" style="border: 0 none;">'.$i.'</td>';
									echo '<td width="150" style="border: 0 none;">'.$v['bidang_usaha'].'</td>';
									echo '<td style="border: 0 none;">'.$v['subbidang_usaha'].'</td>';
									echo '</tr>';
									$i++;
								}
								echo '</table>';
							}
							?></td>
						</tr>
						<tr>
							<th>Kualifikasi Penyedia Barang / Jasa</th>
							<td><?php echo $kategori_rekanan; ?></td>
						</tr>
						<tr>
							<th>Identifikasi Pajak</th>
							<td><?php echo $identifikasi_pajak; ?></td>
						</tr>
						<tr>
							<th>Keterangan Pengadaan</th>
							<td><?php echo $keterangan_pengadaan; ?></td>
						</tr>
					</table>
				</div>
			</div>
			<div class="card mb-3">
				<div class="card-header">Jadwal Pengadaan</div>
				<div class="card-body">
					<div class="alert alert-info">Semua proses pengadaan dilakukan secara online pada situs <a href="<?php echo base_url(); ?>" target="_blank"><?php echo base_url(); ?></a>.</div>
					<div class="text-bold mb-2">1. Pendaftaran Pengadaan/Konfirmasi Undangan Pengadaan</div>
					<div class="table-responsive mb-2">
						<table class="table table-bordered table-detail">
							<tr>
								<th width="200">Tanggal</th>
								<td><?php if(isset($pendaftaran->id)) {
									if(date('Y-m-d',strtotime($pendaftaran->tanggal_awal)) == date('Y-m-d',strtotime($pendaftaran->tanggal_akhir))) {
										echo date_indo($pendaftaran->tanggal_awal,false);
									} else {
										echo date_indo($pendaftaran->tanggal_awal,false).' - '.date_indo($pendaftaran->tanggal_akhir,false);
									}
								} else echo '-'; ?></td>
							</tr>
							<tr>
								<th>Jam</th>
								<td><?php if(isset($pendaftaran->id)) {
									echo date('H:i',strtotime($pendaftaran->tanggal_awal)).' '.$pendaftaran->zona_waktu.' s/d '.date('H:i',strtotime($pendaftaran->tanggal_akhir)).' '.$pendaftaran->zona_waktu;
								} else echo '-'; ?></td>
							</tr>
						</table>
					</div>
					<div class="text-bold mb-2">2. Jadwal Penjelasan RKS, Administrasi & Teknis (Aanwijzing) </div>
					<div class="table-responsive">
						<table class="table table-bordered table-detail mb-0">
							<tr>
								<th width="200">Tanggal</th>
								<td><?php if(isset($aanwijzing->id)) {
									echo date_indo($aanwijzing->tanggal_awal,false);
								} else echo '-'; ?></td>
							</tr>
							<tr>
								<th>Jam</th>
								<td><?php if(isset($aanwijzing->id)) {
									echo date('H:i',strtotime($aanwijzing->tanggal_awal)).' '.$aanwijzing->zona_waktu.' s/d Selesai';
								} else echo '-'; ?></td>
							</tr>
							<tr>
								<th>Tempat</th>
								<td><?php if(isset($aanwijzing->id)) {
									echo str_replace("\n", '<br />', $aanwijzing->lokasi);
								} else echo '-'; ?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="text-center">
				<a href="javascript:;" onclick="history.back()" class="btn btn-secondary"><i class="fa-chevron-left"></i> &nbsp; Kembali</a>
				<a href="<?php echo base_url('pengumuman/download/'.encode_id([$id,rand()])); ?>" target="_blank" class="btn btn-success"><i class="fa-download"></i> &nbsp; Unduh</a>
			</div>
		</div>
	</div>
</div>