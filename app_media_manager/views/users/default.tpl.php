<?php if($this->manager->isTask()): ?>
    <div><?php echo $this->fetch('users/default/default.tpl.php'); ?></div>
<?php endif; // end default _task ?>