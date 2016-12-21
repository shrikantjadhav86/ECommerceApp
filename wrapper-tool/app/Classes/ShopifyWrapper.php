<?php

namespace App\Classes;

class ShopifyWrapper implements EcommerceInterface {

	private $config = array();
	
	public $ShopifyClient; 
	/**
	 * get object instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	 	$this->ShopifyClient = new ShopifyClient();
	}

	public function setConfig($config)
	{
		$this->config = $config;
	}
	/**
     * get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
		$products = $this->ShopifyClient->call('GET','/admin/products.json',$this->config);
		
		$arra = array();
		if(!empty($products['products']))
		{
			foreach($products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['title'] = $val['title'];
					$arr['created_at'] = $val['created_at'];
					$arr['vendor'] = $val['vendor'];
					$arr['product_type'] = $val['product_type'];
					$arr['tags'] = $val['tags'];
					$arr['images'] = $val['images'];
					$arr['image'] = $val['image'];
					$arr['options'] = $val['options'];			
					$arr['variants']['id'] = $val['variants'][0]['id'];
					$arr['variants']['price'] = $val['variants'][0]['price'];
					$arr['variants']['sku'] = $val['variants'][0]['sku'];
					$arr['variants']['compare_at_price'] = $val['variants'][0]['compare_at_price'];
					$arr['variants']['weight'] = $val['variants'][0]['weight'];
					$arr['variants']['weight_unit'] = $val['variants'][0]['weight_unit'];
					$arra['data'][] = $arr;				
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{	
		$status_code = 404;
		$message = 'Products not found';
		$arra['data'] = array();
		$arra['data_count'] = 0; 
	}	
		
		$products = $this->getJsonDataFormat($arra,$status_code,$message);	
		
		return $products;
    }

    /**
     * get product details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductDetails()
    {
		$product = $this->ShopifyClient->call('GET','/admin/products/'.$this->config['id'].'.json');
		
		$arra = array();		
		if(!empty($product['product']))
		{						
			foreach($product as $key=>$val) {
				$arr['id'] = $val['id'];
				$arr['title'] = $val['title'];
				$arr['body_html'] = $val['body_html'];
				$arr['created_at'] = $val['created_at'];
				$arr['vendor'] = $val['vendor'];
				$arr['product_type'] = $val['product_type'];
				$arr['tags'] = $val['tags'];
				$arr['image'] = $val['image'];
				$arr['images'] = $val['images'];
				$arr['options'] = $val['options'];
				$arr['variants']['id'] = $val['variants'][0]['id'];
				$arr['variants']['price'] = $val['variants'][0]['price'];
				$arr['variants']['sku'] = $val['variants'][0]['sku'];
				$arr['variants']['compare_at_price'] = $val['variants'][0]['compare_at_price'];
				$arr['variants']['weight'] = $val['variants'][0]['weight'];
				$arr['variants']['weight_unit'] = $val['variants'][0]['weight_unit'];
				$arra['data'][] = $arr;
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{	
		$status_code = 404;
		$message = 'Product not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}
		$product = $this->getJsonDataFormat($arra,$status_code,$message);	
		return $product;
    }
	
	/**
	* Display users or customers
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCustomers()
    {
		$customers = $this->ShopifyClient->call('GET','/admin/customers.json',$this->config);		
		$arra = array();
		if(!empty($customers['customers']))
		{
			foreach($customers as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['email'] = $val['email'];
					$arr['accepts_marketing'] = $val['accepts_marketing'];
					$arr['first_name'] = $val['first_name'];
					$arr['last_name'] = $val['last_name'];
					$arr['orders_count'] = $val['orders_count'];
					$arr['state'] = $val['state'];			
					$arr['total_spent'] = $val['total_spent'];
					$arr['last_order_id'] = $val['last_order_id'];
					$arr['note'] = $val['note'];
					$arr['verified_email'] = $val['verified_email'];
					$arr['multipass_identifier'] = $val['multipass_identifier'];
					$arr['tax_exempt'] = $val['tax_exempt'];
					$arr['tags'] = $val['tags'];
					$arr['last_order_name'] = $val['last_order_name'];
					$arr['default_address'] = $val['default_address'];									
					$arr['addresses'] = $val['addresses'];
					$arra['data'][] = $arr;				
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;
		$message = 'Customers not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}	
		$customers = $this->getJsonDataFormat($arra,$status_code,$message);	
		return $customers;
    }
	
	/**
	* Get All collections
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCollections()
    {
		$collections = $this->ShopifyClient->call('GET','/admin/smart_collections.json',$this->config);		
		$arra = array();
		if(!empty($collections['smart_collections']))
		{
			foreach($collections as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['handle'] = $val['handle'];
					$arr['title'] = $val['title'];
					$arr['body_html'] = $val['body_html'];
					$arr['published_at'] = $val['published_at'];
					$arr['sort_order'] = $val['sort_order'];
					$arr['template_suffix'] = $val['template_suffix'];				
					$arr['published_scope'] = $val['published_scope'];
					$arr['disjunctive'] = $val['disjunctive'];
					$arr['rules'] = $val['rules'];					
					$arra['data'][] = $arr;				
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;
		$message = 'Collections not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;	
	}	
		$collections = $this->getJsonDataFormat($arra,$status_code,$message);	
		return $collections;
    }
	
	/**
	* Get All collection products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCollectionProducts()
    {
		$collection_Products = $this->ShopifyClient->call('GET','/admin/products.json?collection_id='.$this->config['collection_id']);		
		$arra = array();
		if(!empty($collection_Products['products']))
		{
			foreach($collection_Products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['title'] = $val['title'];
					$arr['vendor'] = $val['vendor'];
					$arr['product_type'] = $val['product_type'];
					$arr['tags'] = $val['tags'];
					$arr['images'] = $val['images'];
					$arr['options'] = $val['options'];			
					$arr['variants']['id'] = $val['variants'][0]['id'];
					$arr['variants']['price'] = $val['variants'][0]['price'];
					$arr['variants']['sku'] = $val['variants'][0]['sku'];
					$arr['variants']['compare_at_price'] = $val['variants'][0]['compare_at_price'];
					$arr['variants']['weight'] = $val['variants'][0]['weight'];
					$arr['variants']['weight_unit'] = $val['variants'][0]['weight_unit'];
					$arra['data'][] = $arr;					
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;  
		$message = 'Collections products not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}	
		$collection_Products = $this->getJsonDataFormat($arra,$status_code,$message);
		return $collection_Products;
    }
	
	/**
	* Get All cart products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCartProduct()
    {		
		$cart_arr = array();		
		$status_code = '';
		$message = '';
		$arrnew = array();
		$cart_id = $this->config['cart_id'];
		if(!empty($cart_id)){
		foreach($cart_id as $id) {
			$cart_Products = $this->ShopifyClient->call('GET','/admin/products/'.$id.'.json');
			
			$cart_arr[] = $cart_Products;
		}	
		
		}
		$arra = array();
		if(!empty($cart_arr))
		{
			foreach($cart_arr as $product){
			
				if(!empty($product['product']))
				{
					
				foreach($product as $key=>$val) {
					
					$arr['id'] = $val['id'];
					$arr['title'] = $val['title'];
					$arr['image'] = $val['image'];
					$arr['variants']['id'] = $val['variants'][0]['id'];
					$arr['variants']['price'] = $val['variants'][0]['price'];
					$arr['variants']['compare_at_price'] = $val['variants'][0]['compare_at_price'];
					$arrnew[] = $arr;
				}
				$arra['data'] = $arrnew;
				$status_code = 200;
				$message = 'success';
				}else
				{	
					$status_code = 404;
					$message = 'Product not found';
					$arra['data'] = array();
					$arra['data_count'] = 0;
				}
			}			
		}
		$cart_Products = $this->getJsonDataFormat($arra,$status_code,$message);
		return $cart_Products;
    }
	
	/**
	* Get All checkout details
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCheckout()
    {
		$checkout = $this->ShopifyClient->call('GET','/admin/checkouts.json');
		return $checkout;
    }
	
	/**
	* Get ordered products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getOrders()
    {
		$order = $this->ShopifyClient->call('GET','/admin/orders.json');
		$arra = array();
		if(!empty($order['orders']))
		{
			foreach($order as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['email'] = $val['email'];
					$arr['created_at'] = $val['created_at'];
					$arr['number'] = $val['number'];
					$arr['note'] = $val['note'];
					$arr['token'] = $val['token'];
					$arr['gateway'] = $val['gateway'];			
					$arr['total_price'] = $val['total_price'];			
					$arr['subtotal_price'] = $val['subtotal_price'];			
					$arr['total_weight'] = $val['total_weight'];			
					$arr['total_tax'] = $val['total_tax'];			
					$arr['taxes_included'] = $val['taxes_included'];			
					$arr['currency'] = $val['currency'];			
					$arr['financial_status'] = $val['financial_status'];			
					$arr['confirmed'] = $val['confirmed'];			
					$arr['total_discounts'] = $val['total_discounts'];
					$arr['total_line_items_price'] = $val['total_line_items_price'];		
					$arr['name'] = $val['name'];		
					$arr['cancelled_at'] = $val['cancelled_at'];		
					$arr['cancel_reason'] = $val['cancel_reason'];		
					$arr['total_price_usd'] = $val['total_price_usd'];		
					$arr['user_id'] = $val['user_id'];		
					$arr['location_id'] = $val['location_id'];					
					$arr['order_number'] = $val['order_number'];
					$arr['discount_codes'] = $val['discount_codes'];
					$arr['payment_gateway_names'] = $val['payment_gateway_names'];
					$arr['processing_method'] = $val['processing_method'];
					$arr['fulfillment_status'] = $val['fulfillment_status'];
					$arr['tax_lines'] = $val['tax_lines'];					
					/*$arr['fulfillments']['order_id'] = $val['fulfillments'][0]['order_id'];
					$arr['fulfillments']['status'] = $val['fulfillments'][0]['status'];
					$arr['line_items']['id'] = $val['line_items'][0]['id'];
					$arr['line_items']['title'] = $val['line_items'][0]['title'];
					$arr['line_items']['price'] = $val['line_items'][0]['price'];
					$arr['line_items']['sku'] = $val['line_items'][0]['sku'];
					$arr['line_items']['quantity'] = $val['line_items'][0]['quantity'];
					$arr['line_items']['product_id'] = $val['line_items'][0]['product_id'];
					$arr['line_items']['gift_card'] = $val['line_items'][0]['gift_card'];*/
					$arr['refunds'] = $val['refunds'];
					$arra['data'][] = $arr;					
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;  
		$message = 'Orders not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}	
		$order = $this->getJsonDataFormat($arra,$status_code,$message);
		return $order;
    }
	
	/**
	* Get ordered products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getSingleOrders()
    {
		$order = $this->ShopifyClient->call('GET','/admin/orders/'.$this->config['order_id'].'.json');
		//print_r($order); exit;
		$arra = array();
		if(!empty($order['order']))
		{
			foreach($order as $key=>$val) {
					$arr['id'] = $val['id'];
					$arr['email'] = $val['email'];
					$arr['created_at'] = $val['created_at'];
					$arr['number'] = $val['number'];
					$arr['note'] = $val['note'];
					$arr['token'] = $val['token'];
					$arr['gateway'] = $val['gateway'];			
					$arr['total_price'] = $val['total_price'];			
					$arr['subtotal_price'] = $val['subtotal_price'];			
					$arr['total_weight'] = $val['total_weight'];			
					$arr['total_tax'] = $val['total_tax'];			
					$arr['taxes_included'] = $val['taxes_included'];			
					$arr['currency'] = $val['currency'];			
					$arr['financial_status'] = $val['financial_status'];			
					$arr['confirmed'] = $val['confirmed'];			
					$arr['total_discounts'] = $val['total_discounts'];
					$arr['total_line_items_price'] = $val['total_line_items_price'];		
					$arr['name'] = $val['name'];		
					$arr['cancelled_at'] = $val['cancelled_at'];		
					$arr['cancel_reason'] = $val['cancel_reason'];		
					$arr['total_price_usd'] = $val['total_price_usd'];		
					$arr['user_id'] = $val['user_id'];		
					$arr['location_id'] = $val['location_id'];					
					$arr['order_number'] = $val['order_number'];
					$arr['discount_codes'] = $val['discount_codes'];
					$arr['payment_gateway_names'] = $val['payment_gateway_names'];
					$arr['processing_method'] = $val['processing_method'];
					$arr['fulfillment_status'] = $val['fulfillment_status'];
					$arr['tax_lines'] = $val['tax_lines'];
					$arr['refunds'] = $val['refunds'];
					$arr['billing_address'] = $val['billing_address'];
					$arr['shipping_address'] = $val['shipping_address'];
					$arr['line_items'] = $val['line_items'];				
					$arra['data'][] = $arr;
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;  
		$message = 'Orders not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}	
		$order = $this->getJsonDataFormat($arra,$status_code,$message);
		return $order;
    }
	
	/**
	* create order
	*
	*@return \Illuminate\Http\Response
	**/
	public function createOrders($json)
    {		
		$orderDetails = $json;		
		$order = $this->ShopifyClient->call('POST','/admin/orders.json',$orderDetails);
		
		if(!empty($order['errors'])) {
			$order['status'] = false;
			$order['message'] = $order['errors']['order'][0];
		}else if(!empty($order['order'])){
			$order['status'] = true;
			$order['message'] = 'success';
		}		
		return $order;
	}
	
	/**
	* Get shipping products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getShippedProducts()
    {		
		$shipped_products = $this->ShopifyClient->call('GET','/admin/orders/'.$this->config['order_id'].'/fulfillments.json');	
		$arra = array();
		if(!empty($shipped_products['fulfillments']))
		{
			foreach($shipped_products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr['id'] = $val['id'];
					$arr['order_id'] = $val['order_id'];
					$arr['created_at'] = $val['created_at'];
					$arr['status'] = $val['status'];
					$arr['service'] = $val['service'];
					$arr['tracking_company'] = $val['tracking_company'];
					$arr['shipment_status'] = $val['shipment_status'];			
					$arr['tracking_number'] = $val['tracking_number'];			
					$arr['tracking_url'] = $val['tracking_url'];			
					$arr['receipt'] = $val['receipt'];			
					$arr['line_items'] = $val['line_items'];					
					$arra['data'][] = $arr;					
				}				
			}
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;  
		$message = 'Not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}	
		$shipped_products = $this->getJsonDataFormat($arra,$status_code,$message);
		return $shipped_products;
    }
	
	/**
	* to get proper json data format
	*
	*@return \Illuminate\Http\Response
	**/
	
	public function getJsonDataFormat($data,$status_code,$message)
	{
		
		$arr['status_code'] = $status_code;
		$arr['message'] = $message;
		
		if($message == 'success') {
			$arr['status'] = true;
		}else {
			$arr['status'] = false;
		}	
		$arr['data_count'] = count($data['data']);
		$arr['data'] = $data['data'];
		$products = json_encode($arr);
		return $products;
	}
	 
	/**
	* to pay by card (payment via authorize.net)
	*
	*@return \Illuminate\Http\Response
	**/
	public function cardPay($json) 
	{

		$LOGINKEY = '8Q87hKq5';
		$TRANSKEY = '9C88H4MpP4AtgU8F';
		if(!empty($json->nameoncard)){
			$name = explode(' ',$json->nameoncard);
		}	
		if(!empty($name)){
			$firstName = $name[0];
			$lastName = $name[1];
		}else {
			$firstName = $json->first_name;
			$lastName = $json->last_name;
		}
		$creditCardType = $json->card_type;
		$creditCardNumber = $json->cardnumber;
		$expDateMonth = $json->expired_month;    
		// Month must be padded with leading zero
		$padDateMonth = str_pad($expDateMonth, 2, '0', STR_PAD_LEFT);      
		$expDateYear = $json->expired_year;
		$cvv2Number = $json->cvv;
		$address1 = $json->address;
		$city = $json->city;
		$state = $json->state;
		$zip = $json->postalcode;
		//give the actual amount below
		$amount = $json->total;
		$currencyCode = "USD";
		$paymentType = "Sale";
		$date = $expDateMonth.$expDateYear;    
		
		$post_values = array(
			"x_login"           => "$LOGINKEY",
			"x_tran_key"        => "$TRANSKEY",
			"x_version"         => "3.1",
			"x_delim_data"      => "TRUE",
			"x_delim_char"      => "|",
			"x_relay_response"  => "FALSE",
			//"x_market_type"       => "2",
			"x_device_type"     => "1",
			"x_type"            => "AUTH_CAPTURE",
			"x_method"          => "CC",
			"x_card_num"        => $creditCardNumber,
			//"x_exp_date"      => "0115",
			"x_exp_date"        => $date,
			"x_amount"          => $amount,
			//"x_description"       => "Sample Transaction",
			"x_first_name"      => $firstName,
			"x_last_name"       => $lastName,
			"x_address"         => $address1,
			"x_state"           => $state,
			"x_response_format" => "1",
			"x_zip"             => $zip
    );

		
		$fields = http_build_query($post_values);
		$post_url = "https://test.authorize.net/gateway/transact.dll";
		$request = curl_init($post_url);
		curl_setopt($request, CURLOPT_HEADER, 0); 
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($request, CURLOPT_POSTFIELDS, $fields); 
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); 
		$post_response = curl_exec($request); 
		curl_close ($request);				
		$response_array = explode($post_values["x_delim_char"],$post_response);
		//print_r($response_array); 
		if($response_array[0]== 2||$response_array[0]== 3)
		{
			//success
			$payres =  'Payment Failure! ';
			$payres .= $response_array[3];
			return $payres;
		}
		else
		{
			$ptid = $response_array[6];
			$ptidmd5 = $response_array[7];
			$payres = "success";
			return $payres;
		}
	}
	
	/**
	* customer registration
	*
	*@return \Illuminate\Http\Response
	**/
	public function register($json)
	{
		$user = $this->ShopifyClient->call('POST','/admin/customers.json',$json);	
		
		$arra = array();
		if(!empty($user['customer']))
		{
			
			foreach($user as $k=>$val) {
				$arr['id'] = $val['id'];
				$arr['email'] = $val['email'];			
				$arra['data'][] = $arr;					
			}	
			$status_code = 200;
			$message = 'success';	
	}else
	{
		$status_code = 404;  
		$message = 'Not found';
		$arra['data'] = array();
		$arra['data_count'] = 0;
	}
		$user = $this->getJsonDataFormat($arra,$status_code,$message);
		return $user;
	}	
	
}	


?>