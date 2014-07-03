<?php
require_once BASE_DIR . LIBS . DB . DBOBJECT;

class languages extends dbobject {

    public $lang_id = 0;
    public $lang_code="";
    public $title="";
    public $title_native="";
    public $sef="";
    public $image="";
    public $description="";
    public $metakey="";
    public $metadesc="";
    public $sitenam="";
    public $published=1;
    public $access=0;
    public $ordering=0;

    public function __construct($id) {
        $cname = get_class();
        return parent::__construct($id, $cname,  null, "lang_id");
    }
    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $DESC
     * @param type $order_field
     * @param type $lower_limit
     * @param type $upper_limit
     * @return languages 
     */
    public function findAll($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::findAll($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     * 
     * @param type $field
     * @param type $value
     * @param type $DESC
     * @param type $order_field
     * @param type $lower_limit
     * @param type $upper_limit
     * @return languages 
     */
    public function find($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {
        return parent::find($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);
    }
    
    /**
     *
     * @return languages 
     */
    public function insert() {
        return parent::insert();
    }
    
    /**
     *
     * @return languages
     */
    public function update() {
        return parent::update();
    }
    
    /**
     * Gets all the languages defined in joomla
     * @return languages array of all languages
     */
    public static function GetLanguages()
    {
        $lang = new languages(0);
        return $lang->findAll();
    }

}

