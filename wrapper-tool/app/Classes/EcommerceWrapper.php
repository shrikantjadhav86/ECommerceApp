<?php

namespace App\Classes;

class EcommerceWrapper implements EcommerceInterface {

	private $config = array();
	
	//public $ecommerceClient; 
	/**
	 * get object instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	 	//$this->ecommerceClient = new ecommerceClient();
		
	}
	public function setConfig($config)
	{
		$this->config = $config;
	}
	/**
     * Get all products
     *
     * @return \Illuminate\Http\Response
     */
    public function getProducts()
    {
        return "hi";
    }

    /**
     * Get product details
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductDetails()
    {
        return "hi details";
    }
	
	/**
	* Display users or customers of domain
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCustomers()
    {
        return "hi";
    }

	/**
	* Get All collections
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCollections()
    {
		return "hi";
    }
	
	/**
	* Get All collection products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCollectionProducts()
    {
		return "hi";
    }
	
	/**
	* Get All cart products
	*
	*@return \Illuminate\Http\Response
	**/
	/*public function getCartProducts()
    {
		return "hi";
    }*/
	
	/**
	* Get All checkout details
	*
	*@return \Illuminate\Http\Response
	**/
	public function getCheckout()
    {
		return "hi";
    }
	
	/**
	* Get ordered products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getOrders()
    {
		return "hi";
    }
	
	
	public function getSingleOrders() 
	{
		return "hi";	
	}	
	
	/**
	* create order
	*
	*@return \Illuminate\Http\Response
	**/
	public function createOrders()
    {
	 	return "hi";
	}
	
	/**
	* Get shipping products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getShippedProducts()
    {
		return "hi";
    }
	
	/**
	* Get shipping products
	*
	*@return \Illuminate\Http\Response
	**/
	public function getShippedProducts()
    {
		return "hi";
    }	
}


?>