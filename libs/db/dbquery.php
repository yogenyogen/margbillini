<?php

require_once BASE_DIR . LIBS . DB . "dbconnection.php";

class dbquery extends stdClass 
{

    /**
     *
     * @var boolean true if you want to show the queries and their errors(when occurs)
     */
    private $debug;

    /**
     *
     * @var string string with the query to execute
     */
    private $query = null;

    /**
     *
     * @var string error message of the last executed query
     */
    var $errormsg;

    /**
     * Array of query parameters
     *
     * @var array
     */
    private $query_params = array();

    /**
     *
     * @var mysqli_result
     */
    var $resource;

    /**
     *
     * @var dbconnect
     */
    protected $connection;
    
    /**
     *
     * @var array of hashes for query reporting
     */
    private static $debug_report=array();   

    /**
     *
     * @param dbprovider $db
     */
    public function __construct($debug = false) 
    {
        if ($this->connection == null) 
        {
            $this->debug = $debug;
            $this->connection = dbconnect::getInstance();
        }
    }

    /**
     * Set de debug mode for query error reporting
     * 
     * @param boolean $debug 
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
    
    public static function getDebugReport()
    {
        return self::$debug_report;
    }
   
    public static function setDebugReport($val=array())
    {
         self::$debug_report=$val;
    }
    
    /**
     * Sets a query for next execution
     * @param type $query string with query
     */
    public function setQuery($query) 
    {
        $this->query = $query;
    }

    /**
     * Shows the internal query of the class
     */
    public function showQuery() 
    {
        echo $this->query;
    }

    /**
     * gets the internal query of the class
     *
     * @return the internal query for a next execution
     */
    public function getQuery() 
    {
        return $this->query;
    }

    /**
     * Función para obtener el error en la ultima interacción con la clase
     *
     * @uses
     * Solo utilize esta función luego de haber ejecutado un query, o iterado en un
     * recurso generado por este.
     *
     * @return string con el error si lo hubo en el ultimo proceso realizado con la clase.
     */
    public function getError() 
    {
        return $this->connection->error;
    }

    /**
     * Función para obtener el # del error en la ultima interacción con la clase
     *
     * @uses
     * Solo utilize esta función luego de haber ejecutado un query, o iterado en un
     * recurso generado por este.
     *
     * @return int con el #error si lo hubo en el ultimo proceso realizado con la clase,
     * false en caso de no haber.
     */
    public function getErrorNo() 
    {
        if ($this->connection->stat() != false) 
        {
            return false;
        }
        else
        {
            return (int) $this->connection->errno;
        }
    }
    
