<?php if(!empty($this->asset->shoot_id)): ?>
<form id="editShootForm">
	<input name="_id" id="_id" value="<?php echo $this->asset->shoot()->id; ?>" type="hidden" />
	<input name="_mode" id="_mode" value="edit" type="hidden" />
	<div class="fmElement">
		<label>Title:</label>
		<div>
			<input type="text" name="editShootTitle" id="editShootTitle" class="required" value="<?php echo $this->asset->shoot()->title; ?>" />
		</div>
	</div>
	<div class="fmElement">
		<label>Description:</label>
		<div>
			<textarea name="eShootDesc" id="eShootDesc"><?php echo $this->asset->shoot()->description; ?></textarea>
		</div>
	</div>
	<div class="fmElement">
		<label>Shoot date:</label>
		<div>
			<input type="text" name="editShootDate" id="editShootDate" class="required datePrompt" value="<?php echo $this->date($this->asset->shoot()->shoot_date, "m/d/Y"); ?>" />
		</div>
	</div>
	<button id="editShootBtn" class="rightBtn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" type="button">
		<span class="ui-button-text">Update</span>
	</button>
	<br style="clear:right;" />
</form>
<?php else: ?>
<div>Please, assign a shoot before editing it.</div>
<?php endif; ?>