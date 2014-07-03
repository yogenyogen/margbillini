<?php

require_once BASE_DIR . LIBS . DB . DBQUERY;

/**
 * Class for Database management
 *
 * @author Gabriel Elias González Disla
 */
class dbprovider extends dbquery 
{

    /**
     *
     * @var array para almacenar los resultados
     */
    private $rows;

    /**
     *
     * @param type $debug true if the db is debugging
     * @return dbprovider
     */
    public function __construct($debug = false) 
    {
        parent::__construct($debug);
        $this->SetCharsetToUTF8();
        return $this;
    }

    public function __destruct() 
    {
        unset($this);
    }

    /**
     * Función para obtener la siguiente fila en la tabla obtenida por la funcion query.
     *
     * @return type la fila siguiente a la actual. en caso de no haber iterado retorna
     * el primer elemento, en caso de que no haya nada en resource retorna false, además
     * al tiempo de acabar con iterar con la tabla. Si retorna null es porque el query esta mal.
     */
    public function getNextRow() 
    {
        if ($this->resource == false) 
        {
            return null;
        }
        $row = $this->resource->fetch_assoc();
        return $row;
    }

    /**
     * Función para obtener un objeto de la siguiente fila en la tabla obtenida por la funcion query.
     *
     * @return type la fila siguiente a la actual. en caso de no haber iterado retorna
     * el primer elemento, en caso de que no haya nada en resource retorna false, además
     * al tiempo de acabar con iterar con la tabla. Si retorna null es porque el query esta mal.
     */
    public function getNextObject() 
    {
        if ($this->resource == false) 
        {
            return null;
        }
        $row = $this->resource->fetch_object();
        return $row;
    }

    /**
     * Función para obtener un arreglo de objetos de las filas obtenida por la funcion query.
     *
     * @return type un arreglo de objetos con los resultados del query. , en caso de que no
     * haya nada en resource retorna false, además al tiempo de acabar con iterar con la tabla.
     * Si retorna null es porque el query esta mal.
     */
    public function getNextObjectList() 
    {
        if ($this->resource == false) 
        {
            return null;
        }
        if (!$this->rows) 
        {
            $this->rows = array();
        }
        while ($row = $this->resource->fetch_object()) 
        {
            $this->rows[] = $row;
        }
        $learray = $this->rows;
        $this->rows = null;
        return $learray;
    }

    /**
     * Función para obtener un arreglo asociativo de las filas obtenida por la funcion query.
     *
     * @return type un arreglo de objetos con los resultados del query. , en caso de que no
     * haya nada en resource retorna false, además al tiempo de acabar con iterar con la tabla.
     * Si retorna null es porque el query esta mal.
     */
    public function getNextArray() 
    {
        if ($this->resource == false) 
        {
            return null;
        }
        if (!$this->rows) 
        {
            $this->rows = array();
        }
        while ($row = $this->resource->fetch_assoc()) 
        {
            $this->rows[] = $row;
        }
        $learray = $this->rows;
        $this->rows = null;
        return $learray;
    }

    /**
     * a hash of id => value
     * @param type $field field of the enum in the table
     * @param type $selected selected value
     *
     * @return array
     */
    public function getEnumValues($table, $field, $selected = null) 
    {
        $query = "SELECT $field FROM #__$table;";
        $this->Query($query);
        $in = $this->getNextObjectList();
        $array = array();
        $max = count($in);
        for ($i = 0; $i < $max; $i++) 
        {
            if ($selected == $in[$i]->$field)
            {
                $array["#__" . $in[$i]->$field] = $in[$i]->$field;
            }
            else
            {
                $array[$in[$i]->$field] = $in[$i]->$field;
            }
        }
        return $array;
    }

    /**
     *
     * @param type $table name of the table
     * @param type $idfield id of the value
     * @param type $valuefield property of the value
     * @param type $selected id or array of id of the selected value
     * @return array
     */
    public function getSelectValues($table, $idfield, $valuefield, $selected = null) 
    {
        $query = "SELECT * FROM `#__$table`";
        $this->Query($query);
        $va = $this->getNextObjectList();
        $array = array();
        foreach ($va as $v) 
        {
            if (is_array($selected) == true) 
            {
                if (array_search($v->$idfield, $selected) !== false)
                {
                    $array["#__" . $v->$idfield] = $v->$valuefield;
                }
                else
                {
                    $array[$v->$idfield] = $v->$valuefield;
                }
            }
            else 
            {
                if ($v->$idfield == $selected)
                {
                    $array["#__" . $v->$idfield] = $v->$valuefield;
                }
                else
                {
                    $array[$v->$idfield] = $v->$valuefield;
                }
            }
        }
        return $array;
    }

    /**
     * Optimizate the database
     *
     * @return boolean true on success or die in error
     */
    public function Optimizate() 
    {
        $this->Query("SHOW TABLES");
        $tables = $this->getNextObjectList();
        foreach ($tables as $tresult) 
        {
            foreach ($tresult as $k => $val) 
            {
                if ($this->Query("OPTIMIZE TABLE `" . $val . "`") != true)
                {
                    die($this->errormsg);
                }
            }
        }
        return true;
    }

}

?>
