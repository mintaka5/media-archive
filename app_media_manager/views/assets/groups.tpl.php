<div class="" id="groupsListHolder">
    <div id="groupsList"></div>
    <?php if ($this->auth->isManager()): ?>

        <div>
            <div class="fmElement">
                <label for="newGrpTitle" style="width:175px;">Add a new set:</label>

                <div class="input">
                    <input type="text" name="newGrpTitle" id="newGrpTitle"/>
                    <button name="newAstGrpBtn" id="newAstGrpBtn">Add &amp; Assign</button>
                </div>
            </div>
            <br class="clearLeft"/>
        </div>
    <?php endif; ?>
</div>

<div id="avlSets" class="goLeft">
    <h3>Available Sets:</h3>

    <div>
        <input type="text" id="filterAvlSets" value="Search"/>
        <a href="#" id="resetFilterSets">Reset</a>
    </div>
    <div id="availSets"></div>
</div>