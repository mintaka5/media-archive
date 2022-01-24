<p>Dear <?php echo $this->order->user()->fullname(); ?>,</p>
<p>Your request <?php echo $this->order->order_id; ?>, is ready for your review, and if assets were approved, ready for download as well.</p>
<p>Use this link to review your request: <br /><a href="<?php echo $this->linkback; ?>"><?php echo $this->linkback; ?></a></p>
<p>Explanation of approval terms:</p>
<p><?php echo $this->message; ?></p>