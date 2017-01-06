<?php

//This class is used to do payment processing such as credit card payment processing/handeling
error_reporting(1);

class Payment_model extends CI_Model {

    //data for authrize.net
    private $_login,
            $_password,
            $_tranKey,
            $_version = AUTHORIZE_VERSION,
            $_delimChar = AUTHORIZE_DELIMITER,
            $_delimData = AUTHORIZE_DELIMIT_DATA,
            $_url = AUTHORIZE_URL_FLAG,
            $_testRequest = AUTHORIZE_TEST_REQUEST,
            $_method = "CC", //cc $pmethod
            $_creditCardResponseArray = "",
            $_cim = "";
    public $transactionType = "AUTH_CAPTURE",
            $paymentMethod = 0,
            $cardType = "",
            $orderNumber = "",
            $paymentAmount = 0,
            $transactionId = 0,
            $cardNumber = "",
            $cardCvv = "",
            $cardExpireDate = "",
            $cardNumberPrint = "",
            $additionalOrderId = 0,
            $additionalOrdersArr = array(),
            $transactionRecordId = 0,
            $customerInfo = array(),
            $creditCardInfo = array(),
            $responceArray = array(),
            $declinedArray = array(),
            $responseArray = array(),
            $CI;

    /*     * *****************************
     * function Payment_model ()
     * custructor for this model
     * ****************************** */

    function __construct() {
        parent::__construct();
        $this->CI = & get_instance();
        $this->_login = AUTHORIZE_LOGIN_NAME;
        $this->_password = AUTHORIZE_LOGIN_PASSWORD;
        $this->_tranKey = AUTHORIZE_TRANSACTION_KEY;
    }

    /*     * *****************************
     * function __get()
     * get defined property
     * ****************************** */

    function __get($property) {
        if (isset($this->$property)) {
            return $this->$property;
        }
    }

    /*     * *****************************
     * function get_response()
     * get defined property
     * ****************************** */

    function get_response() {
        if (!empty($this->responceArray)) {
            return $this->responceArray;
        }
    }

    /*     * *****************************
     * Function create_customer_profile_at_auth
     * This function will process credit card payment.
     * @param - $authorizeNet - Its an array containing customer's and credit card information and login credentials
     * @return - TRUE if profile is created successfully (if response is success)
     * - errormessage if transaction is failed (if resonse is fail)
     * ****************************** */

    public function create_customer_profile_at_auth($authorizeNet, $userDetailsArray = array()) {

        $authorizeNet["x_login"]        = $this->_login;
        $authorizeNet["x_version"]      = $this->_version;
        $authorizeNet["x_delim_char"]   = $this->_delimChar;
        $authorizeNet["x_delim_data"]   = $this->_delimData;
        $authorizeNet["x_url"]          = $this->_url;
        $authorizeNet["x_test_request"] = $this->_testRequest;
        $authorizeNet["x_method"]       = $this->_method;
        $authorizeNet["x_tran_key"]     = $this->_tranKey;
        $authorizeNet["x_type"]         = $this->transactionType;
        $authorizeNet["x_trans_id"]     = $this->transactionId;
        
        $responseArray = array();
        //----------------------------------------------------------------------------
        // intiate AuthNetCim library to process order with customer profile
        //----------------------------------------------------------------------------

        try 
        {
            $this->_cim = $this->CI->authorizenet_cim_class->AuthNetCim($authorizeNet["x_login"], $authorizeNet["x_tran_key"], $this->_testRequest);
            //return $this->_cim->url;
        } 
        catch (Exception $e) 
        {
            echo 'Message: ' . $e->getMessage();
        }

        /* $isProfileIdExits = $this->CI->authorize_details->getUserAuthProfileDetails(
          $authorizeNet["x_user_id"]); */


        // create authorize net profile id
        $this->_cim->setParameter('refId', $authorizeNet["x_user_id"]);

        $this->_create_customer_profile($authorizeNet);
        //die("create profile die");
        // if profile created successfully
        if( $this->_cim->isSuccessful() ) 
        {
            // get authorize net profile information
            $this->_get_customer_profile($this->_cim->customerProfileId);
            $this->_get_customer_payment_profile($this->_cim->customerPaymentProfileId);
            if ($this->_cim->customerProfileId != '') 
            {
                $responseArray['error_code'] = $this->_cim->resultCode;
                $responseArray['error_message'] = $this->_cim->text;
                $responseArray['error_code_number'] = $this->_cim->code;
                $responseArray['customerProfileId'] = $this->_cim->customerProfileId;
                $responseArray['customerPaymentProfileId'] = $this->_cim->customerPaymentProfileId;
            }
        } 
        else 
        {
            $responseArray['error_code'] = $this->_cim->resultCode;
            $responseArray['error_code_number'] = $this->_cim->code;
            $responseArray['error_message'] = $this->_cim->text;
            $responseArray['error_descriptions'] = $this->error_messages;
        }
        return $responseArray;
    }

