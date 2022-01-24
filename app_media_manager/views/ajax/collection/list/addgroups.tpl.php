<?php if(!empty($this->groups)): ?>
<?php foreach($this->groups as $set): ?>
<div class="grpAstTile softlessBox collSetToAdd" data-container="<?php echo $this->container_id; ?>" data-group="<?php echo $set->id; ?>">
	<div class="img" style="margin-left:auto; margin-right:auto; width:125px;">
		<img src="<?php echo $this->thumber->build($set->defaultAsset()->public_id, "{w:125, h:125, zc:true}"); ?>" alt="<?php echo $set->title(); ?>" />
	</div>
	<div class="title" style="text-align:center; font-weight:bold; margin-top:10px;"><?php echo $set->title(); ?></div>
</div>
<?php endforeach; ?>
<?php else: ?>
<?php endif; ?>