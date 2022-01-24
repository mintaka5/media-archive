<div>
	<?php if(!empty($this->groups)): ?>
	
	<div><?php echo $this->pagelinks['all']; ?></div>
	
	<?php foreach($this->groups as $group): ?>
	<div class="droppGrp softlessBox" gid="<?php echo $group->id; ?>">
		<div class="thumb">
			<?php if($group->defaultAsset() != false): ?>
			<img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:125,h:125,zc:true}"); ?>" alt="<?php echo $group->defaultAsset()->viewTitle(); ?>" />
			<?php else: ?>
			<div class="blankThumb">No default asset.</div>
			<?php endif; ?>
		</div>
		<div><?php echo $group->title; ?></div>
	</div>
	<?php endforeach; ?>
	
	<div class="clearLeft"><?php echo $this->pagelinks['all']; ?></div>
	
	<?php else: ?>
	
	<div>Nothing found.</div>
	
	<?php endif; ?>
</div>