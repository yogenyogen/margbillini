<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bll_sale
 *
 * @author Gabriel
 */
class bll_sale extends catalogsale {
    
    public function __construct($id) {
        parent::__construct($id);
    }
    
    /**
     * Create a new Sale
     * 
     * @param array   $Products array of products id
     * @param integer $uid user id
     * @param integer $paymentid payment id
     * @param integer $stateid salestate id
     * @param integer $shipingid shippingmethod id
     * @param integer $cityid city id
     * @param integer $couponid coupon id
     * 
     * @return integer 1 on success,0 on sale creation failed,
     *  -1 payment method unavailable, -2 sale state does not exist, 
     * -3 coupon does not exist, -4 invalid shipping method,
     * -5 shipping not available in city, -6 intented to buy a non existing product.
     */
    public static function createSale($Products, $uid, $paymentid, 
            $stateid, $shippingid, $cityid, $couponid=null)
    {
        $sale = new bll_sale(0);
        $payment = new bll_paymentmethod($paymentid);
        if($payment->Id <= 0 || $payment->Enable != 1)
            return -1;
        $salestate = new bll_salestate($stateid);
        if($salestate->Id <= 0)
            return -2;
        $coupon = new catalogcoupon(0);
        if($couponid !== null && $couponid != "")
        {
            $coupon = new catalogcoupon($couponid);
            if($coupon->Id <= 0)
                return -3;
        }
        $shipping = new bll_shippingmethod($shippingid);
        if($shipping->Id <=0)
            return -4;
        
        if(bll_shippingmethod::checkCity($shippingid, $cityid)!==true)
                return -5;
        
        $prod = array();
        $total=0;
        if(count($Products) <= 0)
            return -6;
        foreach($Products as $pid => $cant)
        {
            $pro = new bll_product($pid);
            if($pro->Id <= 0)
                return -6;
            $prod[]=$pro;
            $price=$pro->SalePrice;
            if($coupon->Id > 0)
                $price = $pro->SalePrice - ($pro->SalePrice*($coupon->Discount/100));
            $total+=$price*$cant;
        }
        $total+= $shipping->Price;
        $dttz = new DateTimeZone(LIB_TIMEZONE);
        $cur_dt = new DateTime("now", $dttz);
        $prop = AuxTools::getPropertiesFromObj($cur_dt);
        $date=$prop['date'];
        //set data
        if($coupon->Id > 0)
        $sale->CouponId = $coupon->Id;
        $sale->UserId = $uid;
        $sale->Date = $date;
        $sale->PaymentMethodId =$payment->Id;
        $sale->SaleStateId = $salestate->Id;
        $sale->ShippingMethodId=$shipping->Id;
        $sale->Total = $total;
        if($sale->insert($Products))
        {
            if($payment->External == 1)
            {
                switch($payment->Id)
                {
                    case 1:
                        self::processPayULatam($sale, $Products, $shipping, $coupon);
                    break;
                }
                return 1;
            }
            //payment is not processed by a third-party we're done.
            else
                return 1;
        }
        return 0;
    }
    
    /**
     * 
     * @param bll_sale $sale sale object
     * @param array   $Products array of products
     * @param bll_shippingmethod $shipping shipping 
     * @param catalogcoupon $coupon
     */
    private static function process2Checkout($sale, $Products, $shipping, $coupon)
    {
        $twoCO = new twoCO();
        $twoCO->ActivateTestMode();
        
        $user= JFactory::getUser($sale->UserId);
        $profile = JUserHelper::getProfile($user->id)->getProperties();
        $profile = $profile['profile'];
        $city = new cities(0);
        $city=$city->find('Name', $profile['city']);
        $province=new province($city->ProvinceId);
        $country = new country($province->CountryId);
        $twoCO->setCardHolderName($user->name);
        $twoCO->setEmail($user->email);
        $twoCO->setCountryCode($country->CountryCode);
        $twoCO->setCity($city->Name);
        $twoCO->setState($province->Name);
        $twoCO->setStreetAddress($profile['address1']);
        $twoCO->setStreetAddress2($profile['address2']);
        $twoCO->setZip($profile['postal_code']);
        $twoCO->setship_Zip($profile['postal_code']);
        $twoCO->setPhone($profile['phone']);
        $twoCO->setship_CardHolderName($user->name);
        $twoCO->setship_CountryCode($country->CountryCode);
        $twoCO->setship_City($city->Name);
        $twoCO->setship_State($province->Name);
        $twoCO->setship_StreetAddress($profile['address1']);
        $twoCO->setship_StreetAddress2($profile['address2']);
        $LangId =  AuxTools::GetCurrentLanguageIDJoomla();
        foreach($Products as $pid => $cant)
        {
            $p = new bll_product($pid);
            $plval = $p->getLanguageValue($LangId);
            $price=$p->SalePrice;
            if($coupon->Id > 0)
                $price = $p->SalePrice - ($p->SalePrice*($coupon->Discount/100));
            $twoCO->AddProduct($plval->Name, $price, $cant, $plval->Description);
        }
        $slval = $shipping->getLanguageValue($LangId);
        $twoCO->AddProduct($slval->Name, $shipping->Price, 1, $slval->Description, 'shipping');
        echo $twoCO->GenerateSell($sale->Id);
        exit;
        
    }
    
