<?php
require_once BASE_DIR . LIBS . HTML . HTML_PURIFIER;

/**
 * Description of AuxTools
 *
 * @author Jhuliano S. Moreno
 */
class AuxTools 
{

    /**
     * Purifies the given html, removing malicious code,
     * missing ending tags, illegal nesting, deprecated tags,
     * invalid css and preserving rich formatting.
     *
     * @param  string $html HTML to purify
     *
     * @return string Purified HTML
     */
    static function Purify_HTML($html) 
    {
        $h_purifier = new HTMLPurifier();
        return $h_purifier->purify($html);
    }

    /**
     * Prints human-readable information about a variable
     *
     * @param mixed $expression <p>
     * The expression to be printed.
     * </p>
     * @param bool $return [optional] <p>
     * If you would like to capture the output of <b>print_r</b>,
     * use the <i>return</i> parameter. When this parameter is set
     * to true, <b>print_r</b> will return the information rather than print it.
     * </p>
     * @return mixed If given a string, integer or float,
     * the value itself will be printed. If given an array, values
     * will be presented in a format that shows keys and elements. Similar
     * notation is used for objects.
     * </p>
     * <p>
     * When the <i>return</i> parameter is true, this function
     * will return a string. Otherwise, the return value is true.
     */
    static function printr($expression, $return = false) 
    {
        $var = print_r($expression, true);
        $value = "<pre>$var</pre>";
        if ($return == false) 
        {
            print($value);
        } 
        else 
        {
            return $value;
        }
    }
    
    /**
     * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
     * Outputs or returns a parsable string representation of a variable
     * @link http://php.net/manual/en/function.var-export.php
     * @param mixed $expression <p>
     * The variable you want to export.
     * </p>
     * @param bool $return [optional] <p>
     * If used and set to <b>TRUE</b>, <b>var_export</b> will return
     * the variable representation instead of outputting it.
     * </p>
     * @return mixed the variable representation when the <i>return</i>
     * parameter is used and evaluates to <b>TRUE</b>. Otherwise, this function will
     * return <b>NULL</b>.
     */
    static function var_export($expression, $return = false) 
    {
        $var = var_export($expression, true);
        $value = "<pre>$var</pre>";
        if ($return == false) 
        {
            print($value);
        } 
        else 
        {
            return $value;
        }
    }
    
    /**
     * Checks if super global contains key
     * The following values for TYPE are possible:
     * - INPUT_POST
     * - INPUT_GET
     * - INPUT_COOKIE
     * - INPUT_ENV
     * - INPUT_SERVER
     * - INPUT_SESSION
     * - INPUT_REQUEST
     * 
     * @param integer $type
     * @param string $key
     * @return boolean true if the global contains key false otherwise
     */
    public static function filter_has_var($type, $key)
    {
        switch($type)
        {
            case INPUT_SESSION:
            case 'INPUT_SESSION':
            {
                $r = isset($_SESSION[$key]);
                break;
            }
            case INPUT_REQUEST:
            case 'INPUT_REQUEST':
            {
                $r = isset($_REQUEST[$key]);
                break;
            }
           
            default:
            {
                $r = filter_has_var($type, $key);
                break;
            }
        }
        return $r;
    }

    /**
     * Pone las tildes en formato HTML sin remover los tags de HTML (usado para los textos que salen de los editores de joomla).
     * @param string $input         el texto que se desea codificar
     * @return string el texto codificado.
     */
    static function htmlentitiesOutsideHTMLTags($input) 
    {
        $list = get_html_translation_table(HTML_ENTITIES);
        unset($list["\""]);
        unset($list["<"]);
        unset($list[">"]);
        unset($list["&"]);
        $string = strtr($input, $list);
        return $string;
    }

    /**
     * Gets all the objects properties
     *
     * @param type $objref reference of the object
     * @return array object properties
     */
    static function getPropertiesFromObj($objref) 
    {
        $result = array();
        foreach ($objref as $property => $value) 
        {
            $result[$property] = $value;
        }
        return $result;
    }

    /**
     * Limpia una string contra ataques SQL Injection
     *
     * @param string $value variable a ser verificada
     * @param link
     *
     * @return string limpio de SQL Injection
     */
    static function AntiInjection($value, $dblink) 
    {
        //Remueve algunas palabras y caracteres de sintaxis SQL que sean peligrosos.
        //$sql = preg_replace(sql_regcase("/(drop table|insert into|\"|'|show tables|\*|--|\\\\)/"),"",$sql);
        $value = mysqli_real_escape_string($dblink, $value);
        return $value;
    }

