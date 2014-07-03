<?php

if (defined('_JEXEC') == true) 
{
    require_once JPATH_ROOT . "/configuration.php";

    class jdbconfig extends JConfig 
    {

    }

} 
else 
{

    class config 
    {

        public $user = 'root';
	public $password = '';
	public $db = 'fittizen_test';
	public $dbprefix = '';
        public $host = 'localhost';
        public $smtpuser = "";
        public $smtpsecure = "";
        public $smtppass = "";
        public $smtpauth = "";
        public $smtpport = "";
        public $smtphost = "";

    }

    
}

