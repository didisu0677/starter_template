<div class="content-header">
	<div class="main-container position-relative">
		<div class="header-info">
			<div class="content-title"><?php echo $title; ?></div>
			<?php echo breadcrumb($title); ?>
		</div>
		<div class="float-right">
			<button type="button" class="btn btn-info btn-sm btn-view"><i class="fa-check-circle"></i>Tandai Sudah Dilihat</button>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<div class="content-body body-light">
	<div class="main-container">
		<ul class="notification"></ul>
		<br />
		<form class="text-center" id="load-more" action="<?php echo base_url('home/notification/load_data'); ?>">
			<button class="btn btn-sm btn-info" id="btn-more" type="submit">
				Muat Lainnya
			</button>
		</form>
	</div>
</div>
<script type="text/javascript">
var offset 	= 0;
var limit	= 15;
var busy	= false;
function get_data() {
	if(busy == false) {
		busy	= true;
		$.ajax({
			url		: $('#load-more').attr('action'),
			data	: {'offset':offset,'limit':limit},
			type	: 'post',
			dataType: 'json',
			success	: function(response) {
				$('.notification').append(response.data);
				busy = false;
				if(parseInt(response.num) < limit) {
					$('#btn-more').hide();
				} else {
					offset += parseInt(response.num);
				}
			}
		});
	}
}
$('#btn-more').click(function(e){
	e.preventDefault();
	get_data();
});
$(document).ready(function(){
	get_data();
});
$('.btn-view').click(function(e){
	e.preventDefault();
	$.ajax({
		url		: $('#load-more').attr('action').replace('load_data','is_read'),
		type	: 'post',
		dataType: 'json',
		success	: function(response) {
			$('.notification').html('');
			$('.dropdown-notification .tag').remove();
			offset = 0;
			get_data();
		}
	});
});
</script>