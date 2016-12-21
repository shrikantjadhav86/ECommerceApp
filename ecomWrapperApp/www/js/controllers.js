angular.module('starter.controllers', [])

.controller('HomeCtrl', function($scope, $window,$http) {
    
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: "get",
        url: baseUrl+'api/v1/collections',
        headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
    }).success( function(data) {
        $scope.collectionsData = data.data;
        console.log('Response Data from product', $scope.productData);
    }).error(function(data) {
        console.log(data);
    });    
})

.controller('RegisterCtrl', function($scope) {})

.controller('ProductsCtrl', function($scope,$window,$http,$sessionStorage,$localStorage) {
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: "get",
        url: baseUrl+'api/v1/products',
        headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
    }).success( function(data) {
        $scope.productData = data.data;
        $scope.currentdate = new Date();
        console.log('Response Data from product', $scope.productData);
    }).error(function(data){
        console.log(data);    
    });
    var dataArray = [];

    //$window.localStorage.clear();
    $scope.addToCart = function (product) {
        var varient_id = product.variants.id;
        var storageData = $window.localStorage.getItem('cart_id');
         
        if(dataArray.length == 0 && storageData == null){          
            product.quantity = 1;
            dataArray[0] = product;
        }else if(dataArray.length != 0 && storageData == null){
            for(var j = 0; j < dataArray.length; j++) {
                if(dataArray[j].id === product.id) {
                  var val = j;
                }
            }
            if(val < 0 || val == undefined){
                 product.quantity = 1;
                dataArray.push(product);
            }else {
               product.quantity = dataArray[val].quantity+1;
               dataArray.splice(val, 1);
               dataArray.push(product);
            }
            
        }else if(dataArray.length != 0 && storageData != null){
            for(var j = 0; j < dataArray.length; j++) {
                if(dataArray[j].id === product.id) {
                  var val = j;
                }
            }
            if(val < 0 || val == undefined){
                 product.quantity = 1;
                dataArray.push(product);
            }else {
               product.quantity = dataArray[val].quantity+1;
               dataArray.splice(val, 1);
               dataArray.push(product);
            }
        }else if(dataArray.length == 0 && storageData != null){               
             dataArray = JSON.parse($window.localStorage.getItem('cart_id'));             
             for(var j = 0; j < dataArray.length; j++) {
                if(dataArray[j].id === product.id) {
                  var val = j;
                }
            }             
            if(val < 0 || val == undefined){
                 product.quantity = 1;
                dataArray.push(product);
            }else {
               product.quantity = dataArray[val].quantity+1;
               dataArray.splice(val, 1);
               dataArray.push(product);
            }                
        }
        $window.localStorage.setItem('cart_id', JSON.stringify(dataArray));
       
        $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
       /* $http({
            method: "POST",
            url: baseUrl+'api/v1/add/cart/'+varient_id,
            headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
        }).success( function(data) {
            $scope.productData = data.data;
            $scope.currentdate = new Date();
        }).error(function(data){
            console.log(data);
        }); */   
    }  
})

.controller('ProductDetailCtrl', function($scope,$window,$http,$stateParams) {
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
  
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: "get",
        url: baseUrl+'/api/v1/product/'+$stateParams.id,
        headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
    }).success( function(data) {
        $scope.detailData = data.data;
        console.log($scope.detailData);
        $scope.currentdate = new Date();
    }).error(function(data){
        console.log(data);    
    });
})

.controller('CartCtrl', function($scope,$rootScope,$window,$http,$localStorage, $sessionStorage) {
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    var cart_detail = [];
    cart_detail =  JSON.parse($window.localStorage.getItem('cart_id'));
    $scope.checkout =  function(){
        $window.location = '#/tab/checkout';
    }
    $scope.updateQuantity = function(quant, key){
        var numberVal = document.getElementById("quantity_"+key).value;
        cart_detail[key].quantity = numberVal;
        $window.localStorage.setItem('cart_id', JSON.stringify(cart_detail));
        getTotal();
    }
    
    $scope.cartData = cart_detail;
    $scope.this_domain = $window.this_domain;
     var updateData = '';
    $scope.updateCart = function(){
        /*for(var i=0; i< cart_detail.length; i++){
            updateData = cart_detail[i].variants.id+':'+cart_detail[i].quantity;
            $http({
            method: "post",
            url: baseUrl+'api/v1/update/cart/'+updateData,
            headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
            }).success(function(data) {
                                                
            }).error(function(data) {
                
            });
        } */       
    }
    
    $scope.getTotal = function() {
        var total = 0;
        for(var i = 0; i < $scope.cartData.length; i++){
            var product = $scope.cartData[i];
            $scope.quantity = $('#quantity_'+i).val();
           product.variants.subtotal = product.variants.price * $scope.quantity;
            total += (product.variants.price * $scope.quantity);
            product.subtotal = total;
        }
        $scope.grandTotal = total;
         $window.localStorage.setItem('grandTotal', JSON.stringify($scope.grandTotal));
        return total;
    }
   
   
    $scope.removeCartProduct = function(line) {
        var val = line-1;
        cart_detail.splice(val, 1);
        $window.localStorage.setItem('cart_id', JSON.stringify(cart_detail)); 
      /* 
        $http({
            method: "post",
            url: baseUrl+'api/v1/remove/cart/'+line,
            headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
        }).success(function(data) {
            getTotal();
        }).error(function(data) {
            console.log(data);
        });*/
    }
})

