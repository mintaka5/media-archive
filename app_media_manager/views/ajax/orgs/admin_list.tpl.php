<?php if (!empty($this->orgs)): ?>
    <div>
        <div><?php echo $this->pagelinks['all']; ?></div>

        <div>
            <table class="list">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th style="text-align:center;">&#35; users</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->orgs as $org): ?>
                        <tr>
                            <td><?php echo $org->title; ?></td>
                            <td style="text-align:center;">
                                <?php echo $org->numUsers(); ?>
                            </td>
                            <td>
                                <ul class="tblMenu">
                                    <li>
                                        <a href="#" class="orgUsers" data-org="<?php echo $org->id; ?>" title="Manage users">Manage users</a>
                                    </li>

                                    <li>
                                        <a href="<?php echo $this->manager->friendlyAction("orgs", "edit", null, array("id", $org->id)); ?>">Edit</a>
                                    </li>

                                    <?php if ($org->numUsers() <= 0): // do not delete if there are any users in org ?>
                                        <li>
                                            <a href="#" data-org="<?php echo $org->id; ?>" title="delete organization!" class="orgDel"><img src="<?php echo $this->manager->getURI(); ?>assets/images/delete.png" alt="x" /></a>
                                        </li>
                                    <?php endif; ?>

                                </ul>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div><?php echo $this->pagelinks['all']; ?></div>
    </div>
<?php else: ?>
    <div>No organizations.</div>
<?php endif; ?>