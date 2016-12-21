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
		<form method="POST" id="frm1" name="frm1"  action="cart1">
        <div class="container">
            <div class="content">
				
				<div class="title" id="dataid"></div>
				
            </div>
			
        </div>
		</form>
    </body>
</html>
<script type="text/javascript">
	cart();
	window.onload = function()
	{    
  
	}
	
function cart()
{

$.ajax({
	type: 'GET',
	url: 'https://healthspan-2.myshopify.com/cart.json',
	dataType: 'jsonp',
	async: false,
	success: function(data) {
		var item_count = data['item_count'];

		//If there are items in cart
		if(item_count > 0) {		
			jQuery.each(data['items'], function( i, val ) {
			  	var line_price = val['line_price']/100;
				val['line_price'] = line_price.toFixed(2);
			});
		}
		
		$('#dataid').append("<input type='hidden' name='jsondata' id='jsondata' value='"+JSON.stringify(data)+"' /><div>"+JSON.stringify(data)+"</div>");
		console.log(data);
		$("#frm1").submit();
		//return window.location = "/wrapper-tool/api/v1/cart1"+'?data='+JSON.stringify(data);
		//return  window.history.back();
		//return data;
	}
});
	}		
</script>	

				