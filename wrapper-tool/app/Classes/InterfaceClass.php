<?php

namespace App\Classes;


/**
     * this is interface of class shopifywrapper and other ecommerce wrapper 
	 *
     */
interface EcommerceInterface {
	public function setConfig($config);
	public function setStore($store);
	public function getProducts();
	public function getProductDetails();
	public function getCustomers();
	public function getCollections();
	public function getCollectionProducts();
	public function getCartProduct();
	public function getCheckout();
	public function getOrders();
	public function getShippedProducts();
	public function createOrders($json);
	public function getSingleOrders();
	public function register($json);
	public function userOrder($id);	
}


?>