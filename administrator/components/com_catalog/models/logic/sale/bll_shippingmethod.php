<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bll_shippingmethod
 *
 * @author Gabriel
 */
class bll_shippingmethod extends catalogshippingmethod {
    
    public function __construct($id) {
        parent::__construct($id);
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
     * @return bll_shippingmethod dbobject or false on failure.
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
     * @return bll_shippingmethod dbobject or false on failure.
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
     * @return boolean|bll_shippingmethod Not false on success.
     */
    public function delete($field = "", $value = "") {
        return parent::delete($field, $value);
    }
    
    /**
     * Insert the object to the database
     *
     * @param array $cities array of city Id
     * 
     * @return bll_shippingmethod not false on success.
     */
    public function insert($cities=array()) {
        $this->setAttributes($_POST);
        $ret= parent::insert();
        $sc = new catalogshippingcities(0);
        $sc->delete('ShippingMethodId', $ret->Id);
        if(is_array($cities))
        {
            foreach($cities as $cid)
            {
                $sc = new catalogshippingcities(0);
                $sc->ShippingMethodId=$ret->Id;
                $sc->CityId=$cid;
                $sc->insert();
            }
        }
        $this->addLangValue($ret->Id);
        return $ret;
    }
    
    /**
     * @param array $cities array of city Id
     * 
     * @return bll_shippingmethod not false on success. 
     */
    public function update($cities=array())
    {
        $this->setAttributes($_POST);
        $ret= parent::update();
        $sc = new catalogshippingcities(0);
        $sc->delete('ShippingMethodId', $ret->Id);
        if(is_array($cities))
        {
            foreach($cities as $cid)
            {
                $sc = new catalogshippingcities(0);
                $sc->ShippingMethodId=$ret->Id;
                $sc->CityId=$cid;
                $sc->insert();
            }
        }
        $this->addLangValue($ret->Id);
        return $ret;
    }
    
    /**
     * Add the language values
     * @param int $id id of the main object 
     */
    private function addLangValue($id)
    {
        //deleting lang values
        $cfl=new catalogshippingmethodlang(0);
        $cfl->delete('ShippingMethodId',$id);
        //adding lang values
        $langs = languages::GetLanguages();
        foreach($langs as $lang)
        {
            $lang_suffix = "_".$lang->lang_id;
            $lv = new catalogshippingmethodlang(0);
            $lv->ShippingMethodId = $id;
            $lv->Name = $_POST['Name'.$lang_suffix];
            $lv->Description = $_POST['Description'.$lang_suffix];
            $lv->LangId=$lang->lang_id;
            $lv = $lv->insert();

        }

    }

    

    

    /**
     * Get the language value
     * @param type $LangId id of the language
     * @return catalogshippingmethodlang catalogshippingmethodlang value object.
     */
    public function getLanguageValue($LangId)
    {

        $language = new languages($LangId);

        if($language->lang_id <= 0)
            return new catalogshippingmethodlang(-1);

        $langval  = new catalogshippingmethodlang(-1);

        return $langval->find(
                              array(
                                  array('ShippingMethodId','='),
                                  array('LangId','=')
                                   ), 
                              array(
                                  array($this->Id,null),
                                  array($LangId,'AND')
                                  )
                             );

    }

    

    /**
     * Get the language value
     * @param type $LangId id of the language
     * @return catalogshippingmethodlang array of catalogshippingmethodlang value object.
     */
    public function getLanguageValues()
    {

        $langval  = new catalogshippingmethodlang(-1);
        return $langval->findAll(
                              array(
                                  array('ShippingMethodId','=')
                                   ), 
                              array(
                                  array($this->Id,null)
                                  )
                             );

    }
    
    /**
     * Checks that the shipping method is from that city
     * 
     * @param integer $method method id
     * @param integer $cid city id
     * @return boolean true if the method is available in that city
     */
    public static function checkCity($method, $cid)
    {
        $sm = new bll_shippingmethod($method);
        if($sm->Global == '1')
            return true;
        $sc = new catalogshippingcities(0);
        $result=$sc->findAll(
                              array(
                                  array('ShippingMethodId','='),
                                  array('CityId','=')
                                   ), 
                              array(
                                  array($method,null),
                                  array($cid,'AND')
                                  )
                             );
        if(count($result) > 0)
            return true;
        return false;
    }
    
    /**
     * 
     * @param type $cid
     * @return bll_shippingmethod array of shipping methods
     */
    public static function getMethodsFromCityId($cid)
    {
        $sc = new catalogshippingcities(0);
        $result=$sc->findAll('CityId', $cid, true, 'ShippingMethodId');
        $methods=array();
        foreach($result as $obj)
        {
            $methods[]= ($obj->ShippingMethodId);
        }
        return $methods;
    }
    
    /**
     * 
     * @param integer $sid
     * @return cities array of cities
     */
    public static function getCitiesFromMethod($sid)
    {
        $sc = new catalogshippingcities(0);
        $result=$sc->findAll('ShippingMethodId', $sid);
        $cities=array();
        foreach($result as $obj)
        {
            $cities[]=($obj->CityId);
        }
        return $cities;
    }
    
}

?>
