<div>
    <div id="keywordsHolder" class="assetInfoBox">
        <div>
            <p style="margin-bottom:5px;">Start typing words to select keywords to assign to this
                image.</p>
            <ul id="assetWords">
                <?php if ($kwords = $this->asset->keywords()): foreach ($kwords as $kword): ?>
                    <li><?php echo $kword->keyword; ?></li>
                <?php endforeach; endif; ?>
            </ul>
        </div>
        <?php if ($this->auth->isAdmin()): ?>
            <div>
                <label for="txtNewKword">New keyword!</label>

                <div class="input">
                    <input type="text" name="txtNewKword" id="txtNewKword"/>
                    <button id="btnNewKword" class="goBtn">Add</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>