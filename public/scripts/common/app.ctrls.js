;(function() {
    'use strict';

    angular.module('app.ctrls', [])

    .controller('AppCtrl', ['$scope', '$mdSidenav', '$mdDialog', function($scope, $mdSidenav, $mdDialog) {

        // Open search btn
        $scope.openSearch = function() {
            $scope.isSearchOpen = true;
        };

        $scope.closeSearch = function(){
            $scope.isSearchOpen = false;
        };

        $scope.toggleSidenav = function() {
            $mdSidenav('sidenav-left').toggle();
        }

        $scope.closeDialog = function() {
            $mdDialog.hide();
        }

    }])


    .controller('DashboardCtrl', ['$scope','$http', function($scope, $http) {
        // === weekly growth
        $scope.weeklygrowthconfig = {
            data: {
    			columns: [
    				['Page Views', 740, 850, 700, 840, 790, 730, 830],
    				['Sessions', 790, 800, 670, 640, 740, 550, 800]
    			],
    			type: 'area-spline',
    		},
    		color: {
    			pattern: ["#40C4FF",  "#448AFF"]
    		},
    		legend: {
    			position: "bottom"
    		},
    		size: {
    			height: 300
    		},
            axis: {
                y: { max: 900, min: 300}

            }
        };

        // === browser sessions
        $scope.browserconfig = {
            data: {
                columns: [
                ["Chrome", 50.9],
                ["Firefox", 16.1],
                ["Safari", 10.9],
                ["IE", 15.1],
                ["Other",7]
                ],
                type: "donut",
            },
            size: {
                width: 320,
                height: 280
            },
            donut: {
                width: 60
            },

            color: {
                pattern: ["#4CAF50", "#8BC34A", "#FFC107", "#CDDC39", "#FF9800"]
            }
        };

        $scope.visitorsageconfig = {
            data: {
                x: 'x',
                y: 'y',
                columns: [
                    ['x', '18-25', '26-35', '36-50', '51-80', '80-above'],
                    ['Male', 29.4, 28, 61, 38, 20],
                    ['Female', 29.6, 60, 23, 49, 34]

                ],


                type: 'bar',
                groups: [
                    ['Male', 'Female']
                ],
            },
            color: {
                pattern: ["#4CAF50", "#CDDC39"]
            },
            axis: {
                x: {
                    type: 'category' // this needed to load string x value
                },
                y: {
                    label: {text: 'Sessions', position: 'outer-middle'}
                }
            }

        }

        $scope.periodsDisplayed = 0;

        $scope.paybackPeriod = {
            periods:0,
            interest: 0,
            principal:0,
            periodInfo : []

        };

        $scope.addPPPeriods = function (){
            $scope.paybackPeriod.periodos = [];
            var i = 1;
            while(i<= $scope.paybackPeriod.periods){
                $scope.paybackPeriod.periodos.push({outflow:0, inflow:0, cumulativeCashFlow:0, period:i});
                i++;
            }
        };
        $scope.cleanPP =function (){
            $scope.paybackPeriod = {
                periods:0,
                tasaInteres: 0,
                principal:0,
                periodos : []
            };
        };

        $scope.calculatePP =  function (){
            $http({
                method : "POST",
                url : "http://gleefull.com.mx/adminProj/adminProj/php/discountedPaybackPeriod.php",
                data : {info:$scope.paybackPeriod},
                headers : {'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).then(function (response) {
                console.log(response);
                $.each($scope.paybackPeriod.periodos, function (index, value){
                    value.cumulativeCashFlow = response.data.periods[index].value;
                });
                console.log(response);
            }, function () {
            });
        };

        $scope.NPV = {
            periods:0,
            interest: 0,
            principal:0,
            taxRate:0,
            salvageValue:0,
            salvageValuePeriod:0,
            periodInfo : []

        };


        $scope.addNPVPeriods = function (){
            $scope.NPV.periodos = [];
            var i = 1;
            while(i<= $scope.NPV.periods){
                $scope.NPV.periodos.push({outflow:0, inflow:0, netCashFlow:0, cumulativeCashFlow:0, period:i});
                i++;
            }
        };
        $scope.calculateNPV =  function (){
            $http({
                method : "POST",
                url : "http://gleefull.com.mx/adminProj/adminProj/php/npv.php",
                data : {info:$scope.NPV},
                headers : {'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).then(function (response) {
                console.log(response);
            }, function () {
            });
        };

        $scope.cleanNPV=function (){
            $scope.NPV = {
                periods:0,
                interest: 0,
                principal:0,
                taxRate:0,
                salvageValue:0,
                salvageValuePeriod:0,
                periodos : []

            };
        };

        $scope.depreciation = {
            periods:0,
            startingYear: 0,
            principal:0,
            taxRate:0,
            salvageValue:0,
            salvageValuePeriod:0,
            depreciationCategory:0,
            periodInfo : []

        };


        $scope.addDepreciationPeriods = function (){
            $scope.depreciation.periodos = [];
            var i = 1;
            while(i<= $scope.depreciation.periods){
                $scope.depreciation.periodos.push({outflow:0, inflow:0, netCashFlow:0, cumulativeCashFlow:0, period:i});
                i++;
            }
        };

        $scope.calculateDepreciation =  function (straightLine, macrs){
            $scope.depreciation.straightLine = straightLine;
            $scope.depreciation.macrs = macrs;
            $http({
                method : "POST",
                url : "http://gleefull.com.mx/adminProj/adminProj/php/depreciation.php",
                data : {info:$scope.depreciation},
                headers : {'Content-Type' : 'application/x-www-form-urlencoded; charset=UTF-8'}
            }).then(function (response) {
                $.each(response.data.accDep, function (index, value){
                    $scope.depreciation.periodos[index].depreciationRate = response.data.depRate[index];
                    $scope.depreciation.periodos[index].AnnualDepreciation = response.data.anualDep[index];
                    $scope.depreciation.periodos[index].accumulatedDepreciation = response.data.accDep[index];
                    $scope.depreciation.periodos[index].ledgersValue = response.data.valueInLedger[index];
                });
            }, function () {
            });
        };

        $scope.cleanDepreciation=function (){
            $scope.depreciation = {
                periods:0,
                startingYear: 0,
                principal:0,
                taxRate:0,
                salvageValue:0,
                salvageValuePeriod:0,
                depreciationCategory:0,
                periodInfo : []

            };
        }


    }])


})()
