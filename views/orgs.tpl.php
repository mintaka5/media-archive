<div id="orgsPage" class="clearfix">
        <?php if($this->manager->isMode()): ?>
	<h1>Organizations/Groups</h1>

	<div class="leftContent">

		<?php if($this->manager->isMode()): ?>
		<div>
			<?php if($this->manager->isTask()): ?>
			
			<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/orgs/default/default.js"></script>
			
			<div id="orgUserWin"><!-- I hold the content for editing organization users --></div>
			
			<div id="orgList"></div>
			
			<?php endif; ?>
		</div>
		<?php endif; ?>

	</div>
	
	<div class="rightContent">
	
		<?php if($this->manager->isMode()): ?>
		<?php if($this->manager->isTask()): ?>
		<div class="softBox">
			<h3>Add Organization</h3>
			<div id="addOrgForm">
				<input type="text" id="orgTitle" name="orgTitle" class="textField" style="width:99px;" />
				<input type="button" value="Add" class="goBtn" id="btnAddOrg" />
			</div>
		</div>
		<?php endif; ?>
		<?php endif; ?>
		
	</div>
        
        <?php endif; ?>
        
        <?php if($this->manager->isMode('edit')): ?>
        <?php if($this->manager->isTask()): ?>
        
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/orgs/edit/default.js"></script>
        
        <h1>Editing Organization</h1>
        
        <div class="leftContent">
            <div id="orgTabs">
                <ul>
                    <li><a href="#infoTab">Information</a></li>
                    <li><a href="#usersTab">Users</a></li>
                    <li><a href="#flickrTab">Flickr Acct.</a></li>
                </ul>
                <div id="infoTab">
                    <div class="fmElement">
                        <label>Title</label>
                        <div>
                            <input style="width:200px;" type="text" name="title" id="title" value="<?php echo $this->org->title(); ?>" />
                            <button id="saveTitle" data-orgid="<?php echo $this->org->id; ?>">Save</button>
                        </div>
                    </div>
                    
                    <div class="fmElement">
                        <label>Created</label>
                        <div><?php echo date("m/d/Y", strtotime($this->org->created)); ?></div>
                    </div>
                </div>
                <div id="usersTab">
                    <?php $users = $this->org->users(); if(!empty($users)): ?>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($users as $num => $user): ?>
                            <tr class="<?php echo ($num%2) ? 'dark' : 'light'; ?>">
                                <td><?php echo $user->user()->fullname(); ?></td>
                                <td><?php echo $user->user()->username; ?></td>
                                <td><?php echo $user->user()->email; ?></td>
                                <td>
                                    <ul class="tblMenu">
                                        <?php if($this->auth->getSession()->id != $user->user()->id): ?>
                                        <li>
                                            <a href="#" class="userDel" data-id="<?php echo $user->user()->id; ?>" data-orgid="<?php echo $this->org->id; ?>"><img src="<?php echo $this->manager->getURI(); ?>assets/images/delete.png" alt="" /></a>
                                        </li>
                                        <?php endif; ?>
                                        
                                        <li>
                                            <a href="<?php echo $this->manager->friendlyAction('users', 'edit', null, array('id', $user->user()->id)); ?>">Manage</a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
                
                <div id="flickrTab">
                    
                </div>
            </div>
        </div>
        
        <div class="rightContent"></div>
        <?php endif; ?>
        <?php endif; ?>
	
</div>