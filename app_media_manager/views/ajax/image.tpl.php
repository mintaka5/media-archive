<?php if(Ode_Manager::getInstance()->isMode("edit")): ?>
<div style="width:500px;">
	<div>
		<img src="<?php echo $this->thumber->build($this->asset['public_id'], "{w:400}"); ?>" alt="" />
	</div>
	<div>
		<?php if(isset($this->update) && $this->update == true): ?>
		<div style="border:1px solid #b5e776; background-color:#176610;">Image update successful.</div>
		<?php elseif (isset($this->update) && $this->update == false): ?>
		<div style="border:1px solid #ca3f35; background-color:#edbebc;">Failed to update image information.</div>
		<?php endif; ?>
	</div>
	<div><?php echo $this->form; ?></div>
</div>
<?php endif; ?>