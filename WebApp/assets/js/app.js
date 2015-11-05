/**
 * Gungho API Script
 *
 * @author Jarrod Sampson
 * @copyright 2015 Planlodge
 * Website: http://www.planlodge.com/gungho-portal/
 *
 */
// Only Dependency is ngAnimate for this app.
var app = angular.module('GunghoApp', ["ngAnimate", "ngRoute", "ng-fusioncharts", "toaster"]);


// configure our routes
app.config(function($routeProvider, $locationProvider) {


    // route for the home page
    $routeProvider.when('/', {
        templateUrl: 'pages/home.html',
        controller: 'loginController',
        resolve: {
            "check": function($location) {
                if (localStorage.getItem("username") === null) {
                    $location.path('/');

                } else {
                    $location.path('/admin');
                }
                console.log(localStorage.getItem("username"));
            }
        }
    })

    // route for the about page
    .when('/help', {
        templateUrl: 'pages/help.html',
        controller: 'HelpController'
    })

    // route for the about page
    .when('/admin', {
            templateUrl: 'pages/admin.html',
            controller: 'MainController',
            resolve: {
                "check": function($location) {
                    if (localStorage.getItem("username") === null) {
                        $location.path('/');

                    } else {
                        $location.path('/admin');
                    }
                    console.log(localStorage.getItem("username"));
                }
            }
        })
        // error catching
        .otherwise({
            redirectTo: '/help'
        });

});

app.controller('loginController', function($scope, $location, $http, $timeout, toaster) {

    $scope.pageClass = 'page page-login';
    $scope.loginMessage = "Please login with your credentials.";
    $scope.titleSet = "- Login";

    $scope.changeView = function(view) {
        $location.path(view); // path not hash
    }

    $scope.attemptLogin = function() {

        $scope.loading = true;
        $('.overlay').fadeIn(100);
        $('#loader').fadeIn(200);

        var username = $scope.user.username;
        var password = $scope.user.password;

        var objLogin = $.param({
            username: username,
            password: password
        });

        $http({
            method: 'POST',
            url: 'http://www.planlodge.com/gungho-portal/login/',
            data: objLogin, // pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            } // set the headers so angular passing info as form data (not request payload)
        }).success(function(data) {
            console.log(data.response);

            if (data.response == 1) {
                if (typeof(Storage) !== "undefined") {
                    localStorage.setItem("username", $scope.user.username);
                    $location.path("/admin");
                }
            } else {
                $scope.loginMessage = "Incorrect Username or Password.";
                toaster.pop('error', "Invalid Login", "Incorrect Username or Password.");
            }

            $scope.user.password = '';



            $scope.loading = false;
            $('.overlay').fadeOut(1000);
            $('#loader').fadeOut(2000);

        });

    };

});

app.controller('HelpController', function($scope, $location) {
    $scope.message = 'Look! I am an about page.';
    $scope.pageClass = 'page page-help';
    $scope.titleSet = "- FAQs and Instructions";

    $scope.changeView = function(view) {
        $location.path(view); // path not hash
    }
});


