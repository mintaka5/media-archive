<div class="clearfix">
	<?php if(!empty($this->orgs)): // user must be part of an organization to upload ?>

	<div id="rightContent" class="leftContent">
	    <?php if($this->manager->isMode()): ?>
	    
	    <?php if($this->auth->isManager() || $this->auth->isArchivist()): // only display to admins and archivists ?>
	    
	    <h1>Upload New Assets</h1>
	    <div style="width:712px;">
	        <?php if($this->manager->isTask()): ?>
	        <script type="text/javascript">
	            var uid = '<?php echo $this->auth->getSession()->id; ?>';
	        </script>
	        
	        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/quick_upload.js"></script>
	        
	        <form id="uploaderForm">
		        <div id="uploader">
		        	<p>Your browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5 support.</p>
		        	<!-- <div id="filelist">No runtime found.</div>
		        	<br />
		        	<a id="pickfiles" href="#">Select files</a>
		        	<a id="uploadfiles" href="#">Upload all files</a> -->
		        </div>
	        </form>
	        <?php endif; ?>
	    </div>
	    
	    <?php else: ?>
	    
	    <div>You do not have sufficient privileges to upload images.</div>
	    
	    <?php endif; // end admin check ?>
	    
	    <?php endif; ?>
    </div>
    
    <div id="leftContent" class="rightContent">
    	<?php if($this->manager->isMode()): ?>
    	<div class="softBox">
    		<h3>Asset Assignment</h3>
    		<div>
    			<p>Please, select the organization(s) you need to have these uploads assigned to. If none or all are checked all of the ones listed below will be used.</p>
    			<ul class="vertMenu">
    				<?php foreach($this->orgs as $org): ?>
    				<li>
    					<input type="checkbox" class="chkUserOrg" checked="checked" data-user="<?php echo $org->user_id; ?>" value="<?php echo $org->org_id; ?>" />
    					<?php echo $org->organization()->title; ?>
    				</li>
    				<?php endforeach; ?>
    			</ul>
    		</div>
    	</div>
    	<?php endif; ?>
    </div>
    
    <?php else: ?>
    
    <div>You are not a part of an organization, and that is required in order to upload assets.</div>
    
    <?php endif; ?>
    
</div>