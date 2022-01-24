<div>
	<input type="hidden" id="grp-id" value="<?php echo $this->group->id; ?>" />
	<div id="group-assets-list">
		<?php $assets = $this->group->assets(); if(!empty($assets)): ?>
		<?php foreach ($assets as $asset): ?>
		<div class="item">
			<input type="hidden" class="asset-id" value="<?php echo $asset->id; ?>" />
			<div>
				<img src="<?php echo $this->thumber->build($asset->public_id, "{w:100,h:100,zc:true,q:50}"); ?>" alt="<?php echo $asset->viewTitle(); ?>" />
			</div>
		</div>
		<?php endforeach; ?>
		<div class="clear-left"></div>
		<?php else: ?>
		
		<?php endif; ?>
	</div>
</div>