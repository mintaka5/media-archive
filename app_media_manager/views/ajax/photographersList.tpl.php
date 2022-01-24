<div>
	<?php if(!empty($this->photographers)): ?>
	<div>
		<table>
			<thead></thead>
			<tbody>
				<?php foreach($this->photographers as $photographer): ?>
				<tr>
					<td><?php echo $photographer->fullname(true); ?></td>
					<td>
						<input type="hidden" class="photogId" value="<?php echo $photographer->id; ?>" />
						<a href="javascript:void(0);" class="selectPhotog" title="Select <?php echo $photographer->fullname(true); ?>">Select</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php else: ?>
	<div>No photographers</div>
	<?php endif; ?>
</div>