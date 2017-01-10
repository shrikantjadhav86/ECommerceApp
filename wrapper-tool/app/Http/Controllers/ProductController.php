<?php

namespace App\Http\Controllers;
use App\Classes\FactoryClass;
use App\Classes\Paypal;
use Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use DB;

/*Header to remove crossed browser error in console or http call from angularjs */

header('Access-Control-Allow-Headers: Content-Type, x-xsrf-token');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");
header('Content-Type: application/json');
 
class ProductController extends Controller
{
    public $eCommerce;
	public $interfaceObj; 
	public  $paypal;
	/**
		It use to get object instance and store info from database
		@return set store info for shopifyclient and return instance of wrapper
	**/	
	
	public function __construct()
	{
		 $store = DB::table('store')
			->where('token',Request::get('token'))
			->first();
		$factory = new FactoryClass();
		$this->interfaceObj = $factory->getInstance($store->type);
		$this->interfaceObj->setStore($store);
	}
	
	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {	
		
    }
	
	/**
     *customer registration
     *
     * @param  $json(user info array)
     * @return \Illuminate\Http\Response
     */
	public function registration()
	{		
		$arr = array();
		$request = Request::instance();
	 	$content = $request->getContent();
		$json = json_decode($content, true);
		$user = $this->interfaceObj->register($json);		
		return $user;
	}
	
	
	/**
     *to get user orders
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function userOrder($id)
	{
		$userOrder = $this->interfaceObj->userOrder($id);
		return $userOrder;
	}	

	/**
     *add to cart products (currently this function ia not in use)
     *
     * @param  $cart_product_id 
     * @return \Illuminate\Http\Response
     */
	public function addToCartProducts($cart_product_id)
	{		
		return view('add-cart')->with('id', $cart_product_id);
	}
	
	
	
	/**
     *to get cart products (currently this function ia not in use)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function cartProducts(Request $request)
	{
		$cart_id = array();
		$cart_id[] = Request::get('cart_ids');
		
		//$cart_id = Session::get('cart_product_id');
		//$cart_id[] = 8686045447;
		//$cart_id = array(7390217031,8686045447,8826701255,8550248327);
		
		//print_r(Session::all()); exit;
		
		$config['cart_id'] = $cart_id;
		$this->interfaceObj->setConfig($config);
		$cart_products = $this->interfaceObj->getCartProduct();
		return $cart_products;
	}
	
	
	/**
     *to remove cart products (currently this function ia not in use)
     *
     * @param  $cart_product_id
     * @return \Illuminate\Http\Response
     */
	public function cartRemoveProducts($line)
	{
		$key = $line-1;
		$cart_id = array();
		$cart_id[] = Session::get('cart_product_id');
		 unset($cart_id[$key]);
		
		//Session::forget('cart_product_id');
		Session::set('cart_product_id',$cart_id);		
		return view('remove-cart')->with('line', $line);
	}
	
	
    /**
     * get all products
     *
     * @param  \Illuminate\Http\Request  $request ($milit, $page)
     * @return \Illuminate\Http\Response
     */
    public function getAllProducts()
    {
		$limit = Request::input('limit');
	 	$page = Request::input('page');
		if($limit != '' && $page != '') {
			$config['limit'] = $limit;
			$config['page'] = $page;
		}else {
			$config['limit'] = 50;
			$config['page'] = 1;
		}
		$this->interfaceObj->setConfig($config);
		$products = $this->interfaceObj->getProducts();
		return $products;
    }
	
	/**
     * get products by comma seprated ids
     *@params $Request(array of ids)
     * 
     */
	public function getProductsByIds()
	{
		$ids = Request::get('ids');
		
		$products = $this->interfaceObj->getProductsByIds($ids);
		return $products;
		
		
	}	

