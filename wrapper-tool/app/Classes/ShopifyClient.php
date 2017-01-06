<?php
namespace App\Classes;

use Exception;

class ShopifyClient {
    /*public $shop_domain = 'angels-shoppy.myshopify.com';
    private $api_key = '869da60badf4cbd73459af8508b2fde7';
    private $password = '8aa58776797e42128a4fe7dbe31afd90';
 	public $shop_domain = 'teststoredomain.myshopify.com';
    private $api_key = 'a374024b6ccf1836a0943b25f81dde32';
    private $password = '20ff272e7b82fc83e71e0d54b1c7fbfd';*/
	
		
	public $shop_domain;
    private $api_key;
    private $password;
	
    private $token;
    private $secret = '';
    private $last_response_headers = null;
	
	/**
     * to get domain name of store
     *@returns  $domain
     * 
     */
	public function __getDomain($domain) {
      	return $domain;
  	}
		
	/**
     * to get key of store
     *@returns  $key
     * 
     */
	public function __getKey($key) {
      	 if (property_exists($this, $key)) {
      		return $this->$key;
    	}
  	}	
	
	/**
     * to get password of store
     *@returns  $password
     * 
     */
	public function __getPassword($password) {
      	 if (property_exists($this, $password)) {
      		return $this->$password;
    	}
  	}
	
	
	/**
     * to set domain of store
     *@params  $domain
     * 
     */
	public function __setDomain($arr,$domain) {		
		 $this->shop_domain = $domain;
  	}
	
	
	/**
     * to set key of store
     *@params  $key
     * 
     */
	public function __setKey($arr,$key) {
		 $this->api_key = $key;
  	}
	
	
	/**
     * to set password of store
     *@params  $password
     * 
     */
	public function __setPassword($arr,$password) {
		 $this->password = $password;
  	}
	
	
	/**
     * to set store variables
	 *@params $shop_domain, $token, $api_key, $password
     */
    public function init($shop_domain, $token, $api_key, $password) {
            $this->name = "ShopifyClient";
            $this->shop_domain = $shop_domain;
            $this->token = $token;
            $this->api_key = $api_key;
            $this->password = $password;
    }
    
	/**
     * to call shopify api
	 *@params $methods and $path
	 *@returns $response from shopify
     */
    public function call($method, $path, $params=array())
    {
        $baseurl = "https://{$this->api_key}:{$this->password}@{$this->shop_domain}/";
        //$baseurl = "https://$this->shop_domain/";

        $url = $baseurl.ltrim($path, '/');

        $query = in_array($method, array('GET','DELETE')) ? $params : array();
        $payload = in_array($method, array('POST','PUT')) ? json_encode($params) : array();
        $request_headers = in_array($method, array('POST','PUT')) ? array("Content-Type: application/json; charset=utf-8", 'Expect:') : array();

        // add auth headers
        //$request_headers[] = 'X-Shopify-Access-Token: ' . $this->token;
        
        $response = $this->curlHttpApiRequest($method, $url, $query, $payload, $request_headers);
        $response = json_decode($response, true);
        return $response; exit;
        if (isset($response['errors']) or ($this->last_response_headers['http_status_code'] >= 400))
                throw new ShopifyApiException($method, $path, $params, $this->last_response_headers, $response);

        return (is_array($response) and (count($response) > 0)) ? array_shift($response) : $response;
    }
    
	
	/**
     * to get response from shopify via curl
	 *
	 *@returns curl response
     */
    private function curlHttpApiRequest($method, $url, $query='', $payload='', $request_headers=array())
    {
        $url = $this->curlAppendQuery($url, $query);
        $ch = curl_init($url);
        $this->curlSetopts($ch, $method, $payload, $request_headers);
        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno) throw new ShopifyCurlException($error, $errno);
        list($message_headers, $message_body) = preg_split("/\r\n\r\n|\n\n|\r\r/", $response, 2);
        $this->last_response_headers = $this->curlParseHeaders($message_headers);

        return $message_body;
    }
    
	
	/**
     * to append curl query
	 *
	 *@returns query response
     */
    private function curlAppendQuery($url, $query)
    {
        if (empty($query)) return $url;
        if (is_array($query)) return "$url?".http_build_query($query);
        else return "$url?$query";
    }
	
	/**
     * to set curl options
	 *
	 *
     */
    private function curlSetopts($ch, $method, $payload, $request_headers)
    {
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      //  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'ohShopify-php-api-client');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, $method);
        if (!empty($request_headers)) curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

        if ($method != 'GET' && !empty($payload))
        {
                if (is_array($payload)) $payload = http_build_query($payload);
                curl_setopt ($ch, CURLOPT_POSTFIELDS, $payload);
        }
    }
    /**
     * to set curl header
	 *
	 *@returns header response
     */
    private function curlParseHeaders($message_headers)
    {
        $header_lines = preg_split("/\r\n|\n|\r/", $message_headers);
        $headers = array();
        list(, $headers['http_status_code'], $headers['http_status_message']) = explode(' ', trim(array_shift($header_lines)), 3);
        foreach ($header_lines as $header_line)
        {
                list($name, $value) = explode(':', $header_line, 2);
                $name = strtolower($name);
                $headers[$name] = trim($value);
        }

        return $headers;
    }
}

class ShopifyCurlException extends Exception { }
	/**
     * if shoipfy respond exception response
	 *
	 *@returns exception response
     */
class ShopifyApiException extends Exception
{
	protected $method;
	protected $path;
	protected $params;
	protected $response_headers;
	protected $response;
	
	function __construct($method, $path, $params, $response_headers, $response)
	{
		$this->method = $method;
		$this->path = $path;
		$this->params = $params;
		$this->response_headers = $response_headers;
		$this->response = $response;
		
		parent::__construct($response_headers['http_status_message'], $response_headers['http_status_code']);
	}

	function getMethod() { return $this->method; }
	function getPath() { return $this->path; }
	function getParams() { return $this->params; }
	function getResponseHeaders() { return $this->response_headers; }
	function getResponse() { return $this->response; }
}
?>