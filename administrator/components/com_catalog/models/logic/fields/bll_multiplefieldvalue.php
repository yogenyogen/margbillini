<?php

/**
 * Description of bll_multiplefieldvalue
 *
 * @author Gabriel
 */
class bll_multiplefieldvalue extends catalogmultiplefieldvalue {
    
    public function __construct($id) {
        return parent::__construct($id);
    }
    
    /**
     * Selects one object from the table depending on which
     * attribute you are looking for.
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * 
     * @return bll_multiplefieldvalue dbobject or false on failure.
     */
    public function find($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::find($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     * Selects multiples object from the table depending on which
     * attribute you are looking for.
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * 
     * @return bll_multiplefieldvalue dbobject or false on failure.
     */
    public function findAll($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::findAll($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     * Delete the object instance in the database
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).
     *
     * @warning if the funtion is used without parameters
     * there`s only a successful delete if the object
     * Id is found in the database.
     *
     * @return boolean|bll_multiplefieldvalue Not false on success.
     */
    public function delete($field = "", $value = "") {
        return parent::delete($field, $value);
    }
    
    /**
     * Insert the object to the database
     *
     * @return bll_multiplefieldvalue not false on success.
     */
    public function insert() 
    {
        $this->setAttributes($_POST);
        $obj= parent::insert();
        if($obj !== false)
        {
            $id = $obj->Id;
            $field = new bll_field($obj->FieldId);
            if($field->MultiLanguage == 1)
                $this->addLangValue($id);
            return $this;
        }
        return false;
    }
    
    /**
     * Updates the object in DB.
     * @return bll_multiplefieldvalue not false on success 
     */
    public function update() {
        
        $this->setAttributes($_POST);
        $obj= parent::update();
        if($obj !== false)
        {
            $id = $obj->Id;
            $field = new bll_field($obj->FieldId);
            if($field->MultiLanguage == 1)
                $this->addLangValue($id);
            return $this;
        }
        return false;
    }
    
    /**
     * Add the language values
     * @param int $id id of the main object 
     */
    private function addLangValue($id)
    {
        //deleting lang values
        $cfl=new catalogmultiplefieldvaluelang(0);
        $cfl->delete('MultipleFieldValueId',$id);
        //adding lang values
        $langs = languages::GetLanguages();
        foreach($langs as $lang)
        {
            $lang_suffix = "_".$lang->lang_id;
            $lv = new catalogmultiplefieldvaluelang(0);
            $lv->MultipleFieldValueId = $id;
            $lv->Value = $_POST['Value'.$lang_suffix];
            $lv->LangId=$lang->lang_id;
            $lv->insert();
        }
    }
    
    /**
     * Get the language value
     * @param type $LangId id of the language
     * @return catalogmultiplefieldvaluelang catalogproductfieldvalue value object.
     */
    public function getLanguageValue($LangId)
    {
        $language = new languages($LangId);
        if($language->lang_id <= 0)
            return new catalogmultiplefieldvaluelang(-1);
        $langval  = new catalogmultiplefieldvaluelang(-1);
        
        return $langval->find(
                              array(
                                  array('MultipleFieldValueId','='),
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
     * @return catalogmultiplefieldvaluelang array of catalogproductfieldvalue value object.
     */
    public function getLanguageValues()
    {
        $langval  = new catalogmultiplefieldvaluelang(-1);
        return $langval->findAll(
                              array(
                                  array('MultipleFieldValueId','=')
                                   ), 
                              array(
                                  array($this->Id,null)
                                  )
                             );
        
    }
    
}

?>
