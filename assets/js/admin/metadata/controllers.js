var metadataController = angular.module('metadataController', []);

metadataController.controller('metadataListController', function($scope, $http) {
    $http.get(globals.ajaxurl + 'admin/metadata/app.php').success(function(data) {
        $scope.metadata = data;
    });
});

metadataController.controller('metadataDetailController', function($scope, $http, $routeParams, $route) {
    console.log($routeParams);

    $http.get(globals.ajaxurl + 'admin/metadata/app.php', {
        params: {
            _mode: 'item',
            _task: 'detail',
            id: $routeParams.id
        }
    }).success(function(data) {
        $scope.metadata = data;
    });
});