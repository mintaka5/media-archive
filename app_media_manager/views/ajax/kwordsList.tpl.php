<div>
	<?php if(!empty($this->keywords)): ?>
	<div>
		<table class="list">
			<thead></thead>
			<tbody>
				<?php foreach($this->keywords as $keyword):?>
				<tr>
					<td>
						<span class="editKword"><?php echo $keyword->keyword; ?></span>
					</td>
					<td>
						<input type="hidden" class="kwordId" value="<?php echo $keyword->id; ?>" />
						
						<?php if($this->auth->isAdmin()): ?>
						<a href="#" class="editKword">Edit</a> |
						<a href="#" class="rmvKword">Remove</a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<div><?php echo $this->kwordlinks['all']; ?></div>
	<?php else: ?>
	<div>No keywords.</div>
	<?php endif; ?>
</div>