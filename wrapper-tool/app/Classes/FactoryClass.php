<?php
namespace App\Classes;

class FactoryClass {

	 /**
     * Create object of class
     *
     * @return $obj (class)
     */
	
    public function getInstance($instance)
    {
	
		//it define class path to create its object
		$className = 'App\\Classes\\' . $instance;
		$obj =  new $className;	
       /* if($instance == 1) {
		 	//get shopify object
			$obj = new ShopifyWrapper();
		}elseif($instance == 2) {
			//get other ecommerce object
			$obj = new EcommerceWrapper();			
		}*/
		return $obj;
    }
}


?>