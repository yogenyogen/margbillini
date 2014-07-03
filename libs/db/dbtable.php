<?php

require_once BASE_DIR.LIBS.DB.DBPROVIDER;

/**
 * Object of a db-table representation.
 * 
 * 
 * @warning Extended properties of this class.
 * Must be different from null
 *
 * @author Gabriel Elias González Disla
 */
class dbtable {
    
    /**
     * String with the name of the db-table
     * @var string 
     */
    private $_tname="";
    
    /**
     * 
     * @var string 
     */
    private $_idfieldname="id";
    
    /**
     * Hash of the data of the table instance
     * @var array
     */
    private $_data=array();
    
    /**
     * Array of columns of the object
     * @var array
     */
    private $_columns=array();
    
    /**
     * True if you want the database object to show
     * the results of the query
     * @var boolean
     */
    private $_debug=false;
    
    /**
     * Db provider instance
     * @var dbprovider 
     */
    private $_provider=null;
    
    
    /**
     * Object constructor
     * 
     * @param type $id id of the element to construct.
     * @param string $cname name of the class inherited
     * @param string $idfieldname name of the primary key of the table
     * @param boolean $debug true for debugging queries
     * 
     */
    public function __construct($id, $cname, $idfieldname="", $debug=false) 
    {
        $this->_tname = $cname;
        $this->_provider=$this->getProvider($debug);
        if($idfieldname != "")
        {
            $this->_idfieldname = $idfieldname;
        }
        $query="SHOW COLUMNS FROM #__$this->_tname";
        $this->_provider->Query($query);
        $rows=$this->_provider->getNextObjectList();
        if($rows)
        {
            foreach($rows as $row)
            {
                $this->_columns[]=$row->Field;
                $this->_data[$row->Field]="";
            }
        }
        

        $properties=$this->getAttributes();
        if(!isset($properties[$this->_idfieldname]))
        {
            return false;
        }
        if(is_numeric($id) == false)
        {
            return false;
        }
        if($id < 0)
        {
            return false;
        }
        $query = $this->_provider->find($this->_tname,$id,$this->_idfieldname, 
                $this->_idfieldname, false,0, 2, $this->_idfieldname);
        $this->_provider->Query($query);
        $row = null;
        $row = $this->_provider->getNextObject();
        if($row)
        {
           foreach($row as $r => $rval)
           {
               if(isset($properties[$r]))
               {
                   $this->_data[$r] = $rval;
               }
           }
        }
        
    }
    
    /**
     * @return array array of data from the object 
     */
    public function getData()
    {
        return $this->_data;
    }
    
    /**
     * Gets the table's columns
     * @return array
     */
    public function getTableColumns()
    {
        return $this->_columns;
    }
    
    /**
     * Update the object to the database 
     * 
     * @return dbobject not false on success.
     */
    public function update()
    {
        $properties= $this->getAttributes();
        $idfield= $this->_idfieldname;
        $id=$this->get($idfield);
        $this->_provider->update($properties, $this->_tname, $id, $idfield);
        $return = $this->_provider->Query($UpdateQuery);
        if($return == false)
            return $return;
        return $this;
    }
    
    /**
     * Insert the object to the database 
     * 
     * @return dbobject not false on success.
     */
    public function insert()
    {
        $properties=$this->getAttributes();
        $InsertQuery=$this->_provider->insert($properties, $this->_tname, $this->_idfieldname);
        $return = $this->_provider->Query($InsertQuery);
        
        if($return == false)
            return false;
        else
        {
            $query="SELECT * FROM #__$this->_tname ";
            $this->_provider->setQuery($query);
            $this->_provider->setOrderBy($this->_idfieldname);
            $this->_provider->Query();
            $row = $this->_provider->getNextObject();
            if($row)
            {
                foreach($row as $r => $rval)
                {
                    if(array_key_exists($r, $properties) == true)
                    {
                        $this->_data[$r] = $rval;
                    }
                }
                return $this;
            }
            return false;
        }
    }
    
    /**
     * Delete the object instance in the database
     * 
     * @param type $field name of the field to search
     * @param type $value name of the value of the field
     * 
     * @warning if the funtion is used without parameters
     * there`s only a successful delete if the object 
     * Id is found in the database.
     *  
     * @return boolean|dbobject Not false on success.
     */
    public function delete($field="", $value="")
    {
        $idfield= $this->_idfieldname;
        $id=$this->get($idfield);
        $DelQuery=$this->_provider->delete($this->_tname, $value, $field, $id, $idfield);
        $return = $this->_provider->Query($DelQuery);
        if($return == false)
                return $return;
        return $this;
    }
    
