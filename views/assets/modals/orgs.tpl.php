<div id="orgWin">
    <div id="orgList"><?php echo $this->fetch('ajax/assets/assign_org_list.tpl.php'); ?></div>
    <div>
        <select data-asset="<?php echo $this->asset->id; ?>" id="selOrg">
            <option value="">- select -</option>
            <?php foreach ($this->orgs as $org): ?>
                <option value="<?php echo $org->id; ?>"><?php echo $org->title; ?></option>
            <?php endforeach; ?>
        </select>
        <button id="btnAssignOrg">Assign</button>
    </div>
</div>