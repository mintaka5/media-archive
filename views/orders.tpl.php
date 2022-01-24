<div id="adminOrderView">
    <div id="leftContent" class="leftContent">
	<?php if($this->manager->isMode('view')): ?>
	<div>
		<?php if($this->manager->isTask()): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/orders/view/default.js"></script>
		<div>
			<?php $items = $this->order->lineitems(); if(!empty($items)): ?>
			<div id="orderView">
					<h3>Order <?php echo $this->order->order_id; ?></h3>
					<div id="info">Request made by: <?php echo $this->order->user()->fullname(); ?></div>
					<?php foreach($items as $item): ?>
					<div class="orderItem">
						<div class="title">
							<?php echo $item->asset()->viewTitle(); ?>
							(<span class="approval <?php echo $this->binaryToText($item->is_approved, "rejected", "approved"); ?>"><?php echo $this->binaryToText($item->is_approved, "Rejected", "Approved"); ?></span>)
						</div>
						<div>
							<div class="img goLeft">
								<div><img src="<?php echo $this->thumber->build($item->asset()->public_id, "{w:100,h:100}"); ?>" alt="<?php echo $item->asset()->viewTitle(); ?>" /></div>
								<div class="options">
									<input type="hidden" class="itemID" value="<?php echo $item->id; ?>" />
									<button title="Approve request" class="apprBtn btn"></button>
									<button title="Reject request" class="unapprBtn btn"></button>
								</div>
							</div>
							<div class="info goLeft">
								<table class="assetInfo">
									<tbody>
										<tr>
											<th>Date Created:</th>
											<td><?php echo $this->date($item->asset()->created, "d M Y"); ?></td>
										</tr>
										<tr>
											<th>Image Tag &#35;:</th>
											<td><?php echo $item->asset()->public_id; ?></td>
										</tr>
									</tbody>
								</table>
							</div>
							<br class="clearLeft" />
						</div>
					</div>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
			<div class="noresults">No items were requested.</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
    
        <?php if($this->manager->isMode()): ?>
        <div>
            <?php if($this->manager->isTask()): ?>
            <h3>Asset Requests</h3>
            <div>
                <?php if(!empty($this->orders)): ?>
                <div>
                    <table class="data borders" style="width:660px;">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>No. of assets</th>
                                <th>Requested by</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->orders as $num => $order): ?>
                            <tr class="<?php echo ($num%2==0) ? "" : "offWhite" ?>">
                                <td><a href="<?php echo $this->manager->friendlyAction("orders", "view", null, array("id", $order->id)); ?>"><?php echo $order->order_id; ?></a></td>
                                <td class="center"><?php echo count($order->lineitems()); ?></td>
                                <td><?php echo $order->user()->fullname(); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div><?php echo $this->pagelinks['all']; ?></div>
                <?php else: ?>
                <div class="noresults">No request have been made.</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Search results -->
        <?php if($this->manager->isMode("search")): ?>
        <div>
        	<h3>Request search:</h3>
            <div>
                <?php if(!empty($this->orders)): ?>
                <div>
                    <table class="data borders" style="width:660px;">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>No. of assets</th>
                                <th>Requested by</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->orders as $num => $order): ?>
                            <tr class="<?php echo ($num%2==0) ? "" : "offWhite" ?>">
                                <td><a href="<?php echo $this->manager->friendlyAction("orders", "view", null, array("id", $order->id)); ?>"><?php echo $order->order_id; ?></a></td>
                                <td class="center"><?php echo count($order->lineitems()); ?></td>
                                <td><?php echo $order->user()->fullname(); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div><?php echo $this->pagelinks['all']; ?></div>
                <?php else: ?>
                <div class="noresults">Your search returned no results.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div id="rightContent" class="rightContent">
        <?php if($this->manager->isMode("view")): ?>
        <div id="notifierDialog">
            <input type="hidden" id="notificationOrder" value="<?php echo $this->order->id; ?>" />
            <div>Sending notification to <?php echo $this->order->user()->email; ?> for request <?php echo $this->order->order_id; ?></div>
            <div class="formElement">
                <label for="">Message</label>
                <div class="input">
                    <textarea style="width:300px; height:100px;" id="txtNotification"></textarea>
                </div>
            </div>
            <div><button id="sendNotification">Send</button></div>
        </div>
        
        <div class="softBox">
            <h3>Options</h3>
            <div>
                <ul class="vertMenu">
                    <li><a href="javascript:void(0);" id="aNotifier" title="Notify, who requested these assets, of the status.">Send notification</a></li>
                </ul>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="softBox">
        	<h3>Reuest Search</h3>
        	<div>
        		<form method="get" action="">
        			<input type="hidden" name="_page" value="orders" />
        			<input type="hidden" name="_mode" value="search" />
        			<input type="hidden" name="pageNum" value="<?php echo (isset($_GET['pageNum']) ? trim($_GET['pageNum']) : 1); ?>" />
        			<div class="">
                        <div class="input">
                                <input type="text" class="textField qry" name="q" id="qry" />
                                <input type="submit" value="Find" class="goBtn" />
                        </div>
               		</div>
        		</form>
        	</div>
        </div>
    </div>
    
    <br class="clearLeft" />
    
</div>