    /**
     * Display product details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showDetails($id)
    {
		$config['id'] = $id;			
		$this->interfaceObj->setConfig($config);
		$productDetails = $this->interfaceObj->getProductDetails();
		return $productDetails;
    }
	
	/**
     * get all customers
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getAllCustomers()
    {
		if(Request::has('limit') && Request::has('page')) {
			$config['limit'] = Input::get('limit');
			$config['page'] = Input::get('page');	
		}else {
			$config['limit'] = 50;
			$config['page'] = 1;
		}
		
		$this->interfaceObj->setConfig($config);
		$customers = $this->interfaceObj->getCustomers();
		return $customers;		
    }
	
	/**
     * get all collections
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	
    public function getAllCollections()
    {
		if(Request::has('limit') && Request::has('page')) {
			$config['limit'] = Input::get('limit');
			$config['page'] = Input::get('page');	
		}else {
			$config['limit'] = 50;
			$config['page'] = 1;
		}
		
		$this->interfaceObj->setConfig($config);
		$collections = $this->interfaceObj->getCollections();
		return $collections;		
    }
	
	/**
     * get all product from $collection_id
     *
     * @param  \Illuminate\Http\Request  $request, $collection_id
     * @return \Illuminate\Http\Response
     */
    public function getCollectionProducts($collection_id)
    {
		$config['collection_id'] = $collection_id;
		$this->interfaceObj->setConfig($config);
		$collection_products = $this->interfaceObj->getCollectionProducts();
		return $collection_products;
		
    }
	
	/**
     * get all cart products
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function getAllCartProducts()
    {
		$cart_products = $this->interfaceObj->getCartProducts();
		return $cart_products;
		
    }

	/**
     * get checkout
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCheckout()
    {
		$checkout = $this->interfaceObj->getCheckout();
		return $checkout;
		
    }
	
	/**
     * get order products
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function getOrder()
	{
		$orders = $this->interfaceObj->getOrders();
		return $orders;
	}
	
	/**
     * create order 
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function createOrder(Request $request)
	{
		$request = Request::instance();
	 	$content = $request->getContent();
		$json = json_decode($content, true);
		
		$order = $this->interfaceObj->createOrders($json);		
		return $order;
		
	}
	
	
	/**
     * get single order 
     *
     * @param  $order_id
     * @return \Illuminate\Http\Response
     */
	public function getSingleOrders($order_id)
    {
		$config['order_id'] = $order_id;
		$this->interfaceObj->setConfig($config);
		$order = $this->interfaceObj->getSingleOrders();
		return $order;
	}	
	
	
	
	/**
     * get shipped products
     *
     * @param  \I$order_id
     * @return \Illuminate\Http\Response
     */
	public function getAllShippedProducts($order_id)
	{
		$config['order_id'] = $order_id;
		$this->interfaceObj->setConfig($config);
		$orders = $this->interfaceObj->getShippedProducts();
		return $orders;
	}

