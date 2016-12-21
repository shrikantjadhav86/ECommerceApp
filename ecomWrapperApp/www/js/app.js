// Ionic Starter App

// angular.module is a global place for creating, registering and retrieving Angular modules
// 'starter' is the name of this angular module example (also set in a <body> attribute in index.html)
// the 2nd parameter is an array of 'requires'
// 'starter.services' is found in services.js
// 'starter.controllers' is found in controllers.js

var this_domain = "http://localhost/wrapper-tool/";

angular.module('starter', ['ionic', 'starter.controllers', 'starter.services','ngStorage'])
.run(function($ionicPlatform) {
  $ionicPlatform.ready(function() {
    // Hide the accessory bar by default (remove this to show the accessory bar above the keyboard
    // for form inputs)
    if (window.cordova && window.cordova.plugins && window.cordova.plugins.Keyboard) {
      cordova.plugins.Keyboard.hideKeyboardAccessoryBar(true);
      cordova.plugins.Keyboard.disableScroll(true);

    }
    if (window.StatusBar) {
      // org.apache.cordova.statusbar required
      StatusBar.styleDefault();
    }
  });
})

.config(function($stateProvider, $urlRouterProvider) {

  // Ionic uses AngularUI Router which uses the concept of states
  // Learn more here: https://github.com/angular-ui/ui-router
  // Set up the various states which the app can be in.
  // Each state's controller can be found in controllers.js
  $stateProvider
  
  // setup an abstract state for the tabs directive
    .state('tab', {
    url: '/tab',
    abstract: false,
    templateUrl: function() {return 'templates/tabs.html';},
    controller: 'TabCtrl'
  })

  .state('tab.home', {
    url: '/home',
    views: {
      '': {
        templateUrl: function() {return 'templates/home.html';},
        controller: 'HomeCtrl'
      }
    }
  })
  
  .state('tab.register', {
    url: '/register',
    views: {
      '': {
        templateUrl: function() {return 'templates/register.html';},
        controller: 'RegisterCtrl'
      }
    }
  })
  
  .state('tab.products', {
    url: '/products',
    views: {
      '': {
        templateUrl: function() {return 'templates/products.html';},
        controller: 'ProductsCtrl'
      }
    }
  })
  
  .state('tab.product_detail', {
      url: '/product/:id',
      views: {
        '': {
            templateUrl: function() {return 'templates/product_detail.html';},
            controller: 'ProductDetailCtrl'
        }
      }
    })
    
    .state('tab.cart', {
        url: '/cart',
        views: {
            '': {
                templateUrl: function() {return 'templates/cart.html';},
                controller: 'CartCtrl'
            }
        }
    })
    
    .state('tab.checkout', {
        url: '/checkout',
        views: {
            '': {
                templateUrl: function() {return 'templates/checkout.html';},
                controller: 'CheckoutCtrl'
            }
        }
    })
    
    .state('tab.review', {
        url: '/review',
        views: {
            '': {
                templateUrl: function() {return 'templates/review.html';},
                controller: 'ReviewCtrl'
            }
        }
    })
    .state('tab.payment', {
        url: '/payment',
        views: {
            '': {
                templateUrl: function() {return 'templates/payment.html';},
                controller: 'PaymentCtrl'
            }
        }
    })
    .state('tab.thank_you', {
        url: '/thank_you',
        views: {
            '': {
                templateUrl: function() {return 'templates/thank_you.html';}                
            }
        }
    })
    
    .state('tab.customer-orders', {
        url: '/orders',
        views: {
            '': {
                templateUrl: function() {return 'templates/customer-orders.html';},
                controller: 'OrdersCtrl'
            }
        }
    })
    
    .state('tab.order_detail', {
        url: '/order/:order_id',
        views: {
            '': {
                templateUrl: function() {return 'templates/order_detail.html';},
                controller: 'OrdersDetailsCtrl'
            }
        }
    })
    

  // if none of the above states are matched, use this as the fallback
  $urlRouterProvider.otherwise('/tab/products');

});
