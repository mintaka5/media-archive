<div id="grpOrgList"><?php echo $this->fetch("ajax/groups/group_org_list.tpl.php"); ?></div>

<div>
	<h3>Add organizations:</h3>
	<div>
		Find organization: <input type="text" id="findOrgs" name="findOrgs" data-group="<?php echo $this->group->id; ?>" />
	</div>
	<div id="orgResults"></div>
</div>