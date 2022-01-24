<?php //Util::debug($this->manager->getPage()); ?>
<?php foreach($this->groups as $group): ?>
<div class="brwsGrpTile">
	<div class="grpCreated"><?php echo $this->date($group->created, "d M Y"); ?></div>
	<div class="grpTitle"><span title="<?php echo $group->title(); ?>"><?php echo $this->truncateChars($group->title(), 30); ?></span></div>
	<div class="grpImg">
		<?php if($this->manager->isPage("browse")): // need to split between group view on sets page or the group view on collections page ?>
		<a href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $group->id)); ?>" title="<?php echo $group->title; ?>"><img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $group->defaultAsset()->title; ?>" /></a>
		<?php endif; ?>
		
		<?php if($this->manager->isPage("browse_collections")): ?>
		<a href="<?php echo $this->manager->friendlyAction("browse_collections", "group", "view", array("id", $group->id), array("cid", $this->collection->id)); ?>" title="<?php echo $group->title; ?>"><img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $group->defaultAsset()->title; ?>" /></a>
		<?php endif; ?>
	</div>
	<hr class="grpDotted" />
	<div><?php echo $group->numAssets(); ?> image(s)</div>
</div>
<?php endforeach; ?>