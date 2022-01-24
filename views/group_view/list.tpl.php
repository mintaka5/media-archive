<?php if(!empty($this->assets)): ?>
<div style="padding-top:20px;" class="pagelinks"><?php echo $this->pagelinks['all']; ?></div>
<div id="assetContainer" class="clearfix">
	<?php foreach ($this->assets as $asset):?>
	<div class="assetItem">
		<div class="img">
			<?php if($this->manager->isPage("group_view")): ?>
			<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $asset->public_id)); ?>" title="View <?php echo $asset->viewTitle("Untitled"); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($asset->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $asset->title; ?>" title="<?php echo $asset->title; ?>" /></a>
			<?php endif; ?>
			
			<?php if($this->manager->isPage("browse_collections")): ?>
			<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $asset->public_id), array("gid", $this->group->id), array("cid", $this->collection->id)); ?>" title="View <?php echo $asset->viewTitle("Untitled"); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($asset->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $asset->title; ?>" title="<?php echo $asset->title; ?>" /></a>
			<?php endif; ?>
		</div>
		<div class="options">
			<input type="hidden" class="assetId" value="<?php echo $asset->id; ?>" />
			<input type="hidden" class="simId" value="<?php echo $asset->public_id; ?>" />
			<button <?php echo ($asset->isOrdered() == true) ? 'disabled="disabled"' : ""; ?> class="<?php echo ($asset->isOrdered() == true) ? "orderedCart" : "shoppingCart"; ?> btn" title="Add to request cart."></button>
			<?php if($asset->hasKeywords()): ?>
			<button class="moreLikeThis btn" title="Show more assets like this."></button>
			<?php endif; ?>
		</div>
		<div class="meta">
			<div>
				<div class="goLeft blurb">Tag &#35;: <?php echo $asset->public_id; ?></div>
				<div class="goRight blurb"><?php echo $this->date($asset->created, "d M Y"); ?></div>
				<div class="clearBoth blurb"><?php echo $this->truncate($asset->viewTitle("Untitled"), 30); ?></div>
				<div class="blurb">By: <?php echo ($asset->photographer() != false) ? $asset->photographer()->fullname() : "Unknown"; ?></div>
				<div class="blurb"><?php echo $asset->viewCredit(""); ?></div>
				<div class="blurb"><?php echo $asset->viewCaption(""); ?></div>
				<div class="numViews blurb"><?php echo $asset->views("No"); ?></div>
			</div>
		</div>
	</div>
	<?php endforeach;?>
</div>
<div class="pagelinks"><?php echo $this->pagelinks['all']; ?></div>
<?php else: ?>
<div>No assets are available for this group.</div>
<?php endif; ?>