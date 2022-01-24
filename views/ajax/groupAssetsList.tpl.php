<div>
	<?php if(!empty($this->assets)): ?>
	<div>
		<?php foreach ($this->assets as $num => $element): ?>
		<div class="grpAstTile softlessBox" style="width:180px; height:180px; float:left; background-color:#e0e0e0; margin:3px; padding:7px; border:1px solid #bbb9ba;">
			<input type="hidden" class="assetId" value="<?php echo $element->asset_id; ?>" />
			<div id="" style="position:relative; left:95px; top:-4px;">
				<input type="checkbox" class="makeDefault" <?php echo $this->binaryToText($element->definition()->is_default, "", 'checked="checked"'); ?> /> default
				<a style="margin-left:10px;" title="remove image from group" href="javascript:void(0);" class="rmvFromGrp" id="<?php echo $element->asset()->id; ?>"><img src="<?php echo $this->manager->getURI(); ?>assets/images/delete.png" alt="remove" /></a>
			</div>
			<div style="margin-left: auto; margin-right:auto; width:125px;">
				<a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $element->asset()->id)); ?>" class="assetsListThumb"><img alt="<?php echo $element->asset()->title; ?>" src="<?php echo $this->thumber->build($element->asset()->public_id, "{w:125,h:125,zc:true,q:75}"); ?>" /></a>
			</div>
			<div style="padding:10px 5px 5px 5px; text-align:center;">
				<div style="font-weight: bold;"><?php echo $this->truncate($element->asset()->viewTitle(), 20); ?></div>
				
			</div>
			<div style="position:relative; top:-5px; left:5px;">
				<?php if($this->auth->isManager()): ?>
				<input type="hidden" class="assetId" value="<?php echo $element->asset()->id; ?>" />
                <a href="javascript:void(0);" class="aPreview" title=""><img src="<?php echo $this->manager->getURI(); ?>assets/images/info.png" alt="preview" /></a>
				<a href="javascript:void(0);" class="delAsset" title="Delete asset"><img src="<?php echo $this->manager->getURI(); ?>assets/images/mini-trash.png" alt="x" /></a>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
		<div style="clear:left;"></div>
	</div>
	<?php else: ?>
	<div>Nothing found</div>
	<?php endif; ?>
</div>