<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recently uploaded</h3>
    </div>
    <div class="panel-body">
        <?php foreach ($this->recents as $asset): ?>
            <div class="assetListItem">
                <div class="title"><?php echo $asset->viewTitle(); ?></div>
                <div class="clearfix content">
                    <div class="goLeft image">
                        <img src="<?php echo $this->thumber->build($asset->public_id, "{w:100,h:100}"); ?>"
                             alt="<?php echo $asset->viewTitle(); ?>"/>
                    </div>
                    <div class="goLeft info">
                        <div class="clearfix item">
                            <div class="goLeft">Date created:</div>
                            <div class="goLeft"><?php echo $this->date($asset->created, "d M Y"); ?></div>
                        </div>
                        <div class="clearfix item">
                            <div class="goLeft">Caption:</div>
                            <div class="goLeft"><?php echo $asset->finalCaption(); ?></div>
                        </div>
                        <div class="clearfix">
                            <div class="goLeft">
                                <a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $asset->id)); ?>"
                                   title="<?php echo $this->truncate($asset->viewTitle(), 20); ?>">Edit</a></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <div>
            <a href="<?php echo $this->manager->friendlyAction("admin_assets"); ?>" title="List all image assets">List
                all...</a>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Recent groups</h3>
    </div>

    <div class="panel-body">
        <?php if (!empty($this->groups)): ?>
                <?php foreach ($this->groups as $group): ?>
                <?php echo $group->title("Untitled"); ?>
                <?php echo $group->numTotalAssets(); ?> image(s)
                <a href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $group->id)); ?>">Edit</a>
                <?php endforeach; ?>
            <div>
                <a href="<?php echo $this->manager->friendlyAction("admin_groups"); ?>" title="">All groups...</a>
            </div>
        <?php endif; ?>
    </div>
</div>