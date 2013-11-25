function JiefCtrl($scope, $http) {
	$scope.name = function() {
		$http.get('bootstrap.php?a=na').success(function(data) {
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
  	$http.get('bootstrap.php?a=sk').success(function(data) {
			$scope.skills = data;
		});
  };

  $scope.socials = function() {
  	$http.get('bootstrap.php?a=so').success(function(data) {
			$scope.socials = data;
		});
  };

  $scope.init = function() {
  	$scope.name();
  	$scope.positions();
  	$scope.skills();
  	$scope.socials();
  };
}
