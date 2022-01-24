<?php if(!empty($this->groups)): ?>
<div>
	<?php foreach ($this->groups as $group): ?>
	<div style="width:175px; height:175px; padding:10px; margin:3px; background-color:#e0e0e0; float:left; text-align:center;">
		<div>
			<?php if($group->defaultAsset() != false): ?>
			<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>"><img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:100,h:100,zc:true,wmk:{opacity:50}}"); ?>" alt="<?php echo $group->defaultAsset()->viewTitle(); ?>" /></a>
			<?php else: ?>
			<div class="blankThumb">No default asset.</div>
			<?php endif; ?>
		</div>
		<div>
			<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>" title="Go to set"><?php echo $group->title; ?></a>
		</div>
		<div>
			<a href="javascript:void(0);" class="delAstGrp" id="<?php echo $group->id; ?>" title="Remove asset from set">Remove set assignment</a>
		</div>
	</div>
	<?php endforeach; ?>
	<br class="clearleft" />
</div>
<?php else: ?>
<div>This asset is not assigned to any sets.</div>
<?php endif; ?>