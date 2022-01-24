<?php $orgs = $this->group->organizations(); if($orgs->count() > 0): ?>
<table class="list">
	<thead>
            <tr>
                <th>Name</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
	<tbody>
		<?php foreach ($orgs as $org): ?>
		<tr>
			<td><?php echo $org->title; ?></td>
			<td class="tasker">
                            <a href="#" data-org="<?php echo $org->id; ?>" data-group="<?php echo $this->group->id; ?>" class="rmvOrgFromGrp">Remove</a>
			</td>
                        <td class="tasker">
                            <a href="#" data-org="<?php echo $org->id; ?>" data-group="<?php echo $this->group->id; ?>" class="reassignAssets">Assign assets</a>
                        </td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div>Not assigned to any organizations.</div>
<?php endif; ?>