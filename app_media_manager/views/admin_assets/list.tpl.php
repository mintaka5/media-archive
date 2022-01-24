<?php if (!empty($this->assets)): ?>

    <?php foreach ($this->assets as $asset): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <input type="checkbox"
                       class="chkEditAsset" <?php echo ($this->assetmanager->isInEdits($asset->id)) ? 'checked="checked"' : ""; ?>
                       data-group="<?php echo $this->assetmanager->getGroup()->id; ?>"
                       value="<?php echo $asset->id; ?>"/>

                <h2 class="panel-title break-word"><?php echo $asset->viewTitle(); ?></h2>
                <!-- <div class="">
                    <span
                        class="<?php //echo $this->binaryToText($asset->isPublic(), "red", "green"); ?>"><?php //echo $this->binaryToText($asset->isPublic(), "Private", "Public"); ?></span>
                </div> -->
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col col-lg-3">
                        <a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $asset->id)); ?>"
                           title="Edit this image"><img class="img img-responsive"
                                                         src="<?php echo $this->thumber->build($asset->public_id, "{w:100,h:100}"); ?>"
                                                         alt="<?php echo $asset->viewTitle(); ?>"/></a>
                    </div>

                    <div class="col col-lg-9">
                        <div class="clearfix">
                            <div class="pull-left">Date created:</div>
                            <div class=""><?php echo $this->date($asset->created, "d M Y"); ?></div>
                        </div>
                        <div class="clearfix">
                            <div class="pull-left">Caption:</div>
                            <div class=""><?php echo $asset->finalCaption(); ?></div>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $asset->id)); ?>"
                               title="<?php echo $this->truncate($asset->viewTitle(), 20); ?>" class="btn btn-default">Edit</a>

                            <?php if ($this->auth->isAdmin()): ?>

                                <?php if (!$asset->isFeatured()): ?>
                                    <a href="<?php echo $this->manager->friendlyAction("assets", "feature", null, array("id", $asset->id), array("pageID", $this->pageid)); ?>"
                                       title="Set featured" class="btn btn-default">Set featured</a>
                                <?php endif; ?>

                            <?php endif; ?>

                            <a data-asset="<?php echo $asset->id; ?>" href="#" class="rmvAsset rmv btn btn-default"
                               title="Remove image from archive permanently">Remove</a>
                        </div>

                        <div class=""><em>Belongs
                                to:</em> <?php echo $asset->organizations("Not assigned to an organization."); ?></div>
                    </div>
                </div>
            </div>

        </div>
    <?php endforeach; ?>

<?php else: ?>
    <div>No results</div>
<?php endif; ?>