    /**
     * Obtiene el ID del idioma actual de joomla
     * @return int con el ID del current language
     */
    static function GetCurrentLanguageIDJoomla() 
    {
        $code = self::GetCurrentLanguageJoomla();
        $lang = new languages(-1);
        return $lang->find("lang_code", $code)->lang_id;
    }

    //OBTIENE EL PREFIJO DE LA BASE DE DATOS PARA EL IDIOMA ACTUAL
    static function GetCurrentLanguageJoomla() 
    {
        if (defined('_JEXEC') == true) 
        {
            //OBTENER LENGUA USADA
            $lang = JFactory::getLanguage();
            $lang_tag = $lang->getTag();
            //SI EL LENGUAGE EXISTE USARLO
            if ($lang_tag) 
            {
                return $lang_tag;
            }
            //SI NO EXISTE BUSCAR EL DEFAULT
            else 
            {
                $lang_tag = $lang->getDefault();
                return $lang_tag;
            }
        } 
        else 
        {
            die("Joomla API is not included.");
        }
    }
    
    private static function loadJoomlaUsers($rows, $mode=1)
    {
        $users = array();
        foreach ($rows as $user) 
        {
            $u = JFactory::getUser($user->user_id);
            if($mode == 1)
            {
                $users = array_merge($users, array($u->id => $u->email));
            }
            else
            {
                $users[] = ($u->email);
            }
        }
        return $users;
    }

    /**
     * Gets a hash of user_id => email
     * @return array
     */
    static function GetJoomlaAdminUsers() 
    {
        $users = array();
        if (defined("_JEXEC") == true) 
        {
            $jconfig = new JConfig();
            $dbprefix = $jconfig->dbprefix;
            $query = "Select distinct user_id from " . $dbprefix . "_user_usergroup_map where group_id = 8 or group_id = 7";
            $db = JFactory::getDbo();
            $db->setQuery($query);
            $db->query();
            $rows = $db->loadObjectList();
            if ($rows) 
            {
                $users = self::loadJoomlaUsers($rows);
            }
        }
        return $users;
    }

    /**
     * Gets an array of emails of the admin users
     * @return array
     */
    static function GetJoomlaAdminUsersEmails() 
    {
        if (defined("_JEXEC") == true) 
        {
            $users = array();
            $jconfig = new JConfig();
            $dbprefix = $jconfig->dbprefix;
            $query = "Select distinct user_id from " . $dbprefix . "user_usergroup_map where group_id = 8 or group_id = 7";
            $db = JFactory::getDbo();
            $db->setQuery($query);
            $db->query();
            $rows = $db->loadObjectList();
            if ($rows) 
            {
                $users = self::loadJoomlaUsers($rows, $mode=2);
            }
        }
        return $users;
    }

    /**
     * Createas a valid alias string
     *
     * @param string $str
     * 
     * @return string alias string
     */
    static function CreateAliasFromString($str) 
    {
        $trans = array(
            'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ä' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'Eth',
            'Ñ' => 'N', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
            'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ä' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'eth',
            'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y',
            'ß' => 'sz', 'þ' => 'thorn', 'ÿ' => 'y', "¡" => "", "¿" => "",
            "." => "", "?" => "", "@" => "", "/" => ""
        );

        return strtolower(str_replace(" ", "-", strtr($str, $trans)));
    }

    /**
     * Obtiene el mensaje equivaliente de un código de mensaje
     *
     * @param int $code     el código del mensaje.
     * @return string con el mensage de error en un DIV con ID correspondiente a exito o no.
     */
    static function GetMessageFromCode($code) 
    {
        //ESTANDARD DE CODIGO DE MENSAJE:
        //PRIMER NÚMERO = TIPO DE MENSAJE (1 = exito, 2 = error, 3 = información, 4 = warning)
        switch ($code) 
        {
            case 1001:
                return "<div id=\"pyt_success\">Se han guardado los datos exitosamente</div>";
                
            case 2001:
                return "<div id=\"pyt_error\">Hubo un problema al guardar los datos</div>";
                
            default:
                return "<div id=\"pyt_warn\">MSG CODE $code NOT FOUND</div>";
        }
    }

