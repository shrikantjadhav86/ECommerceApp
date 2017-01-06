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
				<div class="title"></div>
				<input type="hidden" name="id" id="id" value="{{$id}}" />
            </div>
        </div>
    </body>
</html>
<script>	
	var varnt_id = $('#id').val();
	var data = addCartProduct(varnt_id, 1);
function addCartProduct(variant_id, quantity)
{	
		data = {
      	quantity : quantity,
      	id : variant_id
    }
	
	$.ajax({
		type: 'POST',
		url: 'https://healthspan-2.myshopify.com/cart/add.js',
		data: (data),
		dataType: 'JSONP',
		headers: { 	'Access-Control-Allow-Headers': 'Content-Type, x-xsrf-token',
				 	'Access-Control-Allow-Origin' : '*',
				  	'Access-Control-Allow-Methods' : 'PUT, GET, POST, DELETE, OPTIONS',
				  	'Content-Type' : 'application/json'
				  
				 },
		success: function(data) {
			var datanew = {'status' : 'true', 'status_code': '200','message' : 'success'}
			console.log(datanew);
			return datanew;
		},
            error: function(error){
                if(error.status == 200){
					var datanew = {'status' : 'true', 'status_code': '200','message' : 'success'}
					console.log(datanew);
					return datanew;
				}
				var datanew = {'status' : 'false', 'status_code': '405','message' : 'error'}
				console.log(datanew);
				return datanew;
			
			}	
	});
}		
</script>	
