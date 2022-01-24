<script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/keywords/main.js"></script>

<div id="editKwordHolder">
	Keyword: <input class="textField" type="text" name="editKeywordTxt" id="editKeywordTxt" value="" />
	<input type="hidden" name="editKwordId" id="editKwordId" value="" />
	<!-- <input class="goBtn" type="button" name="editKwordBtn" id="editKwordBtn" value="Save" /> -->
</div>

<div id="keywordsView">
	<h1>Keywords</h1>

	<div class="leftContent">
		<?php if($this->manager->isMode()): ?>
		<div>
			<div id="keywordsList"></div>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="rightContent">
		<div class="softBox">
        	<h3>Search Keywords</h3>
            <div>
            	<input class="textField" type="text" name="kwordSch" id="kwordSch" value="" />
                <input class="goBtn" id="kwordSchBtn" type="button" value="Go" />
            </div>
            <div><a href="#" id="aListAllKwords">List all</a></div>
        </div>
        
        <?php if($this->auth->isAdmin()): ?>
        <div class="softBox">
        	<h3>Add a new keyword</h3>
            <div id="kwordAddHolder">
            	<input class="textField" type="text" name="kwrdTxt" id="kwrdTxt" value="" />
            	<input class="goBtn" id="kwordAddBtn" type="button" value="Add" />
            </div>
        </div>
        <?php endif; ?>
        
	</div>
	
	<br class="clearLeft" />
</div>