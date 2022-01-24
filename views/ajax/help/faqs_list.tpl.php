<div>
	<?php if(!empty($this->faqs)): ?>
	<div>
		<table class="">
			<thead></thead>
			<tbody>
				<?php foreach($this->faqs as $faq): ?>
				<tr>
					<td>
						<h3><a href="<?php echo $this->manager->friendlyAction("help", "view", null, array("id", $faq->solution_id)); ?>"><?php echo $faq->thema; ?></a></h3>
						<div><?php echo $this->truncate($faq->content, 50); ?></div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php else: ?>
	<div>No results.</div>
	<?php endif; ?>
</div>