.controller('CheckoutCtrl', function($scope,$window,$http) {
    $scope.user = {};
    $scope.grandTotal =  JSON.parse($window.localStorage.getItem('grandTotal'));
    $scope.user.country = "India";
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.submitForm = function() {
        console.log($scope.user);
        $scope.user.country_code = "IN";
        $http({
            method: "post",
            url: baseUrl+'api/v1/address_validate',
            data : $scope.user,
        }).success(function(data) {
            $scope.verify = data;          
            if($scope.verify.status == true){
                window.localStorage.setItem('user_data', JSON.stringify( $scope.user));
                $window.location = '#/tab/review';
            }
        }).error(function(data){
            $scope.verify = data;
        });

    }    
})

.controller('ReviewCtrl', function($scope,$window,$http) {
    $scope.grandTotal =  JSON.parse($window.localStorage.getItem('grandTotal'));
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.user_data =  JSON.parse($window.localStorage.getItem('user_data'));
    $scope.cart_product =  JSON.parse($window.localStorage.getItem('cart_id'));
  
})

.controller('PaymentCtrl', function($scope,$window,$http) {
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $scope.user = JSON.parse($window.localStorage.getItem('user_data'));
   
    $scope.grandTotal = JSON.parse($window.localStorage.getItem('grandTotal'));
    $scope.pay = {};
   
  
    $scope.submitPayForm = function() {
       
        var cardno = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
        if($scope.pay.cardnumber.match(cardno))
        {
            $scope.user.cardnumber = $scope.pay.cardnumber;
            $scope.user.expired_year = $scope.pay.expired_year;
            $scope.user.expired_month = $scope.pay.expired_month;
            $scope.user.cvv = $scope.pay.cvv;
            $scope.user.nameoncard = $scope.pay.nameoncard;
            $scope.user.postalcode = $scope.user.zip;
            $scope.user.address = $scope.user.street;
            $scope.user.card_type = 'visa';
            $scope.user.total = $scope.grandTotal;
            $http({
                   method: "post",
                   url: baseUrl+'api/v1/cardPayment',
                   data :  $scope.user,
                }).success(function(data) {
                   $scope.payment = data;          
                   if($scope.payment.status == true) {
                       $window.location = '#/tab/thank_you';
                            
                    }
                }).error(function(data){
                  $scope.payment = data;
               });
        }
        else  
        {  
            alert("Not a valid Visa credit card number!");  
            return false;
        }         
     }
})

.controller('OrdersCtrl', function($scope,$window,$http) {
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
    if ($window.localStorage.getItem('user_data') !== null) {
       
    }else {
        $scope.user = {};
    }
    
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: "get",
        url: baseUrl+'api/v1/order',
        headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
    }).success( function(data) {
        $scope.orderData = data.data;
        $scope.orderdate = new Date();
        console.log('Response Data from product', $scope.orderData);
    }).error(function(data){
        console.log(data);
    });
})

.controller('OrdersDetailsCtrl', function($scope,$window,$http,$stateParams) {
    $scope.this_domain = $window.this_domain;
    var baseUrl = $window.this_domain;
    $http.defaults.headers.post["Content-Type"] = "application/x-www-form-urlencoded";
    $http({
        method: "get",
        url: baseUrl+'api/v1/order/'+$stateParams.order_id,
        headers: "{ 'Content-Type': 'application/x-www-form-urlencoded' }",
    }).success( function(data) {
        $scope.orderDetailData = data.data;
        console.log('Response Data from product', $scope.orderDetailData);
    }).error(function(data) {
        console.log(data);    
    });       
})

.controller('TabCtrl', function($scope,$window,$http) {
    if ($window.localStorage.getItem('cart_id') !== null) {
        var cart_detail =  JSON.parse($window.localStorage.getItem('cart_id'));
        $scope.cartCount = cart_detail.length;
    }    
    
});
