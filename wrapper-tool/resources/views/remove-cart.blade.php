<?php
header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header('Content-Type: application/json');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
		 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
        
    </head>
    <body>
        <div class="container">
            <div class="content">
				<div class="title"><input type="hidden" name="line" id="line" value="{{$line}}" /></div>
            </div>
        </div>
    </body>
</html>
<script>
	var quantity = 0;
	var line =  $('#line').val();
	removeCartProduct(quantity,line);
function removeCartProduct(quantity,line)
{
		
	$.ajax({
		type: 'POST',
		url: 'https://healthspan-2.myshopify.com/cart/change.js',
		data: { quantity: quantity, line: line },
		dataType: 'JSONP',
		headers: { 	'Access-Control-Allow-Headers': 'Content-Type, x-xsrf-token',
				 	'Access-Control-Allow-Origin' : '*',
				  	'Access-Control-Allow-Methods' : 'PUT, GET, POST, DELETE, OPTIONS',
				  	'Content-Type' : 'application/json'
				  
				 },
		success: function() {
				console.log("edited!");
		},
            error: function(error){
				if(error.status == 200){
				console.log("success");	
				}	
                
			}	
	});
}		
</script>	
