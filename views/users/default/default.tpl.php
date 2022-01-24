<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/users/default/default.js"></script>

<div>
    <?php if(!empty($this->users)): ?>
        <div style="margin-bottom:10px;">Pages: <?php echo $this->pagelinks['all']; ?></div>

        <table class="list">
            <thead>
            <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($this->users as $num => $user): ?>
                <tr class="<?php echo ($num%2==0) ? "light" : "dark"; ?>">
                    <td class="userFullName"><?php echo $user->fullname(true); ?></td>
                    <td><?php echo $user->username; ?></td>
                    <td><?php echo $user->email; ?></td>
                    <td>
                        <input type="hidden" class="userId" value="<?php echo $user->id; ?>" />
                        <ul class="tblMenu">

                            <?php if(!$user->isSelf($this->auth->getSession()->id)): ?>
                                <li><a href="javascript:void(0);" class="userDel" title="delete user!"><img src="<?php echo $this->manager->getURI(); ?>assets/images/delete.png" alt="x" /></a></li>
                            <?php endif; ?>

                            <li>
                                <select class="userType">
                                    <?php foreach($this->usertypes as $type): ?>
                                        <option value="<?php echo $type->id; ?>" <?php echo ($user->type()->id == $type->id) ? 'selected="selected"' : ""; ?>><?php echo $type->title; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("users", "edit", null, array("id", $user->id)); ?>" title="Manage user details.">Manage</a>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div style="margin-top:10px;">Pages: <?php echo $this->pagelinks['all']; ?></div>
    <?php else: ?>
    No users found.
<?php endif; //end user list check ?>