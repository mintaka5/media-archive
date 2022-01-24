
    <?php if ($this->auth->isManager() || $this->auth->isArchivist() || $this->auth->isEditor() || $this->auth->isPhotographer()): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Admin Search</h3>
            </div>
            <div class="panel-body">
                <form method="get" action="">
                    <input type="hidden" name="_page" value="admin_assets"/>
                    <input type="hidden" name="_mode" value="search"/>

                    <div class="input-group">
                        <input type="text" name="q" id="assetQuery" placeholder="Search for..."
                               class="form-control"/>
                            <span class="input-group-btn">
                                <button class="btn btn-default">Go!</button>
                            </span>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->auth->isManager() || $this->auth->isArchivist() || $this->auth->isEditor() || $this->auth->isPhotographer()): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Options</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="<?php echo $this->manager->friendlyAction('quick_upload'); ?>" class="list-group-item">Upload
                        new image</a>
                    <a href="<?php echo $this->manager->friendlyAction('admin_groups', 'add'); ?>"
                       class="list-group-item">Add
                        new group</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->auth->isAdmin()): ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Archive Management</h3>
            </div>
            <div class="panel-body">
                <div class="list-group">
                    <a href="<?php echo $this->manager->friendlyAction('admin', 'metadata'); ?>"
                       class="list-group-item">Metadata</a>
                </div>
            </div>
        </div>
    <?php endif; ?>