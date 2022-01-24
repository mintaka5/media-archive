<div>
	<div class="breadcrumb">
		<ul>
			<li>
				<a href="<?php echo $this->manager->friendlyAction("browse_collections"); ?>" title="Browse all collections">Collections</a>
			</li>
			
			<?php if($this->manager->isMode("view") || $this->manager->isMode("group")): ?>
			
			<?php if($this->manager->isTask() || $this->manager->isTask("view")): ?>
			<li><a href="<?php echo $this->manager->friendlyAction("browse_collections", "view", null, array("id", $this->collection->id)); ?>"><?php echo $this->collection->title(); ?></a></li>
			<?php endif; ?>
			
			<?php if(isset($_GET['cid'])): // if collection ID is present in URL ?>
			<li>
				<a href="<?php echo $this->manager->friendlyAction("browse_collections", "group", "view", array("id", $this->group->id), array("cid", $this->collection->id)); ?>"><?php echo $this->group->title(); ?></a>
			</li>
			<?php endif; ?>
			
			<?php endif; ?>
		</ul>
	</div>

	<?php if($this->manager->isMode()): ?>
	<div>
		<?php if($this->manager->isTask()): ?>
		<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/browse_collections/default/default.js"></script>
		
		<div>
			<?php if(!empty($this->listdata)): ?>
			<div id="containers">
				<?php foreach($this->listdata as $listitem): ?>
				<div class="containerItem">
					<h3><?php echo $listitem->title(); ?></h3>
					<div class="containerGroups clearfix shadyImg" style="width:182px; height:182px; padding:none; margin:none;" data-href="<?php echo $this->manager->friendlyAction("browse_collections", "view", null, array("id", $listitem->id)); ?>">
						<?php $groups = $listitem->publicGroups(4); foreach($groups as $group): ?>
						<div class="containerGroupItem" style="float:left;">
							<div><img class="" src="<?php echo $this->thumber->build($group->defaultAsset()->public_id, '{w:91,h:91,zc:true}'); ?>" alt="<?php echo $group->title(); ?>" /></div>
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php else: ?>
			<div>Sorry, but there are no collections at this time.</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	
	<?php if($this->manager->isMode("view")): ?>
	<div class="clearfix">
		<?php echo $this->fetch("browse/group_list.tpl.php"); ?>
	</div>
	<?php endif; ?>
	
	<?php if($this->manager->isMode("group")): ?>
	<div>
		<?php if($this->manager->isTask("view")): ?>
		<div><?php echo $this->fetch("group_view/list.tpl.php"); ?></div>
		<?php endif; ?>
	</div>
	<?php endif; ?>
</div>