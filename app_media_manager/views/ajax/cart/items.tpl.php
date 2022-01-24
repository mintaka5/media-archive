<?php if(!empty($this->items)): ?>
<div id="cartView">
	<?php foreach($this->items as $item): ?>
	<div class="cartItem">
		<div class="title"><?php echo $item->asset()->viewTitle(); ?></div>
		<div>
			<div class="img goLeft">
				<div><img src="<?php echo $this->thumber->build($item->asset()->public_id, "{w:100,h:100}"); ?>" alt="<?php echo $item->asset()->viewTitle(); ?>" /></div>
				<div class="options">
					<input type="hidden" class="itemID" value="<?php echo $item->id; ?>" />
					<button title="Delete from cart" class="delBtn btn"></button>
				</div>
			</div>
			<div class="info goLeft">
				<table class="assetInfo">
					<tbody>
						<tr>
							<th>Date Created:</th>
							<td><?php echo $this->date($item->asset()->created, "d M Y"); ?></td>
						</tr>
						<tr>
							<th>Image Tag &#35;:</th>
							<td><?php echo $item->asset()->public_id; ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<br class="clearLeft" />
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php else: ?>
<div class="noresults">You have no requests for assets.</div>
<?php endif; ?>