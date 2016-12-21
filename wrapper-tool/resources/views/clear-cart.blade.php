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
            </div>
        </div>
    </body>
</html>
<script>
	clearCartProduct();
function clearCartProduct()
{
	$.ajax({
		type: 'POST',
		url: 'https://healthspan-2.myshopify.com/cart/clear.js',
		data: {},
		dataType: 'JSONP',
		success: function() {
			console.log("clear!");
		},
		error: function(error){
			if(error.status == 200){
				console.log("success");
			}
		}
	});
}		
</script>	