    /*     * *****************************
     * Function create_customer_profile_at_auth
     * This function will process credit card payment.
     * @param - $authorizeNet - Its an array containing customer's and credit card information and login credentials
     * @return - TRUE if profile is created successfully (if response is success)
     * - errormessage if transaction is failed (if resonse is fail)
     * ****************************** */

    public function create_customer_payment_profile_at_auth($authorizeNet, $userDetailsArray = array()) {
        $authorizeNet["x_login"] = $this->_login;
        $authorizeNet["x_version"] = $this->_version;
        $authorizeNet["x_delim_char"] = $this->_delimChar;
        $authorizeNet["x_delim_data"] = $this->_delimData;
        $authorizeNet["x_url"] = $this->_url;
        $authorizeNet["x_test_request"] = $this->_testRequest;
        $authorizeNet["x_method"] = $this->_method;
        $authorizeNet["x_tran_key"] = $this->_tranKey;
        $authorizeNet["x_type"] = $this->transactionType;
        $authorizeNet["x_trans_id"] = $this->transactionId;

        $responseArray = array();

        //----------------------------------------------------------------------------
        // intiate AuthNetCim library to process order with customer profile
        //----------------------------------------------------------------------------
        $this->_cim = $this->CI->authorizenet_cim_class->AuthNetCim($authorizeNet["x_login"], $authorizeNet["x_tran_key"], $this->_testRequest);

        // create authorize net profile id
        $this->_cim->setParameter('refId', $authorizeNet["x_user_id"]);
        $this->_cim->setParameter('customerProfileId', $authorizeNet["x_authorize_profile_id"]);

        $this->_create_customer_payment_profile($authorizeNet);

        // if profile created successfully
        if ($this->_cim->isSuccessful()) 
        {
            // get authorize net profile information
            $this->_get_customer_profile($this->_cim->customerProfileId);
            $this->_get_customer_payment_profile($this->_cim->customerPaymentProfileId);

            if ($this->_cim->customerPaymentProfileId != '') 
            {
                $responseArray['error_code'] = $this->_cim->resultCode;
                $responseArray['error_message'] = $this->_cim->text;
                $responseArray['error_code_number'] = $this->_cim->code;
                $responseArray['customerProfileId'] = $this->_cim->customerProfileId;
                $responseArray['customerPaymentProfileId'] = $this->_cim->customerPaymentProfileId;
            }
        } 
        else 
        {
            $responseArray['error_code'] = $this->_cim->resultCode;
            $responseArray['error_code_number'] = $this->_cim->code;
            $responseArray['error_message'] = $this->_cim->text;
            $responseArray['error_descriptions'] = $this->error_messages;
        }
        return $responseArray;
    }

    /*     * *****************************
     * Function create_transaction_request
     * This function will process credit card payment.
     * @param - $authorizeNet - Its an array containing customer's and credit card information and login credentials
     * @return - TRUE if profile is created successfully (if response is success)
     * - errormessage if transaction is failed (if resonse is fail)
     * ****************************** */

