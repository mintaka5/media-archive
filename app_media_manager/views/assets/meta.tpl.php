

<div style="color: #999999; background-color: #C5C5C5;">
    <div>
        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
            <div class="fmElement">
                <label>Shoot name:</label>

                <div>
                    <input name="assetId" id="assetId" type="hidden"
                           value="<?php echo $this->asset->id; ?>"/>
                    <span id="shootNameTxt"><?php echo $this->asset->shoot()->title; ?></span>
                    <a href="#" id="assignShoot" class="btn btn-default" data-toggle="modal" data-target="#shootWin" title="Assign a shoot.">Edit</a>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
            <div class="fmElement">
                <label for="dateCreated">Date created:</label>
                <input type="text" id="dateCreated" name="dateCreated"
                       value="<?php echo $this->date($this->asset->created); ?>"
                       style="border:none;background:inherit;"/>
            </div>
        <?php endif; ?>

        <?php if ($this->auth->isPhotographer() || $this->auth->isArchivist() || $this->auth->isManager()): ?>
            <div class="fmElement">
                <label>Credit:</label>

                <div class="input" id="creditTxt"><?php echo $this->asset->viewCredit(); ?></div>
            </div>

            <div class="fmElement">
                <label for="copyrightTxt">Copyright:</label>

                <div class="input"
                     id="copyrightTxt"><?php echo $this->binaryToText($this->asset->copyright(), "No copyright statement", $this->asset->copyright()->metadata_value); ?></div>
            </div>

            <div class="fmElement">
                <label>Photographer:</label>

                <div class="input">
                    <?php if (!$this->asset->photographer()): ?>
                        <span id="photographer"></span>
                        [<a href="javascript:void(0);" id="assignPhotog">Assign</a>]
                    <?php else: ?>
                        <span
                            id="photographer"><?php echo $this->asset->photographer()->fullname(); ?></span>
                        [<a href="javascript:void(0);" id="changePhotog">Edit</a>]
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="fmElement">
            <label for="camInfo">Camera info:</label>

            <div id="camInfo">
                <div><?php echo $this->exif->getMake(); ?> <?php echo $this->exif->getModel(); ?></div>
                <div>Shutter speed: <?php echo $this->exif->getShutterSpeed(); ?></div>
            </div>
        </div>

        <?php if ($this->auth->isPhotographer() || $this->auth->isArchivist() || $this->auth->isManager()): ?>
            <div class="fmElement">
                <label for="fileName">File:</label>

                <div id="fileName">
                    <?php echo $this->asset->filename(); ?>
                    <br/>
                    <?php echo $this->filesize; ?> -
                    <?php echo $this->imgw; ?> x <?php echo $this->imgh; ?> px
                    (<?php echo $this->imgwin; ?> x <?php echo $this->imghin; ?> in.) -
                    <?php echo $this->resolution; ?> dpi
                </div>
            </div>

            <div class="fmElement">
                <label for="imgRights">Rights:</label>

                <div>
                    <select data-asset="<?php echo $this->asset->id; ?>" name="imgRights"
                            id="imgRights">
                        <option value="">None</option>
                        <?php foreach ($this->rights as $right): ?>
                            <option
                                value="<?php echo $right->id; ?>" <?php echo ($right->id == $this->asset_rights->metadata_value) ? 'selected="selected"' : ''; ?>><?php echo $right->value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="fmElement">
            <label for="orgInfo">Belongs to:</label>

            <div
                id="orgInfo"><?php echo $this->asset->organizations("Not assigned to an organization."); ?></div>

            <?php if ($this->auth->isAdmin()): ?>
                <div>
                    [<a href="#" id="assignOrg" title="Manage asset's organizations.">Edit</a>]
                </div>
            <?php endif; ?>
        </div>

        <div class="fmElement">
            Last modified by
            <!-- <a href="<?php echo $this->manager->friendlyAction("users", "view", null, array("id", $this->asset->modified_by)); ?>" title="View user."> -->
            <?php echo $this->asset->user()->fullname(); ?><!-- </a> -->
            on <?php echo $this->date($this->asset->modified); ?>
        </div>
    </div>
    <div>
        <h4>Status:</h4>
        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
            <div>
                <div id="lblOuttake" class="statusLbl">Out-take?</div>
                <div class="goLeft">
                                    <span
                                        id="astOuttake"><?php echo $this->binaryToText($this->asset->isOuttake(), "No", "Yes"); ?></span>
                    <input type="checkbox" name="" id="chkAssetOtk"
                           value="<?php echo $this->asset->id; ?>" <?php echo $this->binaryToText($this->asset->isOuttake(), "", Util::CHECKED_TEXT); ?> />
                </div>
                <br class="clearLeft"/>
            </div>
        <?php endif; ?>

        <!-- BEGIN PHOTOGRAPHER STATUS CHECK -->
        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
            <div>
                <div class="statusLbl">Select?</div>
                <div class="goLeft">
                                    <span
                                        id="astSelect"><?php echo $this->binaryToText($this->asset->isSelect(), "No", "Yes"); ?></span>
                    <input type="checkbox" name="" id="chkAssetSlct"
                           value="<?php echo $this->asset->id; ?>" <?php echo $this->binaryToText($this->asset->isSelect(), "", Util::CHECKED_TEXT); ?> />
                </div>
                <br class="clearLeft"/>
            </div>
        <?php endif; ?>
        <!-- END PHOTOGRAPHER STATUS CHECK -->

        <!-- BEGIN ARCHIVIST STATUS CHECK -->
        <?php if ($this->auth->isArchivist() || $this->auth->isPhotographer() || $this->auth->isManager()): ?>
            <div style="border:1px solid #bfbfbf; background-color: #e0e0e0; padding:10px;">
                <div class="statusLbl" style="font-weight:bold; margin-bottom: 10px;">Published?</div>
                <div>
                                    <span
                                        id="astPubbed"><?php echo $this->binaryToText($this->asset->isPublished(), "No", "Yes"); ?></span>

                    <div class="fmElement">
                        <label for="txtAstPubd" style="width:100px;">Name of publication</label>

                        <div><input name="txtAstPubd" id="txtAstPubd" type="text"
                                    value="<?php echo ($this->asset->isPublished()) ? $this->asset->published()->publication() : ""; ?>"/>
                        </div>
                    </div>
                    <div class="fmElement">
                        <label for="dateAstPubd" style="width:100px;">Date</label>

                        <div><input name="dateAstPubd" id="dateAstPubd" type="text"
                                    value="<?php echo ($this->asset->isPublished()) ? $this->date($this->asset->published()->pub_date) : ""; ?>"/>
                        </div>
                    </div>
                </div>
                <br class="clearLeft"/>
            </div>
        <?php endif; ?>
        <!-- END ARCHIVIST STATUS CHECK -->
    </div>
</div>