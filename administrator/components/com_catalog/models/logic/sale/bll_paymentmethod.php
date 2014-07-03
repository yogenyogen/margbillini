<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of bll_paymentmethod
 *
 * @author Gabriel
 */
class bll_paymentmethod extends catalogpaymentmethod {
    
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
     * @return bll_paymentmethod dbobject or false on failure.
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
     * @return bll_paymentmethod dbobject or false on failure.
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
     * @return boolean|bll_paymentmethod Not false on success.
     */
    public function delete($field = "", $value = "") {
        return parent::delete($field, $value);
    }
    
    /**
     * Insert the object to the database
     *
     * @param array $cities array of city Id
     * 
     * @return bll_salestate not false on success.
     */
    public function insert() {
        $this->setAttributes($_POST);
        $ret= parent::insert();
        
        $this->addLangValue($ret->Id);
        return $ret;
    }
    
    /**
     * @param array $cities array of city Id
     * 
     * @return bll_salestate not false on success. 
     */
    public function update()
    {
        $this->setAttributes($_POST);
        $ret= parent::update();
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
        $cfl=new catalogpaymentmethodlang(0);
        $cfl->delete('PaymentMethodId',$id);
        //adding lang values
        $langs = languages::GetLanguages();
        foreach($langs as $lang)
        {
            $lang_suffix = "_".$lang->lang_id;
            $lv = new catalogpaymentmethodlang(0);
            $lv->PaymentMethodId = $id;
            $lv->Name = $_POST['Name'.$lang_suffix];
            $lv->Description = $_POST['Description'.$lang_suffix];
            $lv->LangId=$lang->lang_id;
            $lv = $lv->insert();

        }

    }

    

    

    /**
     * Get the language value
     * @param type $LangId id of the language
     * @return catalogpaymentmethodlang catalogpaymentmethodlang value object.
     */
    public function getLanguageValue($LangId)
    {

        $language = new languages($LangId);

        if($language->lang_id <= 0)
            return new catalogpaymentmethodlang(-1);

        $langval  = new catalogpaymentmethodlang(-1);

        return $langval->find(
                              array(
                                  array('PaymentMethodId','='),
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
     * @return catalogpaymentmethodlang array of catalogpaymentmethodlang value object.
     */
    public function getLanguageValues()
    {

        $langval  = new catalogpaymentmethodlang(-1);
        return $langval->findAll(
                              array(
                                  array('PaymentMethodId','=')
                                   ), 
                              array(
                                  array($this->Id,null)
                                  )
                             );

    }
    
   
    
}

?>
