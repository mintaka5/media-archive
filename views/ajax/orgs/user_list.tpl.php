<div>
	<h3>Users in <?php echo $this->org->title; ?></h3>

	<div id="ouList"><?php echo $this->fetch("ajax/orgs/org_users_list.tpl.php"); ?></div>
	
	<div style="border-top:1px solid #bfbfbf;">
		<h4>Add users:</h4>
		<div>
			Find user: <input type="text" data-org="<?php echo $this->org->id; ?>" id="findUsers" name="findUsers" />
		</div>
		<div id="userResults"></div>
	</div>
</div>