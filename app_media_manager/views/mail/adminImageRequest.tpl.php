<p>
	An image request (Request ID: <strong><?php echo $this->order->order_id; ?></strong>) was made by <?php echo $this->user->fullname(); ?>
</p>
<p>
	Click <a href="<?php echo $this->uri; ?>?_page=orders&amp;_mode=view&amp;id=<?php echo $this->order->id; ?>" title="View requests">here</a> to view request, and review for approval.
</p>