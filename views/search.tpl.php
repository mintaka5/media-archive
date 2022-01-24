<div id="searchView">
    <div id="leftContent" class="leftContent">
        <?php if($this->manager->isMode()): ?>
        <?php if($this->manager->isTask()): ?>
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/search/default.js"></script>
        <?php if(!empty($this->assets)): ?>
        <div>
            <?php if(!empty($this->assets)): ?>
            <div id="assetContainer">
                    <?php foreach ($this->assets as $asset):?>
                    <div class="assetItem">
                            <div class="img">
                                    <a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $asset->public_id)); ?>" title="View <?php echo $asset->viewTitle("Untitled"); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($asset->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $asset->title; ?>" title="<?php echo $asset->title; ?>" /></a>
                            </div>
                            <div class="options">
                                    <input type="hidden" class="assetId" value="<?php echo $asset->id; ?>" />
                                    <input type="hidden" class="simId" value="<?php echo $asset->public_id; ?>" />
                                    <button <?php echo ($asset->isOrdered() == true) ? 'disabled="disabled"' : ""; ?> class="<?php echo ($asset->isOrdered() == true) ? "orderedCart" : "shoppingCart"; ?> btn" title="Add to request cart."></button>
                                    <?php if($asset->hasKeywords()): ?>
									<button class="moreLikeThis btn" title="Show more assets like this."></button>
									<?php endif; ?>
                            </div>
                            <div class="meta">
                                    <div>
                                            <div class="goLeft">Tag &#35;: <?php echo $asset->public_id; ?></div>
                                            <div class="goRight"><?php echo $this->date($asset->created, "d M Y"); ?></div>
                                            <div class="clearBoth"><?php echo $this->truncate($asset->viewTitle("Untitled"), 30); ?></div>
                                            <div>By: <?php echo ($asset->photographer() != false) ? $asset->photographer()->fullname() : "Unknown"; ?></div>
                                            <div><?php echo $asset->viewCredit(""); ?></div>
                                            <div><?php echo $asset->viewCaption(""); ?></div>
                                    </div>
                            </div>
                    </div>
                    <?php endforeach;?>
                    <br class="clearLeft" />
                    <div><?php echo $this->pagelinks['all']; ?></div>
            </div>
            <?php else: ?>
            <div class="noresults">No assets are available for this group.</div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="noresults">No results</div>
        <?php endif; ?>
        
        <?php endif; ?>
        <?php endif; ?>
        
        <?php if($this->manager->isMode("kwords")): ?>
        <?php if($this->manager->isTask()): ?>
        <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/search/kwords.js"></script>
        <div>
        	<h2>Assets similar to <?php echo $this->asset->viewTitle(); ?></h2>
            <?php if(!empty($this->assets)): ?>
            <div id="assetContainer">
                    <?php foreach ($this->assets as $asset):?>
                    <div class="assetItem">
                            <div class="img">
                            	<a href="<?php echo $this->manager->friendlyAction("asset_view", null, null, array("id", $asset->public_id)); ?>" title="View <?php echo $asset->viewTitle("Untitled"); ?>"><img class="shadyImg" src="<?php echo $this->thumber->build($asset->public_id, "{w:170,h:170}"); ?>" alt="<?php echo $asset->title; ?>" title="<?php echo $asset->title; ?>" /></a>
                            </div>
                            <div class="options">
                                    <input type="hidden" class="assetId" value="<?php echo $asset->id; ?>" />
                                    <input type="hidden" class="simId" value="<?php echo $asset->public_id; ?>" />
                                    <button <?php echo ($asset->isOrdered() == true) ? 'disabled="disabled"' : ""; ?> class="<?php echo ($asset->isOrdered() == true) ? "orderedCart" : "shoppingCart"; ?> btn" title="Add to request cart."></button>
                                    <button class="moreLikeThis btn" title="Show more assets like this."></button>
                            </div>
                            <div class="meta">
                                    <div>
                                            <div class="goLeft">Tag &#35;: <?php echo $asset->public_id; ?></div>
                                            <div class="goRight"><?php echo $this->date($asset->created, "d M Y"); ?></div>
                                            <div class="clearBoth"><?php echo $this->truncate($asset->viewTitle("Untitled"), 30); ?></div>
                                            <div>By: <?php echo ($asset->photographer() != false) ? $asset->photographer()->fullname() : "Unknown"; ?></div>
                                            <div><?php echo $asset->viewCredit(""); ?></div>
                                            <div><?php echo $asset->viewCaption(""); ?></div>
                                    </div>
                            </div>
                    </div>
                    <?php endforeach;?>
                    <br class="clearLeft" />
                    <div><?php echo $this->pagelinks['all']; ?></div>
            </div>
            <?php else: ?>
            <div class="noresults">No related assets.</div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    
    <div id="rightContent" class="rightContent">
        <div>
            <div class="softBox">
                <h3>Search</h3>
                <div style="margin-top: 10px;">
                    <form method="get" action="<?php echo $this->manager->friendlyAction("search"); ?>">
                        <div>
                            <input class="textField" type="text" name="terms" id="browseTerms" value="" />
                        </div>
                        <input type="submit" class="goBtn" value="Go" />
                    </form>
                </div>
            </div>
            <div>
                <div id="" class="softBox">
                        <h3>Recent Cart Items</h3>
                        <div id="cartPreview"></div>
                </div>
            </div>
        </div>
        <?php if(Ode_Manager::getInstance()->getMode()): ?>
        <div></div>
        <?php endif; ?>
    </div>
    <br class="clearLeft" />
</div>