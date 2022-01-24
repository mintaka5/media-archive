<div id="capnInfo">
    <div class="captionHolder">
        <input type="hidden" class="capnType" value="final"/>
        <label for="finalCapn" class="capnLbl">Final</label>
        <img src="<?php echo $this->manager->getURI(); ?>assets/images/pencil.png"
             title="Click caption text to edit" alt="Click caption text to edit"/>

        <div id="finalCapn"
             class="editCapn"><?php echo (!$this->asset->finalCaption()) ? "No caption" : $this->asset->finalCaption(); ?></div>
    </div>
    <div class="captionHolder hideMe">
        <input type="hidden" class="capnType" value="feat"/>
        <label for="featCapn" class="capnLbl">Feature</label>
        <img src="<?php echo $this->manager->getURI(); ?>assets/images/pencil.png"
             title="Click caption text to edit" alt="Click caption text to edit"/>

        <div id="featCapn"
             class="editCapn"><?php echo (!$this->asset->featureCaption()) ? "No caption" : $this->asset->featureCaption(); ?></div>
    </div>
    <div class="captionHolder hideMe">
        <input type="hidden" class="capnType" value="gen"/>
        <label for="genCapn" class="capnLbl">Generic</label>
        <img src="<?php echo $this->manager->getURI(); ?>assets/images/pencil.png"
             title="Click caption text to edit" alt="Click caption text to edit"/>

        <div id="genCapn"
             class="editCapn"><?php echo (!$this->asset->genericCaption()) ? "No caption" : $this->asset->genericCaption(); ?></div>
    </div>
    <br class="clearLeft"/>
</div>