<div>
	<?php if(!empty($this->assets)): ?>
	<div>
		<div id="searchRefinement">
			<!-- search refinement -->
			Search refinement:
			<a href="" title=""></a>
		</div>
		<div id="assetsHolder">
			<?php foreach ($this->assets as $asset): ?>
			<div class="draggImg softlessBox" aid="<?php echo $asset->id; ?>">
				<div class="adminMenu">
					<?php //if($this->auth->isAdmin()): ?>
					<!-- <input type="checkbox" class="assetSelection" value="<?php //echo $asset->id; ?>" /> -->
					<?php //endif; ?>
				</div>
				<div class="assetTile">
					<input type="hidden" class="assetId" value="<?php echo $asset->id; ?>" />
					<div class="thumb">
						<a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $asset->id)); ?>" class="assetsListThumb"><img class="tileImg" alt="<?php echo $this->truncate($asset->viewTitle(), 20); ?>" src="<?php echo $this->thumber->build($asset->public_id, "{w:125,h:125,zc:true,q:75}"); ?>" /></a>
					</div>
					<div class="info">
						<div class="title"><?php echo $this->truncate($asset->viewTitle(), 20); ?></div>
					</div>
				</div>
				<div class="assetTileTools">
					<input type="hidden" class="hdnAssetId" value="<?php echo $asset->id; ?>" />
                    <a href="javascript:void(0);" class="aPreview" title="Preview"><img src="<?php echo $this->manager->getURI(); ?>assets/images/info.png" alt="preview" /></a>
					<?php if($this->auth->isAdmin()): ?>
					
					<?php if($asset->isFeatured()): // is featured ?>
					<img src="<?php echo $this->manager->getURI(); ?>assets/images/feature.png" alt="featured" title="featured" />
					<?php else: // end is featured ?>
					<img src="<?php echo $this->manager->getURI(); ?>assets/images/nofeature.gif" alt="not featured" title="not featured" />
					<?php endif; // end is admin check ?>
					
					<?php if($asset->isActive()): ?>
					<img src="<?php echo $this->manager->getURI(); ?>assets/images/icon-people.gif" alt="activated asset" title="activated asset" />
					<?php endif; ?>
					
					<a href="javascript:void(0);" class="aDelAsset" title="Delete asset permanently."><img src="<?php echo $this->manager->getURI(); ?>assets/images/mini-trash.png" alt="Delete" /></a>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
		<br class="clearLeft" />
		<div class="goLeft">
			<div id="assetPages">Pages: <?php echo $this->assetlinks['all']; ?></div>
		</div>
	</div>
	<?php else: ?>
	<div>Nothing found</div>
	<?php endif; ?>
</div>