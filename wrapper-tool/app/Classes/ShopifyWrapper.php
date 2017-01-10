<?php

namespace App\Classes;

class ShopifyWrapper extends CommonWrapper implements EcommerceInterface  {

	private $config = array();
	
	public $ShopifyClient;
	/**
		It use to get object instance
		@return shopify client class object
	**/
	public function __construct()
	{
	 	$this->ShopifyClient = new ShopifyClient();
	}
	public function setStore($store)
	{
		$arr = array();	
		$this->domain = $store->domain;
		$this->ShopifyClient->__setDomain($arr,$store->domain);
		$this->ShopifyClient->__setKey($arr,$store->key);
		$this->ShopifyClient->__setPassword($arr,$store->password);		
	}
	
	/**
		to set config array globally
		@return config array.
	**/	
	public function setConfig($config)
	{
		$this->config = $config;
	}
	/**
     * get all products
     *
     * @return \Illuminate\Http\Response of products from shopify
     */
    public function getProducts()
    {
		$products = $this->ShopifyClient->call('GET','/admin/products.json',$this->config);
		$arra = array();
		if(!empty($products['products']))
		{
			$keyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
							array('variants'=>array('id','price','sku','compare_at_price','weight','weight_unit')));
			$shopifyKeyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
								   array('variants'=>array(0 =>array('id','price','sku','compare_at_price','weight','weight_unit'))));
			foreach($products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr = $this->createProductArray($val,$shopifyKeyArr,$keyArr);
					$arr['product_url'] = 'https://'.$this->domain.'/products/'.$val['handle'];
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
	public function getProductsByIds($ids) {
		$products = $this->ShopifyClient->call('GET','/admin/products.json?ids='.$ids);
		$arra = array();
		if(!empty($products['products']))
		{
			$keyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
							array('variants'=>array('id','price','sku','compare_at_price','weight','weight_unit')));
			$shopifyKeyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
								   array('variants'=>array(0 =>array('id','price','sku','compare_at_price','weight','weight_unit'))));
			foreach($products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arr = $this->createProductArray($val,$shopifyKeyArr,$keyArr);
					$arr['product_url'] = 'https://'.$this->domain.'/products/'.$val['handle'];
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
     * @return \Illuminate\Http\Response of single product details
     */
    public function getProductDetails()
    {
		$product = $this->ShopifyClient->call('GET','/admin/products/'.$this->config['id'].'.json');
		
		$arra = array();
		if(!empty($product['product']))
		{
			$keyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
							array('variants'=>array('id','price','sku','compare_at_price','weight','weight_unit')));
			$shopifyKeyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
								   array('variants'=>array(0 =>array('id','price','sku','compare_at_price','weight','weight_unit'))));
			
			foreach($product as $key=>$val) {
				$arr = $this->createProductArray($val,$shopifyKeyArr,$keyArr);
				$arr['product_url'] = 'https://'.$this->domain.'/products/'.$val['handle'];
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
	*Display users or customers of shopify
	*@param  $limit , and $page which set in config array
	*@return \Illuminate\Http\Response
	**/
	public function getCustomers()
    {
		$customers = $this->ShopifyClient->call('GET','/admin/customers.json',$this->config);		
		$arra = array();
		if(!empty($customers['customers']))
		{
			$keyArr = array('id','email','accepts_marketing','first_name','last_name','orders_count','state','total_spent','last_order_id','note',
							'verified_email','multipass_identifier','tax_exempt','tags','last_order_name','addresses');
			$shopifyKeyArr = array('id','email','accepts_marketing','first_name','last_name','orders_count','state','total_spent','last_order_id','note',
							'verified_email','multipass_identifier','tax_exempt','tags','last_order_name','addresses');
			
			foreach($customers as $key=>$var) {
				foreach($var as $k=>$val) {
					if(!empty($val['default_address'])){
						array_push($keyArr, 'default_address');
						array_push($shopifyKeyArr, 'default_address');								
					}					
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);	
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
	**@param  $limit , and $page which set in config array
	*@return \Illuminate\Http\Response
	**/
	public function getCollections()
    {
		$collections = $this->ShopifyClient->call('GET','/admin/smart_collections.json',$this->config);		
		$arra = array();
		if(!empty($collections['smart_collections']))
		{
			$keyArr = array('id','handle','title','body_html','published_at','sort_order','template_suffix','published_scope','disjunctive','rules');
			$shopifyKeyArr = array('id','handle','title','body_html','published_at','sort_order','template_suffix','published_scope','disjunctive','rules');
			
			foreach($collections as $key=>$var) {
				foreach($var as $k=>$val) {								
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);				
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
	**@param  $collection_id
	*@return \Illuminate\Http\Response
	**/
	public function getCollectionProducts()
    {
		$collection_Products = $this->ShopifyClient->call('GET','/admin/products.json?collection_id='.$this->config['collection_id']);		
		$arra = array();
		if(!empty($collection_Products['products']))
		{
			$keyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images',
							'image','options',array('variants'=>array('id','price','sku','compare_at_price','weight','weight_unit')));
			$shopifyKeyArr = array('id','title','body_html','created_at','vendor','product_type','tags','images','image','options',
								   array('variants'=>array(0 =>array('id','price','sku','compare_at_price','weight','weight_unit'))));
			
			foreach($collection_Products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);					
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
	* Get All cart products (currently not in use)
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCartProduct()
    {		
		$cart_arr = array();		
		$status_code = '';
		$message = '';
		$arrNew = array();
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
					$keyArr = array('id','title','image',array('variants'=>array('id','price','compare_at_price')));
					$shopifyKeyArr = array('id','title','image',array('variants'=>array(0 =>array('id','price','compare_at_price'))));				
				foreach($product as $key=>$val) {
					$arrNew[] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);	
					
				}
				$arra['data'] = $arrNew;
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
	* Get All checkout details (currently not in use)
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
			$keyArr = array('id','email','created_at','number','note','token','gateway','total_price',
							'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed','total_discounts',
							'total_line_items_price','name','cancelled_at','cancel_reason','total_price_usd','user_id','location_id',
						   'order_number','discount_codes','payment_gateway_names','processing_method','fulfillment_status','tax_lines','refunds');
			$shopifyKeyArr = array('id','email','created_at','number','note','token','gateway','total_price',
								'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed',
							  	'total_discounts','total_line_items_price','name','cancelled_at','cancel_reason','total_price_usd','user_id','location_id',
						   		'order_number','discount_codes','payment_gateway_names','processing_method','fulfillment_status','tax_lines','refunds');
			
			foreach($order as $key=>$var) {
				foreach($var as $k=>$val) {
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);
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
	* Get signle ordered products details
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
			$keyArr = array('id','email','created_at','number','note','token','gateway','total_price',
							'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed','total_discounts',
							'total_line_items_price','name','cancelled_at','cancel_reason','total_price_usd','user_id','location_id',
						   'order_number','discount_codes','payment_gateway_names','processing_method','fulfillment_status','tax_lines','refunds',
							'line_items');
			$shopifyKeyArr = array('id','email','created_at','number','note','token','gateway','total_price',
								'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed',
							  	'total_discounts','total_line_items_price','name','cancelled_at','cancel_reason','total_price_usd','user_id','location_id',
						   		'order_number','discount_codes','payment_gateway_names','processing_method','fulfillment_status','tax_lines','refunds',
								  'line_items');
			
			foreach($order as $key=>$val) {				
					if(!empty($val['billing_address'])){
						array_push($keyArr, 'billing_address');
						array_push($shopifyKeyArr, 'billing_address');						
					}
					if(!empty($val['shipping_address'])){
						array_push($keyArr, 'shipping_address');
						array_push($shopifyKeyArr, 'shipping_address');
					}
					
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);
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
	*@params array $orderDetails
	*@return \Illuminate\Http\Response
	**/
	public function createOrders($json)
    {
		$orderDetails = $json;		
		$order = $this->ShopifyClient->call('POST','/admin/orders.json',$orderDetails);
		
		if(!empty($order['errors'])) {
			$status = false;
			$message = $order['errors']['order'][0];
			$data_count = 0;
			$data['data'] = array();
			$status_code = 404; 
		}else if(!empty($order['order'])){
			$status = true;
			$message = 'success';
			$data['data'] = $order;
			$status_code = 200;
		}
		$order = $this->getJsonDataFormat($data,$status_code,$message);
		return $order;
	}
	
	/**
	* Get shipping products
	*@params $order_id
	*@return \Illuminate\Http\Response
	**/
	public function getShippedProducts()
    {		
		$shipped_products = $this->ShopifyClient->call('GET','/admin/orders/'.$this->config['order_id'].'/fulfillments.json');	
		$arra = array();
		if(!empty($shipped_products['fulfillments']))
		{
			$keyArr = array('id','order_id','created_at','status','service','tracking_company','shipment_status','tracking_number',
							'tracking_url','receipt','line_items');
			$shopifyKeyArr = array('id','order_id','created_at','status','service','tracking_company','shipment_status','tracking_number',
							'tracking_url','receipt','line_items');
			
			foreach($shipped_products as $key=>$var) {
				foreach($var as $k=>$val) {
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);					
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
	* customer registration
	*@params $inputArr (which have all details of user/customer)
	*@return \Illuminate\Http\Response
	**/
	public function register($json)
	{		
		$inputArr['customer'] = $json['user'];		
		$user = $this->ShopifyClient->call('POST','/admin/customers.json',$inputArr);
		
		$arra = array();			
		if(!empty($user['errors'])){
			$message =  'email '.$user['errors']['email'][0];
			if($message == 'email has already been taken') {
				$email = $inputArr['customer']['email'];
				$user1 = $this->ShopifyClient->call('GET','/admin/customers/search.json?query=email:'.$email);
				$userNew['customer'] = $user1['customers'][0];
				$status_code = 208;
			}	
		}else {
			$userNew['customer'] = $user['customer'];
		}	
		
		if(!empty($userNew['customer']))
		{
			foreach($userNew as $k=>$val) {
				$arr['id'] = $val['id'];
				$arr['email'] = $val['email'];
				$arr['first_name'] = $val['first_name'];	
				$arr['last_name'] = $val['last_name'];
				if(!empty($val['default_address'])){
					$arr['address'] = $val['default_address'];	
				}	
				$arra['user'] = $arr;
			}
			if(empty($message)){
				$status_code = 200;
				$message = 'User created successfully.';
			}	
			$status = true;
		}else {
			$message = 'Internal server error while creating user.';
			$status_code = 404;
			$arra['user'] = array();	
			$status = false;
		}			
		
		$arrJson['status_code'] = $status_code;
		$arrJson['message'] = $message;
		$arrJson['status'] = $status;
		$arrJson['user'] = $arra['user'];
		$user = json_encode($arrJson);
		return $user;
	}	
	
	
	/**
	* To get perticular user order by id
	*@params $id(customer_id)
	*@return \Illuminate\Http\Response
	**/
	public function userOrder($id)
	{		
		$order = $this->ShopifyClient->call('GET','/admin/orders.json?customer_id='.$id);

		if(!empty($order['orders']))
		{
			$keyArr = array('id','email','created_at','number','note','token','total_price',
							'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed','total_discounts',
							'total_line_items_price','name','cancelled_at','cancel_reason','order_number','fulfillment_status','line_items');
			$shopifyKeyArr =  array('id','email','created_at','number','note','token','total_price',
							'subtotal_price','total_weight','total_tax','taxes_included','currency','financial_status','confirmed','total_discounts',
							'total_line_items_price','name','cancelled_at','cancel_reason','order_number','fulfillment_status','line_items');
			foreach($order as $key=>$var) {
				foreach($var as $k=>$val) {
					$arra['data'][] = $this->createProductArray($val,$shopifyKeyArr,$keyArr);				
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
	* to pay by card (payment via authorize.net)
	*@params $json (user card details with amount)
	*@return \Illuminate\Http\Response
	**/
	public function cardPay($json) 
	{		
		$pay = $this->cardPayment($json);		
		return $pay;
	}
	
	
	
	
	
}	


?>