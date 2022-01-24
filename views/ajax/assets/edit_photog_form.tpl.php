<?php if(!empty($this->photographer)): ?>
<form id="editPhotogForm">
	<input type="hidden" id="_mode" name="_mode" value="asset" />
	<input type="hidden" id="_task" name="_task" value="edit" />
	<input type="hidden" id="_id" name="_id" value="<?php echo $this->photographer->id; ?>" />
	<div class="fmElement">
		<label>First name:</label>
		<div>
			<input type="text" name="editPhotogFname" id="editPhotogFname" class="required" value="<?php echo $this->photographer->firstname; ?>" />
		</div>
	</div>
	<div class="fmElement">
		<label>Last name:</label>
		<div>
			<input type="text" name="editPhotogLname" id="editPhotogLname" class="required" value="<?php echo $this->photographer->lastname; ?>" />
		</div>
	</div>
	<button id="editPhotogBtn" class="rightBtn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" type="button">
		<span class="ui-button-text">Update</span>
	</button>
	<br style="clear:right;" />
</form>
<?php else: ?>
<div>Please, assign or add a photographer.</div>
<?php endif; ?>