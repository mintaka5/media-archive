<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/admin_assets/default.js"></script>

<h1>Assets</h1>

<div class="row">
    <div class="col-lg-9">
        <?php if ($this->manager->isMode()): ?>
            <div class="row">
                <?php if ($this->manager->isTask()): ?>
                    <div><?php echo $this->fetch("admin_assets/list.tpl.php"); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($this->manager->isMode("by")): ?>
            <div>
                <?php if ($this->manager->isTask("org")): ?>
                    <div><?php echo $this->fetch("admin_assets/list.tpl.php"); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($this->manager->isMode("search")): ?>
            <div>
                <?php if ($this->manager->isTask()): ?>
                    <div><?php echo $this->fetch("admin_assets/list.tpl.php"); ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($this->manager->isMode("edit")): ?>
            <div>
                <?php if ($this->manager->isTask("batch")): ?>
                    <script type="text/javascript"
                            src="<?php echo $this->manager->getURI(); ?>assets/js/admin_assets/edit/batch.js"></script>

                    <div>
                        <?php echo $this->fetch("admin_assets/batch_edit.tpl.php"); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="col-lg-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Search images</h3>
            </div>

            <div class="panel-body">
                <form method="get" action="<?php echo $this->manager->action("admin_assets", "search"); ?>">
                    <input type="hidden" name="_page" value="admin_assets"/>
                    <input type="hidden" name="_mode" value="search"/>

                    <div class="input-group">
                        <input type="text" name="q" id="assetQuery" placeholder="Search for..." class="form-control"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" id="queryBtn">Go!</button>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Organizations</h3>
            </div>

            <div class="panel-body">
                <p>Select from menu to acquire assets by organization or what departments assets belong to.</p>

                <form id="limitOrgs" action="" method="get">
                    <input type="hidden" name="_page" value="admin_assets"/>
                    <input type="hidden" name="_mode" value="by"/>
                    <input type="hidden" name="_task" value="org"/>

                    <select class="form-control" id="org_id" name="org_id">
                        <option value="">all</option>
                        <?php foreach ($this->userorgs as $userorg): ?>
                            <option <?php echo ($userorg->id == $this->curorgid) ? 'selected="selected"' : ''; ?>
                                value="<?php echo $userorg->id; ?>"><?php echo $userorg->title; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Options</h3>
            </div>

            <div class="panel-body">
                <div>
                    <a class="btn btn-default" href="<?php echo $this->manager->friendlyAction("quick_upload"); ?>" title="">Upload new
                        images</a>
                </div>

                <div id="editBatch">
                    <?php if (!$this->manager->isMode("edit") && $this->assetmanager->haveAssets()): ?>
                        <div class="btn-group" role="group" aria-label="...">
                            <a class="btn btn-default" href="<?php echo $this->manager->friendlyAction("group", "view", null, array("id", $this->assetmanager->getGroup()->id)); ?>">Edit
                                selected assets</a>
                            <a href="#" class="btn btn-default" id="clearEdits">Clear all</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div> <!-- end .row -->