var am = angular.module('jiefApp', [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol('[[');
    $interpolateProvider.endSymbol(']]');
});

am.controller('JiefController', function UserController($scope, $http) {
  $scope.name = function() {
    $http.get('identity').success(function(data) {
      $scope.name = data.name;
      $scope.headline = data.headline;
    });
  }

  $scope.positions = function() {
    $http.get('bootstrap.php?a=po').success(function(data) {
      $scope.positions = data;
    });
  };

  $scope.skills = function() {
    $http.get('skills').success(function(data) {
      $scope.skills = data;
    });
  };

  $scope.socials = function() {
    $http.get('social').success(function(data) {
      $scope.socials = data;
    });
  };

  $scope.init = function() {
    $scope.name();
    $scope.positions();
    $scope.skills();
    $scope.socials();
  };
});
