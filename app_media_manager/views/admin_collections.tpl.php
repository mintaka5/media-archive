<div class="clearfix">
	<h1>Collections</h1>
	
	<div class="leftContent">
		<?php if($this->manager->isMode()): ?>
		<div>
			<?php if($this->manager->isTask()): ?>
			<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/admin_collections/default/default.js"></script>
			
			<div id="containerLister">
				<?php if(!empty($this->containers)): ?>
				
				<?php foreach ($this->containers as $container): ?>
				<div class="containerListItem">
					<div class="title">
						<a href="<?php echo $this->manager->friendlyAction("collection", "view", null, array("id", $container->id)); ?>"><?php echo $container->title(); ?></a>
					</div>
					<div class="content">
						<div class="">
							<div class="description"><?php echo $this->truncate($container->description, 100); ?></div>
							<div class="menu clearfix">
								<div class="goLeft options">
									<a href="<?php echo $this->manager->friendlyAction("collection", "view", null, array("id", $container->id)); ?>">Edit</a>
								</div>
								<div class="options goLeft">
									<a href="#" data-id="<?php echo $container->id; ?>" class="rmvContainer">Remove</a>
								</div>
							</div>
							<div class="orgs">
								Belongs to: <?php echo $container->organizations("n/a"); ?>
							</div>
						</div>
					</div>
				</div>
				<?php endforeach; ?>
				
				<?php else: ?>
				<div>No containers available.</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="rightContent">
		<?php if($this->auth->isManager() || $this->auth->isArchivist() || $this->auth->isEditor()): ?>
		<div class="softBox">
			<h3>Add Collection</h3>
			<div>
				<div>
					<form action="" method="post">
						<input type="hidden" name="_page" value="<?php echo $this->manager->getPage(); ?>" />
						<input type="hidden" name="_mode" value="add" />
						<label for="collTitle">Title:</label>
						<input name="collTitle" id="collTitle" type="text" class="textField" style="width:99px;" />
						<input type="submit" value="Add" id="addCollBtn" class="goBtn" />
						<p style="margin-top:10px; margin-bottom:10px;">Please, select the organization(s) you need to have the new collection assigned to. If none or all are checked all of the ones listed below will be used.</p>
						<ul class="vertMenu">
							<?php foreach($this->user_orgs as $org): ?>
							<li>
								<input type="checkbox" checked="checked" name="userOrg[]" value="<?php echo $org->org_id; ?>" />
								<?php echo $org->organization()->title; ?>
							</li>
							<?php endforeach; ?>
						</ul>
					</form>
				</div>
			</div>
		</div>
		
		<div class="softBox">
			<h3>Options</h3>
			<div>
				<ul class="vertMenu">
					<li></li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>