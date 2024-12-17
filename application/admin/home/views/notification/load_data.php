<?php foreach($data as $l) { ?>
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