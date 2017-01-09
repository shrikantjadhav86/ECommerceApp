<?php

namespace App\Classes;

abstract class CommonWrapper {

	/**
	 * get object instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		
	}
	
	
	/**
	* create product array from shoipfy array
	*
	*@return \Illuminate\Http\Response
	**/
	public function createProductArray($val,$shopifyKeyArr,$keyArr)
	{		
		for($i=0;$i < count($keyArr); $i++){
			if(is_array($keyArr[$i])) {
				$varArray = $keyArr[$i]['variants'];				
				for($j=0;$j < count($varArray); $j++){
					$arr['variants'][$varArray[$j]] = $val['variants'][0][$varArray[$j]];			
				}	
			}else {
				$arr[$keyArr[$i]] = $val[$shopifyKeyArr[$i]];				
			}	
			
		}	
		return $arr;
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
			$arr['data_count'] = count($data['data']);
		}else {
			$arr['status'] = false;
			$arr['data_count'] = 0;
		}	
		
		$arr['data'] = $data['data'];
		$products = json_encode($arr);
		return $products;
	}
	 
	/**
	* to pay by card (payment via authorize.net)
	*
	*@return \Illuminate\Http\Response
	**/
	public function cardPayment($json) 
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
			$payRes =  'Payment Failure! ';
			$payRes .= $response_array[3];
			return $payRes;
		}
		else
		{
			$ptid = $response_array[6];
			$ptidmd5 = $response_array[7];
			$payRes = "success";
			return $payRes;
		}
	}

}


?>