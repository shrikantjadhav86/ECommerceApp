<?php
namespace App\Classes;

class FactoryClass {

	 /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	
    public function getInstance($instance)
    {
        if($instance == 1) {
		 	//get shopify object
			$obj = new ShopifyWrapper();
		}elseif($instance == 2) {
			//get other ecommerce object
			$obj = new EcommerceWrapper();			
		}
		return $obj;
    }
}


?>