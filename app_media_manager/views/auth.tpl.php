<div>
	<?php if($this->manager->isMode()): ?>
	<?php if(isset($this->login_error)): ?>
	<div>Login failed</div>
	<?php endif; ?>
	<div><?php echo $this->form; ?></div>
	<?php endif; ?>
</div>