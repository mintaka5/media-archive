<div class="panel panel-default" ng-app="metadataApp">
    <script src="<?php echo $this->manager->getURI(); ?>assets/js/admin/metadata/app.js"></script>
    <script src="<?php echo $this->manager->getURI(); ?>assets/js/admin/metadata/controllers.js"></script>
    <div class="panel-heading">
        <h3 class="panel-title">Metadata</h3>
    </div>
    <div class="panel-body">
        <div ng-view></div>

        <script type="text/ng-template" id="metadataList.html">
            <ul>
                <li ng-repeat="metadatum in metadata">
                    <a href="#/item/{{metadatum.id}}">{{metadatum.title}}</a>
                    {{metadatum.description}}
                </li>
            </ul>
        </script>

        <script type="text/ng-template" id="metadataDetail.html">
            <div>
                {{metadata.title}}
            </div>
        </script>

    </div>
</div>