    static function xml_entity_decode($text, $charset = 'Windows-1252') 
    {
        // Double decode, so if the value was &amp;trade; it will become Trademark
        $text_1 = html_entity_decode($text, ENT_COMPAT, $charset);
        return html_entity_decode($text_1, ENT_COMPAT, $charset);
    }

    static function xml_entities($text, $charset = 'Windows-1252') 
    {
        // Debug and Test
        // $text = "test &amp; &trade; &amp;trade; abc &reg; &amp;reg; &#45;";
        // First we encode html characters that are also invalid in xml
        $text = htmlentities($text, ENT_COMPAT, $charset, false);

        // XML character entity array from Wiki
        // Note: &apos; is useless in UTF-8 or in UTF-16
        $arr_xml_special_char = array("&amp;", "&apos;", "&lt;", "&gt;");

        // Building the regex string to exclude all strings with xml special char
        $arr_xml_special_char_regex = "(?";
        foreach ($arr_xml_special_char as $key => $value) 
        {
            $arr_xml_special_char_regex .= "(?!$value)";
        }
        $arr_xml_special_char_regex .= ")";

        // Scan the array for &something_not_xml; syntax
        $pattern = "/$arr_xml_special_char_regex&([a-zA-Z0-9]+;)/";

        // Replace the &something_not_xml; with &amp;something_not_xml;
        $replacement = '&amp;${1}';
        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * Change the selected value from an array
     *
     * @param array $array associative array of values "value"=> "text"
     * @param array $selectedvals array of selected values
     * @return array associative array of "value"=> "text"
     */
    static function Change_Selected_Values($array, $selectedvals = array()) 
    {
        $temp = array();
        foreach ($array as $k => $val) {
            $key = $k;
            if (stripos($k, '#__') !== false)
            {
                $key = str_replace('#__', '', $key);
            }
            if (array_search($key, $selectedvals) !== false)
            {
                $key = "#__" . $key;
            }

            $temp[$key] = $val;
        }
        return $temp;
    }

    static function getRealIpAddr() 
    {
        if (filter_input(INPUT_SERVER,'HTTP_CLIENT_IP')) 
        {   //check ip from share internet
            $ip = filter_input(INPUT_SERVER,'HTTP_CLIENT_IP');
        }
        elseif (filter_input(INPUT_SERVER,'HTTP_X_FORWARDED_FOR')) 
        {   //to check ip is pass from proxy
            $ip = filter_input(INPUT_SERVER,'HTTP_X_FORWARDED_FOR');
        } 
        else 
        {
            $ip = filter_input(INPUT_SERVER,'REMOTE_ADDR');
        }
        return $ip;
    }

    /**
     *  Generates an organize file array from the superglobal $_FILE
     *  @param array $files file array
     *  @return array
     */
    static function GlobalFileArrayFix($files) 
    {
        $farr = array();
        $maxfile = count($files['name']);
        for ($i = 0; $i < $maxfile; $i++) 
        {
            $farr[$i] = array("name" => $files['name'][$i], "type" => $files['type'][$i],
                "tmp_name" => $files['tmp_name'][$i], "error" => $files['error'][$i],
                "size" => $files['size'][$i]);
        }
        return $farr;
    }

    /**
     * Forzar que el archivo PHP se llame desde HTTPS y el URL con www
     * @example http://mydomain.com se redirecciona a https://www.mydomain.com
     */
    static function forzarHTTPSandWWW() 
    {
        if ((strpos(filter_input(INPUT_SERVER, 'HTTP_HOST'), 'www.') === false)) 
        {
            header('Location: https://www.' . filter_input(INPUT_SERVER, "SERVER_NAME") . filter_input(INPUT_SERVER, "REQUEST_URI"));
            exit();
        }
        if (filter_input(INPUT_SERVER, "HTTPS") != "on") 
        {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location: https://" . filter_input(INPUT_SERVER, "SERVER_NAME") . filter_input(INPUT_SERVER, "REQUEST_URI"));
            exit();
        }
    }

    static function setJoomlaAlertMessage($str_msg, $type = 'message') 
    {
        if (defined('_JEXEC') == true) 
        {
            JFactory::getApplication()->enqueueMessage($str_msg, $type);
        }
    }

    /**
     * Gets the current time object
     * @param string $tz valid PHP:DateTimeZone
     * @return DateTime
     */
    static function DateTimeCurrent($tz = LIB_TIMEZONE) 
    {
        $dttz = new DateTimeZone($tz);
        $current_date = new DateTime("now", $dttz);
        return $current_date;
    }
    
    /**
     * Gets the current time as string 
     * @param string $format string with the format of the date
     * @param string $tz valid PHP:DateTimeZone
     * Mysql DateFormat as default value 
     * 
     * @return string 
     */
    static function DateTimeCurrentString($format="Y-m-d H:i:s", $tz = LIB_TIMEZONE) 
    {
        return self::DateTimeCurrent($tz)->format($format);
    }

    /**
     * Gets the datetime object
     * @param string $date_str valid PHP:DateTime date string
     * @param string $tz valid PHP:DateTimeZone format
     * @return DateTime datetime object
     */
    static function DateTimeGenerate($date_str, $tz = LIB_TIMEZONE) 
    {
        $dttz = new DateTimeZone($tz);
        $current_date = new DateTime($date_str, $dttz);
        return $current_date;
    }

    /**
     *
     * @param string $file_uri the URI to check for the remote file
     * @return bool Returns whether the file could be reached or not.
     */
    static function remote_file_exists($file_uri) 
    {
        if (AuxTools::isValidURL($file_uri)) {
            if (ini_get('allow_url_fopen') == 0) {
                trigger_error('ERROR: allow_url_fopen is not enabled on this server', E_USER_WARNING);
                return false;
            }

            $handle = fopen($file_uri, 'r');

            if ($handle) 
            {
                fclose($handle);
                return true;
            }
        }
        else
        {
            return file_exists(BASE_DIR . $file_uri);
        }
        return false;
    }

    /**
     *
     * @param string $url URL to check
     * @return bool Returns if the URL is a valid URL or not
     */
    static function isValidURL($url) 
    {
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }
    
    /**
     * Generate random bytes.
     *
     * @param   integer  $length  Length of the random data to generate
     * @param   bool   $strong  If passed into the function, this will hold a boolean value that determines
     * if the algorithm used was "cryptographically strong", e.g., safe for usage with GPG,
     * passwords, etc. TRUE if it did, otherwise FALSE.
     * 
     * @return  string  Random binary data
     */
    static function genRandomBytes($length = 16, $strong = false)
    {
            $sslStr = '';
            /*
                * if a secure randomness generator exists and we don't
                * have a buggy PHP version use it.
                */
            if (function_exists('openssl_random_pseudo_bytes')
                    && (version_compare(PHP_VERSION, '5.3.4') >= 0
                            || substr(PHP_OS, 0, 3) !== 'WIN'
                    )
            )
            {
                    $sslStr = openssl_random_pseudo_bytes($length, $strong);
                    if ($strong)
                    {
                            return $sslStr;
                    }
            }

            /*
                * Collect any entropy available in the system along with a number
                * of time measurements of operating system randomness.
                */
            $bitsPerRound = 2;
            $maxTimeMicro = 400;
            $shaHashLength = 20;
            $randomStr = '';
            $total = $length;

            // Check if we can use /dev/urandom.
            $urandom = false;
            $handle = null;
            if (function_exists('stream_set_read_buffer') && is_readable('/dev/urandom'))
            {
                    $handle = fopen('/dev/urandom', 'rb');
                    if ($handle)
                    {
                            $urandom = true;
                    }
            }

            while ($length > strlen($randomStr))
            {
                    $bytes = ($total > $shaHashLength)? $shaHashLength : $total;
                    $total -= $bytes;
                    /*
                        * Collect any entropy available from the PHP system and filesystem.
                        * If we have ssl data that isn't strong, we use it once.
                        */
                    $entropy = rand() . uniqid(mt_rand(), true) . $sslStr;
                    $entropy .= implode('', fstat(fopen( __FILE__, 'r')));
                    $entropy .= memory_get_usage();
                    $sslStr = '';
                    if ($urandom)
                    {
                            stream_set_read_buffer($handle, 0);
                            $entropy .= fread($handle, $bytes);
                    }
                    else
                    {
                            /*
                                * There is no external source of entropy so we repeat calls
                                * to mt_rand until we are assured there's real randomness in
                                * the result.
                                *
                                * Measure the time that the operations will take on average.
                                */
                            $samples = 3;
                            $duration = 0;
                            for ($pass = 0; $pass < $samples; ++$pass)
                            {
                                    $microStart = microtime(true) * 1000000;
                                    $hash = sha1(mt_rand(), true);
                                    for ($count = 0; $count < 50; ++$count)
                                    {
                                            $hash = sha1($hash, true);
                                    }
                                    $microEnd = microtime(true) * 1000000;
                                    $entropy .= $microStart . $microEnd;
                                    if ($microStart > $microEnd) 
                                    {
                                            $microEnd += 1000000;
                                    }
                                    $duration += $microEnd - $microStart;
                            }
                            $duration = $duration / $samples;

                            /*
                                * Based on the average time, determine the total rounds so that
                                * the total running time is bounded to a reasonable number.
                                */
                            $rounds = (int)(($maxTimeMicro / $duration) * 50);

                            /*
                                * Take additional measurements. On average we can expect
                                * at least $bitsPerRound bits of entropy from each measurement.
                                */
                            $iter = $bytes * (int) ceil(8 / $bitsPerRound);
                            for ($pass = 0; $pass < $iter; ++$pass)
                            {
                                    $microStart = microtime(true);
                                    $hash = sha1(mt_rand(), true);
                                    for ($count = 0; $count < $rounds; ++$count)
                                    {
                                            $hash = sha1($hash, true);
                                    }
                                    $entropy .= $microStart . microtime(true);
                            }
                    }

                    $randomStr .= sha1($entropy, true);
            }
            if ($urandom)
            {
                    fclose($handle);
            }

            return substr($randomStr, 0, $length);
    }
    
    /**
     * Gets the direction from a php directory path to js rootless path.
     * @param string $phpdirpath folder to get the path.
     * 
     * @return string path to the folder prepared for JS
     */
    static function getJSPathFromPHPDir($phpdirpath)
    {
        $needle = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT');
        $iswin = false;
        if(stripos($phpdirpath, DIRECTORY_SEPARATOR) === false)
        {
            $iswin = true;
        }
        if($iswin === true)
        {
            $winDS = '\\';
            $phpdirpath = str_replace($winDS, DIRECTORY_SEPARATOR, $phpdirpath);
        }
        if($needle!="")
        {
            $arr = explode($needle, $phpdirpath);
        }
        $path=DIRECTORY_SEPARATOR;
        if(isset($arr[1]))
        {
            $path.=DIRECTORY_SEPARATOR.$arr[1];
        }
        return $path;
    }
    
    /**
     * Gets an html with querys
     * @param type $return true if you want to return the output
     * @return string html reporting database querys
     */
    static function DatabaseDebugging($return = false)
    {
        $html="<div class=\"span12\"><h3>Query Reporting</h3><table border=\"1\" class=\"db-debug\">";
        $html.="<th>Query</th><th>Error Reporting</th><th># Rows</th>";
        foreach(dbprovider::getDebugReport() as $report)
        {
            $html.="<tr>";
            foreach($report as $k => $v)
            {
                $html.="<td>$v</td>";
            }
            $html.="</tr>";
        }
        dbprovider::setDebugReport();
        $html.="</table>"."</div>";
        if($return === true)
            return $html;
        echo $html;
    }

    /**
     * Converts a string to a serach engine friendly one
     * 
     * @param string $str string to convert
     * @param string $encode string with the encoding of the string
     * @return string SEF ready string
     */
    static function SEFReady($str, $encode='UTF-8')
    {
        $trans = array(
            "." => "", "?" => "", "&" => "", "/" => "", "'"=>"","\""=>"",
            "\\"=>"", "”"=>"", "“"=>""
        );
        $str_1=strtr($str, $trans);
        $str=str_replace(" ", "-", $str_1);
        return mb_strtolower($str, $encode);
    }
    
    /**
     * Formats a string like money
     * 
     * @param double $total double with the price to convert.
     * @param string $curr currency code string US$, RD$, EUR
     * @param double $conver_rate convertion rate
     * @param integer $style 1 currency at left, 2 currency at right.
     * 
     * @return string formated money
     */
    static function MoneyFormat($total, $curr=DEFAULT_CURRENCY, $conver_rate=DEFAULT_CONVERTION_RATE, $style=1)
    {
        $total_1 = $total*$conver_rate;
        $total = round($total_1, 2);
        switch($style)
        {
            case 1:
                return "<span class=\"curency_tag\">".$curr."</span>".$total;
            
            case 2:
                return $total."<span class=\"curency_tag\">".$curr."</span>";
            
        }
    }
    
    
}