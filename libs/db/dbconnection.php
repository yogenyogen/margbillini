<?php

/*
 * Definiciones del path para incluir la clase jConfig que provee
 * el usuario y contraseña de la BD
 */
if (class_exists("dbconfig") == false) 
{
    if (defined('DS') != true) 
    {
        define('DS', '/');
    }
    $dbconfigpath = dirname(dirname(__FILE__)) . DS . CONFIG_FILE;
    if (file_exists($dbconfigpath) == false)
    {
        die("Database has no configuration file config.php, path = $dbconfigpath");
    }
    else
    {
        require_once $dbconfigpath;
    }
}

/**
 * Clase que permite acceder a la base de datos
 *
 * @author Gabriel Elias González Disla
 */
class dbconnect extends mysqli 
{

    /**
     * @var string string con el usuario de la DB
     */
    private $user = " ";

    /**
     * @var string string con la contraseña del usuario
     */
    private $pass = " ";

    /**
     * @var string string con la base de datos
     */
    var $dbname = " ";

    /**
     *
     * @var string con el prejito de la base de datos
     */
    var $dbprefix = " ";

    /**
     *
     * @var bool para el manejo de la conexion
     */
    private static $checklink = false;

    /**
     * @var dbconnect indica la instacia estatica de la clase
     */
    private static $instance = null;

    /**
     * inicializa la clase, asignando el usuario y contraseña de la DB y realizando la conexion con esta
     */
    private function __construct() 
    {
        if (defined('_JEXEC') == true) 
        {
            $c = new jdbconfig();
        } 
        else 
        {
            $c = new config();
        }
        $this->pass = $c->password;
        $this->user = $c->user;
        $this->dbname = $c->db;
        $this->dbprefix = $c->dbprefix;
        parent::__construct($c->host, $this->user, $this->pass, $this->dbname);
        unset($c);
    }

    /**
     * Destructor de la clase
     */
    public function __destruct() 
    {

        $this->db_disconnect();
        self::$instance = null;
        $this->dbname = null;
        $this->pass = null;
        $this->user = null;
    }

    /**
     * Obtiene la unica instancia de la clase
     * @return dbconnect con la instancia de la clase
     */
    public static function getInstance() 
    {
        if (!self::$instance) 
        {
            self::$instance = new dbconnect();
            if (self::$instance) 
            {
                self::$checklink = true;
            } 
            else 
            {
                echo "No se pudo realizar la conexi&oacute;n";
            }
        }
        return self::$instance;
    }

    /**
     * Termina la conexión con la base de datos
     */
    private function db_disconnect() 
    {
        if (self::$checklink != false) 
        {
            $this->close();
            self::$checklink = false;
            unset($this);
        }
    }

    /**
     *
     * @return bool true si hay conexión, false en el caso contrario
     */
    static function getcheckcon() 
    {
        return self::$checklink;
    }

}

?>