     /**
     *
     * @return int # of rows affected by the last query
     * execution.
     */
    public function getResultNumber() 
    {
        if(isset($this->resource) && isset($this->resource->num_rows))
        {
            return $this->resource->num_rows;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Hace un Set al texto traido al encode UTF-8
     */
    public function SetCharsetToUTF8() 
    {
        $this->connection->set_charset("utf8");
    }

    /**
     * Función para ejecutar un query
     *
     * @param type $query string con el query a ejecutar. El string no debe llevar
     * punto y coma, si lo lleva ocurre un error. si $query es null entonces
     * utilizara el interno de la clase. (Ver detalle función
     * setQuery($str) para mas entendimient)
     *
     * @warning class var resource,  For SELECT, SHOW, DESCRIBE, EXPLAIN and other statements resource is a resultset from mysql_query, that returns a resource on success, or false on error.
     * For other type of SQL statements, INSERT, UPDATE, DELETE, DROP, etc, mysql_query returns true on success or false on error.
     * The returned result resource should be passed to mysql_fetch_array, and other functions for dealing with result tables, to access the returned data.
     * mysql_num_rows to find out how many rows were returned for a SELECT statement or mysql_affected_rows to find out how many rows were affected by a DELETE, INSERT, REPLACE, or UPDATE statement.
     * mysql_query will also fail and return false if the user does not have permission to access the table(s) referenced by the query.
     *
     * @return boolean true on successful, false otherwise.
     */
    public function Query($query = null) 
    {
        $report = array();
        if ($query !== null) 
        {
            
            if (dbconnect::getcheckcon() == true) 
            {
                $query = str_replace("#__", $this->connection->dbprefix, $query);
                if ($this->debug == true)
                {
                    $report['Query']= "<pre>$query</pre>";
                }
                if ($this->connection->multi_query($query) == true) 
                {
                    $this->resource = $this->connection->store_result();

                    if ($this->connection->more_results())
                    {
                        $this->connection->next_result();
                    }
                    if ($this->resource == false && $this->connection->error != "") 
                    {
                        if ($this->debug == true) 
                        {
                            $report['Error message']="<pre>$this->errormsg</pre>";
                            $report['# Rows']=0;
                            self::$debug_report[]=$report;
                        }
                        return false;
                    }
                    
                    if ($this->debug == true) 
                    {
                        $report['Error message']="";
                        $report['# Rows']=$this->getResultNumber();
                        self::$debug_report[]=$report;
                    }
                    return true;
                } 
                else 
                {
                    $this->errormsg = "Error #[" . $this->connection->errno . "] " . $this->connection->error;
                    if ($this->debug == true) 
                    {
                        $report['Error message']= "<pre>$this->errormsg</pre>";
                        $report['# Rows']=0;
                        self::$debug_report[]=$report;
                    }
                    return false;
                }
            }
            $this->errormsg = "Error #[" . $this->connection->errno . "] " . $this->connection->error;
            if ($this->debug == true)
            {
                $report['Error message']= "<pre>$this->errormsg</pre>";
                $report['# Rows']=0;
                self::$debug_report[]=$report;
            }           
            return false;
        } 
        else 
        {
            if ($this->query === null) 
            {
                if (count($this->query_params) > 0)
                {
                    $this->query = $this->parseQuery();
                }
                if ($this->query === false)
                {
                    if ($this->debug == true) 
                    {
                        $report['Query']= "";
                        $report['Error message']= "<pre>$this->errormsg</pre>";
                        $report['# Rows']=0;
                        self::$debug_report[]=$report;
                        return false;
                    }
                }
            }
            if (dbconnect::getcheckcon() == true) 
            {
                $this->query = str_replace("#__", $this->connection->dbprefix, $this->query);
                $report['Query']= "<pre>$this->query</pre>";
                if ($this->connection->multi_query($this->query) == true) 
                {
                    $this->resource = $this->connection->store_result();
                    if ($this->connection->more_results())
                    {
                        $this->connection->next_result();
                    }
                    if ($this->resource == false) 
                    {
                        if ($this->debug == true) 
                        {
                            
                            $report['Error message']= "<pre>$this->errormsg</pre>";
                            $report['# Rows']=0;
                            self::$debug_report[]=$report;
                        }
                        return false;
                    }
                    if ($this->debug == true) 
                    {
                        $report['Error message']= "";
                        $report['# Rows']=$this->getResultNumber();
                        self::$debug_report[]=$report;
                    }
                    return true;
                } 
                else 
                {
                    $this->errormsg = "Error #[" . $this->connection->errno . "] " . $this->connection->error;
                    if ($this->debug == true) 
                    {
                        $report['Error message']= "<pre>$this->errormsg</pre>";
                        $report['# Rows']=0;
                        self::$debug_report[]=$report;
                    }
                    return false;
                }
            }
            $this->errormsg = "Error #[" . $this->connection->errno . "] " . $this->connection->error;
            if ($this->debug == true) 
            {
                $report['Error message']= "<pre>$this->errormsg</pre>";
                $report['# Rows']=0;
                self::$debug_report[]=$report;
            }
            return false;
        }
    }

    /**
     * @return boolean true if the db provider is debugging
     */
    public function isDebugging() 
    {
        return $this->debug;
    }

    /**
     *
     * @return dbconnect
     */
    public function getConnectionLink() 
    {
        return $this->connection;
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of the connection
     * @link http://php.net/manual/en/mysqli.real-escape-string.php
     *
     * @param string $value The string to be escaped. Characters encoded are NUL (ASCII 0), \n, \r, \, ', ", and Control-Z.
     *
     * @return string an escaped string, if $value is not an string then the print_r() value.
     */
    public function escape_string($value) 
    {
        //if is an array theres nothing to do here
        if (is_array($value) == true)
        {
            return print_r($value, true); //returns an print_r($array)
        }
        if (is_int($value) == true)
        {
            return $value; //theres nothing to escape
        }
        if (is_object($value) == true)
        {
            return print_r($value, true); //returns an print_r($object)
        }
        return $this->connection->real_escape_string($value);
    }

    /**
     * Generates the query from the configuration
     *
     * @return boolean|string String with the query, false on error parsing the query.
     */
    public function parseQuery() 
    {
        $query = "";
        $tname = '';
        $qaction = '';
        $join = '';
        $where = '';
        $order = '';
        $limit = '';
        $dbprefix=$this->connection->dbprefix;
        if (!isset($this->query_params['action']) || !isset($this->query_params['tname'])) 
        {
            $this->errormsg = 'Query parser error:Table name or Statement missing, valid statements:(INSERT,DELETE,UPDATE,SELECT)';
            return false;
        }
        $tname = '';
        foreach ($this->query_params['tname'] as $table) 
        {
            $tname.=' `' .$dbprefix. $this->escape_string($table['name']) . '` ';
            if (isset($table['alias']))
            {
                $tname.='AS `' . $this->escape_string($table['alias']) . '` ';
            }
        }
        $action = $this->query_params['action'];
        $stop = false;
        switch ($action['name']) 
        {
            case 'delete':
                $qaction = 'DELETE FROM ' . $tname;
                $stop = true;
                break;

            case 'select':
                if (isset($action['values']))
                {
                    $values = $action['values'];
                }
                else
                {
                    $values = array();
                }
                if (count($values) <= 0) 
                {
                    $qaction = 'SELECT * FROM ' . $tname;
                }
                else 
                {
                    $qaction = 'SELECT ';
                    foreach ($values as $celem => $alias) {
                        $qaction .="`$celem`";
                        if (is_string($alias) && strlen($alias) > 0)
                            $qaction.=" AS $alias";
                        $qaction.=',';
                    }
                    $qaction = substr($qaction, 0, strlen($qaction) - 1);
                    $qaction.=' FROM ' . $tname;
                }
                break;
            default:
                if ($action['name'] != 'insert' || $action['name'] != 'update') 
                {
                    $this->errormsg = 'Query parser error: Invalid query statement valids:(INSERT,DELETE,UPDATE,SELECT)';
                    return false;
                }

                $aname = strtoupper($this->escape_string($action['name']));
                if (isset($action['values'])) 
                {
                    $values = $action['values'];
                    if (is_array($values)) 
                    {
                        //the query most have columns on insert and update
                        $insert = false;
                        if ($aname == 'INSERT') 
                        {
                            $qaction = $aname . ' INTO ' . $tname . ' ';
                            $insert = true;
                        }
                        else
                        {
                            $qaction = $aname . ' ' . $tname . ' SET ';
                        }
                        $ins_columns = '(';
                        $ins_vals = ' VALUES (';
                        foreach ($values as $column => $val) 
                        {
                            if ($insert === true) 
                            {
                                $ins_columns.=" `$column`,";
                                $ins_vals.=" '" . $this->escape_string($val) . "',";
                            } 
                            else 
                            {
                                $qaction = " `" . $this->escape_string($column) . "`='" . $this->escape_string($val) . "',";
                            }
                        }

                        if ($insert === true) 
                        {
                            $ins_vals = substr($ins_vals, 0, strlen($ins_vals) - 1);
                            $ins_columns = substr($ins_columns, 0, strlen($ins_columns) - 1);
                            $ins_columns.=') ';
                            $ins_vals.=') ';
                            $query = $qaction . $ins_columns . $ins_vals;
                            unset($this->query_params);
                            return $query;
                        }
                        else
                        {
                            $qaction = substr($qaction, 0, strlen($qaction) - 1);
                        }
                        $stop = true;
                    }
                    else 
                    {
                        $this->errormsg = 'Query parser error: Invalid value for columns and values in Insert|Update Statement';
                        return false;
                    }
                } 
                else 
                {
                    $this->errormsg = 'Query parser error: Missing columns and values in Insert|Update Statement';
                    return false;
                }
                break;
        }
        //configures the where, joins, order by and limits of the query
        foreach ($this->query_params as $param => $config) 
        {
            switch ($param) 
            {
                case 'where':
                    if ($stop === true && strripos($qaction, 'INSERT') !== false)
                    {
                        break;
                    }
                    $clauses = $config;
                    $index = 0;
                    foreach ($clauses as $clause) 
                    {
                        $key = $clause['key'];
                        $glue = $clause['glue'];
                        $op = strtoupper($clause['operator']);
                        $value = $clause['value'];
                        if ($index <= 0) 
                        {
                            $where = 'WHERE';
                            if ($op != 'BETWEEN')
                            {
                                if($op == 'IN')
                                {
                                    $where .= " $glue " . $this->escape_string($key) . " $op (" . $this->escape_string($value) . ") ";
                                }
                                else
                                {
                                    if($value !== null)
                                    {
                                        $where .= " $glue " . $this->escape_string($key) . " $op '" . $this->escape_string($value) . "' ";
                                    }
                                    else
                                    {
                                        $where .= " $glue " . $this->escape_string($key) . " $op NULL "; 
                                    }
                                }
                            }
                            else 
                            {
                                $arr = explode('AND', $value);
                                $value = ' \'' . trim($this->escape_string($arr[0])) . '\' AND \'' . trim($this->escape_string($arr[1])) . '\'';

                                $where .= " " . $this->escape_string($key) . " $op " . $this->escape_string($value) . " ";
                            }
                        } 
                        else 
                        {
                            if ($op != 'BETWEEN')
                            {
                                if($op == 'IN')
                                {
                                    $where .= " $glue " . $this->escape_string($key) . " $op (" . $this->escape_string($value) . ") ";
                                }
                                else
                                {
                                    if($value !== null)
                                    {
                                        $where .= " $glue " . $this->escape_string($key) . " $op '" . $this->escape_string($value) . "' ";
                                    }
                                    else
                                    {
                                        $where .= " $glue " . $this->escape_string($key) . " $op NULL "; 
                                    }
                                }
                            }
                            else 
                            {
                                $arr = explode('AND', $value);
                                $value = ' \'' . trim($this->escape_string($arr[0])) . '\' AND \'' . trim($this->escape_string($arr[1])) . '\'';
                                $where .= " $glue " . $this->escape_string($key) . " $op " . $this->escape_string($value) . " ";
                            }
                        }
                        $index++;
                    }

                    break;

                case 'limit':
                    if ($stop === true)
                    {
                        break;
                    }
                    $lower_limit = $config['lower'];
                    $upper_limit = $config['upper'];
                    if ($lower_limit !== null && $upper_limit !== null):
                        if ((is_int($lower_limit) && is_int($upper_limit)) || ( is_numeric($lower_limit) && is_numeric($upper_limit)))
                        {
                            $limit.=" LIMIT $lower_limit,$upper_limit";
                        }
                    endif;
                    break;

                case 'order':
                    if ($stop === true)
                    {
                        break;
                    }
                    $DESC = $config['order type'];
                    $idfield = $config['key'];
                    $order = '';
                    if ($DESC == 'DESC') 
                    {
                        $order.=" order by `" . $this->escape_string($idfield) . "` DESC";
                    }
                    else
                    {
                        $order.=" order by `" . $this->escape_string($idfield) . "` ASC";
                    }
                    break;

                case 'join':
                    if ($stop === true)
                    {
                        break;
                        
                    }
                    $clauses = $this->query_params['join'];
                    $count = count($clauses);
                    $index = 0;
                    foreach ($clauses as $clause) 
                    {
                        $alias1 = null;
                        $alias2 = null;
                        $tname1 = $clause['table1'];
                        $tname2 = $clause['table2'];
                        $op = $clause['op'];
                        $type = $clause['type'];
                        if (isset($clause['alias1']))
                            $alias1 = $clause['alias1'];
                        if (isset($clause['alias2']))
                            $alias2 = $clause['alias2'];
                        $index1 = $clause['index1'];
                        $index2 = $clause['index2'];
                        $join.="$type JOIN `$tname1` ";
                        if ($alias1 !== null)
                            $join.=" AS $alias1 ";

                        $join.=" ON ";
                        if ($alias1 !== null)
                            $join.=" $alias1.$index1 ";
                        else
                            $join.=" $tname1.$index1 ";
                        $join.="$op ";

                        if ($alias2 !== null)
                            $join.=" $alias2.$index2 ";
                        else
                            $join.=" $tname2.$index2 ";
                        $index++;
                    }
                    break;
            }
        }
        $query = $qaction . $join . $where . $order . $limit;
        unset($this->query_params);
        return $query;
    }

    /**
     * sets the query as an insert one
     * @param array $arr hash of the columns => values
     */
    public function setInsert($arr = array()) 
    {
        $this->query_params['action'] = array('name' => 'insert', 'values' => $arr);
    }

    /**
     * sets the query as a update one
     * @param array $arr hash of the columns => values
     */
    public function setUpdate($arr = array()) 
    {
        $this->query_params['action'] = array('name' => 'update', 'values' => $arr);
    }

    /**
     * sets the query as a delete one
     */
    public function setDelete() 
    {
        $this->query_params['action'] = array('name' => 'delete');
    }

    /**
     * sets the query as a update one
     * @param array $arr hash of the columns => alias array(table1.column => C),
     * empty array for select * table
     */
    public function setSelect($arr = array()) 
    {
        $this->query_params['action'] = array('name' => 'select', 'alias' => $arr);
    }

    /**
     * Sets the table name for the query
     * @param string $name name of the table
     * @param string $alias alias of the tables
     */
    public function setTable($name, $alias = null) 
    {
        if ($alias === null)
        {
            $this->query_params['tname'][] = array('name' => $name);
        }
        else
        {
            $this->query_params['tname'][] = array('name' => $name, 'alias' => $alias);
        }
    }

    /**
     * Function to add a where clause to the query
     *
     * @param string $key string with the field to validate
     * @param string $value string with the value to search
     * @param string $op operator of the where clause to add
     * @param string $glue Where clause combiner operator, AND, OR.
     *
     * @warning the table and database name in which the search is performed
     * must not contain 'where' any caps variations apply too.
     *
     */
    public function setWhere($key, $value, $op = '=', $glue = null) 
    {
        if(strtolower($op) == 'like')
        {
            $value = "%$value%";
        }
        $this->query_params['where'][] = array('key' => $key, 'operator' => $op, 'value' => $value, 'glue' => $glue);
    }

    /**
     * Sets an order by into the internal query
     *
     * @param type $key Field(column) that you are going to order
     * @param type $ordertype The way you will order the $key field
     * ('ASC' 'DESC'.... etc)
     *
     */
    public function setOrderBy($key, $ordertype = 'DESC') 
    {
        $this->query_params['order'] = array('key' => $key, 'order type' => $ordertype);
    }

    /**
     * Sets a limit into the internal query
     *
     * @param int $lowerlimit lower limit
     * @param int $upperlimit top limit
     *
     *
     */
    public function setLimit($lowerlimit = 0, $upperlimit = 10) 
    {
        $this->query_params['limit'] = array('lower' => $lowerlimit, 'upper' => $upperlimit);
    }

    /**
     * Sets an inner join in the query
     *
     * @param string $type   type of the join(INNER,OUTER, LEFT, RIGHT)
     * @param string $tname1 first name or alias of the table to join
     * @param string $tname2 second name or alias of the table to join
     * @param string $index1 first column to match within the tables
     * @param string $index2 second column to match within the tables
     * @param string $op operator of the join clause to add
     * @param string $alias1 alias of table 1
     * @param string $alias2 alias of table 2
     */
    public function setJoin($type, $tname1, $tname2, $index1, $index2, $op = '=', $alias1 = null, $alias2 = null)
    {
        $param = array('type' => $type, 'op' => $op, 'table1' => $tname1, 'table2' => $tname2, 'index1' => $index1, 'index2' => $index2);
        if ($alias1 !== null) 
        {
            $param['alias1'] = $alias1;
        }
        if ($alias2 !== null) 
        {
            $param['alias2'] = $alias2;
        }
        $this->query_params['join'][] = $param;
    }

    /**
     * Generates a query to update a database table
     *
     * @param array Hash of columns and values from the table
     * @param string $tname table name
     * @param integer $id id of the element to update
     * @param string $idfield name of the id field
     *
     * @return boolean|string false on error, string with the query in success
     */
    public function update($properties, $tname, $id, $idfield = 'Id') 
    {
        $UpdateQuery = "";
        if (is_numeric($id) != true) 
        {
            return false;
        }
        $UpdateQuery = "UPDATE #__$tname ";
        $change=false;
        foreach ($properties as $i => $val) 
        {
            if ($i != $idfield)
            {
                if($change === false)
                {
                    $UpdateQuery.=" SET ";
                }
                if($val !== null)
                {
                    $UpdateQuery .= "`$i` = '" . $this->escape_string($val) . "', ";    
                }
                else
                {
                    $UpdateQuery .= "`$i` = NULL ,";   
                }
                $change=true;
            }
        }
        if($change===true)
        {
            $UpdateQuery = substr($UpdateQuery, 0, strlen($UpdateQuery) - 2);
        }
        $UpdateQuery .= " Where `$idfield` = $id";
        return $UpdateQuery;
    }

    /**
     * Generates a query to insert a row in the database table
     *
     * @param array Hash of columns and values from the table
     * @param string $tname table name
     * @param string $idfield name of the id field
     *
     * @return boolean|string false on error, string with the query in success
     */
    public function insert($properties, $tname, $idfield = 'Id') 
    {
        $InsertQuery = "";
        if (count($properties) == 1) 
        {
            foreach ($properties as $k => $a) 
            {
                if ($k == $idfield)
                    break;
                else
                    return false;
            }
            $InsertQuery = "INSERT INTO #__$tname (`$idfield`) VALUES(0)";
        }
        else 
        {
            $InsertQuery = "INSERT INTO #__$tname (";
            foreach ($properties as $i => $val) 
            {
                if ($i != $idfield)
                {
                    
                    $InsertQuery .= "`$i`, ";
                }
            }
            $InsertQuery = substr($InsertQuery, 0, strlen($InsertQuery) - 2);
            $InsertQuery .=") VALUES (";

            foreach ($properties as $i => $val) 
            {
                if ($i != $idfield)
                {
                    if ($val !== null)
                        $InsertQuery .= "'" . $this->escape_string($val) . "', ";
                    else
                        $InsertQuery .= " NULL, ";
                }
                    
            }
            $InsertQuery = substr($InsertQuery, 0, strlen($InsertQuery) - 2);
            $InsertQuery .=")";
        }
        return $InsertQuery;
    }

    /**
     * Generates a query to delete a row in the database table
     *
     * @param string $tname table name
     * @param string|array $value value of the field to search.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).. 
     * @param string|array $field name of the field to search
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param integer $id value of the id
     * @param string $idfield name of the id field
     * @return boolean|string false on error, string with the query in success
     */
    public function delete($tname, $value, $field, $id, $idfield = 'Id') 
    {
        $DelQuery = "";
        $this->setDelete();
        $this->setTable($tname);
        if ($field == "" && $value == "") 
        {
            if (is_numeric($id) != true) 
            {
                return false;
            }
            $this->setWhere($idfield, $this->escape_string($id));
        } 
        else if ($field != "" && $value != "") 
        {
            $this->setWhere($field, $this->escape_string($value));
        }
        else if(is_array($field) && is_array($value))
        {
            if(count($field)==count($value))
            {
                $fields=array();
                $values=array();
                $operators=array();
                $glues=array();
                foreach($field as $f)
                {
                    $fields[]=$f[0];
                    $operators[]=$f[1];
                }
                foreach($value as $val)
                {
                    $values[]=$val[0];
                    $glues[]=$val[1];
                }
                for($i = 0; $i < count($field); $i++)
                {
                    if($i == 0)
                        $glue=null;
                    
                    $this->setWhere($fields[$i], $values[$i], 
                           $operators[$i], $glues[$i]);
                }
            }
            else
                return false;
        }
        $DelQuery = $this->parseQuery();
        return $DelQuery;
    }

    /**
     * Generates a query to find a row or rows in the database table
     *
     * @param string $tname table name
     * @param string|array $value value of the field to search.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).. 
     * @param string|array $field name of the field to search
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string $order_field field to arrange the rows
     * @param boolean $DESC true if the query will order by DESC
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param string $idfield name of the id field
     *
     * @return boolean|string false on error, string with the query in success
     */
    public function find($tname, $value, $field, $order_field, $DESC, $lower_limit, $upper_limit, $idfield = 'id')
    {
        $this->setSelect();
        $this->setTable($tname);
        if (($field != "" && $value != "") && is_string($value)) 
        {
            $this->setWhere($field, $this->escape_string($value) );
        } 
        else if ($field != "" && is_numeric($value) == true) 
        {
            $this->setWhere($field, $value);
        } 
        else if ($field != "" && ($value === null || $value=="")) 
        {
            $this->setWhere($field, null);
        }
        else if(is_array($field) && is_array($value))
        {
            if(count($field)==count($value))
            {
                $fields=array();
                $values=array();
                $operators=array();
                $glues=array();
                foreach($field as $f)
                {
                    $fields[]=$f[0];
                    $operators[]=$f[1];
                }
                foreach($value as $val)
                {
                    $values[]=$val[0];
                    $glues[]=$val[1];
                }
                for($i = 0; $i < count($fields); $i++)
                {
                    $glue=$glues[$i];
                    if($i == 0)
                        $glue=null;
                    
                    $this->setWhere($fields[$i], $values[$i], 
                           $operators[$i], $glue);
                }
            }
            else
                return false;
        }
        else if ($field == $idfield && is_numeric($value) != true) 
        {
            return false;
        }
        if ($order_field == "") {
            if ($DESC == true) {
                $this->setOrderBy($idfield);
            }
            else
                $this->setOrderBy($idfield,  'ASC');
        }
        else 
        {
            $order_fields = explode(",", $order_field);
            $order_fields_qty = count($order_fields);
            if ($DESC == true) 
                $order_type = 'DESC';
            else 
                $order_type = 'ASC';
            
            if ($order_fields_qty > 1) 
            {
                    $counter = 0;
                    foreach ($order_fields as $of) 
                    {
                        $counter++;
                        $this->setOrderBy($of, $order_type);
                    }
            }
            else
            {
                $this->setOrderBy($order_field, $order_type); 
            }
        }
        if ($lower_limit !== null && $upper_limit !== null):
            if ((is_int($lower_limit) && is_int($upper_limit)) || ( is_numeric($lower_limit) && is_numeric($upper_limit)))
                $this->setLimit ($lower_limit, $upper_limit);
        endif;
       
        $getQuery = $this->parseQuery();
        return $getQuery;
    }

}

?>

