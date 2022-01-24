<div id="groupView">
	<div id="rightContent" class="leftContent">
		<?php if(Ode_Manager::getInstance()->isMode()): ?>
		<div class="breadcrumb">
			<ul>
				<li>
					<a href="<?php echo $this->manager->friendlyAction("browse", "groups"); ?>" title="Back to browsing all sets">Sets</a>
				</li>
				<li>
					<a class="active" href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $this->group->id)); ?>"><?php echo $this->group->title(); ?></a>
				</li>
			</ul>
		</div>
		<div><?php echo $this->fetch("group_view/list.tpl.php"); ?></div>
		<?php endif; ?>
	</div>
	
	<div id="leftContent" class="rightContent">
		<?php if(($this->auth->isEditor() || $this->auth->isPhotographer() || $this->isArchivist()) && !$this->auth->isGuest()): ?>
		<div class="softBox">
			<h3>Admin options</h3>
			<div>
				<ul class="vertMenu">
					<li><a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $this->group->id)); ?>">Edit <?php echo $this->group->title(); ?></a></li>
				</ul>
			</div>
		</div>
		<?php endif; ?>
	
	
                <div class="softBox">
                    <h3>Search</h3>
                    <div>
                        <form method="get" action="">
                            <input type="hidden" name="_page" value="search" />
                            <input class="textField" type="text" name="terms" id="browseTerms" value="" />
                            <input type="submit" class="goBtn" value="Go" />
                        </form>
                    </div>
                </div>
		<?php if(Ode_Manager::getInstance()->isMode()): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/group_view/default.js"></script>
		<div>
			<div id="" class="softBox">
				<h3>Recent Cart Items</h3>
				<div id="cartPreview"></div>
			</div>
		</div>
		<?php endif; ?>
	</div>
	<br class="clearLeft" />
</div>