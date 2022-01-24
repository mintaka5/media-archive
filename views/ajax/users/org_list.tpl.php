<?php $orgs = $this->user->organizations(); if(!empty($orgs)): ?>
<table class="">
	<thead></thead>
	<tbody>
		<?php foreach($orgs as $org): ?>
		<tr>
			<td>
                            <a href="<?php echo $this->manager->friendlyAction('orgs', 'edit', null, array("id", $org->organization()->id)); ?>"><?php echo $org->organization()->title; ?></a>
                        </td>
			<td>
				<a data-user="<?php echo $this->user->id; ?>" data-org="<?php echo $org->organization()->id; ?>" href="#" class="rmvOrg">Remove</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div>Belongs to no organizations.</div>
<?php endif; ?>