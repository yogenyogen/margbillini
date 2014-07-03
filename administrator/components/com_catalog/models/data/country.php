<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of country
 *
 * @author Gabriel
 */
class country extends dbobject {
     
    public $Id=0;
    public $Name="";
    public $CountryCode="";
        
    public function __construct($id) {
        parent::__construct($id, get_class());
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
     * @return country dbobject or false on failure.
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
     * @return country dbobject or false on failure.
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
     * @return boolean|country Not false on success.
     */
    public function delete($field = "", $value = "") {
        return parent::delete($field, $value);
    }
    
    /**
     * Insert the object to the database
     *
     * @return country not false on success.
     */
    public function insert() {
        return parent::insert();
    }
    
    /**
     *
     * @return country not false on success. 
     */
    public function update()
    {
        return parent::update();
    }
    
    /**
     * Gets the location tree 
     * 
     * @return array hash of countries array(Id=>data) in form of a tree example:
     * Array
     * (
     *    [1] => Array
     *        (
     *            [provinces] => Array
     *                (
     *                    [1] => Array
     *                        (
     *                            [cities] => Array
     *                                (
     *                                    [2] => Array
     *                                        (
     *                                            [sectors] => Array
     *                                                (
     *                                                    [1] => Array
     *                                                        (
     *                                                            [name] => Panamá
     *                                                        )
     *                                                )
     *                                            [name] => Panamá
     *                                        )
     *                                )
     *                            [name] => Panamá
     *                        )
     *                )
     *            [name] => Panamá
     *            [code] => PAN
     *        )
     * )
     */
    public static function getLocationTree()
    {
        $country = new country(0);
        $countries = $country->findAll(null,null,false,'Name');
        $locations= array();
        foreach($countries as $c)
        {
            $location_index = $c->Id;
            $location_data = array();
            $pr = new province(0);
            $provinces = $pr->findAll('CountryId',$c->Id,false,'Name');
            foreach($provinces as $p)
            {
                $location_pro_index=$p->Id;
                $location_pro_data = array();
                $_ci = new cities(0);
                $_cities = $_ci->findAll('ProvinceId',$p->Id,false,'Name');
                foreach($_cities as $___c)
                {
                    $location_ci_index=$___c->Id;
                    $location_ci_data = array();
                    $_sector = new sector(0);
                    $_sectors = $_sector->findAll('CityId',$___c->Id,false,'Name');
                    foreach($_sectors as $_s)
                    {
                        $location_ci_data[]=array('name'=>$_s->Name, 'id'=>$_s->Id);
                    }
                    $location_pro_data[]=array('sectors'=>$location_ci_data,'name'=>$___c->Name, 'id'=>$location_ci_index);
                }
                $location_data[]=array('cities'=>$location_pro_data, 'name'=>$p->Name, 'id'=>$location_pro_index);
            }
            $locations[]=array('provinces'=>$location_data, 'id'=>$location_index, 'name'=>$c->Name, 'code'=>$c->CountryCode);
        }
        return $locations;
    }
}

?>
