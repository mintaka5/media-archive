
<?php if(!empty($this->groups)): ?>
<div>
	<?php foreach($this->groups as $group): ?>
	<div class="grpAstTile softlessBox">
		<div style="margin-bottom:10px; text-align:right;"><a href="#" class="rmvFromColl" data-container="<?php echo $this->container_id; ?>" data-group="<?php echo $group->id; ?>" title="Remove from this collection"><img src="<?php $this->manager->getURI(); ?>/assets/images/delete.png" alt="remove from collection" /></a></div>
		<div style="margin-left:auto; margin-right:auto; width:125px;">
			<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>" class="assetsListThumb"><img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:125, h:125, zc:true}"); ?>" alt="<?php echo $group->title(); ?>" /></a>
		</div>
		<div class="title" style="text-align:center; font-weight:bold; margin-top:10px;">
			<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>"><?php echo $group->title(); ?></a>
		</div>
	</div>
	<?php endforeach; ?>
</div>
<?php else: ?>
<div>No sets in this collection.</div>
<?php endif; ?>