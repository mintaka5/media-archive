<?php if(!empty($this->groups)): ?>
<table class="selector" style="border-spacing: 0px;">
    <tbody>
    <?php foreach($this->groups as $num => $group): ?>
    <tr style="background-color: <?php echo ($num%2 == 0) ? '#fff':'#e0e0e0'; ?>">
        <td><img src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, "{w:16, h:16, zc:'C'}"); ?>" alt="" /></td>
        <td class="grp-title"><?php echo $group->title(); ?></td>
        <td><a href="#" class="selectAvailGrp" data-id="<?php echo $group->id; ?>">Select</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</div>
<?php else: ?>
<div>None available. Add a group.</div>
<?php endif; ?>