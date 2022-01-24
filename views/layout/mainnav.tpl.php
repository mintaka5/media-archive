<ul class="nav navbar-nav">
    <li>
        <a href="<?php echo $this->manager->getURI(); ?>" title="Return to the main page">Home</a>
    </li>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Browse <span class="caret"></span></a>
        <ul class="dropdown-menu" role="menu">
            <li>
                <a href="<?php echo $this->manager->friendlyAction("browse_collections"); ?>" title="Browse Collections">Collections</a>
            </li>
            <li>
                <a href="<?php echo $this->manager->friendlyAction("browse", "groups"); ?>" title="Browse Sets">Sets</a>
            </li>
        </ul>
    </li>

    <?php if ($this->auth->isAuth()): ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">My Account <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("account", "orders"); ?>">Requests</a>
                </li>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("account", "settings"); ?>">Settings</a>
                </li>
            </ul>
        </li>
    <?php endif; ?>

    <?php if ($this->auth->isManager() || $this->auth->isArchivist() || $this->auth->isEditor()): ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button">Tools <span class="caret"></span></a>
            <ul class="dropdown-menu" role="menu">
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("admin"); ?>">Admin</a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("admin_assets"); ?>">Assets</a>
                </li>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("quick_upload"); ?>" title="Add a new image to archive">New Images</a>
                </li>
                <li class="divider"></li>
                <?php if ($this->auth->isManager() || $this->auth->isAdmin() || $this->auth->isArchivist()): ?>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("admin_collections"); ?>" title="Manage collections">Collections</a>
                </li>
                <?php endif; ?>

                <li>
                    <a href="<?php echo $this->manager->friendlyAction("admin_groups"); ?>">Sets</a>
                </li>
                <li class="divider"></li>

                <?php if ($this->auth->isAdmin() || $this->auth->isArchivist() || $this->auth->isEditor()): ?>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("keywords"); ?>" title="Manage keywords">Keywords</a>
                </li>
                <?php endif; ?>

                <?php if ($this->auth->isAdmin() || $this->auth->isManager()): ?>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("orgs"); ?>">Organizations</a>
                </li>
                <?php endif; ?>

                <?php if ($this->auth->isAdmin() || $this->auth->isManager()): ?>
                <li>
                    <a href="<?php echo $this->manager->friendlyAction("users"); ?>" title="Manage archive users">Users</a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
    <?php endif; // end isAuth() ?>
</ul>