<?php if(!empty($this->asset_orgs)): ?>
<table>
	<thead></thead>
	<tbody>
		<?php foreach($this->asset_orgs as $asset_org): ?>
		<tr>
			<td><?php echo $asset_org->title; ?></td>
			<td>
				<a href="#" class="rmvAstOrg" data-org="<?php echo $asset_org->id; ?>">Remove</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>
<div>No organizations assigned.</div>
<?php endif; ?>