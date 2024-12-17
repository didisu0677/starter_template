<ol class="sortable">
	<?php foreach($menu[0] as $m0) { ?>
	<li id="menuItem_<?php echo $m0->id; ?>" class="module" data-module="<?php echo $m0->target; ?>">
		<div class="sort-item">
			<span class="item-title"><?php echo $m0->nama; ?></span>
		</div>
		<?php if(isset($menu[$m0->id]) && count($menu[$m0->id]) > 0) { ?>
		<ol>
			<?php foreach($menu[$m0->id] as $m1) { ?>
			<li id="menuItem_<?php echo $m1->id; ?>" data-module="<?php echo $m0->target; ?>">
				<div class="sort-item">
					<span class="item-title"><?php echo $m1->nama; ?></span>
				</div>
				<?php if(isset($menu[$m1->id]) && count($menu[$m1->id]) > 0) { ?>
				<ol>
					<?php foreach($menu[$m1->id] as $m2) { ?>
					<li id="menuItem_<?php echo $m2->id; ?>" data-module="<?php echo $m0->target; ?>">
						<div class="sort-item">
							<span class="item-title"><?php echo $m2->nama; ?></span>
						</div>
						<?php if(isset($menu[$m2->id]) && count($menu[$m2->id]) > 0) { ?>
						<ol>
							<?php foreach($menu[$m2->id] as $m3) { ?>
							<li id="menuItem_<?php echo $m3->id; ?>" data-module="<?php echo $m0->target; ?>">
								<div class="sort-item">
									<span class="item-title"><?php echo $m3->nama; ?></span>
								</div>
							</li>
							<?php } ?>
						</ol>
						<?php } ?>
					</li>
					<?php } ?>
				</ol>
				<?php } ?>
			</li>
			<?php } ?>
		</ol>
		<?php } ?>
	</li>
	<?php } ?>
</ol>