<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/asset_view.js"></script>
<div>
    <div id="assetLeft" class="leftContent">
    		<?php if(!is_null($_SESSION[CURRENT_GROUP_VAR])): ?>
            <div class="breadcrumb">
                <ul>
                	<li><a href="<?php echo $this->manager->friendlyAction("browse", "groups"); ?>" title="Browse sets">Sets</a></li>
                    <li><a href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $this->group->id)); ?>" title="Back to <?php echo $this->group->title("Untitled"); ?>"><?php echo $this->group->title("Untitled"); ?></a></li>
                    <li><a class="active" href="" title="<?php echo $this->asset->viewTitle(); ?>"><?php echo $this->truncate($this->asset->viewTitle("Untitled")); ?></a></li>
            	</ul>
            </div>
            <?php endif; ?>
            
            <?php if(isset($_GET['gid']) && isset($_GET['cid'])):  // we are coming from collection browsing so we need a path back ?>
            <div class="breadcrumb">
            	<ul>
            		<li><a href="<?php echo $this->manager->friendlyAction("browse_collections"); ?>">Collections</a></li>
            		<li><a href="<?php echo $this->manager->friendlyAction("browse_collections", "view", null, array("id", $this->collection->id)); ?>"><?php echo $this->collection->title(); ?></a></li>
            		<li><a href="<?php echo $this->manager->friendlyAction("browse_collections", "group", "view", array("id", $this->group->id), array("cid", $this->collection->id)); ?>"><?php echo $this->group->title(); ?></a></li>
            	</ul>
            </div>
            <?php endif; ?>
        
            <?php if($this->manager->isMode('preview')): ?>
            <div>
                <?php if($this->manager->isTask('failed')): ?>
                <div>Your image download failed. Please try again.</div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php if($this->manager->isMode()): ?>
            <div id="">
                    <?php if($this->manager->isTask()): ?>
                    <div id="astViewMeta">
                        <div>
                                <img data-hash="<?php echo ""; ?>" data-asset_id="<?php echo $this->asset->id; ?>" id="viewImg" class="shadyImg" src="<?php echo $this->thumber->build($this->asset->public_id, "{w:599,h:599,q:95}"); ?>" alt="<?php echo $this->asset->viewTitle("Untitled"); ?>" />
                        </div>
                        <table class="assetInfo">
                                <tbody>
                                        <tr>
                                                <th>Title:</th>
                                                <td><?php echo $this->asset->viewTitle("Untitled"); ?></td>
                                        </tr>
                                        <tr>
                                                <th>Caption:</th>
                                                <td><?php echo $this->asset->finalCaption(); ?></td>
                                        </tr>
                                        <tr>
                                                <th>Date Created:</th>
                                                <td><?php echo $this->date($this->asset->created, "d M Y"); ?></td>
                                        </tr>
                                        <tr>
                                                <th>Image Tag &#35;:</th>
                                                <td><?php echo $this->asset->public_id; ?></td>
                                        </tr>
                                        <tr>
                                                <th>Photographer:</th>
                                                <td><?php echo ($this->asset->photographer() != false) ? $this->asset->photographer()->fullname() : $this->asset->viewCredit(""); ?></td>
                                        </tr>
                                        <tr>
                                                <th>Credit:</th>
                                                <td><?php echo $this->asset->viewCredit(); ?></td>
                                        </tr>
                                        <tr>
                                        	<th>Rights:</th>
                                        	<td><?php echo $this->asset->rights(); ?></td>
                                        </tr>
                                        <tr>
                                                <th>File size/dimensions/dpi:</th>
                                                <td>
                                                        <?php echo $this->filesize; ?> - 
                                                        <?php echo $this->imgWidth ?> x <?php echo $this->imgHeight; ?> px (<?php echo $this->imgWidthIn ?> x <?php echo $this->imgHeightIn; ?> in.) -
                                                        <?php echo $this->resolution; ?> dpi
                                                </td>
                                        </tr>
                                        <tr>
                                                <th>Keywords:</th>
                                                <td><?php echo $this->asset->viewKeywords(); ?></td>
                                        </tr>
                                        <tr>
                                        	<th>Views:</th>
                                        	<td><?php echo $this->asset->views("No"); ?></td>
                                        </tr>
                                </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
            </div>
            <?php endif; ?>
    </div>
    <div id="assetRight" class="rightContent">
    		<?php if(($this->auth->isEditor() || $this->auth->isPhotographer() || $this->isArchivist() || $this->auth->isManager()) && !$this->auth->isGuest()): ?>
    		<div class="softBox">
    			<h3>Admin options</h3>
    			<div>
    				<ul class="vertMenu">
    					<li><a href="<?php echo $this->manager->friendlyAction("assets", "view", null, array("id", $this->asset->id)); ?>">Edit this image</a></li>
    				</ul>
    			</div>
    		</div>
    		<?php endif; ?>
    
            <div class="softBox">
                <h3>Search</h3>
                <div>
                	<div>
	                    <form method="get" action="">
	                        <input type="hidden" name="_page" value="search" />
	                        <input class="textField" type="text" name="terms" id="browseTerms" value="" />
	                        <input type="submit" class="goBtn" value="Go" />
	                    </form>
                    </div>
                    <?php if($this->searching->isFromSearch()): ?>
                    <div style="margin-top:10px;">
                    	Last search:
                    	<a href="<?php echo $this->manager->friendlyAction("search", null, null, array("terms", $this->searching->getTerms(true))); ?>" title=""><?php echo $this->searching->getTerms(); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div id="" class="softBox">
            	<h3>Recent Cart Items</h3>
                <div id="cartPreview"></div>
            </div>
            
            <?php if($this->manager->isMode()): ?>
            <div>
                    <?php if($this->manager->isTask()): ?>
                    <div class="softBox">
                            <input type="hidden" name="assetId" id="assetId" value="<?php echo $this->asset->id; ?>" />
                            <h3>Options</h3>
                            <ul class="vertMenu">
                                    <li>
                                    	<?php if($this->asset->isOrdered() == false): ?>
                                    	<a href="javascript:void(0);" class="aAddToCart assetAction" title="Add to request cart.">Add to request cart.</a>
                                    	<?php else: ?>
                                    	<a href="javascript:void(0);" class="aDelFromCart assetAction" title="remove from request cart.">Remove from cart.</a>
                                    	<?php endif; ?>
                                    </li>
                                    <li>
                                    	<a href="<?php echo $this->manager->friendlyAction("asset_view", "preview", null, array("id", $this->asset->public_id)); ?>" title="Download this image." class="aDownload assetAction">Download this image</a>
                                    </li>
                                    <?php if($this->asset->hasKeywords()): ?>
                                    <li>
                                    	<a href="javascript:void(0);" title="Search for more images like this." class="aLikeThis assetAction">More images like this</a>
                                    </li>
                                    <?php endif; ?>
                            </ul>
                    </div>
                    
                    <!-- Image download TOA -->
                    <div class="softBox">
                    	<h3>Terms of Service</h3>
                    	<ul class="vertMenu">
                    		<li>By downloading this image, you agree to the <a href="<?php echo $this->manager->friendlyAction("copyright"); ?>" title="Terms of Service">Image Archive Terms of Service</a>.</li>
                    	</ul>
                    </div>
                    
                    <?php if(is_null($_SESSION[CURRENT_GROUP_VAR])): ?>
                    <div class="softBox">
                    	<h3>Also in Sets:</h3>
                    	<ul class="vertMenu">
                    		<?php $groups = $this->asset->groups(); foreach($groups as $group): ?>
                    		<li>
                    			<a href="<?php echo $this->manager->friendlyAction("group_view", null, null, array("id", $group->id)); ?>" title="<?php echo $group->title; ?>"><?php echo $group->title(); ?></a>
                    		</li>
                    		<?php endforeach; ?>
                    	</ul>
                    </div>
                    <?php endif; ?>
                    
                    <?php endif; ?>
            </div>
            <?php endif; ?>
    </div>
    <br class="clearLeft" />
</div>