<div id="browseView">
	<div id="rightContent" class="leftContent">
		<?php if(Ode_Manager::getInstance()->isMode()): ?>
		<div>
        	<h2>Welcome, to the Image Archive</h2>
        </div>
		<?php endif; ?>
		
		<?php if(Ode_Manager::getInstance()->isMode("groups")): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.imagesloaded.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery.masonry.min.js"></script>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/browse/groups.js"></script>
		
		<div class="breadcrumb">
			<ul>
				<li><a href="" title="Reload sets." class="active">Sets</a></li>
			</ul>
		</div>
		<div>
			<?php if(!empty($this->groups)): ?>
			<div class="pagerTop"><?php echo $this->pagelinks['all']; ?></div>
			
			<div id="groupsTiles" class="clearfix">
				<?php echo $this->fetch("browse/group_list.tpl.php"); ?>
			</div>
			
			<div class="pagerBottom"><?php echo $this->pagelinks['all']; ?></div>
			<?php else: ?>
			<div class="noresults">There are currently no image groups at this time.</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div id="leftContent" class="rightContent">
            <div>
                <div class="softBox">
                    <h3>Search</h3>
                    <div>
                        <form method="get" action="">
                            <input type="hidden" name="_page" value="search" />
                            <input class="textField" type="text" name="terms" id="browseTerms" value="" />
                            <input class="goBtn" type="submit" value="Go" />
                        </form>
                    </div>
                </div>
            </div>
            <?php if(Ode_Manager::getInstance()->isMode()): ?>
            <div>
                
            </div>
            <?php endif; ?>

            <?php if(Ode_Manager::getInstance()->isMode("groups")): ?>
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