    /**
     * Selects one object from the table depending on which 
     * attribute you are looking for.
     * 
     * @param  string  $field Field to search within the object.
     * @param  string  $value Value looked within the object table.
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be 
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be 
     * an integer otherwise is going to be ignored
     * 
     * @return dbobject|boolean object on success, false on error.
     */
    public function find($field="", $value="", $DESC=true, $order_field="", $lower_limit=null, $upper_limit=null)
    {  
        $getQuery = $this->_provider->find($this->_tname, $value, $field, $order_field, 
                $DESC, $lower_limit, $upper_limit, $this->_idfieldname);
        $properties = $this->getAttributes();
        $obj = $this->getObjectName();
        if($this->_provider->Query($getQuery) != true)
        {
           return new $obj(-1,$this->_tname);
        }
        
        $row = null;
        $row = $this->_provider->getNextObject();
        $newobj =new $obj(-1,$this->_tname);
        if($row)
        {
           foreach($row as $r => $rval)
           {
               if(array_key_exists($r, $properties) == true)
               {
                   $newobj->set($r,$rval);
               }
           }
           return $newobj;
        }
        return new $obj(-1,$this->_tname);
    }
    
    /**
     * Selects all the objects from the table depending on which 
     * attribute you are looking for.
     * 
     * @param  string  $field Field to search within the object.
     * @param  string  $value Value looked within the object table.
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be 
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be 
     * an integer otherwise is going to be ignored
     * 
     * @return dbobject|boolean array on success, false on error.
     */
    public function findAll($field="", $value="", $DESC=true, $order_field="", $lower_limit=null, $upper_limit=null)
    {
        $getQuery = $this->_provider->find($this->_tname, $value, $field, $order_field, 
                $DESC, $lower_limit, $upper_limit, $this->_idfieldname);
        if($this->_provider->Query($getQuery) != true)
        {
            return array();
        }
        $row = null;
        $arr = array();
        while($row = $this->_provider->getNextObject())
        {
           $temp= new $this(-1,$this->_tname);
           $temp->setAttributes($row);
           $arr[]=$temp;
        }
        if (count($arr) == 0)
             return array();
        else
            return $arr;
    }
    
    /**
     * Returns the name of the object.
     * @return string  
     */
    public function getObjectName()
    {
        return get_class($this);
    }

    /**
     * Checks if the object exists in the database
     * 
     * @return boolean false if does not have a record
     */
    public function ObjExist()
    {
        $properties = $this->getAttributes();
        if(isset($properties[$this->_idfieldname]))
        {
            $Id=$this->_provider->escape_string($properties[$this->_idfieldname]);
            $getQuery = "SELECT * FROM #__$this->_tname ";
            $getQuery .= " Where `$this->_idfieldname` = '$Id'";
            if($this->_provider->Query($getQuery) == true)
            {
                return true;
            }
            else
                return false;
        }
        return false;
    
        
    }
    
    /**
     * sets the debug
     * 
     * @param type $bool true if you want to debug
     */
    public function setDebug($bool)
    {
        $this->_debug=$bool;
    }
    
    /**
     * gets the name of the database table 
     * @return string
     */
    public function getTableName()
    {
        return $this->_tname;
    }
    
    /**
     * return an instance of the dbprovider object.
     * 
     * @return dbprovider 
     */
    public function getProvider($debug=false)
    {
        if($this->_provider === null)
            $this->_provider= new dbprovider($debug);
        
        $this->_provider->setDebug($debug);
        return $this->_provider;
    }
    
    /**
     * 
     * @return array Hash of $name => $value from the
     * object attributes.
     */
    public function getAttributes()
    {
        return $this->_data;
    }
    
    /**
     * Updates the class attributes from the array
     * 
     * @param array $values Hash of $name => $value
     */
    public function setAttributes($values)
    {
        $properties=$this->getAttributes();
        foreach($values as $k => $val)
        {
            if(isset($properties[$k]))
            {
               $this->_data[$k]=$val; 
            }
        }
    }
    
    /**
     * @return string the name of the primary key field
     */
    public function getPrimaryKeyField()
    {
        return $this->_idfieldname;
    }
    
    /**
     * gets the property of the object
     * 
     * @param string $key name of the property
     * @return mixed value of the property 
     * false if the property does not exist.
     */
    public function get($key)
    {
        if(isset($this->_data[$key]))
        {
            return $this->_data[$key];
        }
        return false;
    }
    
    /**
     * sets the property of the object
     * 
     * @param string $key name of the property
     * @param mixed $value value of the property
     * 
     * @return boolean false if the property does not exist.
     * 
     */
    public function set($key, $value)
    {
        if(!isset($this->_data[$key]))
              return false;  
        
        $this->_data[$key]=$value;
        return true;
    }
    
    /**
     * 
     * @param type $query
     * @return array Mysqli resource if the query had results and 
     * empty array in case no result or an error ocurred.
     */
    public static function Query($query, $debug=false)
    {
        $dbpro = new dbprovider($debug);
        if($dbpro->Query($query) == true)
            return $dbpro->getNextObjectList();
        else 
            return array();
        
    }
}

?>
