<h1>Administration Area</h1>

<div class="row">
    <div class="col-lg-9">
        <?php if ($this->manager->isMode()): ?>
            <?php echo $this->fetch('admin/default.tpl.php'); ?>
        <?php endif; ?>

        <?php if($this->manager->isMode('metadata')): echo $this->fetch('admin/metadata.tpl.php'); endif; ?>
    </div>

    <div class="col-lg-3">
        <?php echo $this->fetch("admin/sidebar.tpl.php"); ?>
    </div>
</div>