    public function create_transaction_request($authorizeNet, $userDetailsArray = array()) {
        $authorizeNet["x_login"] = $this->_login;
        $authorizeNet["x_version"] = $this->_version;
        $authorizeNet["x_delim_char"] = $this->_delimChar;
        $authorizeNet["x_delim_data"] = $this->_delimData;
        $authorizeNet["x_url"] = $this->_url;
        $authorizeNet["x_test_request"] = $this->_testRequest;
        $authorizeNet["x_method"] = $this->_method;
        $authorizeNet["x_tran_key"] = $this->_tranKey;
        $authorizeNet["x_type"] = $this->transactionType;
        $authorizeNet["x_trans_id"] = $this->transactionId;

        $responseArray = array();
        //----------------------------------------------------------------------------
        // intiate AuthNetCim library to process order with customer profile
        //----------------------------------------------------------------------------
        $this->_cim = $this->CI->authorizenet_cim_class->AuthNetCim($authorizeNet["x_login"], $authorizeNet["x_tran_key"], $this->_testRequest);

        // create authorize net profile id
        $this->_cim->setParameter('refId', $authorizeNet["x_user_id"]);

        if (isset($authorizeNet["x_Card_Code"]) && $authorizeNet["x_Card_Code"] != 0)
            $this->_cim->setParameter('transactionCardCode', $authorizeNet["x_Card_Code"]);

        $this->_cim->setParameter('cardNumber', $authorizeNet["x_card_num"]);
        $this->_cim->setParameter('expirationDate', $authorizeNet["x_expiration_date"]);
        $this->_cim->setParameter('customerProfileId', $authorizeNet["x_customer_profile_id"]);
        $this->_cim->setParameter('customerPaymentProfileId', $authorizeNet["x_customer_payment_profile_id"]);

        $this->_create_transaction_request($authorizeNet);

        // if profile created successfully
        if ($this->_cim->isSuccessful()) {
            if ($this->_cim->resultCode == 'Ok') {
                $raw_response = explode(",", $this->_cim->rawResponse);
                if (!empty($raw_response)) {
                    $responseArray['transId'] = $raw_response[6];
                }

                $responseArray['error_code'] = $this->_cim->resultCode;
                $responseArray['error_code_number'] = $this->_cim->code;
                $responseArray['error_message'] = $this->_cim->text;

                return $responseArray;
            }
        } else {
            $responseArray['error_code'] = $this->_cim->resultCode;
            $responseArray['error_code_number'] = $this->_cim->code;
            $responseArray['error_message'] = $this->_cim->text;
            $responseArray['error_descriptions'] = $this->error_messages;
            $responseArray['transId'] = '0';
        }
        return $responseArray;
    }

    private function set_cim_response() {
        if ($this->_cim->isSuccessful()) {
            echo "<br>" . $this->_cim->response;
            echo "YES<br>" . $this->_cim->directResponse;
            echo "<br>" . $this->_cim->validationDirectResponse;
            echo "<br>" . $this->_cim->resultCode;
            echo "<br>" . $this->_cim->code;
            echo "<br>" . $this->_cim->text;
            echo "<br>" . $this->_cim->refId;
            echo "<br>" . $this->_cim->customerProfileId;
            echo "<br>" . $this->_cim->customerPaymentProfileId;
            echo "<br>" . $this->_cim->customerAddressId;
        } else {
            echo "NO<br>" . $this->_cim->directResponse;
            echo "<br>" . $this->_cim->validationDirectResponse;
            echo "<br>" . $this->_cim->resultCode;
            echo "<br>" . $this->_cim->code;
            echo "<br>" . $this->_cim->text;
            echo "<br><pre>";
            print_r($this->_cim->error_messages);
            echo "</pre>";
        }
    }

    /*     * *****************************
     * Function Provate create_customer_profile
     * This function will create authorize.net create customer profile
     * @param - array of all authorizeNet parameter
     * return NULL
     * ****************************** */

