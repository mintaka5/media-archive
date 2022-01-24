<div>
    <?php if ($this->manager->isMode()): ?>
        <div>
            <?php if (!empty($this->groups)): ?>
                <table class="data">
                    <thead></thead>
                    <tbody>
                    <?php foreach ($this->groups as $group): ?>
                        <tr>
                            <td><?php echo $group->title(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <div><?php echo $this->links['all']; ?></div>
            <?php else: ?>
                <div>No sets available.</div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("edit")): ?>
        <div>
            <?php if ($this->manager->isTask()): ?>
                <div>
                    <div><?php echo $this->form; ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($this->manager->isMode("view")): ?>
    <script type="text/javascript">
        var uid = '<?php echo $this->auth->getSession()->id; ?>';
        var gid = '<?php echo $this->group->id; ?>';
    </script>
    <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/jq/tag-it/tag-it.js"></script>
    <script type="text/javascript" src="<?php echo $this->manager->getURI(); ?>assets/js/groups/view.js"></script>

    <!-- modal image import -->
    <div id="grpAddImages">
        <h3>Search by title or keyword:</h3>

        <div>
            <input type="text" name="existingAstQry" id="existingAstQry"/>

            <div id="existingAsts"></div>
        </div>
    </div>

    <!-- manage orgs window -->
    <div id="manageOrgsWin">
        <h3>Organizations assigned to <?php $this->group->title(); ?></h3>

        <div id="manageOrgsList"></div>
    </div>

    <!-- modal shoot settings -->
    <div id="shootWin" style="text-align:left; font-size:12px;">
        <div id="shootTabs">
            <ul>
                <li><a href="#newShootTab">New Shoot</a></li>
                <li><a href="#selectShootTab">Select</a></li>
            </ul>
            <div id="newShootTab">
                <h3>New Shoot</h3>

                <div>
                    <form id="shootForm">
                        <input name="gid" id="gid" value="<?php echo $this->group->id; ?>" type="hidden"/>
                        <input name="_mode" id="_mode" value="group" type="hidden"/>
                        <input name="_task" id="_task" value="add" type="hidden"/>

                        <div class="fmElement">
                            <label>Title:</label>

                            <div>
                                <input type="text" name="shootTitle" id="shootTitle" class="required"/>
                            </div>
                        </div>
                        <div class="fmElement">
                            <label>Description:</label>

                            <div>
                                <textarea name="shootDesc" id="shootDesc"></textarea>
                            </div>
                        </div>
                        <div class="fmElement">
                            <label>Shoot date:</label>

                            <div>
                                <input type="text" name="shootDate" id="shootDate" class="required datePrompt"/>
                            </div>
                        </div>
                        <button id="addShootBtn"
                                class="rightBtn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                                role="button" type="button">
                            <span class="ui-button-text">Add</span>
                        </button>
                        <br style="clear:right;"/>
                    </form>
                </div>
            </div>

            <div id="selectShootTab">
                <h3>Select</h3>

                <div id="shootsList"></div>
            </div>
        </div>
    </div>

    <!-- modal photographer settings -->
    <div id="dialogPhotog">
        <div id="photogTabs">
            <ul>
                <li><a href="#addPhotogTab">Add</a></li>
                <li><a href="#selectPhotogTab">Select</a></li>
            </ul>

            <div id="addPhotogTab">
                <form id="addPhotogForm">
                    <input type="hidden" id="_mode" name="_mode" value="group"/>
                    <input type="hidden" id="_task" name="_task" value="add"/>
                    <input type="hidden" id="_gid" name="_gid" value="<?php echo $this->group->id; ?>"/>

                    <div class="fmElement">
                        <label>First name:</label>

                        <div>
                            <input type="text" name="addPhotogFname" id="addPhotogFname" class="required"/>
                        </div>
                    </div>
                    <div class="fmElement">
                        <label>Last name:</label>

                        <div>
                            <input type="text" name="addPhotogLname" id="addPhotogLname" class="required"/>
                        </div>
                    </div>
                    <button id="addPhotogBtn"
                            class="rightBtn ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
                            role="button" type="button">
                        <span class="ui-button-text">Add</span>
                    </button>
                    <br style="clear:right;"/>
                </form>
            </div>

            <!-- Select photographer -->
            <div id="selectPhotogTab">
                <div id="photogsList"></div>
            </div>
        </div>
    </div>

    <input type="hidden" id="grpId" value="<?php echo $this->group->id; ?>"/>

    <div>
        <h2>
            <span id="grpTitle"><?php echo $this->group->title(); ?></span>
        </h2>

        <div>
            Last modified by
            <strong><?php echo $this->group->user()->fullname(); ?></strong>
            on <?php echo $this->date($this->group->modified); ?>
        </div>

        <h2>Images (<?php echo $this->group->numTotalAssets(); ?> total)</h2>

        <div class="smLeftContent">
            <div id="grpAssetList"></div>
        </div>

        <div class="smRightContent">
            <div id="grpTabs">
                <ul>
                    <li>
                        <a href="#infoTab">Information/Actions</a>
                    </li>
                    <li>
                        <a href="#statusTab">Status</a>
                    </li>
                    <!-- <li>
                        <a href="#locTab">Location</a>
                    </li> -->
                    <li>
                        <a href="#kwordsTab">Keywords</a>
                    </li>
                    <li>
                        <a href="#imgsTab">Images</a>
                    </li>

                    <?php if ($this->group->isBatch()): ?>
                        <li>
                            <a href="#setsTab">Set Assignment</a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div id="infoTab">
                    <div class="clearfix">
                        <div class="goLeft left">
                            <h3>Information</h3>

                            <div>
                                <div>
                                    Photographer:
                                    <span id="spanGrpPhotog"></span>
                                    [<a href="javascript:void(0);" id="aGrpPhotog" title="Set the set's photographer">Set</a>]
                                </div>

                                <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
                                    <div>
                                        Shoot name:
                                        <span id="shootNameTxt"></span>
                                        [<a href="javascript:void(0);" id="assignShoot" title="Set the shoot.">Set</a>]
                                    </div>
                                <?php endif; ?>

                                <?php if ($this->auth->isPhotographer() || $this->auth->isArchivist() || $this->auth->isManager()): ?>
                                    <div>
                                        <label for="imgRights">Rights:</label>

                                        <div>
                                            <select data-group="<?php echo $this->group->id; ?>" name="imgRights"
                                                    id="imgRights">
                                                <option value="">- select -</option>
                                                <option value="">None</option>
                                                <?php foreach ($this->rights as $right): ?>
                                                    <option
                                                        value="<?php echo $right->id; ?>"><?php echo $right->value; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                    </div>
                                <?php endif; ?>

                                <div style="border-top:1px solid #bfbfbf; margin:10px 10px 0 0; padding:10px 10px 0 0;">
                                    Belongs to: <span
                                        id="grpOrgStr"><?php echo $this->group->organizations("Unassigned"); ?></span>

                                    <?php if ($this->auth->isAdmin()): ?>
                                        [<a href="#" id="manageOrgs"
                                            data-group="<?php echo $this->group->id; ?>">Manage</a>]
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="goLeft">
                            <h3>Actions</h3>

                            <div>
                                <ul class="vertMenu">
                                    <li>
                                        <input type="hidden" name="hdnActivityStatus" id="hdnActivityStatus"
                                               value="<?php echo $this->group->is_approved; ?>"/>
                                        <input type="checkbox"
                                               id="activityStatus" <?php echo $this->binaryToText($this->group->is_approved, "", 'checked="checked"'); ?> />
                                        <span
                                            id="apprvStatus"><?php echo $this->binaryToText($this->group->is_approved, "Private", "Public"); ?></span>
                                    </li>
                                    <li>
                                        <input type="checkbox"
                                               id="assetsStatus" <?php echo $this->binaryToText($this->group->hasApprovedAssets(), "", 'checked="checked"'); ?> />
                                        <span id="txtAssetStatus">Set's assets are publicly available?</span>
                                    </li>
                                    <li>
                                        <input type="hidden" name="hdnFeatureStatus" id="hdnFeatureStatus"
                                               value="<?php echo $this->binaryToText($this->group->isFeatured(), 0, 1); ?>"/>
                                        <input type="checkbox"
                                               id="featureStatus" <?php echo $this->binaryToText($this->group->isFeatured(), "", 'checked="checked"'); ?> />
                                        <span
                                            id="txtFeatureStatus"><?php echo $this->binaryToText($this->group->isFeatured(), "Not featured", "Featured"); ?></span>
                                    </li>

                                    <?php if ($this->auth->isManager()): ?>
                                        <li>
                                            <a href="#" id="saveMeta" data-id="<?php echo $this->group->id; ?>"><img
                                                    src="<?php echo $this->manager->getURI(); ?>assets/images/icon_file.gif"
                                                    alt="Save metadata to image files."/> Save Metadata</a>
                                        </li>

                                        <?php if ($this->group->numTotalAssets() > 0): ?>
                                            <li>
                                                <a class="aDownload assetAction"
                                                   href="<?php echo $this->manager->friendlyAction("group", "download", null, array("id", $this->group->id)); ?>"
                                                   title="Download all images in this set.">Download</a>
                                            </li>
                                        <?php endif; ?>

                                    <?php endif; ?>

                                    <li style="margin:5px 0 0 0;">
                                        <a href="javascript:void(0);" title="Delete set!" id="delGrp"><img
                                                src="<?php echo $this->manager->getURI(); ?>assets/images/mini-trash.png"
                                                alt="x"/> Delete set</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="statusTab">
                    <div>
                        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
                            <div>
                                <input type="checkbox" name="chkOuttake" id="chkOuttake"/> Outtake
                            </div>
                        <?php endif; ?>

                        <?php if ($this->auth->isPhotographer() || $this->auth->isManager()): ?>
                            <div>
                                <input type="checkbox" name="chkSelect" id="chkSelect"/> Select
                            </div>
                        <?php endif; ?>

                        <div>
                            <div>
                                <input type="checkbox" name="chkPublished" id="chkPublished"/> Published
                            </div>
                            <div class="fmElement">
                                <label for="txtPubName">Name of publication</label>

                                <div><input type="text" name="txtPubName" id="txtPubName"/></div>
                            </div>
                            <div class="fmElement">
                                <label for="datePublished">Date</label>

                                <div><input type="text" name="datePublished" id="datePublished"/></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- <div id="locTab">
                    <div>
                        <div style="margin-bottom:5px;">
                            <input class="textField" style="width:250px;" type="text" name="locSearch" id="locSearch"
                                   value="Search for location..."/>
                            <input class="goBtn" type="button" name="locBtn" id="locBtn" value="Find!"/>
                        </div>
                        <div id="locMap" style="width:inherit; height:350px; background-color:#e0e0e0;"></div>
                        <div>Click an item below to set set's location or <a href="#" id="rmvLocs">remove</a></div>
                        <div id="locList" style="overflow:auto; height:175px; width:inherit;"></div>
                    </div>
                </div> -->

                <div id="kwordsTab">
                    <div>
                        <p style="margin-bottom:5px;">Start typing a word in the field below, to select a word to assign
                            to this set's images.</p>
                        <ul id="assetWords"></ul>
                        <?php if ($this->auth->isAdmin()): ?>
                            <div>
                                <label for="txtNewKword">New keyword</label>

                                <div>
                                    <input type="text" name="txtNewKword" id="txtNewKword"/>
                                    <button id="btnNewKword">Add</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="imgsTab">
                    <div id="assetUploader">
                        <div><a href="javascript:void(0);" id="aAddCurImages">Select</a> from existing images.</div>
                        <div>
                            <?php if ($this->auth->isManager() || $this->auth->isArchivist()): // only display to admins and archivists ?>

                                <div>
                                    <form id="uploaderForm" enctype="multipart/form-data">
                                        <div id="uploader">
                                            <p>Your browser doesn't have Flash, Silverlight, Gears, BrowserPlus or HTML5
                                                support.</p>
                                            <!-- <div id="filelist">No runtime found.</div>
                                            <br />
                                            <a id="pickfiles" href="#">Select files</a>
                                            <a id="uploadfiles" href="#">Upload all files</a> -->
                                        </div>
                                    </form>
                                </div>

                            <?php else: ?>

                                <div>You do not have sufficient privileges to upload assets.</div>

                            <?php endif; // end admin check ?>
                        </div>
                    </div>
                </div>

                <?php if ($this->group->isBatch()): ?>
                    <div id="setsTab">
                        <div class="goLeft" id="groupsListHolder">
                            <h3> Assigned Sets:</h3>

                            <div id="groupsList"></div>
                            <?php if ($this->auth->isManager()): ?>

                                <div style="margin-top:30px;">
                                    <div class="fmElement">
                                        <label for="newGrpTitle" style="width:175px;">Add a new set:</label>

                                        <div class="input">
                                            <input type="text" name="newGrpTitle" id="newGrpTitle"/>
                                            <button name="newAstGrpBtn" id="newAstGrpBtn">Add &amp; assign</button>
                                        </div>
                                    </div>
                                    <br class="clearLeft"/>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div id="avlSets" class="goLeft" style="margin-left:4px; width:263px;">
                            <h3>Available Sets:</h3>

                            <div style="margin-bottom:10px;">
                                <input type="text" id="filterAvlSets" value="Search"/>
                                <a href="#" id="resetFilterSets">Reset</a>
                            </div>
                            <div id="availSets" style="overflow:auto; height:300px;"></div>
                        </div>
                        <br class="clearLeft"/>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>