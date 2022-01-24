<div>
	<div>
		<img title="<?php echo $this->asset->title; ?>" alt="<?php echo $this->asset->title; ?>" src="<?php echo $this->thumber->build($this->asset->public_id, "{w:225,q:75}"); ?>" />
	</div>
	<div>
		<h3><?php echo $this->asset->viewTitle(); ?></h3>
		<div>
			<div class="horizInfo">
				<div>Photographer:</div>
				<div><?php echo $this->asset->viewCredit("Not specified"); ?></div>
			</div>
			<div class="horizInfo">
				<div>Date created:</div>
				<div><?php echo $this->date($this->asset->created); ?></div>
			</div>
			<div class="clearLeft"><?php echo $this->asset->finalCaption(); ?></div>
		</div>
	</div>
</div>