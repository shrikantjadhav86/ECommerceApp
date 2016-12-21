<?php 
if(!defined('BASEPATH'))exit('No direct script access allowed');
// Codeigniter access check, remove it for direct use
// Development and live operation servers
define( 'PP_SERVER_API_DEV', 'https://api-3t.sandbox.paypal.com/nvp' );
define( 'PP_SERVER_API_LIVE', 'https://api-3t.paypal.com/nvp' );
define( 'PP_SERVER_IPN_DEV', 'ssl://sandbox.paypal.com' );
define( 'PP_SERVER_IPN_LIVE', 'ssl://www.paypal.com' );
define( 'PP_SERVER_POST_DEV', 'https://www.sandbox.paypal.com/' );
define( 'PP_SERVER_POST_LIVE', 'https://www.paypal.com/' );
// General stuff
define( 'PP_API_VERSION', '62.0' );
define( 'PP_CMD_NOTIFY_VALIDATE', 'cmd=_notify-validate' );
define( 'PP_RSP_VERIFIED', 'VERIFIED' );
define( 'PP_RSP_INVALID', 'INVALID' );
define( 'PP_MODE_DEVELOPMENT', 'dev' );
define( 'PP_MODE_LIVE', 'live' );
// General response codes
define( 'PP_ACK_SUCCESS', 'Success' );
define( 'PP_ACK_SUCCESS_WARNING', 'SuccessWithWarning' );
define( 'PP_ACK_FAILURE', 'Failure' );
define( 'PP_ACK_FAILURE_WARNING', 'FailureWithWarning' );
define( 'PP_ACK_WARNING', 'Warning' );
// API method names
define( 'PP_METHOD_ADDRESS_VERIFY', '' );
define( 'PP_METHOD_DO_CAPTURE', 'DoCapture' );
define( 'PP_METHOD_DO_AUTHORIZATION', 'DoAuthorization' );
define( 'PP_METHOD_DO_REAUTHORIZATION', 'DoReauthorization' );
define( 'PP_METHOD_DO_VOID', 'DoVoid' );
define( 'PP_METHOD_DIRECTPAYMENT', 'DoDirectPayment' );
define( 'PP_METHOD_NON_REFERENCED_CREDIT', 'DoNonReferencedCredit' );
define( 'PP_METHOD_SET_EXPRESS_CHECKOUT', 'SetExpressCheckout' );
define( 'PP_METHOD_GET_EXPRESS_CHECKOUT_DETAILS', 'GetExpressCheckoutDetails' );
define( 'PP_METHOD_EXPRESS_CHECKOUT_PAYMENT', 'DoExpressCheckoutPayment' );
define( 'PP_METHOD_GET_BALANCE', 'GetBalance' );
define( 'PP_METHOD_GET_PAL_DETAILS', 'GetPalDetails' );
define( 'PP_METHOD_GET_TRANSACTION_DETAILS', 'GetTransactionDetails' );
define( 'PP_METHOD_MANAGE_PENDING_TRANSACTION', 'ManagePendingTransactionStatus' );
define( 'PP_METHOD_CREATE_RECURRING_PAYMENT_PROFILE', 'CreateRecurringPaymentsProfile' );
define( 'PP_METHOD_GET_RECURRING_PAYMENT_PROFILE_DETAILS', 'GetRecurringPaymentsProfileDetails' );
define( 'PP_METHOD_MANAGE_RECURRING_PAYMENT_PROFILE_STATUS', 'ManageRecurringPaymentsProfileStatus' );
define( 'PP_METHOD_BILL_OUTSTANDING_AMOUNT', 'BillOutstandingAmount' );
define( 'PP_METHOD_UPDATE_RECURRING_PAYMENT_PROFILE', 'UpdateRecurringPaymentsProfile' );
define( 'PP_METHOD_SET_CUSTOMER_BILLING_AGREEMENT', 'SetCustomerBillingAgreement' );
define( 'PP_METHOD_GET_BILL_AGREEMENT_CUSTOMER_DETAILS', 'GetBillingAgreementCustomerDetails' );
define( 'PP_METHOD_REFERENCE_TRANSACTION', 'DoReferenceTransaction' );
define( 'PP_METHOD_REFUND_TRANSACTION', 'RefundTransaction' );
define( 'PP_METHOD_TRANSACTION_SEARCH', 'TransactionSearch' );
/************************************************************************************
* An easy-to-use library to access PayPal API services and include their 
* capabilities on any web site or application
* CodeIgniter Usage:
* 		$this->load->library( 'paypal', $credentials ); 
* 		$this->paypal->go_live();					// For live usage, omit this while testing
* 		$this->paypal->get_pal_details();			// Or any method, check the docs for required parameters
************************************************************************************/
class Paypal
{
	private $_credentials;							/** PayPal API access credentials */
	private $_mode = PP_MODE_DEVELOPMENT;			/** The mode to use the library: 'Development' by default */
	private $_error;								/** A holder for any connection error */
	/*******************************************************************************
	* Constructor method
	* @param array $credentials 	The PayPal required credentials: username, password, signature
	*******************************************************************************/
	function __construct( $credentials = null )
	{
		if($credentials)							/** Set API credentials if any */
		{
			$this->_credentials = array(
                            'user'		=> $credentials['username'],
                            'pwd'		=> $credentials['password'],
                            'signature'	=> $credentials['signature'],
                            'version'	=> PP_API_VERSION
                        );
		}
	}
	/*******************************************************************************
	* Utility function to set the API crendentials to use
	* @param string $username 
	* @param string $password
	* @param string $signature
	*******************************************************************************/
	public function set_credential( $username, $password, $signature )
	{
		// Set API credentials
		$this->_credentials = array(
                        'user'		=> $username,
                        'pwd'		=> $password,
                        'signature'	=> $signature,
                        'version'	=> PP_API_VERSION
                    );
	}
	/*******************************************************************************
	* Utility function to read the latest error message
	* @return string		The latest CURL connection error message
	*******************************************************************************/
	public function get_error()
	{
		return $this->_error;
	}
	/*******************************************************************************
	* Use this method to change from 'Development' to 'Live Usage' mode
	*******************************************************************************/
	public function go_live()
	{
		$this->_mode = PP_MODE_LIVE;
	}
	/*******************************************************************************
	* This function listen for and validate IPN messages from the PayPal services,
	* it return FALSE if the message turns to be INVALID and the array of variables
	* from the message contents for further process otherwise
	*******************************************************************************/
	public function validate_ipn()
	{
		$ipn = array();										// Notification parameters holder
		$req = PP_CMD_NOTIFY_VALIDATE;						// Get original message and add the command option to it
		foreach($_POST as $key => $value)
		{
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		parse_str( urldecode( $req ), $ipn );				// Add the original notification variables to the array holder
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";		// Post back to PayPal system to validate
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen( $req ) . "\r\n\r\n";
		if( $this->_mode == PP_MODE_DEVELOPMENT )
		{
			$fp = fsockopen( PP_SERVER_IPN_DEV, 443, $errno, $errstr, 30 );
		}
		else
		{
			$fp = fsockopen( PP_SERVER_IPN_LIVE, 443, $errno, $errstr, 30 );
		}
		if(!$fp)
		{
			return false;									// Socket connection error
		}
		else
		{
			fputs( $fp, $header . $req );					// Put response contents on file
			while(!feof($fp))
			{
				$res = fgets( $fp, 1024 );					// Get file contents and check for a VERIFIED or INVALID response
				if(strcmp( $res, PP_RSP_VERIFIED ) == 0)
				{
					return $ipn;							// Message is VALID, return IPN contents
				}
				else if(strcmp( $res, PP_RSP_INVALID ) == 0)
				{
					return false;							// Message is INVALID, return false
				}
			}
			fclose($fp);
		}
	}
	/*******************************************************************************
	* Verify the address of a PayPal user.
	* Required Parameters:
	*		EMAIL 	-> The PP user email address (maxchars. 255)
	*		STREET	-> First line of the billing or shipment postal address (maxchars. 35)
	*		ZIP		-> Postal code to verify (maxchars. 16)
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function verify_address( $params )
	{
		if(!is_array( $params ) || empty( $params ))	// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_ADDRESS_VERIFY;
		$req = array_merge($required,$params);
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Capture a previously authorized payment
	* Required Parameters:
	* 		AUTHORIZATIONID	-> Authorization ID number returned by expressCheckout or Direct payment
	* 		AMT				-> The amount to capture ( max 10,000.00 USD or equivalent in any currency )
	* 		CURRENCYCODE	-> 3 chars currency code, defaults to 'USD'
	* 		COMPLETETYPE	-> Either 'Complete' or 'NotComplete'
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function capture($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_DO_CAPTURE;
		$req = array_merge($required, $params);
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Authorize a payment
	* Required Parameters:
	* 		TRANSACTIONID	-> The transaction identification number from PayPal
	* 		AMT				-> Amount to authorize
	* 		CURRENCYCODE	-> 3 chars currency code
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function authorization($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_DO_AUTHORIZATION;
		$req = array_merge($required, $params);
		return $this->_execute_call( http_build_query( $req ) );
	}
	/*******************************************************************************
	* Re-Authorize a payment
	* Required Parameters:
	* 		AUTHORIZATIONID	-> Authorization ID number returned by expressCheckout or Direct payment
	* 		AMT				-> The amount to capture ( max 10,000.00 USD or equivalent in any currency )
	* 		CURRENCYCODE	-> 3 chars currency code
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function reauthorization($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_DO_REAUTHORIZATION;
		$req = array_merge( $required, $params );
		return $this->_execute_call( http_build_query( $req ) );
	}
	/*******************************************************************************
	* Void an order or an authorization
	* Required Parameters:
	* 		AUTHORIZATIONID	-> Original Authorization ID number returned by PayPal
	* 		NOTE			-> A note about this void operation (maxchars. 255)
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function do_void($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_DO_VOID;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Process a credit card payment
	* Required Payments:
	* 		IPADDRESS				-> IP Address of the payer's browser
	* 		RETURNFMFDETAILS		-> Wheter you want to receive FraudManagementFilters results (1=Yes, 0=No)
	* 		CREDITCARDTYPE			-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT					-> Credit card number, no spaces or punctuation
	* 		EXPDATE					-> Credit card expiration date, format MMYYYY
	* 		CVV2					-> Card Verification Value
	* 		ISSUENUMBER				-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		STREET					-> First street address (maxchars. 100)
	* 		CITY					-> Name of the city (maxchars. 40)
	* 		STATE					-> State or Province (maxchars. 40)
	* 		COUNTRYCODE				-> Official ISO country code (maxchars. 2)
	* 		ZIP						-> Postal code
	* 		AMT						-> Total cost of the transaction, use 0 for a recurring payments agreement.
	* 		CURRENCYCODE			-> 3 chars currency code, default to 'USD'
	* 		DESC					-> A description of the purchase
	* 		CUSTOM					-> Free-form field or your personal use
	* 		ALLOWEDPAYMENTMETHOD	-> 'InstantPaymentOnly' to only accept instant cash ;)
	* 		INVNUM					-> Your own internal invoice number
	* 		NOTIFYURL				-> The URL address to receive IPN notifications about this payment
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function direct_payment($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_DIRECTPAYMENT;
		$req = array_merge( $required, $params );
		return $this->_execute_call( http_build_query($req));
	}
	/*******************************************************************************
	* Process a payment
	* Required Payments:
	* 		IPADDRESS				-> IP Address of the payer's browser
	* 		RETURNFMFDETAILS		-> Wheter you want to receive FraudManagementFilters results (1=Yes, 0=No)
	* 		CREDITCARDTYPE			-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT					-> Credit card number, no spaces or punctuation
	* 		EXPDATE					-> Credit card expiration date, format MMYYYY
	* 		CVV2					-> Card Verification Value
	* 		ISSUENUMBER				-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		STREET					-> First street address (maxchars. 100)
	* 		CITY					-> Name of the city (maxchars. 40)
	* 		STATE					-> State or Province (maxchars. 40)
	* 		COUNTRYCODE				-> Official ISO country code (maxchars. 2)
	* 		ZIP						-> Postal code
	* 		AMT						-> Total cost of the transaction, use 0 for a recurring payments agreement.
	* 		CURRENCYCODE			-> 3 chars currency code, default to 'USD'
	* 		DESC					-> A description of the purchase
	* 		CUSTOM					-> Free-form field or your personal use
	* 		ALLOWEDPAYMENTMETHOD	-> 'InstantPaymentOnly' to only accept instant cash ;)
	* 		INVNUM					-> Your own internal invoice number
	* 		NOTIFYURL				-> The URL address to receive IPN notifications about this payment
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function setExpressCheckout_payment($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_SET_EXPRESS_CHECKOUT;
		$req = array_merge( $required, $params );
		$httpParsedResponseAr = $this->_execute_call( http_build_query($req));
                //echo "<pre>". print_r($httpParsedResponseAr, true); die;
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
                        "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"]))
		{
			// Redirect to paypal.com.
			$this->_mode == PP_MODE_DEVELOPMENT ? $payPalURL = PP_SERVER_POST_DEV : $payPalURL = PP_SERVER_POST_LIVE;
			$token = urldecode($httpParsedResponseAr["TOKEN"]);
			$payPalURL.= "webscr&cmd=_express-checkout&token=".$token;
			header("Location: $payPalURL");
			exit();
		}
		else
		{
			$returnURL = site_url('recuriing/register/');
			header("Location: $returnURL");
			exit();
		}
	}
	public function getExpressCheckout_payment($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_GET_EXPRESS_CHECKOUT_DETAILS;
		$req = array_merge( $required, $params );
		return $httpParsedResponseAr = $this->_execute_call( http_build_query($req));
	}
	public function doExpressCheckout_payment($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_EXPRESS_CHECKOUT_PAYMENT;
		$req = array_merge( $required, $params );
		return $httpParsedResponseAr = $this->_execute_call( http_build_query($req));
	}
	/*******************************************************************************
	* Issue a credit to a card not referenced by the original transaction.
	* Required Parameters:
	* 		AMT				-> Total amount to be charged
	* 		CURRENCYCODE	-> 3 chars currency code, valid values are: AUD, CAD, EUR, GBP, JPY, USD
	* 		NOTE			-> A note about why this credit was issued
	* 		CREDITCARDTYPE	-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT			-> Credit card number, no spaces or punctuation
	* 		EXPDATE			-> Credit card expiration date, format MMYYYY
	* 		CVV2			-> Card Verification Value
	* 		ISSUENUMBER		-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		FIRSTNAME		-> Payer's first name
	* 		LASTNAME		-> Payer's last name
	* 		STREET			-> Payer's first street address
	* 		CITY			-> Payer's City name (maxchars. 40)
	* 		STATE			-> Payer's State or Province (maxchars. 40)
	* 		COUNTRY			-> Payer's Official ISO country code (maxchars. 2)
	* 		ZIP				-> Postal code	
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function non_referenced_credit($params)
	{
		if( !is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_NON_REFERENCED_CREDIT;
		$req = array_merge($required, $params);
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Obtain the available balance on all currencies for a PayPal account
	*******************************************************************************/
	public function get_balance()
	{
		$required = $this->_credentials;				// Get required credentials and set API method to the request
		$required[ 'method' ] = PP_METHOD_GET_BALANCE;
		$required[ 'returnallcurrencies' ] = 1;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Obtain your Pal ID and other details about your PayPal account
	*******************************************************************************/
	public function get_pal_details()
	{
		$required = $this->_credentials;				// Get required credentials and set API method to the request
		$required[ 'method' ] = PP_METHOD_GET_PAL_DETAILS;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Obtain information about a specific transaction
	* @param string $transactionID	:  The PayPal identification number of the transaction
	*******************************************************************************/
	public function get_transaction_details($transactionID)
	{
		if(trim($transactionID) == '')			// Check for a non empty transaction ID
		{
			return false;
		}
		$required = $this->_credentials;		// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_GET_TRANSACTION_DETAILS;
		$required[ 'transactionid' ] = $transactionID;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Accept or Deny a pending transaction held by PayPal fraud management filters
	* @param string $transactionID		The PayPal identification number of the transaction
	* @param boolean $accept			TRUE for accept the operation or FALSE to deny it, defaults to 'FALSE'
	*******************************************************************************/
	public function manage_pending_transaction($transactionID, $accept = false)
	{
		if(trim( $transactionID ) == '')		// Check for a non empty transaction ID
		{
			return false;
		}
		$required = $this->_credentials;		// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_MANAGE_PENDING_TRANSACTION;
		$required[ 'transactionid' ] = $transactionID;
		!$accept ? $required[ 'action' ] = 'Deny' : $required[ 'action' ] = 'Accept';
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Create a Recurring Payments Profile
	* Required Parameters:
	* 		PROFILESTARTDATE	-> The date when billings for this profile begins, in UTC/GMT format
	* 		PROFILEREFERENCE	-> Your own UNIQUE reference for the profile
	* 		DESC				-> Description for the recurring payment, MUST match the one on the billing agreement
	* 		MAXFAILEDPAYMENTS	-> The number of payments that can fail before the profile is suspended
	* 		AUTOBILLAMT			-> Choose to have PayPal charge any pending balance. 'NoAutoBill' or 'AddToNextBilling'
	* 		BILLINGPERIOD		-> Unit for billing, possible values: 'Day', 'Week', 'SemiMonth', 'Month', 'Year'
	* 		BILLINGFREQUENCY	-> Number of billing periods that make up one cycle
	* 		TOTALBILLINGCYCLES	-> The number of cycles for the hole payment period, use 0 to continue indefinitely
	* 		AMT					-> Billing amount for each payment
	* 		CURRENCYCODE		-> 3 chars currency code, default to 'USD'
	* 		INITAMT				-> Initial non-recurring payment amount due immediately upon profile creation
	* 		FAILEDINITAMTACTION	-> What to do if init charge fails, options 'ContinueOnFailure' or 'CancelOnFailure'
	* 		CREDITCARDTYPE		-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT				-> Credit card number, no spaces or punctuation
	* 		EXPDATE				-> Credit card expiration date, format MMYYYY
	* 		CVV2				-> Card Verification Value
	* 		ISSUENUMBER			-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		STREET				-> Payer's first street address
	* 		CITY				-> Payer's City name (maxchars. 40)
	* 		STATE				-> Payer's State or Province (maxchars. 40)
	* 		COUNTRYCODE			-> Payer's official ISO country code (maxchars. 2)
	* 		ZIP					-> Postal code
	* 		BUSINESS			-> Payer's business name
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function create_rp_profile($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_CREATE_RECURRING_PAYMENT_PROFILE;
		$req = array_merge($required, $params);
		return $this->_execute_call( http_build_query( $req ) );
	}
	/*******************************************************************************
	* Obtain information about a recurring payments profile
	* @param string $profileID	:	The PayPal identification number for the profile
	*******************************************************************************/
	public function get_rp_profile_details($profileID)
	{
		if(trim($profileID) == '')			// Check for a non empty profile ID
		{
			return false;
		}
		$required = $this->_credentials;	// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_GET_RECURRING_PAYMENT_PROFILE_DETAILS;
		$required[ 'profileid' ] = $profileID;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Cancel, Suspend or Reactivate a recurring payments profile
	* @param string $profileID		The PayPal identification number for the profile
	* @param string $action		The action to perform on the profile: 'Cancel', 'Suspend' or 'Reactivate'
	*******************************************************************************/
	public function manage_rp_profile_status($profileID, $action)
	{
		if(trim($profileID) == '')				// Check for a non empty profile ID
		{
			return false;
		}
		if($action != 'Cancel' && $action != 'Suspend' && $action != 'Reactivate')		// Check for invalid action
		{
			return false;
		}
		$required = $this->_credentials;		// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_MANAGE_RECURRING_PAYMENT_PROFILE_STATUS;
		$required[ 'profileid' ] = $profileID;
		$required[ 'action' ] = $action;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Bill the buyer for the outstanding balance associated with a recurring payments profile.
	* @param string $profileID		The PayPal identification number for the profile
	*******************************************************************************/
	public function bill_outstanding_amount($profileID)
	{
		if(trim( $profileID ) == '')			// Check for a non empty profile ID
		{
			return false;
		}
		$required = $this->_credentials;		// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_BILL_OUTSTANDING_AMOUNT;
		$required[ 'profileid' ] = $profileID;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Update an existing Recurring Payments Profile
	* Required Parameters:
	* 		PROFILEID			-> The PayPal identification number for the profile
	* 		SUBSCRIBERNAME		-> Full name of the person receiving the product or service paid
	* 		PROFILEREFERENCE	-> Your own UNIQUE reference for the profile
	* 		AMT					-> The new amount to charge, can't increse more than 20% in 180 days
	* 		AUTOBILLAMT			-> Choose to have PayPal charge any pending balance. 'NoAutoBill' or 'AddToNextBilling'
	* 		CREDITCARDTYPE		-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT				-> Credit card number, no spaces or punctuation
	* 		EXPDATE				-> Credit card expiration date, format MMYYYY
	* 		CVV2				-> Card Verification Value
	* 		ISSUENUMBER			-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		STREET				-> Payer's first street address
	* 		CITY				-> Payer's City name (maxchars. 40)
	* 		STATE				-> Payer's State or Province (maxchars. 40)
	* 		COUNTRYCODE			-> Payer's official ISO country code (maxchars. 2)
	* 		ZIP					-> Postal code
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function update_rp_profile($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_UPDATE_RECURRING_PAYMENT_PROFILE;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Initiates the creation of a billing agreement
	* Required Parameters:
	* 		RETURNURL						-> A success url the user will see after choose to pay with PayPal
	* 		CANCELURL						-> A page where the user will return if don't accept to use PayPal
	* 		L_BILLINGTYPE					-> Type of billing agreement, for RP use 'RecurringPayments'
	* 		L_BILLINGAGREEMENTDESCRIPTION	-> A description for the agreement terms 
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function set_customer_ba($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_SET_CUSTOMER_BILLING_AGREEMENT;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Obtain information about a billing agreement�s PayPal account holder.
	* @param string $token		The time-stamped token returned in the setCustomerBA response
	*******************************************************************************/
	public function get_ba_customer_details($token)
	{
		if(trim($token) == '')				// Check for a non empty token
		{
			return false;
		}
		$required = $this->_credentials;	// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_GET_BILL_AGREEMENT_CUSTOMER_DETAILS;
		$required[ 'token' ] = $token;
		return $this->_execute_call(http_build_query($required));
	}
	/*******************************************************************************
	* Process a payment from a buyer�s account, which is identified by a previous transaction
	* Required Parameters:
	* 		REFERENCEID			-> A transaction ID from a previous purchase
	* 		RETURNFMFDETAILS	-> Wheter you want to receive FraudManagementFilters results (1=Yes, 0=No)
	* 		AMT					-> Total cost of the transaction to the customer
	* 		CURRENCYCODE		-> 3 chars currency code, default to 'USD'
	* 		DESC				-> A description of the purchase
	* 		CUSTOM				-> Free-form field or your personal use
	* 		INVNUM				-> Your own internal invoice number
	* 		NOTIFYURL			-> The URL address to receive IPN notifications about this payment
	* 		CREDITCARDTYPE		-> Supported values are: Visa, MasterCard, Discover, Amex, Maestro, Solo
	* 		ACCT				-> Credit card number, no spaces or punctuation
	* 		EXPDATE				-> Credit card expiration date, format MMYYYY
	* 		CVV2				-> Card Verification Value
	* 		ISSUENUMBER			-> Issue Number. ONLY FOR Maestro AND Solo CREDIT CARDS
	* 		FIRSTNAME			-> Payer's first name
	* 		LASTNAME			-> Payer's last name
	* 		STREET				-> First street address (maxchars. 100)
	* 		CITY				-> Name of the city (maxchars. 40)
	* 		STATE				-> State or Province (maxchars. 40)
	* 		COUNTRYCODE			-> Official ISO country code (maxchars. 2)
	* 		ZIP					-> Postal code
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function reference_transaction($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_REFERENCE_TRANSACTION;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Issue a refund to the PayPal account holder associated with a transaction
	* Required Parameters:
	* 		TRANSACTIONID		-> Unique transaction identifier
	* 		REFUNDTYPE			-> Could be 'Other', 'Full', 'Partial'
	* 		AMT					-> Amount to refund if this is a partial refund
	* 		NOTE				-> A note about the refund operation
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function refund_transaction($params)
	{
		if( !is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_REFUND_TRANSACTION;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
        /*******************************************************************************
	* Cancel a subscription to the PayPal account holder associated with a transaction
	* Required Parameters:
	* 		METHOD		-> Required Must be ManageRecurringPaymentsProfileStatus.
	* 		PROFILEID       -> Required Recurring payments profile ID returned in the 
        *                                 CreateRecurringPaymentsProfile response
        *               ACTION          -> Required The action to be performed to the recurring payments profile. 
        *                                Must be one of the following:

                                            Cancel — Only profiles in Active or Suspended state can be canceled.

                                            Suspend — Only profiles in Active state can be suspended.

                                            Reactivate — Only profiles in a suspended state can be reactivated.
	*               NOTE            -> A note about the cancel subscription
        * @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function cancel_subscription($params)
	{
            if( !is_array($params) || empty($params)) // Check for a valid parameters array
            {
                    return false;
            }
            $required = $this->_credentials; // Get required credentials, set API method and add user parameters to the request
            $required[ 'method' ] = PP_METHOD_MANAGE_RECURRING_PAYMENT_PROFILE_STATUS;
            $req = array_merge( $required, $params );
            return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* Search transaction history for transactions that meet the specified criteria
	* Required Parameters:
	* 		STARTDATE			-> The date at which start the search, in valid UTM/GMT format
	* 		ENDATE				-> The latest date to be included in the search
	* 		EMAIL				-> Search by buyer's email address
	* 		TRANSACTIONID		-> Search by PayPal transaction identification number
	* 		INVNUM				-> Search by your own invoice number
	* 		ACCT				-> Search by credit card number
	* 		AMT					-> Search by amount
	* 		CURRENCYCODE		-> Search by a 3 chars currency code
	* 		STATUS				-> Search by status: 'Pending', 'Processing', 'Success', 'Denied', 'Reversed'
	* @param array $params		Required parameters by the API method
	*******************************************************************************/
	public function search_transaction($params)
	{
		if(!is_array($params) || empty($params))		// Check for a valid parameters array
		{
			return false;
		}
		$required = $this->_credentials;				// Get required credentials, set API method and add user parameters to the request
		$required[ 'method' ] = PP_METHOD_TRANSACTION_SEARCH;
		$req = array_merge( $required, $params );
		return $this->_execute_call(http_build_query($req));
	}
	/*******************************************************************************
	* PRIVATE FUNCTION ( for internal use ONLY )
	* Perform the actual API call to the paypal services, it returns an array with
	* the response parameters or an error message
	* NOTE: To use this class without Codeingniter simply change this method to use plain
	* 		 CURL functions and remove the internal $CI variable and initialization code ;)
	* @param srting $nvpString	:  The URL encoded string containing the complete API call parameters
	* @return array	 :	An indexed array containing all the response parameters already decoded
	*******************************************************************************/
	private function _execute_call($nvpString)
	{
		$response = array();		// Reponse holder
		// Choose the right server according to the working mode and create the request
		$this->_mode == PP_MODE_DEVELOPMENT ? $server = PP_SERVER_API_DEV : $server = PP_SERVER_API_LIVE;
		$req = curl_init();
		curl_setopt($req, CURLOPT_URL, $server);
		curl_setopt($req, CURLOPT_SSL_VERIFYPEER, FALSE);		// Set curl options
		curl_setopt($req, CURLOPT_SSL_VERIFYHOST, FALSE);		
		curl_setopt($req, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($req, CURLOPT_VERBOSE, TRUE);
		curl_setopt($req, CURLOPT_POST, TRUE);					// Set POST parameters
		curl_setopt($req, CURLOPT_POSTFIELDS, $nvpString);
		$data = curl_exec($req);								// Execute the request
		if(!$data)
		{
			$this->_error = curl_error($req);					// Save the error string and return FALSE
			curl_close($req);
			return FALSE;
		}
		else
		{
			curl_close($req);									// Return the response as an array
			parse_str(urldecode($data), $response);
			return $response;
		}
	}
}
// End Paypal Class
/* End of file Paypal.php */
/* Location: ./{APPLICATION}/libraries/Paypal.php */