<?php if(!empty($this->users)): ?>
<table style="width:100%;">
	<thead></thead>
	<tbody>
		<?php foreach($this->users as $user): ?>
		<tr>
			<td><?php echo $user->fullname(); ?></td>
			<td>
				<a href="#" class="assignUserToOrg" data-org="<?php echo $this->org_id; ?>" data-user="<?php echo $user->id; ?>">Add</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php else: ?>

<?php endif; ?>