<?php // googple api to validate address.
    public function address_validate($input_araay = array())
    {
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
//            echo '<pre>';print_r($decoded_array);die;
            
            if(isset($decoded_array->results[0]))
            {
                
                $address_components = $decoded_array->results[0]->address_components;
                foreach($address_components as $obj=>$item)
                {
                    
                    if($item->types[0] == "locality")
                    {
//                        if(strcasecmp($input_araay['city'],$item->short_name) == 0)
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
                    return 100; exit;
                }
                else 
                {
                    return "Address details are invalid. City and Zip code doesn't match";
                }
            }
            else
            {
                return "Address details are invalid. City and Zip code doesn't match";
            }
        }
        else
        {
            return "Invalid input provided for the address validation.";
        }
    }
	?>