<?php $users = $this->org->users(); if(!empty($users)): ?>
<table>
	<thead></thead>
	<tbody>
		<?php foreach($users as $user): ?>
		<tr>
			<td><?php echo $user->user()->fullname(); ?></td>
			<td>
				<a href="#" class="rmvOrgUser" data-user="<?php echo $user->user()->id; ?>" data-org="<?php echo $this->org->id; ?>">Remove</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php endif; ?>