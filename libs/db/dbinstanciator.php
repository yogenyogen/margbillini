<?php
require_once BASE_DIR.LIBS.DB.DBPROVIDER;
require_once BASE_DIR.LIBS.TOOLS.CONSTANTS;

/**
 * This object control the instanciation of a db-table object
 * 
 * @author Gabriel Elias González Disla
 * @internal This class should only be used in situations
 * that the object-table structure is not going to be changed
 * 
 */
class dbinstanciator
{
    
    public $response="";
    
    /**
     * Array with the Mysqli datatypes 
     * defined in php platform
     * DECIMAL=>0
     * TINY=>1
     * SHORT=>2
     * LONG=>3
     * FLOAT=>4
     * DOUBLE=>5
     * NULL=>6
     * TIMESTAMP=>7
     * LONGLONG=>8
     * INT24=>9
     * DATE=>10
     * TIME=>11
     * DATETIME=>12
     * YEAR=>13
     * NEWDATE=>14
     * ENUM=>247
     * SET=>248
     * TINY_BLOB=>249
     * MEDIUM_BLOB=>250
     * LONG_BLOB=>251
     * BLOB=>252
     * VAR_STRING=>253
     * STRING=>254
     * CHAR=>1
     * INTERVAL=>247
     * GEOMETRY=>255
     * NEWDECIMAL=>246
     * BIT=>16
     * @var array key => value
     */
    private $MySqliTypes=array();
    
    /**
     * 
     * @var string
     */
    private $objFullName="";
    
    /**
     * This string is used as a map to insert in the query 
     * 
     * @var string 
     */
    private $MappingSupport = "`BIT` bit(2) NOT NULL,
  `INT` int(11) NOT NULL,
  `MEDIUMINT` mediumint(9) NOT NULL,
  `SMALLINT` smallint(6) NOT NULL,
  `TINYINT` tinyint(4) NOT NULL,
  `BIGINT` bigint(20) NOT NULL,
  `VARCHAR` varchar(50) DEFAULT NULL,
  `TEXT` text,
  `DATE` date NOT NULL,
  `DECIMAL` decimal(10,0) NOT NULL,
  `FLOAT` float DEFAULT NULL,
  `DOUBLE` double DEFAULT NULL,
  `REAL` double DEFAULT NULL,
  `BOOLEAN` tinyint(1) DEFAULT NULL,
  `DATETIME` datetime NOT NULL,
  `TIMESTAMP` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TIME` time NOT NULL,
  `YEAR` year(4) NOT NULL,
  `CHAR` char(1) NOT NULL,
  `SERIAL` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `TINYTEXT` tinytext NOT NULL,
  `MEDIUMTEXT` mediumtext NOT NULL,
  `LONGTEXT` longtext NOT NULL,
  `BINARY` binary(5) NOT NULL,
  `VARBINARY` varbinary(5) NOT NULL,
  `TINYBLOB` tinyblob NOT NULL,
  `MEDIUMBLOB` mediumblob NOT NULL,
  `BLOB` blob NOT NULL,
  `LONGBLOB` longblob NOT NULL,
  `GEOMETRY` geometry NOT NULL,
  `POINT` point NOT NULL,
  `LINESTRING` linestring NOT NULL,
  `POLYGON` polygon NOT NULL,
  `MULTIPOINT` multipoint NOT NULL,
  `MULTLINESTRING` multilinestring NOT NULL,
  `MULTIPOLYGON` multipolygon NOT NULL,
  `GEOMETRYCOLLECTION` geometrycollection NOT NULL,";
    
    /**
     * The result from the construction of dbinstanciator
     * is a representation of the object in the database.
     * 
     * @param string $cname
     * @param array $values array with the fields of the object and their
     * data representation within the database. it follows this
     * structure array("Name" => )
     * @param string $lang 
     * 
     * @return boolean different from true on failure.
     */
    public function __construct($cname, $values, $lang = "")
    {
        $flag = false;
        
        $db = new dbprovider();
        if(count($values) == 0)
        { 
            return false;
        }
        $this->MySqliTypes = Constants::getMySqliDataTypes();
        $this->objFullName = $cname.$lang;
        $query = "CREATE TABLE IF NOT EXISTS `$this->objFullName` (";
        foreach($values as $name => $type)
        {
            $key = strtoupper($type['type']);
            if($key == "INT")
            {
                $key .="24";
            }
            if(array_key_exists($key, $this->MySqliTypes) == true)
            {
                //if true insert in query
                $key = strtoupper($type['type']);
                $structure = explode(',', $this->MappingSupport);
                foreach($structure as $str)
                {
                    if(strripos($str, $key) != false)
                    {
                        if($flag == true)
                        {
                            $query.=",
                                ";
                        }
                        $tstr = $str;
                        $tstr=substr($str, 0,strpos($str, '`')+1);
                        $tstr.=$name;
                        $tstr.=substr($str, strripos($str, '`'), strlen($str));
                        
                        $pos=strripos($tstr, "(");
                        if($pos != false && $type['value'] != "")
                        {
                            $init = substr($tstr, 0, $pos+1);
                            $fpos = strripos($tstr, ")");
                            $end = ($type['value'].substr($tstr, ($fpos), strlen($tstr)-1));
                            $tstr = $init.$end;
                        }
                        $pos=false;
                        $fpos=false;
                        $init="";
                        $end="";
                        $query.=$tstr;
                        if($flag == false)
                        {
                            $flag = true;
                        }
                        break;
                    }
                }
                unset($structure);
            }
        }
        $query.=$this->getPrimaryKey($values);
        $query.=") ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
        $this->response = $db->Query($query);
        if($this->response == false)
        {
            $this->response= "The object already exist";
        }
    }
    
    /**
     * Obtiene el primary key del arreglo de valores si existe.
     * 
     * @param array $values arreglo con los campos del objeto
     * 
     * @return string nombre del primary key
     */
    private function getPrimaryKey($values)
    {
        foreach($values as $name => $type)
        {
            if(array_key_exists("primary", $type) == true)
            {
                $s = ", 
                      PRIMARY KEY (`$name`) ";
                return $s;
                break;
            }
        }
        return "";
    }
}

?>
