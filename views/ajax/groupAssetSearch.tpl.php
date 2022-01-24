<?php if(!empty($this->assets)): ?>
<?php foreach($this->assets as $asset): ?>
<div class="selectAssetForGrp" style="width:180px; height:180px; float:left; background-color:#e0e0e0; margin:3px; padding:7px; border:1px solid #bbb9ba;">
	<input type="hidden" class="selectAssetId" value="<?php echo $asset->id; ?>" />
	<div style="margin-left: auto; margin-right:auto; background-color:#009900; width:100px;">
		<img alt="<?php echo $asset->title; ?>" src="<?php echo $this->thumber->build($asset->public_id, "{w:100,h:100,zc:true,q:95}"); ?>" />
	</div>
	<div style="padding:10px 5px 5px 5px; text-align:center;">
		<div style="font-weight: bold;"><?php echo $this->truncate($asset->viewTitle()); ?></div>
		<!-- <div style="font-size: 80%;"><?php echo $this->truncate($asset->viewCaption()); ?></div> -->
		<div>Credit: <?php echo $asset->viewCredit(); ?></div>
		<!-- <div style="font-size: 80%;">
			Last modified by <?php echo $asset->user()->fullname(); ?>
			on <?php echo $this->date($asset->modified); ?>
		</div> -->
	</div>
</div>
<?php endforeach; ?>
<br class="clearLeft" />
<?php endif; ?>