    /**
     * 
     * @param bll_sale $sale sale object
     * @param array   $Products array of products
     * @param bll_shippingmethod $shipping shipping 
     * @param catalogcoupon $coupon
     */
    private static function processPayULatam($sale, $Products, $shipping, $coupon)
    {
        $payulatam = new payulatam();
        $payulatam->test=false;//for real time sales
        $LangId =  AuxTools::GetCurrentLanguageIDJoomla();
        $user= JFactory::getUser($sale->UserId);
        $profile = JUserHelper::getProfile($user->id)->getProperties();
        $profile = $profile['profile'];
        $city = new cities(0);
        $city=$city->find('Name', $profile['city']);
        $province=new province($city->ProvinceId);
        $country = new country($province->CountryId);
        $payulatam->buyerEmail = $user->email;
                    
        $payulatam->buyerFullName = $user->name;
        $payulatam->zipCode = $profile['postal_code'];
        $payulatam->discount = $coupon->Discount/100;
        $payulatam->billingAddress = $profile['address1'].' '.$profile['address2'];
        $payulatam->shippingAddress = $profile['address1'].' '.$profile['address2'];
        $payulatam->billingCity = $city->Name;
        $payulatam->shippingCity = $city->Name;
        $payulatam->billingCountry = $country->CountryCode;
        $payulatam->shippingCountry = $country->CountryCode;
        $payulatam->payerEmail = $user->email;
        $payulatam->payerFullName = $user->name;
        $payulatam->payerDocument = $user->name;
        $payulatam->payerPhone = $profile['phone'];
        $payulatam->referenceCode = $sale->Id;
        $payulatam->confirmationUrl='http://freeroamingpanama.com/index.php?option=com_catalog&view=sales&layout=payment_success';
        $payulatam->responseUrl='http://freeroamingpanama.com/index.php?option=com_catalog&task=payulatam_confirmation';
        foreach($Products as $pid => $cant)
        {
            $p = new bll_product($pid);
            $plval = $p->getLanguageValue($LangId);
            $price=$p->SalePrice * $cant;
            if($coupon->Id > 0)
                $price = ($p->SalePrice * $cant) - ($p->SalePrice*($coupon->Discount/100));
            $payulatam->amount+=$price;
            $payulatam->extra1.=$plval->Name."\n";
        }
        $payulatam->extra2.=$shipping->getLanguageValue($LangId)->Name."\n";
        $payulatam->description = "$payulatam->extra1".","."$payulatam->extra2";
        $payulatam->amount+=$shipping->Price;
        $payulatam->signature = $payulatam->generateSignature($payulatam->referenceCode, $payulatam->amount, $payulatam->currency);
        echo $payulatam->generateForm();
        exit;
    }
    
    /**
     * Selects one object from the table depending on which
     * attribute you are looking for.
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the ment field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the ment field[i] and GLue are logic operators:
     * Logic(AND, OR).
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * 
     * @return bll_sale dbobject or false on failure.
     */
    public function find($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::find($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     * Selects one object from the table depending on which
     * attribute you are looking for.
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the ment field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the ment field[i] and GLue are logic operators:
     * Logic(AND, OR).
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * 
     * @return bll_sale dbobject or false on failure.
     */
    public function findAll($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::findAll($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     * Delete the object instance in the database
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the ment field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the ment field[i] and GLue are logic operators:
     * Logic(AND, OR).
     *
     * @warning if the funtion is used without parameters
     * there`s only a successful delete if the object
     * Id is found in the database.
     *
     * @return boolean|bll_sale Not false on success.
     */
    public function delete($field = "", $value = "") {
        return parent::delete($field, $value);
    }
    
    /**
     * Insert the object to the database
     *
     * @return bll_sale not false on success.
     */
    public function insert($Products=array()) {
        $sale= parent::insert();
        if($sale===false)
            return false;
        foreach($Products as $pid => $cant)
        {
            $pro = new bll_product($pid);
            if($pro->Id <= 0)
                continue;
            for($i=0; $i<$cant; $i++)
            {
                $newpsale = new catalogproductsale(0);
                $newpsale->SaleId=$sale->Id;
                $newpsale->ProductId=$pro->Id;
                $newpsale->insert();
            }
        }
        return $sale;
        
    }
    
    /**
     *
     * @return bll_sale not false on success. 
     */
    public function update()
    {
        return parent::update();
    }
    
    /**
     * 
     * @param integer $sid sale id
     * @param string $format format of the array of products
     * could be hash for $productid=>$quantity or array
     * format array($productsId_array,$quantity_array) 
     * where $result[0][0] = product id and 
     * $result[1][0] = quantity
     * 
     * @return array
     */
    public static function getProductsFromSale($sid, $format='array')
    {
        $return = array();
        $findProducts = new catalogproductsale(0);
        $searchRes=$findProducts->findAll('SaleId', $sid);
        switch($format)
        {
            case 'hash':
                $products = array();
                foreach($searchRes as $cps)
                {
                    $added=false;
                    if(isset($products[$cps->ProductId]))
                    {
                        $products[$cps->ProductId]=$products[$cps->ProductId]+1;
                    }
                    else
                    {
                        $products[$cps->ProductId]=1;
                    }
                }
                $return = $products;
            break;
            default:
                $products = array();
                $cant=array();
                foreach($searchRes as $cps)
                {
                    $added=false;
                    for($k=0; $k < count($products); $k++)
                    {
                        if($products[$k] == $cps->ProductId)
                        {
                            $cant[$k]=$cant[$k]+1;
                            $added=true;
                        }
                    }
                    if(!$added)
                    {
                        $products[count($products)]=$cps->ProductId;
                        $cant[count($cant)]=1;
                    }
                }
                $return = array($products,$cant);
            break;
        }
        return $return;
        
    }
    
}

?>
