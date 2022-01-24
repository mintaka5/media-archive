<?php if($this->manager->isMode()): ?>
<?php if($this->manager->isTask()): ?>
<?php if(!empty($this->assets)): ?>
<?php foreach($this->assets as $asset): ?>
<div style="float:left; margin:0 5px 0 5px;" class="newImageThumb">
	<a href="javascript:void(0);" class="newImage" id="<?php echo $asset->id; ?>"><img src="<?php echo $this->thumber->build($asset->public_id, "{w:100,h:100,zc:true}"); ?>" alt="" /></a>
</div>
<?php endforeach; ?>
<br class="clearLeft" />
<?php endif; ?>
<?php endif; ?>
<?php endif; ?>