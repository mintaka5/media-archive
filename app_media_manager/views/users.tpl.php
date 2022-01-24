<div>
	<h1>Users</h1>

    <div>
		<?php if($this->manager->isMode() || $this->manager->isMode("search")): ?>
		<div><?php echo $this->fetch('users/default.tpl.php'); ?></div>
		<?php endif; // end default _mode ?>
		
		<?php if($this->manager->isMode("edit")): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/users/edit/default.js"></script>
		<div>
			<h2><?php echo $this->user->fullname(); ?></h2>
			<div>
				<input type="hidden" id="userId" value="<?php echo $this->user->id; ?>" />
				<h3>User type:</h3>
				<select id="userType">
					<?php foreach($this->usertypes as $usertype): ?>
					<option <?php if($this->user->type()->id == $usertype->id): ?>selected="selected"<?php endif; ?> value="<?php echo $usertype->id; ?>"><?php echo $usertype->title; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			
			<div id="orgHolder">
				<h3>Organizations</h3>
				<div id="orgTable">
					<div id="userOrgList">
						<?php echo $this->fetch("ajax/users/org_list.tpl.php"); ?>
					</div>
					
					<div id="orgAddHolder">
						<select id="assignOrg" data-user="<?php echo $this->user->id; ?>">
							<option value="">- select one -</option>
							<?php foreach($this->allorgs as $allorg): ?>
							<option value="<?php echo $allorg->id; ?>"><?php echo $allorg->title; ?></option>
							<?php endforeach; ?>
						</select>
						<button id="btnAssignOrg">Assign</button>
					</div>
				</div>
			</div>
		</div>
		<?php endif; // end edit _mode ?>
    </div>
    
    <div class="rightContent">
        <div class="softBox">
            <h3>User Search</h3>
            <div>
	            <form method="get" action="">
	                <input type="hidden" name="_page" value="users" />
	                <input type="hidden" name="_mode" value="search" />
	                <input type="hidden" name="pageNum" value="<?php echo (isset($_GET['pageNum']) ? trim($_GET['pageNum']) : 1); ?>" />
	                <div class="">
	                        <div class="input">
	                                <input type="text" class="textField" name="txtSearchUsers" id="txtSearchUsers" />
	                                <input type="submit" class="goBtn" value="Find" />
	                        </div>
	                </div>
	            </form>
            </div>
		</div>
		
		<?php if($this->manager->isMode("edit")): ?>
		<div></div>
		<?php endif; //end edit _mode ?>
    </div>
    <br class="clearLeft" />
</div>