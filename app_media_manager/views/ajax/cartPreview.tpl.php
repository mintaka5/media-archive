<div>
	<?php if(!empty($this->items)): ?>
	<div id="">
		<?php foreach($this->items as $item): ?>
		<div class="previewItem">
			<div class="img goLeft"><img src="<?php echo $this->thumber->build($item->asset()->public_id, "{w:50,h:50,zc:true}"); ?>" alt="<?php echo $item->asset()->viewTitle(); ?>" /></div>
			<div class="title goLeft"><?php echo $this->truncate($item->asset()->viewTitle(), 15); ?></div>
			<div class="delete goLeft">
				<input type="hidden" class="itemId" value="<?php echo $item->id; ?>" />
				<button class="itemDelBtn deleteBtn btn" title="Delete from cart."></button>
			</div>
		</div>
		<br class="clearLeft" />
		<?php endforeach; ?>
		<div class="clearLeft">
			<a href="<?php echo $this->manager->friendlyAction("cart"); ?>" title="View entire cart contents">More...</a>
		</div>
	</div>
	<?php else: ?>
	<div>Your request cart is empty.</div>
	<?php endif; ?>
</div>