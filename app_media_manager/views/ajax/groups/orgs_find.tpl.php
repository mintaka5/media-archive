<?php if(!empty($this->orgs)): ?>
<table>
	<thead></thead>
	<tbody>
		<?php foreach($this->orgs as $org): ?>
		<tr>
			<td><?php echo $org->title; ?></td>
			<td>
				<a href="#" class="addOrgToGrp" data-group="<?php echo $this->group_id; ?>" data-org="<?php echo $org->id; ?>">Add</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div>No results.</div>
<?php endif; ?>