<div>
    <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
        <div class="restrictHolder">
            <h4>Embargoed?</h4>

            <div class="yesNo">
                                    <span id="embgdTxt"
                                          class="<?php echo ($this->asset->isEmbargoed() != false) ? "restrStatYes" : "restrStatNo"; ?>"><?php echo ($this->asset->isEmbargoed() != false) ? "Yes" : "No" ?></span>
            </div>
            <div id="embgdStart" class="clearLeft">
                Date: <input type="text" name="embgoDate" id="embgoDate" maxlength="15" size="16"
                             value="<?php echo ($this->asset->isEmbargoed() != false) ? $this->date($this->asset->isEmbargoed()->start_date, "m/d/Y g:i a") : ""; ?>"/>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
        <!-- HIPPA restriction checkbox -->
        <div class="restrictHolder">
            <h4>HIPPA:</h4>

            <div>
                <div class="yesNo">
                                        <span
                                            id="hippaStat"><?php echo ($this->asset->isHippaRestricted() != false) ? "Yes" : "No" ?></span>
                    <input type="checkbox" name="hippaRestr" id="hippaRestr"
                           value="<?php echo $this->asset->id; ?>"/>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
        <!-- NCAA restriction checkbox -->
        <div class="restrictHolder">
            <h4>NCAA:</h4>

            <div>
                <div class="yesNo">
                                        <span
                                            id="ncaaStat"><?php echo ($this->asset->isNCAARestricted() != false) ? "Yes" : "No" ?></span>
                    <input type="checkbox" name="ncaaRestr" id="ncaaRestr" value=""/>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
        <!-- Subject restriction explanation -->
        <div class="restrictHolder">
            <h4>Subject:</h4>

            <div>
                <div class="yesNo">
                                        <span id="subjStat"
                                              class="<?php echo ($this->asset->isSubjectRestricted() != false) ? "restrStatYes" : "restrStatNo"; ?>"><?php echo ($this->asset->isSubjectRestricted() != false) ? "Yes" : "No" ?></span>
                </div>
                <div class="">
                    Explanation: <textarea data-asset="<?php echo $this->asset->id; ?>" name="subjRsn" id="subjRsn"
                                           class="restrictRsn"><?php echo ($this->asset->isSubjectRestricted() != false) ? $this->asset->isSubjectRestricted()->description : ""; ?></textarea>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="restrictHolder">
        <h4>Internal:</h4>

        <div>
            <div class="yesNo">
                                    <span id="internalTxt"
                                          class="<?php echo ($this->asset->isSubjectRestricted() != false) ? "restrStatYes" : "restrStatNo"; ?>"><?php echo ($this->asset->isInternalRestricted() != false) ? "Yes" : "No" ?></span>
            </div>
            <div class="">
                Reason:
                                    <textarea data-asset="<?php echo $this->asset->id; ?>" name="internalRsn" id="internalRsn"
                                              class="restrictRsn"><?php echo ($this->asset->isInternalRestricted() != false) ? $this->asset->isInternalRestricted()->description : ""; ?></textarea>
            </div>
        </div>
    </div>
    <div class="restrictHolder">
        <h4>External:</h4>

        <div>
            <div class="yesNo">
                                    <span id="externalTxt"
                                          class="<?php echo ($this->asset->isSubjectRestricted() != false) ? "restrStatYes" : "restrStatNo"; ?>"><?php echo ($this->asset->isExternalRestricted() != false) ? "Yes" : "No" ?></span>
            </div>
            <div class="">
                Reason: <textarea data-asset="<?php echo $this->asset->id; ?>" name="externalRsn" id="externalRsn"
                                  class="restrictRsn"><?php echo ($this->asset->isExternalRestricted() != false) ? $this->asset->isExternalRestricted()->description : ""; ?></textarea>
            </div>
        </div>
    </div>
</div>