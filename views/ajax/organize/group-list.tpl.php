<?php if(!empty($this->groups)): ?>
<div id="group-items">
	<?php foreach($this->groups as $grp): ?>
	<div class="item">
		<input type="hidden" class="grp-id" value="<?php echo $grp->id; ?>" />
		<div class="img">
			<?php if($grp->defaultAsset() != false): ?>
			<img src="<?php echo $this->thumber->build($grp->defaultAsset()->public_id, "{w:100,h:100,zc:true,q:50}"); ?>" alt="<?php echo $grp->defaultAsset()->viewTitle(); ?>" />
			<?php else: ?>
			<div class="" style="width:100px; height:100px; background-color:#000;">No default asset.</div>
			<?php endif; ?>
		</div>
		<div class="title"><?php echo $grp->title; ?></div>
		<div class="num">
			<span><?php echo $grp->numAssets(); ?></span> items
		</div>
	</div>
	<?php endforeach; ?>
	<div class="clear-left"></div>
</div>
<?php else: ?>
<?php endif; ?>