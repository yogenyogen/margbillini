<?php

/**
 * This class obtain the predefined views on the define.php of the component
 *
 * @author Gabriel Elias González Disla
 */
class Constants {
    
    /**
     * Get the administration views from a defined component
     * @param type $comp_name the component name
     * @param type $mode 1 on define terminal. example COMP_PORT_ADMIN_VIEW_CATEGORIES
     * ('CATEGORIES') or $mode = 2 it will be the return on URL mode 'categories'
     * 
     * @return array An array of strings with the names of the view
     */
    static public function getAdminViewsConstants($comp_name, $mode=2)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $result = array();
        foreach($defined_vars as $var => $val)
        {
            if((strripos($var, $comp_name) !== false) && (strripos($var, 'ADMIN_VIEW') !== false))
            {
                if($mode == 2)
                {
                 $temp=str_replace('/', '', $val);
                 $temp[0] = strtoupper($temp[0]); 
                 $result[]=$temp;
                }
                else
                {
                    $result[]= substr($var, strripos($var, '_'), strlen($var)-strripos($var, '_'));
                }
            }
        }  
        return $result;
    }
    
    /**
     * Get the administration views from a defined component
     * @param type $comp_name the component name
     * @param type $lang components language
     * ('CATEGORIES') or $mode = 2 it will be the return on URL mode 'categories'
     * 
     * @return string Constant value, null if not found
     */
    static public function getAdminViewsConstantName($comp_name, $var_name, $lang)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $key= $comp_name."_".$var_name."_".$lang;
        if(array_key_exists($key, $defined_vars ) == true)
        {
            return $defined_vars[$key];
        }
        else
        {
            return null;
        }
    }
    
    /**
     * Get the languages a defined component
     * @param type $comp_name the component name
     * @param type $mode 1 on define terminal. example COMP_PORT_ADMIN_VIEW_CATEGORIES
     * ('CATEGORIES') or $mode = 2 it will be the return on URL mode 'categories'
     * 
     * @return array An array of strings with the names of the view
     */
    static public function getLanguageConstants($comp_name, $mode=2)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $result = array();
        foreach($defined_vars as $var => $val)
        {
            if((strripos($var, $comp_name) !== false) && (strripos($var, 'LANGS') !== false))
            {
                if($mode == 2)
                {
                    $result[]=str_replace('/', '', $val);
                }
                else
                {
                    $result[]= substr($var, strripos($var, '_'), strlen($var)-strripos($var, '_'));
                }
            }
        }  
        return $result;
    }
    
    /**
     * Finds the define variable name value
     * 
     * @param type $var_name define variable name
     * @return string value of the variable to get 
     */
    static public function getConstant($var_name)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $result = "";
        if(array_key_exists($var_name, $defined_vars) == true)
        {
            return $defined_vars[$var_name];
        }
        if($result == "")
        {
            die("Error! defined constant $var_name not found");
        }
        return $result;
    }
    
    /**
     * Finds the constants of a component
     * @param type $comp_name component name
     * @param type $var_name variable name
     * @return string value of the variable to get 
     */
    static public function getLanguageConstant($comp_name, $var_name)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $result = "";
        foreach($defined_vars as $var => $val)
        {
            if((strripos($var, $comp_name) !== false) && (strripos($var, $var_name) !== false))
            {
                    $result=str_replace('/', '', $val);
                    break;
            }
        }  
        if($result == "")
        {
            die("Error! defined constant $comp_name$var_name not found");
        }
        return $result;
    }
    
    static public function getLanguageName($lang_tag)
    {
        $defined_vars = get_defined_constants('USER');
        $defined_vars = $defined_vars['user'];
        $ret = "";
        $letag = 'LANG_NAME'.strtoupper($lang_tag);
        if(array_key_exists($letag,$defined_vars) == true)
        {
            return $defined_vars[$letag];
        }
        if($ret == "")
            die("Language constant not found");
        
        
    }
    
    /**
     * Finds the MySQLi data types
     * @return array An array of strings with the names of
     * mysql data types.
     */
    static public function getMySqliDataTypes()
    {
        $needle = 'MYSQLI_TYPE';
        $result = array();
        $defined_vars = get_defined_constants();
        foreach($defined_vars as $var => $val)
        {
            if((strripos($var, $needle) !== false))
            {
                $pos = strripos($var, $needle) + strlen($needle);
                $key= substr($var, $pos+1, strlen($var));
                $result[$key]=$val;
            }
        }
        return $result;
    }
    
    /**
     * Finds the MySQL data types
     * @return array An array of strings with the names of
     * mysql data types.
     */
    static public function getMySqlDataTypes()
    {
        $needle = 'MYSQL_TYPE';
        $result = array();
        $defined_vars = get_defined_constants();
        foreach($defined_vars as $var => $val)
        {
            if((strripos($var, $needle) !== false))
            {
                $pos = strripos($var, $needle) + strlen($needle);
                $key= substr($var, $pos+1, strlen($var));
                $result[$key]=$val;
            }
        }
        return $result;
    }
}
?>
