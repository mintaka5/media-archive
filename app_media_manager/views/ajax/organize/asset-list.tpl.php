<?php if(!empty($this->assets)): ?>
<div class="items">
	<?php foreach($this->assets as $asset): ?>
	<div class="item">
		<input type="hidden" class="asset-id" value="<?php echo $asset->id; ?>" />
		<img src="<?php echo $this->thumber->build($asset->public_id, "{w:75,h:75,zc:true,q:50}"); ?>" alt="" />
	</div>
	<?php endforeach; ?>
	<div class="clear-left"></div>
</div>
<?php else: ?>
<?php endif; ?>