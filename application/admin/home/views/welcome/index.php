<link rel="stylesheet" href="<?php echo base_url('assets/plugins/breakingnews/breakingnews.css'); ?>">
<div class="content-body body-home bg-grey">
	<div class="position-relative">
		<div class="offset-header"></div>
		<div class="main-container pt-3">
			<div class="row">
				<div class="col-sm-4 mb-3 mb-sm-4">
					<div class="card">
						<div class="card-body">
							<div class="dashboard-avatar">
								<img src="<?php echo user('foto'); ?>" class="rounded-circle" alt="avatar">
							</div>
							<div class="dashboard-content">
								<div class="dashboard-main-text single-line"><?php echo user('nama'); ?></div>
								<div class="single-line mb-1 dashboard-secondary-text"><?php echo user('email'); ?></div>
								<a href="<?php echo base_url('account/profile'); ?>" class="d-inline-block mr-3 mb-1">Edit Profil</a>
								<a href="<?php echo base_url('account/changepwd'); ?>" class="d-inline-block">Ubah Kata Sandi</a>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 mb-3 mb-sm-4">
					<div class="card">
						<div class="card-body">
							<div class="dashboard-avatar">
								<div class="icon-avatar"><i class="fa-box"></i></div>
							</div>
							<div class="dashboard-content">
								<div class="row">
									<div class="col-6">
										<div class="single-line dashboard-secondary-text">Aset Saya</div>
										<div class="dashboard-main-text single-line mb-1"><?php echo $my_aset; ?></div>
										<a href="<?php echo base_url('aset/aset_saya'); ?>" class="d-inline-block mr-3 mb-1">Lihat Aset</a>
									</div>
									<div class="col-6">
										<div class="single-line dashboard-secondary-text">Aset Departmen</div>
										<div class="dashboard-main-text single-line mb-1"><?php echo $dept_aset; ?></div>
										<a href="<?php echo base_url('aset/aset_department'); ?>" class="d-inline-block mr-3 mb-1">Lihat Aset</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 mb-3 mb-sm-4">
					<div class="card">
						<div class="card-body">
							<div class="dashboard-avatar">
								<div class="icon-avatar"><i class="fa-question-circle"></i></div>
							</div>
							<div class="dashboard-content">
								<div class="row">
									<div class="col-6">
										<div class="single-line dashboard-secondary-text">Tiket Aktif</div>
										<div class="dashboard-main-text single-line mb-1"><?php echo $tiket; ?></div>
										<a href="<?php echo base_url('helpdesk'); ?>" class="d-inline-block mr-3 mb-1">Lihat Tiket</a>
									</div>
									<div class="col-6">
										<div class="single-line dashboard-secondary-text">Tugas Aktif</div>
										<div class="dashboard-main-text single-line mb-1"><?php echo $task; ?></div>
										<a href="<?php echo base_url('helpdesk/task'); ?>" class="d-inline-block mr-3 mb-1">Lihat Tugas</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if(count($pengumuman) > 0) { ?>
				<div class="col-sm-12 mb-3 mb-sm-4 sticky-top">
					<div class="breakingNews" id="bn7">
						<div class="bn-title"><h2>Informasi</h2><span></span></div>
						<ul>
							<?php foreach($pengumuman as $p) { ?>
							<li><span><?php echo $p['nama']; ?></span> - <?php echo $p['pengumuman']; ?></li>
							<?php } ?>
						</ul>
						<div class="bn-navi">
							<span></span>
							<span></span>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="col-sm-8 mb-3 mb-sm-4">
					<div class="card">
						<div class="card-header pt-2 pb-2 pr-3 pl-3">
							<div class="float-left pt-2 mb-2 mb-sm-0">
								Performa
							</div>
							<div class="float-right">
								<select class="custom-select select2 infinity d-inline-block" style="width: 200px;" id="id_department">
									<option value="all">Semua Departmen</option>
									<?php foreach($department as $c) { ?>
									<option value="<?php echo $c['id']; ?>">Departmen <?php echo $c['nama']; ?></option>
									<?php } ?>
								</select>
								<select class="custom-select select2 infinity d-inline-block" style="width: 80px;"  id="tahun">
									<?php for($i=date('Y');$i >= 2018; $i--) { ?>
									<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="card-body p-3">
							<canvas id="myChart" height="390"></canvas>
						</div>
					</div>
				</div>
				<div class="col-sm-4 mb-3 mb-sm-4">
					<div class="card">
						<div class="card-header pt-2 pb-2 pr-3 pl-3">
							<div class="float-left pt-2">
								Kategori
							</div>
							<div class="float-right">
								<select class="custom-select select2 infinity" style="width: 170px;" id="periode">
									<option value="bulanan">Bulan Ini</option>
									<option value="tahunan">Tahun Ini</option>
									<option value="all">Sepanjang Waktu</option>
								</select>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="card-body p-3">
							<canvas id="pieChart" height="390"></canvas>
						</div>
					</div>
				</div>
				<div class="col-12 mb-2">
					<div class="card">
						<div class="card-header p3">
							<ul class="nav nav-pills card-header-pills pill-w-badge">
								<li class="nav-item">
									<a href="#" class="nav-link disabled">Daftar Tiket</a>
								</li>
								<?php 
								$n = 0;
								foreach($department as $k => $d) {
									if($d['id'] == user('id_department')) $n = $k;
								}
								foreach($department as $k => $d) { ?>
								<li class="nav-item">
									<?php if(($k == $n)) { ?>
									<a class="nav-link have-badge active" href="#dept<?php echo $k; ?>" data-toggle="pill" href="#pills-dept<?php echo $k; ?>" role="tab" aria-controls="pills-dept<?php echo $k; ?>" aria-selected="true"><?php echo $d['nama']; ?> <span class="badge"><?php echo count($task_list[$d['id']]) < 10 ? count($task_list[$d['id']]) : '9+'; ?></span></a>
									<?php } else { ?>
									<a class="nav-link have-badge" href="#dept<?php echo $k; ?>" data-toggle="pill" href="#pills-dept<?php echo $k; ?>" role="tab" aria-controls="pills-dept<?php echo $k; ?>" aria-selected="false"><?php echo $d['nama']; ?> <span class="badge"><?php echo count($task_list[$d['id']]) < 10 ? count($task_list[$d['id']]) : '9+'; ?></span></a>
									<?php } ?>
								</li>
								<?php } ?>
							</ul>
						</div>
						<div class="card-body p-0 tab-content">
							<?php foreach($department as $k => $d) { ?>
							<div class="table-responsive tab-pane fade<?php if($k == $n) echo ' show active'; ?>" id="dept<?php echo $k; ?>">
								<table class="table table-app table-striped table-hover">
									<thead>
										<tr>
											<th>No. Tiket</th>
											<th>Subjek</th>
											<th>Diajukan Oleh</th>
											<th>Aset</th>
											<th>Kategori</th>
											<th>Tanggal Submit</th>
											<th>Aktifitas Terakhir</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
										<?php if(count($task_list[$d['id']]) == 0) { ?>
										<tr>
											<td class="text-center" colspan="8">Tidak ada task dari departmen <?php echo $d['nama']; ?></td>
										</tr>
										<?php } ?>
										<?php foreach($task_list[$d['id']] as $tl) { ?>
										<tr>
											<td>
												<?php if($n == $k || user('id_department') == $tl['id_department_pengirim']) { ?>
												<a href="<?php echo base_url('helpdesk/tiket/'.encode_id([$tl['id'],rand(11111,999999)])); ?>"><?php echo $tl['no_tiket']; ?></a>
												<?php } else  echo $tl['no_tiket'] ?>
											</td>
											<td>
												<?php if($n == $k || user('id_department') == $tl['id_department_pengirim']) { ?>
												<a href="<?php echo base_url('helpdesk/tiket/'.encode_id([$tl['id'],rand(11111,999999)])); ?>"><?php echo $tl['subjek']; ?></a>
												<?php } else echo $tl['no_tiket']; ?>
											</td>
											<td><?php echo $tl['nama_user_pengirim'].' / '.$tl['department_pengirim']; ?></td>
											<td>
												<?php if($n == $k || user('id_department') == $tl['id_department_pengirim']) { ?>
												<a href="<?php echo base_url('detail/aset/tiket?id='.encode_id([$tl['id_aset'],rand(11111,999999)])); ?>"><?php echo $tl['nama_aset']; ?></a>
												<?php } else echo $tl['nama_aset']; ?>
											</td>
											<td><?php echo $tl['kategori']; ?></td>
											<td><?php echo c_date($tl['create_date']); ?></td>
											<td><?php echo c_date($tl['last_activity']); ?></td>
											<td><?php 
												if($tl['status'] == '0') echo 'Belum Direspon';
												else if($tl['status'] == '1') echo 'Sudah Direspon';
												else if($tl['status'] == '9') {
													if($tl['teruskan_vendor']) echo 'Diteruskan ke Vendor';
													else {
														echo $tl['u_penerima'] ? 'Diteruskan ('.$tl['u_penerima'].' / '.$tl['d_penerima'].')' : 'Diteruskan ('.$tl['d_penerima'].')';
													}
												} else echo 'Selesai';
											?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="main-container p-4 text-center">
			<img src="<?php echo base_url(dir_upload('setting').setting('logo')); ?>" alt="logo">
			<div class="version">Version <?php echo APP_VERSION; ?></div>
		</div>
	
	</div>
</div> 
<script src="<?php echo base_url('assets/plugins/breakingnews/breakingnews.js'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/chartjs/Chart.bundle.min.js'); ?>"></script>
<script>
	var myBar, myPie;
	var serialize_color = [
		'#404E67',
		'#22C2DC',
		'#ff6384',
		'#ff9f40',
		'#ffcd56',
		'#4bc0c0',
		'#9966ff',
		'#36a2eb',
		'#848484',
		'#e8b892',
		'#bcefa0',
		'#4dc9f6',
		'#a0e4ef',
		'#c9cbcf',
		'#00A5A8',
		'#10C888'
	];
	function get_chart(e) {
		$.ajax({
			url 		: base_url + 'home/welcome/chart_data',
			data 		: {tahun:$('#tahun').val(), id_department: $('#id_department').val()},
			type 		: 'post',
			dataType	: 'json',
			success 	: function(response) {
				var label_chart 	= [];
				var data_bar_l 		= [];
				var data_bar_p 		= [];
				$.each(response,function(k,v){
					label_chart.push(response[k]['bulan']);
					data_bar_l.push(parseInt(response[k]['open']));
					data_bar_p.push(parseInt(response[k]['close']));
				});
				
				myBar.data = {
					labels: label_chart,
					datasets: [{
						label: 'Permasalahan',
						backgroundColor: 'rgba(255, 117, 136, .8)',
						borderColor: 'transparent',
						borderWidth: 0,
						data: data_bar_l
					},{
						label: 'Tiket Selesai',
						backgroundColor: 'rgba(34, 194, 220,.8)',
						borderColor: 'transparent',
						borderWidth: 0,
						data: data_bar_p
					}]
				};

				myBar.update();
				if(typeof e != 'undefined' && e == 'category') {
					get_category();
				}
			}
		});
	}
	function get_category() {
		$.ajax({
			url 		: base_url + 'home/welcome/get_category',
			data 		: {periode:$('#periode').val()},
			type 		: 'post',
			dataType	: 'json',
			success 	: function(response) {
				var data_pie 		= [];
				var color_pie 		= [];
				var label_chart 	= [];
				$.each(response,function(k,v){
					data_pie.push(parseInt(v['jml']));
					color_pie.push(serialize_color[k]);
					label_chart.push(v['kategori']);
				});
				myPie.data = {
					datasets: [{
						data: data_pie,
						backgroundColor: color_pie,
						label: 'Kategori'
					}],
					labels: label_chart,
				};
				myPie.update();
			}
		});
	}
	$(document).ready(function(){
		var ctxBar  	= document.getElementById('myChart').getContext('2d');
		myBar 			= new Chart(ctxBar, {
			type: 'bar',
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				}
			}
		});

		var ctxPie = document.getElementById('pieChart').getContext('2d');
		myPie = new Chart(ctxPie, {
			type: 'pie',
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					display: true,
					position: 'right',
					labels: {
						boxWidth: 15,
						generateLabels: function(chart) {
							var data = chart.data;
							if (data.labels.length && data.datasets.length) {
								return data.labels.map(function(label, i) {
									var meta = chart.getDatasetMeta(0);
									var ds = data.datasets[0];
									var arc = meta.data[i];
									var custom = arc && arc.custom || {};
									var getValueAtIndexOrDefault = Chart.helpers.getValueAtIndexOrDefault;
									var arcOpts = chart.options.elements.arc;
									var fill = custom.backgroundColor ? custom.backgroundColor : getValueAtIndexOrDefault(ds.backgroundColor, i, arcOpts.backgroundColor);
									var stroke = custom.borderColor ? custom.borderColor : getValueAtIndexOrDefault(ds.borderColor, i, arcOpts.borderColor);
									var bw = custom.borderWidth ? custom.borderWidth : getValueAtIndexOrDefault(ds.borderWidth, i, arcOpts.borderWidth);

									var value = chart.config.data.datasets[arc._datasetIndex].data[arc._index];

									return {
										text: label + " : " + value,
										fillStyle: fill,
										strokeStyle: stroke,
										lineWidth: bw,
										hidden: isNaN(ds.data[i]) || meta.data[i].hidden,
										index: i
									};
								});
							} else {
								return [];
							}
						}
					}
				}
			}
		});
		get_chart('category');
	});
	$('#tahun,#id_department').change(function(){
		get_chart();
	});
	$('#periode').change(function(){
		get_category();
	});
	$("#bn7").breakingNews({
		effect		:"slide-v",
		autoplay	:true,
		timer		:5000,
	});
</script>