    private function _create_customer_profile($authorizeNet) {

        //$authorizeNet["x_expiration_date"] = "04/17";
        // creditCard payment method - (aka creditcard)
        $this->_cim->setParameter('paymentType', 'creditCard');
        $this->_cim->setParameter('cardNumber', $authorizeNet["x_card_num"]);
        $this->_cim->setParameter('expirationDate', $authorizeNet["x_expiration_date"]); // (YYYY-MM)
        $this->_cim->setParameter('billTo_firstName', $authorizeNet["x_first_name"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_lastName', $authorizeNet["x_last_name"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_company', $authorizeNet["x_company"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_address', $authorizeNet["x_address"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_city', $authorizeNet["x_city"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_state', $authorizeNet["x_state"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_zip', $authorizeNet["x_zip"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_country', $authorizeNet["x_country"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('billTo_phoneNumber', $authorizeNet["x_phoneNumber"]); // Up to 50 characters (no symbols)
        $this->_cim->setParameter('email', $authorizeNet["x_email"]); // Up to 255 characters (optional)
        $this->_cim->setParameter('description', $authorizeNet["x_description"]); // Up to 255 characters (optional)
        $this->_cim->setParameter('merchantCustomerId', $authorizeNet["x_customer_id"]); // individual or business (optional)
        //$this->_cim->setParameter('customerType', 'individual'); // individual or business (optional)
        //echo "<pre>".print_r($authorizeNet, true)."</pre>"; die('erfwerw');
        $this->_cim->createCustomerProfileRequest();
    }

    /*     * *****************************
     * Function Provate create_customer_profile
     * This function will create authorize.net create customer profile
     * @param - array of all authorizeNet parameter
     * return NULL
     * ****************************** */

    private function _create_transaction_request($authorizeNet) {
        $transactionName = "profileTransAuthOnly";

        // transactionType = (profileTransCaptureOnly, profileTransAuthCapture or
        // profileTransAuthOnly , profileTransRefund, profileTransPriorAuthCapture , profileTransVoid
        if ($this->transactionType == "AUTH_ONLY")
            $transactionName = "profileTransAuthOnly";
        else if ($this->transactionType == "AUTH_CAPTURE")
            $transactionName = "profileTransAuthCapture";
        else if ($this->transactionType == "CREDIT")
            $transactionName = "profileTransRefund";
        else if ($this->transactionType == "VOID")
            $transactionName = "profileTransVoid";
        else if ($this->transactionType == "PRIOR_AUTH_CAPTURE")
            $transactionName = "profileTransPriorAuthCapture";

        // Total Amount: This amount should include all other amounts such as tax amount, shipping amount, etc.
        // not for void order
        if ($transactionName != "profileTransVoid") {
            $this->_cim->setParameter('transaction_amount', $authorizeNet["x_amount"]); // Up to 4 digits with a decimal (required)
        }

        $this->_cim->setParameter('transactionType', $transactionName); // see options above
        // Payment gateway assigned ID associated with the customer profile
        //$this->_cim->setParameter('customerProfileId', $this->_cim->customerProfileId); // Numeric (required)
        // Payment gateway assigned ID associated with the customer payment profile
        //$this->_cim->setParameter('customerPaymentProfileId', $this->_cim->customerPaymentProfileId); // Numeric (required)
        //|--------------------------------------------------------------------------
        //| process auth_only or Auth_capture transaction
        //|--------------------------------------------------------------------------

        if ($transactionName == "profileTransAuthOnly" || $transactionName == "profileTransAuthCapture" || $transactionName == "simplesemester") {
            // Up to 20 characters (no symbols) (optional)
            $this->_cim->setParameter('order_invoiceNumber', $authorizeNet["x_Invoice_Num"]);
            // Up to 255 characters (no symbols) (optional)
            $this->_cim->setParameter('order_description', "Prohealthspan " . $authorizeNet["x_Invoice_Num"]);
            // Up to 25 characters (no symbols) (optional)
            //$this->_cim->setParameter('order_purchaseOrderNumber', $authorizeNet["x_Invoice_Num"]); 
            // The customer's card code (the three- or four-digit number on the back or front of a credit card)
            // Required only when the merchant would like to use the Card Code Verification (CCV) filter
            if (isset($authorizeNet["x_Card_Code"]) && $authorizeNet["x_Card_Code"] != 0)
                $this->_cim->setParameter('transactionCardCode', $authorizeNet["x_Card_Code"]); // (conditional)
        }

        //|--------------------------------------------------------------------------
        //| process credit transaction
        //|--------------------------------------------------------------------------

        if ($transactionName == "profileTransRefund") {
            $this->_cim->setParameter('transaction_amount', $authorizeNet["x_amount"]);
            // Up to 20 characters (no symbols) (optional)
            $this->_cim->setParameter('order_invoiceNumber', $authorizeNet["x_Invoice_Num"]);
            // Up to 255 characters (no symbols) (optional)
            $this->_cim->setParameter('order_description', "Prohealthspan order number " . $authorizeNet["x_Invoice_Num"]);
            // Up to 25 characters (no symbols) (optional)
            $this->_cim->setParameter('order_purchaseOrderNumber', $authorizeNet["x_Invoice_Num"]);
        }

        //|--------------------------------------------------------------------------
        //| add transaction id for Prior Auth Capture, Credit  or Void transaction
        //|
        //| The transaction code of an original transaction required for a profileTransRefund, profileTransPriorAuthCapture , profileTransVoid
        //| This element is only required for the Capture Only transaction type.
        //|--------------------------------------------------------------------------

        if ($transactionName == "profileTransPriorAuthCapture" || $transactionName == "profileTransVoid" || $transactionName == "profileTransRefund") {
            $this->_cim->setParameter('transactionId', $authorizeNet["x_trans_id"]); // 6 characters only (conditional)
        }
        //echo "<pre>". print_r( $this->_cim->params, true). "</pre>"; die('jhgds');
        $this->_cim->createCustomerProfileTransactionRequest();
    }

    /*     * *****************************
     * Function Private get_customer_profile
     * This function will get details of authorize.net customer profile
     * @param - int - $customerProfileId -- customer Profile Id
     * return null
     * ****************************** */

    private function _get_customer_profile($customerProfileId) {
        // Payment gateway assigned ID associated with the customer profile
        $this->_cim->setParameter('customerProfileId', $customerProfileId); // Numeric (required)

        $this->_cim->getCustomerProfileRequest();
    }

    /*     * *****************************
     * Function Private get_customer_profile
     * This function will get details of authorize.net customer profile
     * @param - int - $customerProfileId -- customer Profile Id
     * return null
     * ****************************** */

    private function _get_customer_payment_profile($customerPaymentProfileId) {
        // Payment gateway assigned ID associated with the customer profile
        $this->_cim->setParameter('customerPaymentProfileId', $customerPaymentProfileId); // Numeric (required)

        $this->_cim->getCustomerPaymentProfileRequest();
    }

    /*     * *****************************
     * Function Provate _create_customer_payment_profile
     * This function will create authorize.net create customer payment profile
     * @param - array of all authorizeNet parameter
     * return NULL
     * ****************************** */

    private function _create_customer_payment_profile($authorizeNet) {
        // creditCard payment method - (aka creditcard)
        $this->_cim->setParameter('paymentType', 'creditCard');
        $this->_cim->setParameter('cardNumber', $authorizeNet["x_card_num"]);
        $this->_cim->setParameter('expirationDate', $authorizeNet["x_expiration_date"]); // (YYYY-MM)
        $this->_cim->setParameter('billTo_firstName', $authorizeNet["x_first_name"]); // Up to 50 characters (no symbols)
        //$this->_cim->setParameter('email', $authorizeNet["x_email"]); // Up to 255 characters (optional)
        $this->_cim->setParameter('description', $authorizeNet["description"]); // Up to 255 characters (optional)
        $this->_cim->setParameter('customerType', 'individual'); // individual or business (optional)
        $this->_cim->createCustomerPaymentProfileRequest();
    }
    
    
   

}
