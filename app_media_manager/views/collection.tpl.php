<div class="clearfix">
	<?php if($this->manager->isMode() || $this->manager->isMode("view")): ?>
	<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/collection/view/default.js"></script>
	
	<h1><?php echo $this->container->title(); ?></h1>
	<?php endif; ?>

	<div class="">
		<input type="hidden" id="container_id" value="<?php echo $this->container->id; ?>" />
	
		<div>
			<?php if($this->manager->isMode() || $this->manager->isMode("view")): ?>
			<div id="groupsList" data-id="<?php echo $this->container->id; ?>" class="clearfix"></div>
			<?php endif; ?>
		</div>
		
		<div id="collTabs">
			<ul>
				<li><a href="#infoTab">Information/Actions</a></li>
				<li><a href="#groupsTab">Sets</a></li>
			</ul>
			
			<div id="infoTab" class="clearfix">
				<div class="goLeft">
					<h3>Information</h3>
					<div>
						<label for="collTitle">Title:</label>
						<div>
							<input type="text" data-id="<?php echo $this->container->id; ?>" name="collTitle" class="textField" id="colltitle" value="<?php echo $this->container->title(); ?>" />
							<input type="button" id="saveCollTitle" value="Save" class="goBtn" />
						</div>
					</div>
					
					<div>
						<label for="collDesc">Description</label>
						<div>
							<textarea class="descArea" data-id="<?php echo $this->container->id; ?>"><?php echo $this->container->description; ?></textarea>
							<input type="button" id="saveCollDesc" value="Save" class="goBtn" />
						</div>
					</div>
				</div>
				
				<div class="goLeft" style="margin-left:20px;">
					<h3>Actions</h3>
					<ul class="vertMenu">
						<li>
							<input type="checkbox" id="chxCollAppr" value="<?php echo $this->container->id; ?>" <?php echo $this->binaryToText($this->container->is_approved, "", 'checked="checked"'); ?> />
							<span id="spanCollAppr"><?php echo $this->binaryToText($this->container->is_approved, "Private", "Public"); ?></span>
						</li>
					</ul>
				</div>
			</div>
			<div id="groupsTab">
				<div>
					<div id="addSetSearch">
						<label for="">Search for a set:</label>
						<input type="text" name="setSearch" id="setSearch" />
						<input type="button" id="btnSetSearch" value="Search" />
					</div>
					
					<div id="setsToAdd" class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- <div class="rightContent"></div> -->
</div>