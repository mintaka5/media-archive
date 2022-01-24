<div>
	<?php if($this->shoots != false): ?>
	<div>
		<table>
			<thead></thead>
			<tbody>
				<?php foreach($this->shoots as $shoot): ?>
				<tr>
					<td><?php echo $shoot->title; ?></td>
					<td><?php echo $this->date($shoot->shoot_date, "m/d/Y"); ?></td>
					<td>
						<input type="hidden" class="shootId" value="<?php echo $shoot->id; ?>" />
						<a href="javascript:void(0);" class="selectShoot" title="Select <?php echo $shoot->title; ?>">Select</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php else: ?>
	<div>No shoots.</div>
	<?php endif; ?>
</div>