<div>
	<?php if(!empty($this->featuredimage->id)): ?>
	<div id="feat-image">
		<div>
			<div id="feat-left" class="goLeft">
				<h3>
					<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $this->featuredimage->public_id)); ?>" title="<?php echo $this->featuredimage->viewTitle(); ?>"><?php echo $this->featuredimage->viewTitle(); ?></a>
				</h3>
				<h4><?php echo ($this->featuredimage->photographer() != false) ? $this->featuredimage->photographer()->fullname() : $this->featuredimage->viewCredit(""); ?></h4>
				<div id="feat-caption"><?php echo $this->featuredimage->finalCaption(); ?></div>
			</div>
			<div id="feat-right" class="goLeft">
				<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $this->featuredimage->public_id)); ?>" title="<?php echo $this->featuredimage->viewTitle(); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($this->featuredimage->public_id, "{w:617,h:417,zc:true,q:85}"); ?>" alt="" /></a>
			</div>
			<br class="clearLeft" />
		</div>
	</div>
	
	<?php else: ?>
	
	<div class="noresults">No images have yet been featured.</div>
	
	<?php endif; ?>
	
	<?php if(!empty($this->mostviewed)): ?>
	<div class="clearfix" style="margin-top:10px; border-bottom:1px solid #ccc;">
		<h2>Most viewed</h2>
		<?php foreach($this->mostviewed as $mostview): ?>
		<div class="assetItem mostViewed">
			<div class="img">
				<a title="<?php echo $mostview->viewTitle(); ?>" href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $mostview->public_id)); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($mostview->public_id, "{w:182, h:130, zc:true, q:85}"); ?>" alt="<?php echo $mostview->viewTitle(); ?>" /></a>
			</div>
			<div class="meta clearfix">
				<div class="goLeft blurb">Tag &#35;: <?php echo $mostview->public_id; ?></div>
				<div class="goRight blurb"><?php echo $this->date($mostview->created, "d M Y"); ?></div>
				<div class="clearBoth blurb"><?php echo $mostview->viewTitle(); ?></div>
				<div class="blurb">By: <?php echo ($mostview->photographer() != false) ? $mostview->photographer()->fullname() : "Unknown"; ?></div>
				<div class="blurb"><?php echo $mostview->viewCredit(""); ?></div>
				<div class="blurb"><?php echo $mostview->viewCaption(""); ?></div>
				<div class="numViews blurb"><?php echo $mostview->views("No"); ?></div>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endif; ?>
	
	<?php if(!empty($this->featuredgroups)): ?>
	<div>
		<h2>Featured Sets</h2>
		<div>
		
			<?php foreach($this->featuredgroups as $group): ?>
			<div class="feat-groups">
				<div class="img">
					<a href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $group->id)); ?>" title="<?php echo $group->title(); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:182, h:130, zc:true, q:85}"); ?>" alt="<?php echo $group->title(); ?>" /></a>
				</div>
				<div class="title">
					<?php echo $group->title(); ?>
				</div>
				<div class="link">
					<a href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $group->id)); ?>" title="View <?php echo $group->title(); ?>">View <?php echo $group->title(); ?></a>
				</div>
			</div>
			<?php endforeach; ?>
			
			<div class="clearLeft"></div>
		</div>
	</div>
	<?php endif; ?>
	
</div>