var metadataApp = angular.module('metadataApp', [
    'ngRoute',
    'metadataController'
]);

metadataApp.config([
    '$routeProvider',
    function($routeProvider) {
        $routeProvider.when('/', {
            controller: 'metadataListController',
            templateUrl: 'metadataList.html'
        }).when('/item/:id', {
            controller: 'metadataDetailController',
            templateUrl: 'metadataDetail.html'
        }).otherwise({
            redirectTo: '/'
        });
    }
]);