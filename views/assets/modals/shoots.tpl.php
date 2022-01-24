<!-- assign a shoot window -->
<div id="shootWin" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Photo Shoot Manager</h4>
            </div>
            <div class="modal-body">
                <div role="tabpanel">
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#newShootTab" aria-controls="newShootTab" role="tab" data-toggle="tab">New</a>
                        </li>
                        <li role="presentation">
                            <a href="#editShootTab" aria-controls="editShootTab" role="tab" data-toggle="tab">Edit</a>
                        </li>
                        <li role="presentation">
                            <a href="#selectShootTab" aria-controls="selectShootTab" role="tab"
                               data-toggle="tab">Select</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="newShootTab" role="tabpanel" class="tab-pane active">
                            <h3>New Shoot</h3>

                            <form id="shootForm">
                                <input name="_id" id="_id" value="<?php echo $this->asset->id; ?>" type="hidden"/>
                                <input name="_mode" id="_mode" value="add" type="hidden"/>

                                <div class="form-group">
                                    <label for="shootTitle">Title</label>
                                    <input class="form-control" type="text"
                                           name="shootTitle" id="shootTitle" class="required"/>
                                </div>
                                <div class="form-group">
                                    <label for="shootDesc">Description</label>
                                    <input type="text" class="form-control"
                                           name="shootDesc"
                                           id="shootDesc"/>
                                </div>
                                <div class="form-group">
                                    <label for="shootDate">Date</label>
                                    <input class="form-control" type="text"
                                           name="shootDate" id="shootDate" class="required datePrompt"/>
                                </div>
                                <button id="addShootBtn" class="btn btn-default" role="button" type="button">Add
                                </button>
                            </form>
                        </div>

                        <div id="editShootTab" role="tabpanel" class="tab-pane">
                            <h3>Edit</h3>

                            <div id="editShootFormHolder">
                                <div class="form-group">
                                    <label for="editShootTitle">Title</label>
                                    <input class="form-control" type="text"
                                           name="editShootTitle" id="editShootTitle" class="required"/>
                                </div>
                                <div class="form-group">
                                    <label for="editShootDesc">Description</label>
                                    <input type="text" class="form-control"
                                           name="editShootDesc"
                                           id="editShootDesc"/>
                                </div>
                                <div class="form-group">
                                    <label for="editShootDate">Date</label>
                                    <input class="form-control" type="text"
                                           name="editShootDate" id="editShootDate" class="required datePrompt"/>
                                </div>
                                <button id="editShootBtn" class="btn btn-default" role="button" type="button">Update
                                </button>
                            </div>
                        </div>

                        <div id="selectShootTab" role="tabpanel" class="tab-pane">
                            <h3>Select</h3>

                            <div id="shootsList"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <!--<button type="button" class="btn btn-primary">Save</button>-->
            </div>
        </div>
    </div>
</div>