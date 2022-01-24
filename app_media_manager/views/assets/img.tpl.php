<div class="panel panel-default">
    <div class="panel-heading">
    <h3 class="panel-title">
        <span id="titleTxt" class="break-word"><?php echo $this->asset->viewTitle(); ?></span>
    </h3>
        </div>

    <div id="previewImg" class="panel-body">
        <div>
            <img class="img-responsive" src="<?php echo $this->thumber->build($this->asset->public_id, "{w:450}"); ?>"
                 alt="<?php echo $this->asset->viewTitle(); ?>"/>
        </div>
        <?php if ($this->asset->isActive()): ?>
            <div>
                <a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $this->asset->public_id)); ?>"
                   title="view public ">Public view</a>
            </div>
        <?php endif; ?>
    </div>
</div>