app.controller('MainController', function($scope, $http, $location, toaster) {

    $scope.pageClass = 'page-admin';

    $scope.welcomeMessage = "Hello, " + localStorage.getItem("username") + ".";
    $scope.titleSet = "- Welcome " + localStorage.getItem("username");

    $scope.employerTable = true;

    $scope.isLoggedIn = true;
    // set data limit
    $scope.quantity = 35;
    // beginning filter from JSON API
    $scope.predicate = 'ID';
    // Set to Standard Wait Position
    $scope.reverse = false;
    // Initialized variables ready for scope to give value
    $scope.total;
    $scope.SearchDescription;
    $scope.menuFilterItem;

    $scope.employeeListGather = function() {

        // gather employee data, initial API call
        $http.get("http://www.planlodge.com/gungho-portal/xxzy/json/v1/")
            .success(function(response) {
                // set the frameworks
                $scope.employeeList = response.data;
                // recive the total number of frameworks
                $scope.total = response.items;
                // bind value in template for dynamic total change
                $scope.SearchDescription = $scope.total + " employees found.";
                // intro loading for json data
                $('.overlay').fadeOut(1000);
                $('#loader').fadeOut(1000);
                $('.wrapper').fadeIn(900);
            });
    };

    // start list
    $scope.employeeListGather();

    /**
     * $scope.order
     * 
     * Will allow us to toggle the orderBy method 
     * back and forth assigning an icon as well
     */
    $scope.order = function(predicate) {
        $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
        $scope.predicate = predicate;
    };

    /**
     * $scope.searchingFilterActive
     * 
     * Will allow us to use the filter only for specified objects
     * 
     */
    $scope.searchingFilterActive = function(obj) {
        var re = new RegExp($scope.name, 'i');
        return !$scope.name || re.test(obj.LastName) || re.test(obj.FirstName) || re.test(obj.ID);
    }

    /**
     * $scope.showOptionsAn
     * 
     * Toggle data filters
     * 
     */
    $scope.showOptionsAn = function() {
        // toggle options
        $scope.showOptions = !$scope.showOptions;

        if ($scope.showOptions == true) {
            // scroll animation for UI/UX enhancements
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
        }
    };

    /**
     * $scope.toTop
     * 
     * UI/UX Scroller
     * 
     */
    $scope.toTop = function() {
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    }


    /**
     * $scope.editItem and doneEditing
     * 
     * Double Click to Edit Entry
     * Ajax Call when done
     */

    $scope.editItem = function(item) {
        item.editing = true;
    }

    $scope.doneEditing = function(item) {
        item.editing = false;

        var xsrf = $.param({
            FirstName: item.FirstName,
            LastName: item.LastName,
            HireDate: item.HireDate,
            ID: item.ID,
            BirthDate: item.BirthDate,
            Gender: item.Gender
        });
        $http({
            method: 'PUT',
            url: 'http://www.planlodge.com/gungho-portal/xxzy/json/v1/',
            data: xsrf, // pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            } // set the headers so angular passing info as form data (not request payload)
        }).success(function(data) {
            console.log(data);
            toaster.pop('success', "Employee #" + item.ID, "Edit complete.");

        });
    };

    /**
     * $scope.logout
     * 
     * Clear Session Data 
     *
     */
    $scope.logout = function() {
        localStorage.removeItem("username");
        $location.path("/");
        toaster.pop('info', "User Info", "You have logged out.");
    };

    $scope.addEmploy = function() {

        $scope.loading = true;
        $('.overlay').fadeIn(100);
        $('#loader').fadeIn(200);
        $('.modal').modal('hide');

        var userID = $scope.user.id;
        var empFirstName = $scope.user.first;
        var empLastName = $scope.user.last;
        var birthDate = $scope.user.birthDate;
        var hireDate = $scope.user.hireDate;
        var gender = $scope.user.gender;

        // alert(userID + '' + empFirstName + '' + empLastName + '' + gender );

        var objEmployee = $.param({
            ID: userID,
            FirstName: empFirstName,
            LastName: empLastName,
            BirthDate: birthDate,
            HireDate: hireDate,
            Gender: gender,
        });

        $http({
            method: 'POST',
            url: 'http://www.planlodge.com/gungho-portal/xxzy/json/v1/',
            data: objEmployee, // pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            } // set the headers so angular passing info as form data (not request payload)
        }).success(function(data) {
            console.log(data);

            $scope.user.id = '';
            $scope.user.first = '';
            $scope.user.last = '';
            $scope.user.birthDate = '';
            $scope.user.hireDate = '';

            $scope.loading = false;
            $('.overlay').fadeOut(1000);
            $('#loader').fadeOut(2000);

            $scope.employeeListGather();

            toaster.pop('success', "Employee Added", "You have added a new employee.");
        });
    };

    $scope.deleteEmploy = function() {

        $scope.loading = true;
        $('.overlay').fadeIn(100);
        $('#loader').fadeIn(200);
        $('.modal').modal('hide');

        var userID = $scope.user.deleteUserID;

        // alert(userID + '' + empFirstName + '' + empLastName + '' + gender );

        var objDelEmployee = $.param({
            ID: userID
        });

        $http({
            method: 'DELETE',
            url: 'http://www.planlodge.com/gungho-portal/xxzy/json/v1/',
            data: objDelEmployee, // pass in data as strings
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            } // set the headers so angular passing info as form data (not request payload)
        }).success(function(data) {
            console.log(data);


            $scope.user.deleteUserID = '';

            $scope.loading = false;
            $('.overlay').fadeOut(1000);
            $('#loader').fadeOut(2000);

            $scope.employeeListGather();
            toaster.pop('success', "Employee #" + userID, "Employee has been deleted.");

        });
    };

    /**
     * $scope.myDataSource
     * 
     * Initial Values for chart
     * 
     */
    $scope.myDataSource = {
        chart: {
            caption: "Employee BreakDown",
            subCaption: "Top 5 Categories in last month by revenue",
        },
        data: [{
            label: "Monthly",
            value: "880000"
        }, {
            label: "Quarterly",
            value: "730000"
        }, {
            label: "Annually",
            value: "590000"
        }, {
            label: "Daily",
            value: "520000"
        }, {
            label: "Weekly",
            value: "330000"
        }]
    };

    $scope.graphData = function() {
        $scope.showGraphData = !$scope.showGraphData;
    };


});


/**
 * capitalize
 * 
 * Created filter to help with uppercasing
 * Of the first letter in framework
 *
 */
app.filter('capitalize', function() {
    return function(input) {
        return (!!input) ? input.charAt(0).toUpperCase() + input.substr(1) : '';
    }
});

app.directive('bsPopover', function() {
    return function(scope, element, attrs) {
        element.find("a[rel=popover]").popover({
            placement: 'right',
            html: 'true'
        });
    };
});

app.directive('tooltips', function() {
    return function(scope, element, attrs) {
        element.find("[data-toggle='tooltip']").tooltip({
            html: 'true'
        });
    };
});

app.directive('datepicker', function() {
    return {
        restrict: "A",
        link: function(scope, el, attr) {
            el.datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true
            });
        }
    };
});

app.run(['$route', function($route) {
    /**
     * jQuery Usage
     * Bug fixes
     *
     */

    $(".navbar-nav li a").click(function(event) {
        $(".navbar-collapse").collapse('hide');
    });

    // UI toogle for up scroller
    $(window).scroll(function() {
        if ($(this).scrollTop() > 100) {
            $('.scroll-up').fadeIn();
        } else {
            $('.scroll-up').fadeOut();
        }
    });

    $('.overlay').fadeOut(1000);
    $('#loader').fadeOut(2000);
}]);