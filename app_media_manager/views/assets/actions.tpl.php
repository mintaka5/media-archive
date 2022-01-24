<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Actions</h3>
    </div>

    <div class="panel-body">

        <div class="list-group">
            <?php if ($this->auth->isManager()): ?>
                <a class="list-group-item" href="<?php echo $this->manager->friendlyAction("assets", "del", null, array("id", $this->asset->id)); ?>"
                   id="assetDelete" title="Delete this asset!"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete</a>
                <a class="list-group-item" href="#" id="apprvAsset" data-isactive="<?php echo $this->asset->is_active; ?>"
                   title="<?php echo $this->binaryToText($this->asset->is_active, "Make public!", "Make private!"); ?>"><span class="glyphicon glyphicon-thumbs-up"></span> Status</a>
            <?php endif; ?>

            <?php if ($this->auth->isArchivist() || $this->auth->isManager()): ?>
                <a class="list-group-item" href="#" id="saveMeta"
                   title="Write all data to image file"><span class="glyphicon glyphicon-floppy-save"></span> Save metadata</a>
                <a class="aDownload list-group-item"
                   href="<?php echo $this->manager->friendlyAction("assets", "download", "single", array("id", $this->asset->id)); ?>"><span class="glyphicon glyphicon-download"></span> Download
                    (size: <?php echo $this->filesize; ?>)</a>
            <?php endif; ?>

            <?php if ($this->auth->isAdmin()): ?>
                <a class="list-group-item" href="#" data-hidden="<?php echo $this->binaryToText($this->asset->isFeatured(), "0", "1"); ?>"
                   id="setFeature"
                   title="<?php echo $this->binaryToText($this->asset->isFeatured(), "Make featured!", "Remove from featured!"); ?>"><span class="glyphicon glyphicon-star"></span> Featured?
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
