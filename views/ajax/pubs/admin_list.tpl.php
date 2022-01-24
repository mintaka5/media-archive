<?php if(!empty($this->pubs)): ?>
<table>
	<?php foreach($this->pubs as $pub): ?>
	<tr>
		<td><?php echo $pub->title; ?></td>
		<td><a href="#" class="assignPub" title="<?php echo $pub->title; ?>" data-id="<?php echo $pub->id; ?>">Assign</a></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<div>No publications available.</div>
<?php endif; ?>