<div id="photogWin">
    <div id="photogTabs">
        <ul>
            <li><a href="#addPhotogTab">Add</a></li>
            <li><a href="#editPhotogTab">Edit</a></li>
            <li><a href="#selectPhotogTab">Select</a></li>
        </ul>
        <div id="addPhotogTab">
            <form id="addPhotogForm">
                <input type="hidden" id="_mode" name="_mode" value="asset"/>
                <input type="hidden" id="_task" name="_task" value="add"/>
                <input type="hidden" id="_aid" name="_aid" value="<?php echo $this->asset->id; ?>"/>

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
        <div id="editPhotogTab"></div>
        <div id="selectPhotogTab">
            <div id="photogsList"></div>
        </div>
    </div>
</div>