	/**
     * paypal payment gateway (currently all paypal functions are not in use)
     *
     * 
     * 
     */
	public function paypalPayment(Request $request)
	{
		 if(Request::input('email'))
        {
            $this->paypal->go_live();
            $this->paypal->set_credential(PAYPAL_USERNAME, PAYPAL_PASSWORD, PAYPAL_SIGNATURE);
            $amount =  Request::input('total');
            $this->paypalData['HDRIMG'] = $this->config->item("imagePath") . 'paypal_logo.gif';
            $this->paypalData['AMT'] = $amount;
            $this->paypalData['CURRENCYCODE'] = 'USD';
            $this->paypalData['PAYMENTACTION'] = 'Sale';
            $this->paypalData['RETURNURL'] = base_url() . 'recurring/confirm_paypal';
            $this->paypalData['CANCELURL'] = base_url() . 'recurring/cancel_paypal';
            $this->paypalData['BILLINGAGREEMENTDESCRIPTION'] = Request::input('product_title') . " ($" . $amount . ")";
            $this->paypalData['PAYMENTREQUEST_0_CURRENCYCODE'] = 'USD';
            $this->paypalData['PAYMENTREQUEST_0_DESC'] = Request::input('product_title') ." ($" . $amount . ")";
            $this->paypalData['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';
            $this->paypalData['NOSHIPPING'] = "0";
            $this->paypalData['PAYMENTREQUEST_0_ITEMAMT'] = $amount;
            $this->paypalData['PAYMENTREQUEST_0_AMT'] = $amount;

            if (Request::input('recurring_type')) {
                $this->paypalData['BILLINGTYPE'] = "RecurringPayments";
            } else {
                $this->paypalData['BILLINGTYPE'] = "SetExpressCheckout";
            }
			 
			Request::session()->put('description', $this->paypalData['BILLINGAGREEMENTDESCRIPTION']);
			Request::session()->put('paypalData', $this->paypalData);            

            $this->paypal->setExpressCheckout_payment($this->paypalData);
        } else {
            echo "else";
        }
	}
	/**
     * paypal payment gateway (currently all paypal functions are not in use)
     *Make tpaypal transaction, if recurring order then store paypal customer profile id .
     * 
     * 
     */
    public function confirm_paypal()
    {
        if (!Session::has('token')) {
			Request::session()->put('token', urlencode(htmlspecialchars($_GET['token'])));
			Request::session()->put('PAYERID', urlencode(htmlspecialchars($_GET['PayerID'])));            
        }
        $this->paypal->go_live();
        $this->paypal->set_credential(PAYPAL_USERNAME, PAYPAL_PASSWORD, PAYPAL_SIGNATURE);
        $data = array(
            'token' => Request::session()->get('token'),
            'PayerID' => Request::session()->get('PAYERID')
        );

        $expressData = array_merge(Request::session()->get('paypalData'), $data);

        $get_expresess_response = $this->paypal->getExpressCheckout_payment($expressData);
        $payment_response = $this->paypal->doExpressCheckout_payment($expressData);
        //echo '<pre>';print_r($payment_response);
        if ($payment_response['ACK'] == "Success")
        {
            if(Request::input('recurring_type'))
            {
                $amount = Request::input('subtotal');
                $start_date_count  =  Request::input('SubscriptionType') * 30;
                $data['PROFILESTARTDATE'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'). " +".$start_date_count." days"));
                $data['AMT'] = $amount;    // Recurring Amount
                $data['DESC'] = Request::input('description');
                $data['MAXFAILEDPAYMENTS'] = '12';
                $data['AUTOBILLAMT'] = 'AddToNextBilling';
                $data['BILLINGPERIOD'] = 'Month';
                $data['BILLINGFREQUENCY'] = intval(Request::input('product_subtype'));
                $data['TOTALBILLINGCYCLES'] = '0';
                $data['FAILEDINITAMTACTION'] = 'ContinueOnFailure';

                $customer_pr_response = $this->paypal->create_rp_profile($data);

                Session::forget('token');
			 	Session::forget('PAYERID');
            
                $subcriptions = array();
                
                if ($customer_pr_response['ACK'] == "Success") 
                {
                    $paypal_profile_id = $customer_pr_response['PROFILEID'];

                    $subcriptions = array(
                        //'OrderId'               => Request::input('order_id'),
                        'SubscriptionId'        => $paypal_profile_id,
                        'SubscriptionType'      => 'PayPal',
                        'SubscriptionStatus'    => 'Active', 	
                        'CreatedDate'           => date('Y-m-d h:i:s'),
                        'UpdatedDate'           => date('Y-m-d h:i:s')
                    );
                }
                else 
                {
                    $subcriptions = array(
                        //'OrderId'               => Request::input('order_id'),
                        'SubscriptionType'      => 'PayPal',
                        'SubscriptionStatus'    => 'Failed', 	
                        'CreatedDate'           => date('Y-m-d h:i:s'),
                        'UpdatedDate'           => date('Y-m-d h:i:s')
                    );                   
                }              
            }
             redirect()->intended(base_url());
        }
        else
        {
			 Session::forget('token');
			 Session::forget('PAYERID');
			 Request::session()->put('paypal_error', $payment_response['L_LONGMESSAGE0']);
             redirect()->intended(base_url());
        }
    }
	
   	/**
     * paypal payment gateway (currently all paypal functions are not in use)
     *
     * redirect user to payment page with cancle message.
     * 
     */
    public function cancel_paypal()
    {
		Request::session()->put('paypal_error', 'Payment canceled by customer.');
        redirect()->intended(base_url());
    }
	
	/**
     * authorized  payment gateway (currently not in use)
     *
     * 
     */
	public function authorizedPayment(Request $request)
	{
		$request = Request::instance();
		$content = $request->getContent();
		$json = json_decode($content);
	
		$data = array();
			
       /* if (Request::input('order') == "") 
        {
            redirect(PRODUCT_PAGE_URL);
        }*/

        // If is post request then make payment and proced.
        if ($json->cardnumber) 
        {				
            if (!$json->expired_year) 
            {
                $year = date('Y');
            } 
            else 
            {				
                $year = $json->expired_year;				
            }           
			Request::session()->put('expired_year', $year);

			$google_api_input = array(
				'country_code'  => $json->billing_country,
				'state'         => $json->billing_state,
				'city'          => $json->billing_city,
				'zip'           => $json->billing_postcode
			);

			$user_data = array(
				'billing_firstname'     => $json->billing_firstname,
				'billing_lastname'      => $json->billing_lastname,
				'billing_address'       => $json->billing_address,
				'billing_apt'           => $json->billing_apt,
				'billing_city'          => $json->billing_city,
				'billing_country'       => $json->billing_country,
				'billing_state'         => $json->billing_state,
				'billing_postcode'      => $json->billing_postcode,
				'billing_country_full_name'      => $json->country_full_name_diff,
				'subscribe_newsletter'  => '1',
			);                    


                // set sesions data for shipping and billing address.
				Request::session()->put('user_data', $user_data);
				Request::session()->put('payment_type', $json->payment_type);                          
                // If paypal payment methode then return to paypal.
                if($json->payment_type === 'paypal')
                {
                    echo "101";
                    exit;
                }
                // Load required models and libraries.
               // $this->load->model('payment_model');
               // $this->load->model('customer_profile');
               // $this->load->library('authorizenet_cim_class');
               // $this->load->helper('subscription_helper');

                if( Request::session()->get('email') && 
                    Request::session()->get('payment_type') == 'auth') 
                {
                    $sub_id = 0;
                    $cust_exists = array();

                    $formated_cn = string2stars($json->cardnumber, 4, -4);                                      

                    /*$cust_exists = $this->customer_profile->get_cust_info(
                        Request::input('email'), 
                        $formated_cn, 
                        Request::input('app_customer_id')
                    );*/
                   
                    // create explod date and year.
                    $dateElements = explode('-', $json->expired_month);

                    $month = $json->expired_month;
                    $year = $json->expired_year;
                    $name_on_card = $json->nameoncard;
                    $name_pieces = explode(" ", $name_on_card);
                    $first_name = "";
                    $last_name = "";
                    if(count($name_pieces))
                    {
                        $first_name = $name_pieces[0];
                        $last_name = $name_pieces[1];
                    }
                    else
                    {
                        $first_name = $json->firstname;
                        $last_name =  $json->lastname;
                    }
                    // If customer profile id is not present then create it.
                    if( !isset($cust_exists[0]['CustomerProfileId']) )
                    {
                        // Set auth array to create auth profile.
                        $authorizeNet = array(
                            'x_card_num'        => $json->cardnumber,
                            'x_expiration_date' => $year . '-' . $month,
                            'x_first_name'      => $first_name,
                            'x_last_name'       => $last_name,
                            'x_company'         => $json->Shipping_company,
                            'x_address'         => $json->shipping_address,
                            'x_city'            => $json->Shipping_city,
                            'x_state'           => $json->Shipping_state,
                            'x_zip'             => $json->Shipping_postalcode,
                            'x_country'         => $json->Shipping_country,
                            'x_phoneNumber'     => $json->phone_number,
                            'x_email'           => $json->email,                                   
                            'x_Card_Code'       => $json->cvv,
                        );

                      
                    }
                    
                    // convert amount to valid amount.
                    $amount = $json->total;
                    $amount = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $amount);
                    $amount = number_format($amount, 2, '.', '');
                    $amount = (float) $amount;
                    $inovice = mt_rand(5, 15);

                    // Set auth array for transaction request.
                    $authorizeNet = array(
                        'x_card_num'                    => $json->cardnumber,
                        'x_expiration_date'             => $year . '-' . $month,
                        'x_first_name'                  => $first_name,
                        'x_email'                       => $json->email,
                        'x_Card_Code'                   => $json->cvv,
                        'x_last_name'                   => $json->lastname,
                        'x_amount'                      => $amount,
                        'x_Invoice_Num'                 => 'PR_' . time(),
                    );

                    // Make transaction request and get transaction request response.
                    $tran_response = $this->payment_model->create_transaction_request(
                            $authorizeNet, 
                            $userDetailsArray = array()
                        );

                    if ($tran_response['error_code'] !== 'Ok')
                    {
                        echo "<pre> Transaction : " . print_r($tran_response['error_message'], true) . "</pre>";
                        $bounce_data = array('Status' => 'PaymentIssue');
                      //  $this->update_bounce_data($bounce_data);
                        exit;
                    }
                    else 
                    {
                        //$this->load->model('order');                        
                        /* $transId = $tran_response['transId'];
                        
                        $order = array(
                            'OrderAmount'           => Request::input('total'),
                            'OrderSubtotal'         => Request::input('subtotal'),
                            'Status'                => 'Ordered',
                            'CustomerProfileRef'    => $cust_exists[0]['Id'],
                            'TransactionId'         => $transId,
                            'UpdatedDate'           => date('Y-m-d H:i:s'),
                        );                                                
                        if(Session::has('order_id'))
                        {
                            //$this->order->update(Request::input('order_id'), $order);
                            echo '100';
                            exit;
                        }*/
                    }
                }
                else 
                {
                    echo "Session cleared";
                }            
        } 
        else 
        {
            //$this->load->model('customer_model');
           /* $is_customer_exists = $this->customer_model->get_customer_information(
                  Request::input('email')
            );
            if (isset($is_customer_exists[0]['Email'])) 
            {
                $this->session->set_userdata('app_customer_id', $is_customer_exists[0]['Id']);
            } 
            else 
            {*/
                $cust_data = array(
                    'FirstName'     => Request::input('firstname'),
                    'LastName'      => Request::input('lastname'),
                    'Email'         => Request::input('email'),
                    'phone_number'  => Request::input('number'),
                    'Status'        => '1',
                    'CreatedDate'   => date('Y-m-d')
                );
               // $cust_id = $this->customer_model->insert($cust_data);
                //$this->session->set_userdata('app_customer_id', $cust_id);
            //}

            //echo "<pre>". print_r($this->session->userdata(), true); die;
            /*if (!$this->session->has_userdata('order_id')) 
            {
                //$this->create_app_order();
            }*/
            
            
            //$this->load->model('country_model');
           //$this->load->model('state_model');

           // $data['countries'] = $this->country_model->get_countries();
            //$data['default_country'] = 'US';
            //$data['states'] = $this->state_model->get_state($data['default_country']);

           // $this->checkShipping();
            //$this->load->helper('constant_helper');    
           //$res = getAllConstants();
           if(!empty($res))
        {
            if($res[1]['field_name'] == "shipping_price_other")
            {
                 $data['international_SHIPPING_PRICE'] = $res[1]['value'];
            }
        }
            //$this->layout->setLayout("layout/cust_app");
            //$this->layout->view('app/payment_info', $data);
        }	
	}
	
	
	/**
     * to verify address by google api
     *@params $request and $json(array of all address details)
     * 
     */
	public function address_validate(Request $request)
    {		
		$request = Request::instance();
	 	$content = $request->getContent();
		$json = json_decode($content);
		
		//$input_araay = json_decode(Request::input('address'));
		$input_araay['country_code'] = $json->country_code;
		$input_araay['zip'] = $json->zip;
		$input_araay['city'] = $json->city;
		$input_araay['state'] = $json->state;
			
        if(isset($input_araay['country_code']) && isset($input_araay['zip']))
        {
            $address_results    = array();
            $address_components = array();
            $city_array         = array();
            
            $is_locality    = 0;
            $is_state       = 0;
            $is_country     = 0;
            $is_postal_code = 0;
            $arr_geocode_address = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?components=country:'.
                    $input_araay['country_code'].'|postal_code:' . $input_araay['zip'] . '&sensor=false');
            $decoded_array = json_decode($arr_geocode_address);
         // echo '<pre>';print_r($decoded_array);die;
            
            if(isset($decoded_array->results[0]))
            {
                
                $address_components = $decoded_array->results[0]->address_components;
                foreach($address_components as $obj=>$item)
                {
                    
                    if($item->types[0] == "locality")
                    {
                        if(strcasecmp($input_araay['city'],$item->long_name) == 0)
                        {
                            $is_locality = 1;
                        }
                    }
                    else if(($item->types[0] == "administrative_area_level_2") && !$is_locality)
                    {
                        if(strcasecmp($input_araay['city'],$item->short_name) == 0)
                        {
                            $is_locality = 1;
                        }
                    }
                    else if(($item->types[0] == "neighborhood") && !$is_locality)
                    {
                        if(strcasecmp($input_araay['city'],$item->short_name) == 0)
                        {
                            $is_locality = 1;
                        }
                    }
                    if($item->types[0] == "administrative_area_level_1")
                    {
                        if(strcasecmp($input_araay['state'],$item->short_name) == 0)
                        {
                            $is_state = 1;
                        }
                        else if(strcasecmp($input_araay['state'],$item->long_name) == 0){
                            $is_state = 1;
                        }
                    }
                    if($item->types[0] == "country")
                    {
                        if(strcasecmp($input_araay['country_code'],$item->short_name) == 0)
                        {
                            $is_country = 1;
                        }
                    }
                    if($input_araay['country_code'] == "MC"){
                            $is_state = 1;
                            $is_locality = 1;
                        }
                }
              
                if ($is_locality && $is_state && $is_country)
                {
                    return response()->json(['message' => 'success', 'status' => true]); exit;
                }
                else 
                {
                     return response()->json(['message' => 'City and Zip code doesnt match', 'status' => false]); exit;
                }
            }
            else
            {
                 return response()->json(['message' => 'This address is not a valid Adress', 'status' => false]);
            }
        }
        else
        {
			 return response()->json(['message' => 'Invalid input provided for the address validation', 'status' => false]);
            
        }
    }
	
	/**
     * authorize.net payment gateway 
     *@params $request and $json(array of card details)
     * 
     */
	public function cardPay()
	{
		$request = Request::instance();
	 	$content = $request->getContent();
		$json = json_decode($content);
		
		$pay = $this->interfaceObj->cardPay($json);
		if($pay == "success"){
			return response()->json(["message" => $pay, 'status' => true]);
		}else {
			return response()->json(["message" => $pay, 'status' => false]);	
		}	
		
	}	
	
	
	
	
}
