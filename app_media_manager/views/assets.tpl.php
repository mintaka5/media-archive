<div>
    <?php if ($this->manager->isMode()): ?>
        <div></div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("download")): ?>
        <div>
            <?php if ($this->manager->isTask("failed")): ?>
                <div>Your download has failed, please, try again at a later time.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("new")): ?>
        <div>
            <?php if ($this->assets): ?>
                <table class="">
                    <tbody>
                    <?php foreach ($this->assets as $asset): ?>
                        <tr>
                            <td><img
                                    src="<?php echo $this->thumber->build($asset->public_id, "{w:100}"); ?>" alt=""/>
                            </td>
                            <td>
                                <div class="break-word"><?php echo $asset->viewTitle(); ?></div>
                                <div><?php echo $asset->viewCaption(); ?></div>
                            </td>
                            <td>
                                <div>Photo credit: <?php echo $asset->viewCredit("n/a"); ?></div>
                            </td>
                            <td>
                                <a href="<?php echo $this->manager->friendlyAction("assets", "edit", null, array("id", $asset->id)); ?>">Edit</a>
                                <a href="#">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div>There are no new assets.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("edit")): ?>
        <div>
            <div>
                <img alt=""
                     src="<?php echo $this->manager->getURI(); ?>imggen.php?id=<?php echo $this->asset->id; ?>&w=300"/>
            </div>
            <div><?php echo $this->form; ?></div>
        </div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("view")): ?>

        <?php if ($this->asset->allowed($this->auth->getSession()->id)): ?>

            <script type="text/javascript"
                    src="<?php echo $this->manager->getURI(); ?>assets/js/jq/tag-it/tag-it.js"></script>
            <script type="text/javascript"
                    src="<?php echo $this->manager->getURI(); ?>assets/js/assets/view.js"></script>
            <script type="text/javascript"
                    src="<?php echo $this->manager->getURI(); ?>assets/js/jq/jquery-ui-timepicker-addon.js"></script>

            <!-- dialogs -->
            <div id="saveMetaDialog">Saving data. Please, wait...</div>
            <!-- end dialogs -->

            <!-- organization assignment window -->
            <?php echo $this->fetch('assets/modals/orgs.tpl.php'); ?>
            <!-- end organization modal window -->

            <!-- photo shoot modal window -->
            <?php echo $this->fetch('assets/modals/shoots.tpl.php'); ?>
            <!-- end photo shoot modal window -->

            <!-- photographers modal window -->
            <?php echo $this->fetch('assets/modals/photographers.tpl.php'); ?>
            <!-- ed photographers modal window -->

            <div class="row">
                <div class="col-lg-9"><?php echo $this->fetch('assets/img.tpl.php'); ?></div>

                <div class="col-lg-3"><?php echo $this->fetch('assets/actions.tpl.php'); ?></div>
            </div>

            <div class="row">
                <div id="addnInfo" role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#infoTab" aria-controls="infoTab" role="tab" data-toggle="tab">Information</a>
                        </li>
                        <li role="presentation">
                            <a href="#capnTab" aria-controls="capnTab" role="tab" data-toggle="tab">Captions</a>
                        </li>
                        <li role="presentation">
                            <a href="#restrictTab" aria-controls="restricTab" role="tab"
                               data-toggle="tab">Restrictions</a>
                        </li>
                        <li role="presentation">
                            <a href="#grpTab" aria-controls="grpTab" role="tab" data-toggle="tab">Sets</a>
                        </li>
                        <li role="presentation">
                            <a href="#locTab" aria-controls="locTab" role="tab" data-toggle="tab">Locations</a>
                        </li>
                        <li role="presentation">
                            <a href="#keywordsTab" aria-controls="keywordsTab" role="tab" data-toggle="tab">Keywords</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="infoTab" role="tabpanel" class="tab-pane active">
                            <?php echo $this->fetch('assets/meta.tpl.php'); ?>
                        </div>

                        <div id="capnTab" role="tabpanel" class="tab-pane">
                            <?php echo $this->fetch('assets/captions.tpl.php'); ?>
                        </div>

                        <!-- Restriction assignments -->
                        <div id="restrictTab" role="tabpanel" class="tab-pane">
                            <?php echo $this->fetch('assets/restrictions.tpl.php'); ?>
                        </div>
                        <!-- end restriction assignments -->

                        <!-- Asset group assignments -->
                        <div id="grpTab" role="tabpanel" class="tab-pane">
                            <?php echo $this->fetch('assets/groups.tpl.php'); ?>
                        </div>
                        <!-- end asset group assignments -->

                        <!-- location tab -->
                        <div id="locTab" role="tabpanel" class="tab-pane">
                            <?php echo $this->fetch('assets/location.tpl.php'); ?>
                        </div>
                        <!-- end location tab ->

                        <!-- keywords tab -->
                        <div id="keywordsTab" role="tabpanel" class="tab-pane">
                            <?php echo $this->fetch('assets/keywords.tpl.php'); ?>
                        </div>
                        <!-- end keywords tab -->
                    </div>
                    <!-- end .tab-content -->
                </div>
            </div>
        <?php endif; // end is allowed check ?>

    <?php endif; // end view _mode ?>

    <?php if ($this->manager->isMode("multi")): ?>
        <div>
            <?php if ($this->manager->isTask("del")): ?>
                <div>
                    <?php if (!empty($this->assets)): ?>
                        <h2>These images have been deleted:</h2>
                        <?php foreach ($this->assets as $asset): ?>
                            <div style="">
                                <div>
                                    <img src="<?php echo $this->thumber->build($asset->public_id, "{w:200}"); ?>"
                                         alt="<?php echo $asset->viewTitle(); ?>"/>
                                </div>
                                <div class="break-word"><?php echo $asset->viewTitle(); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>Nothing to see here.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>