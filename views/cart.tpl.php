<div>
	<div id="cartLeft" class="leftContent">
		<?php if($this->manager->isMode()): ?>
		<h3>Your cart</h3>
		
		<div style="color:red;">Cart contents will expire after logging out.</div>
		
		<div>
			<?php if($this->manager->isTask()): ?>
			<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/cart/default.js"></script>
			<div id="cartItemsHolder">
				<?php echo $this->fetch("ajax/cart/items.tpl.php"); ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	
	<div id="cartRight" class="rightContent">
		<?php if($this->manager->isMode()): ?>
		<div>
			<?php if($this->manager->isTask()): ?>
			<div class="softBox">
				<h3>Options</h3>
				<?php if(!empty($this->items)): ?>
				<ul class="vertMenu">
					<!-- <li>
						<a href="<?php //echo $this->manager->friendlyAction("cart", "request"); ?>" title="Request all items in your cart.">Request items</a>
					</li> -->
					<li>
						<a href="<?php echo $this->manager->friendlyAction("cart", "download", null, array("id", Order::getInstance()->getOrderId())); ?>" title="">Download cart items</a>
					</li>
				</ul>
				<?php else: ?>
				
				<?php endif; ?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
		
		<!-- Image download TOA -->
        <div class="softBox">
        	<h3>Terms of Service</h3>
            <ul class="vertMenu">
            	<li>By downloading these images, you agree to the <a href="<?php echo $this->manager->friendlyAction("copyright"); ?>" title="Terms of Service">Image Archive Terms of Service</a>.</li>
        	</ul>
        </div>
	</div>
	<br class="clearLeft" />
</div>