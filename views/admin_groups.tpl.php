<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/admin_groups/default.js"></script>
<div>
	<h1>Sets</h1>

	<div class="leftContent">
		<?php if($this->manager->isMode()): ?>
		<div>
			<?php if($this->manager->isTask()): ?>
			<div>
				<?php if(!empty($this->groups)): ?>
				
				<div class="pagerTop">Viewing <?php echo $this->num_items; ?> result(s)</div>
				
				<?php if($this->num_pages > 1): ?>
				<div class="pagerTop">Pages: <?php echo $this->assetlinks['all']; ?></div>
				<?php endif; ?>
				
				<div class="clearfix assetLister">
					<?php foreach($this->groups as $group): ?>
					<div class="assetListItem">
						<div class="title">
							<div class="goLeft"><?php echo $group->title("Untitled"); ?></div>
							<div class="goRight listTR">
								<span class="<?php echo $this->binaryToText($group->is_approved, "red", "green"); ?>"><?php echo $this->binaryToText($group->is_approved, "Private", "Public"); ?></span>
							</div>
						</div>
						<div class="clearfix content">
							<div class="goLeft image">
								<?php if($group->assets() != false): ?>
								<img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:100,h:100}"); ?>" alt="<?php echo $group->defaultAsset()->title(); ?>" />
								<?php else: ?>
								<div style="background:#bfbfbf; width:100px; height:100px;"></div>
								<?php endif; ?>
							</div>
							<div class="goLeft info">
								<div class="clearfix item">
									<div class="goLeft">Date created:</div>
									<div class="goLeft"><?php echo $this->date($group->created, "d M Y"); ?></div>
								</div>
								
								<div class="clearfix item">
									<?php $assets = $group->assets(); if(!empty($assets)): ?>
									
									<div style="margin-bottom:5px;">(preview of up to 5 images only)</div>
									
									<div class="clearfix">
										<?php for($i=0; $i<5; $i++): ?>
										
										<?php if($assets[$i] instanceof DBO_Asset_Model): ?>
										<div class="goLeft imgPreview">
											<img src="<?php echo $this->thumber->build($assets[$i]->public_id, "{w:50,h:50,zc:true}"); ?>" alt="<?php echo $assets[$i]->viewTitle(); ?>" />
										</div>
										<?php endif; ?>
										
										<?php endfor; ?>
									</div>
									<?php endif; ?>
								</div>
								
								<div class="clearfix item">
									<input type="hidden" class="groupId" value="<?php echo $group->id; ?>" />
									<div class="goLeft options">
										<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>" title="<?php echo $this->truncate($group->title(), 20); ?>">Edit</a>
									</div>
									
                                                                        <?php if($this->auth->isAdmin() && $group->is_approved == 1): // only admins can set featured sets ?>
									<div class="goLeft options">
                                                                                <?php Misc::featuredGroupsCapped(DBO_Properties::FEATURED_GROUPS, 4); ?>
										<?php if(!$group->isFeatured() && !Misc::featuredGroupsCapped(DBO_Properties::FEATURED_GROUPS, 4)): // toggle featured/not featured and check to see if the limit has been reached. ?>
										<a class="" href="<?php echo $this->manager->friendlyAction("admin_groups", "featured", "yes", array("id", $group->id), array("pageID", $this->pageid)); ?>">Set featured</a>
										<?php else: ?>
										<a class="" href="<?php echo $this->manager->friendlyAction("admin_groups", "featured", "no", array("id", $group->id), array("pageID", $this->pageid)); ?>">Set unfeatured</a>
										<?php endif; ?>
									</div>
                                                                        <?php endif; // end admin check ?>
									
									<?php if($this->auth->isManager() && $group->numTotalAssets() > 0): ?>
									<div class="goLeft options">
										<a href="<?php echo $this->manager->friendlyAction("group", "download", null, array("id", $group->id)); ?>" title="Download all images in this group.">Download images</a>
									</div>
									<?php endif; ?>
									
									<div class="goLeft options">
										<a class="rmv rmvGroup" href="#">Remove</a>
									</div>
								</div>
								
								<div class="">
									<em>Belongs to:</em>
									<?php echo $group->organizations("Not assigned."); ?>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				
				<?php if($this->num_pages > 1): ?>
				<div class="pagerBottom">Pages: <?php echo $this->assetlinks['all']; ?></div>
				<?php endif; ?>
				
				<?php else: ?>
				<div>No groups</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<?php if($this->manager->isMode("search")): ?>
		<div>
			<?php if($this->manager->isTask()): ?>
			<div>
			
				<?php if(!empty($this->groups)): ?>
				
				<div style="margin-bottom:5px;">Viewing <?php echo $this->num_items; ?> result(s) for &quot;<?php echo $this->query; ?>&quot;</div>
			
				<?php if($this->num_pages > 1): ?>
				<div class="pagerTop">Pages: <?php echo $this->assetlinks['all']; ?></div>
				<?php endif; ?>
				
				<div class="clearfix assetLister">
					<?php foreach($this->groups as $group): ?>
					<div class="assetListItem">
						<div class="title"><?php echo $group->title("Untitled"); ?></div>
						<div class="clearfix content">
							<div class="goLeft image">
								<?php if($group->assets() != false): ?>
								<img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:100,h:100}"); ?>" alt="<?php echo $group->defaultAsset()->title(); ?>" />
								<?php else: ?>
								<div style="background:#bfbfbf; width:100px; height:100px;"></div>
								<?php endif; ?>
							</div>
							<div class="goLeft info">
								<div class="clearfix item">
									<div class="goLeft">Date created:</div>
									<div class="goLeft"><?php echo $this->date($group->created, "d M Y"); ?></div>
								</div>
								
								<div class="clearfix item">
									<?php $assets = $group->assets(); if(!empty($assets)): ?>
									
									<div style="margin-bottom:5px;">(preview of up to 5 images only)</div>
									
									<div class="clearfix">
										<?php for($i=0; $i<5; $i++): ?>
										
										<?php if($assets[$i] instanceof DBO_Asset_Model): ?>
										<div class="goLeft imgPreview">
											<img src="<?php echo $this->thumber->build($assets[$i]->public_id, "{w:50,h:50,zc:true}"); ?>" alt="<?php echo $assets[$i]->viewTitle(); ?>" />
										</div>
										<?php endif; ?>
										
										<?php endfor; ?>
									</div>
									<?php endif; ?>
								</div>
								
								<div class="clearfix item">
									<div class="goLeft options">
										<a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>" title="<?php echo $this->truncate($group->title(), 20); ?>">Edit</a>
									</div>
									
									<div class="goLeft options">
										<?php if(!$group->isFeatured()): ?>
										<a class="" href="<?php echo $this->manager->friendlyAction("admin_groups", "featured", "yes", array("id", $group->id), array("pageID", $this->pageid), array("r_mode", $this->manager->getMode(), array("q", $this->query))); ?>">Set featured</a>
										<?php else: ?>
										<a class="" href="<?php echo $this->manager->friendlyAction("admin_groups", "featured", "no", array("id", $group->id), array("pageID", $this->pageid), array("r_mode", $this->manager->getMode(), array("q", $this->query))); ?>">Set unfeatured</a>
										<?php endif; ?>
									</div>
									
									<div class="goLeft options">
										<a class="rmv rmvGroup" href="#">Remove</a>
									</div>
								</div>
								
								<div class="">
									<em>Belongs to:</em>
									<?php echo $group->organizations("Not assigned."); ?>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</div>
				
				<?php if($this->num_pages > 1): ?>
				<div class="pagerBottom">Pages: <?php echo $this->assetlinks['all']; ?></div>
				<?php endif; ?>
				
				<?php else: ?>
				<div>No results</div>
				<?php endif; ?>
				
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="rightContent">
		<div class="softBox">
			<h3>Search sets</h3>
			<div>
				<form method="get" action="">
					<input type="hidden" name="_page" value="admin_groups" />
					<input type="hidden" name="_mode" value="search" />
					<input type="text" name="q" id="groupQuery" class="textField" value="" />
					<input type="submit" id="queryBtn" value="Go" class="goBtn" />
				</form>
			</div>
		</div>
		
		<div class="softBox">
			<h3>Add new set</h3>
			<div>
				<form method="post" action="">
					<div>
						<input type="hidden" name="_page" value="admin_groups" />
						<input type="hidden" name="_mode" value="add" />
						<label for="groupTitle">Title:</label>
						<input type="text" name="groupTitle" id="groupTitle" class="textField" value="" />
						<input type="submit" value="Add" id="addGrpBtn" class="goBtn" />
					</div>
					<p style="margin-top:10px; margin-bottom:10px;">Please, select the organization(s) you need to have the new set assigned to. If none or all are checked all of the ones listed below will be used.</p>
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
</div>