<div>
    <div class="leftContent">
        <?php if($this->manager->isMode('settings')): ?>
        <div>
        	<h2>Information</h2>
        	<div>
        		<table>
        			<thead></thead>
        			<tbody>
        				<tr>
        					<td>Name:</td>
        					<td><?php echo $this->auth->getSession()->fullname(); ?></td>
        				</tr>
        				<tr>
        					<td>Email:</td>
        					<td><?php echo $this->auth->getSession()->email; ?></td>
        				</tr>
        			</tbody>
        		</table>
        	</div>
        </div>
        
        <?php $orgs = $this->auth->getSession()->organizations(); if(!empty($orgs)): ?>
        <div>
        	<h2>Organizations</h2>
        	<ul class="vertMenu">
        		<?php foreach($orgs as $org): ?>
        		<li><?php echo $org->organization()->title; ?></li>
        		<?php endforeach; ?>
        	</ul>
        </div>
        <?php endif; ?>
        
        <div>
            <?php if($this->manager->isTask()): ?>
            <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/account/settings/default.js"></script>
            <div>
                <h2>Account settings</h2>
                <div>
                    <div class="goLeft">
                        API key:
                        <span id="apiKey"><?php echo ($this->auth->getSession()->api() != false) ? $this->auth->getSession()->api()->api_key : ""; ?></span>
                    </div>
                    <div class="goLeft">
                        <input type="hidden" id="userId" value="<?php echo $this->auth->getSession()->id; ?>" />
                        <a href="javascript:void(0);" id="apiKeyGen" title="Generate new API key">Generate new key</a>
                    </div>
                    <br class="clearLeft" />
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
	<?php if($this->manager->isMode("orders")): ?>
	<div>
		<?php if($this->manager->isTask()): ?>
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/account/orders/default.js"></script>
        
		<div class="breadcrumb">
			<ul>
				<li><a href="" title="My Requests" class="active">Requests</a></li>
			</ul>
		</div>
		<h3>My Requests</h3>
		<div>
			<?php if(isset($_GET['from_req'])): ?>
        	<div class="notification">Successfully sent request.</div>
        	<?php endif; ?>
        	
			<?php if(!empty($this->orders)): ?>
			<div>
				<table class="data borders" style="width:600px;">
					<thead>
						<tr>
							<th>ID</th>
							<th>Request Date</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($this->orders as $order): ?>
						<tr>
							<td>
								<?php echo $this->linkAlternate(Ode_Manager::getInstance()->action("account", "orders", "view", array("id", $order->id)), $order->order_id, !(boolean)$order->is_deleted, "Request"); ?>
							</td>
							<td><?php echo $this->date($order->created, "d M Y"); ?></td>
							<td style="text-align:center;">
								<input type="hidden" class="orderId" value="<?php echo $order->id; ?>" /> 
                                
                                <?php if($order->hasDownloads()): ?>
								<button title="Download approved requests" class="downloadBtn btn"></button>
                                <?php endif; ?>
                                
								<button title="Delete request" class="deleteBtn btn"></button>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php else: ?>
			<div class="noresults">You have no current requests.</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<?php if($this->manager->isTask("view")): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/account/orders/view.js"></script>
		<div class="breadcrumb">
			<ul>
				<li><a href="<?php echo $this->manager->friendlyAction("account", "orders"); ?>" title="My Requests">Requests</a></li>
				<li><a href="" title="<?php echo $this->order->order_id; ?>" class="active"><?php echo $this->order->order_id; ?></a></li>
			</ul>
		</div>
		<h3>Request <?php echo $this->order->order_id; ?></h3>
		<div>Date: <?php echo $this->date($this->order->created, "d M Y"); ?></div>
		<!-- <div id="requests"> -->
		<div id="assetContainer">
			<?php $items = $this->order->lineitems(); foreach($items as $item): ?>
			<div class="assetItem">
				<div class="img">
					<!-- <div class="approval <?php //echo $this->binaryToText($item->is_approved, "rejected", "approved"); ?>">
						<?php //echo $this->binaryToText($item->is_approved, "Not approved", "Approved"); ?>
					</div> -->
					<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $item->asset()->public_id)); ?>"><img src="<?php echo $this->thumber->build($item->asset()->public_id, "{w:170}"); ?>" alt="<?php echo $item->asset()->viewTitle(); ?>" /></a>
				</div>
				<div class="options">
					<input type="hidden" class="assetId" value="<?php echo $item->asset()->id; ?>" />
					<input type="hidden" class="simId" value="<?php echo $item->asset()->public_id; ?>" />
					<?php if($item->is_approved): ?>
					<button class="btn downloadBtn" title="Download" onclick="javascript:window.location.href='<?php echo $this->manager->friendlyAction("account", "img", "down", array("id", $item->asset()->public_id)); ?>';"></button>
					<?php endif; ?>
				</div>
				<div class="meta">
					<div>
						<div class="goLeft">Tag &#35;: <?php echo $item->asset()->public_id; ?></div>
						<div class="goRight"><?php echo $this->date($item->asset()->created, "d M Y"); ?></div>
						<div class="clearBoth"><?php echo $this->truncate($item->asset()->viewTitle("Untitled"), 30); ?></div>
						<div>By: <?php echo ($item->asset()->photographer() != false) ? $item->asset()->photographer()->fullname() : "Unknown"; ?></div>
						<div><?php echo $item->asset()->viewCredit(""); ?></div>
						<div><?php echo $item->asset()->viewCaption(""); ?></div>
					</div>
				</div>
			</div>
			<?php endforeach; ?>
			<br class="clearLeft" />
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
    </div>
    
    <div class="rightContent">
        <div class="softBox">
            <h3>Options</h3>
            
            <?php if($this->manager->isMode("orders")): ?>
            <div>
            	<?php if($this->manager->isTask()): ?>
            	<div>No options.</div>
            	<?php endif; ?>
            
                <?php if($this->manager->isTask("view")): ?>
                <div>
                	<ul class="vertMenu">
	                    
	                    <li>
	                    	<a href="<?php echo $this->manager->friendlyAction("account", "orders", "dl", array("id", $this->order->id)); ?>" title="">Download request items</a>
	                    </li>
	                    
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if($this->manager->isMode('settings')): ?>
            <?php if($this->manager->isTask()): ?>
            <div>No options.</div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <?php if($this->manager->isMode("orders")): ?>
        <?php if($this->manager->isTask("view")): ?>
        <!-- Image download TOA -->
        <div class="softBox">
        	<h3>Terms of Service</h3>
            <ul class="vertMenu">
            	<li>By downloading these images, you agree to the <a href="<?php echo $this->manager->friendlyAction("copyright"); ?>" title="Terms of Service">Image Archive Terms of Service</a>.</li>
        	</ul>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <br class="clearLeft" />
</div>