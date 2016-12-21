<?php
/**
 * CodeIgniter Authorize Dot Net API Class
 *
 * Permits to make Authorize.Net Integration via API
 *
 * @package	CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author	Rohit Patil (rohitpatil30) @ Codaemon Softwares, Pune
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH. 'third_party/AuthDotNet/autoload.php';

class Authnet_api {

    protected $CI;  // CodeIgniter instance
    protected $api_login;  // Merchant API Login Id
    protected $transaction_key;  // Merchant Account Trans key
    public $sandbox;  // true means Sandbox & false means Live

    public function __construct($data=array()) {
        if(isset($data['api_login']) && isset($data['transaction_key']) && trim($data['api_login'])!="" && trim($data['transaction_key'])!="") {
            $this->CI =& get_instance();
            $this->api_login=trim($data['api_login']);
            $this->transaction_key=trim($data['transaction_key']);
            if(isset($data['sandbox'])) $this->sandbox=trim($data['sandbox']);
            if(isset($data['timezone']) && trim($data['timezone'])!="") date_default_timezone_set(trim($data['timezone']));
        }
    }

    public function CreateSubscription($subscription_data=array(),$refId='') {
        if(count($subscription_data)>0) {
            $subscription = new AuthorizeNet_Subscription;
            foreach($subscription_data as $key => $value) {
                if($key=='name' && $value=='') $subscription->$key='Test-Subscription'.date('Y-m-d_H:i:s');
                else if($key=='intervalLength' && ($value=='' || $value<7) ) $subscription->$key='7';
                else if($key=='intervalUnit' && $value=='') $subscription->$key='days';
                else if($key=='startDate') $subscription->$key=date('Y-m-d',strtotime($value));
                //else if($key=='creditCardExpirationDate') $subscription->$key=date('Y-m',strtotime($value));
                else if($key=='customerId' && $value=='') $subscription->$key=rand(1, 1000000);
                else if($key=='customerEmail' && $value=='') $subscription->$key='sample@demo.com';
                else if($key=='billToFirstName' && $value=='') $subscription->$key='Customer-Fname';
                else if($key=='billToLastName' && $value=='') $subscription->$key='Customer-Lname';
                else $subscription->$key = $value;
            }

            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->createSubscription($subscription);
            return $response;
        } else return false;
    }
    
    public function test_refund()
    {
//  $transaction    = new AuthorizeNetTransaction;
//  $transaction->amount = "69.99";
//	$transaction->customerProfileId = '';
//	$transaction->customerPaymentProfileId = '';
//	$transaction->transId = 60008995263; // original transaction ID
//	$transaction->request_type  = "createCustomerProfileTransaction"; // original transaction ID
        $request    = new AuthorizeNetARB;
        $data       = array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
        $request->setValues($data);
        $response = $request->refundTransaction($transactionId=60008995300);
        echo '<pre>'; print_r($response); exit;
    }
    
    public function refund_arb_transaction($ccdigits=0,$transAmt=0,$transId=0)
    {
        $request    = new AuthorizeNetARB;
        $data       = array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
        $request->setValues($data);
        $response = $request->refundTransaction($ccdigits,$transAmt,$transId);
        return $response;
    }
    
    public function CreateSubscriptionFromCustomerProfile($subscription_data=array(),$refId='') {
        if(count($subscription_data)>0 && isset($subscription_data['customerProfileId']) && 
                $subscription_data['customerProfileId']!='' && isset($subscription_data['customerPaymentProfileId']) && 
                $subscription_data['customerPaymentProfileId']!='') 
            {
            $subscription = new AuthorizeNet_SubscriptionFromCustProfile;
            foreach($subscription_data as $key => $value) 
            {
                if($key=='name' && $value=='') $subscription->$key='Prohealthspan'.date('Y-m-d_H:i:s');
                else if($key=='intervalLength' && ($value=='' || $value<7) ) $subscription->$key='1';
                else if($key=='intervalUnit' && $value=='') $subscription->$key='month';
                else if($key=='startDate') $subscription->$key=date('Y-m-d',strtotime($value));
                else $subscription->$key = $value;
            }

            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->CreateSubscriptionFromCustomerProfile($subscription);
            return $response;
        } else return false;
    }

    public function GetSubscriptionList($subscription_data=array(),$refId='') {
        if(count($subscription_data)>0) {
            $limit=''; if(isset($subscription_data['limit']) && $subscription_data['limit']!='') $limit=$subscription_data['limit'];
            $offset=''; if(isset($subscription_data['offset']) && $subscription_data['offset']!='') $offset=$subscription_data['offset'];
            $orderBy=''; if(isset($subscription_data['orderBy']) && $subscription_data['orderBy']!='') $orderBy=$subscription_data['orderBy'];
            $orderDescending=''; if(isset($subscription_data['orderDescending']) && $subscription_data['orderDescending']!='') $orderDescending=$subscription_data['orderDescending'];
            $searchType=''; if(isset($subscription_data['searchType']) && $subscription_data['searchType']!='') $searchType=$subscription_data['searchType'];

            $paging='';
            if($limit!='' || $offset!='') {
                $paging=new AuthorizeNetSubscriptionListPaging();
                if($limit!='') $paging->limit=$limit;
                if($offset!='') $paging->offset=$offset;
            }

            $sorting='';
            if($limit!='' || $offset!='') {
                $sorting=new AuthorizeNetSubscriptionListSorting();
                if($orderBy!='') $sorting->orderBy=$orderBy;
                if($orderDescending!='') $sorting->orderDescending=$orderDescending;
            }

            $getSubscriptionList = new AuthorizeNetGetSubscriptionList;
            if($searchType!='') $getSubscriptionList->searchType=$searchType;
            if($paging!='') $getSubscriptionList->paging=$paging;
            if($sorting!='') $getSubscriptionList->sorting=$sorting;

            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->getSubscriptionList($getSubscriptionList);
            return $response;
        } else return false;
    }

    public function UpdateSubscription($subscription_id,$subscription_data=array(),$refId='') {
        if($subscription_id!='' && count($subscription_data)>0) {
            $subscription = new AuthorizeNet_Subscription;
            foreach($subscription_data as $key => $value) {
                if($key=='name' && $value=='') $subscription->$key='Test Subscription';
                else if($key=='intervalLength' && ($value=='' || $value<7) ) $subscription->$key='7';
                else if($key=='intervalUnit' && $value=='') $subscription->$key='days';
                else if($key=='startDate') $subscription->$key=date('Y-m-d',strtotime($value));
                //else if($key=='creditCardExpirationDate') $subscription->$key=date('Y-m',strtotime($value));
                else if($key=='customerId' && $value=='') $subscription->$key=rand(1, 1000000);
                else if($key=='customerEmail' && $value=='') $subscription->$key='sample@demo.com';
                else if($key=='billToFirstName' && $value=='') $subscription->$key='Customer Fname';
                else if($key=='billToLastName' && $value=='') $subscription->$key='Customer Lname';
                else $subscription->$key = $value;
            }

            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->updateSubscription($subscription_id,$subscription);
            return $response;
        } else return false;
    }

    public function GetSubscriptionDetails($subscription_id,$refId='') {
        if($subscription_id!='') {
            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->getSubscriptionDetails($subscription_id);
            return $response;
        } else return false;
    }

    public function GetSubscriptionStatus($subscription_id,$refId='') {
        if($subscription_id!='') {
            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->getSubscriptionStatus($subscription_id);
            return $response;
        } else return false;
    }

    public function CancelSubscription($subscription_id,$refId='') {
        if($subscription_id!='') {
            $request = new AuthorizeNetARB;
            if($refId!='') $request->setRefId($refId);
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response = $request->cancelSubscription($subscription_id);
            return $response;
        } else return false;
    }

    public function GetDatewiseBatchList($batch_data=array()) {
        if(isset($batch_data['from_date']) && $batch_data['from_date']!='') $from_date=$batch_data['from_date'];
        else $from_date=date('Y-m-d');
        if(isset($batch_data['to_date']) && $batch_data['to_date']!='') $to_date=$batch_data['to_date'];
        else $to_date=date('Y-m-d');
        if(isset($batch_data['utc'])) $utc=$batch_data['utc'];
        else $utc=true;

        $from_date_arr=explode('-',$from_date);
        $from_year=$from_date_arr[0];
        $from_month=$from_date_arr[1];
        $from_day=$from_date_arr[2];
        $to_date_arr=explode('-',$to_date);
        $to_year=$to_date_arr[0];
        $to_month=$to_date_arr[1];
        $to_day=$to_date_arr[2];
        $firstSettlementDate=substr(date('c',mktime(0,0,0,(int)$from_month,(int)$from_day,(int)$from_year)),0,-6);
        $lastSettlementDate=substr(date('c',mktime(0,0,0,(int)$to_month,(int)$to_day,(int)$to_year)),0,-6);

        $request=new AuthorizeNetTD;
        $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
        $request->setValues($data);
        $response=$request->getSettledBatchList(true, $firstSettlementDate, $lastSettlementDate);
        return $response;
    }

    public function GetDatewiseTransaction($trans_data=array()) {
        if(isset($trans_data['from_date']) && trim($trans_data['from_date'])!='') {
            $from_date=date('Y-m-d',strtotime(trim($trans_data['from_date'])));
            if(isset($trans_data['to_date']) && trim($trans_data['to_date'])!='') $to_date=date('Y-m-d',strtotime(trim($trans_data['to_date'])));
            else $to_date=date('Y-m-d');
        } else $from_date=$to_date=date('Y-m-d');

        $batch_data=array(''=>true,'from_date'=>$from_date,'to_date'=>$to_date);
        $response=$this->GetDatewiseBatchList($batch_data);
        $batches=$response->xpath("batchList/batch");
        $transactions=array();
        foreach($batches as $batch) {
            $batch_id=(string)$batch->batchId;
            $tran_list=$this->GetBatchwiseTransaction($batch_id);
            $transactions=array_merge($transactions, $tran_list->xpath("transactions/transaction"));
        }
        return $transactions;
    }

    public function GetBatchwiseTransaction($batch_id) {
        if($batch_id!='') {
            $request=new AuthorizeNetTD;
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response=$request->getTransactionList($batch_id);
            return $response;
        } else return false;
    }

    public function GetTransactionDetails($trans_id) {
        if($trans_id!='') {
            $request=new AuthorizeNetTD;
            $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
            $request->setValues($data);
            $response=$request->getTransactionDetails($trans_id);
            return $response;
        } else return false;
    }
    
    public function GetUnsettledTransactionList()
    {
        $request=new AuthorizeNetTD;
        $data=array('api_login'=>$this->api_login,'transaction_key'=>$this->transaction_key,'sandbox'=>$this->sandbox);
        $request->setValues($data);
        $response=$request->getUnsettledTransactionList();
        return $response;
    }

    
}
