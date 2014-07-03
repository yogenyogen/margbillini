<?php

/**
 * Clase para validar strings
 *
 * @author V.A.
 */
class validator {
   
    /**
     * Determines if a string is a numeric one
     * @param type $str string to analize
     * @return bool true if is a numeric one.
     * 
     * @author Gabriel Elias González Disla
     */
    static function is_number($str)
    {
        return is_numeric($str);
    }
    
    /**
     * Determines if a string contains only chars.
     * @param type $str string to analize.
     * @return bool true if successful.
     * 
     * @author Gabriel Elias González Disla
     */
    static function is_chars($str)
    {
        return is_nan($str);
    }
    
    /**
     * Determines if a string is a phone number.
     * @param type $str string to analize.
     * @return bool true if successful.
     * 
     * @author Gabriel Elias González Disla
     */
    static function is_phone_number($str)
    {
        if(preg_match("/(^(([\+]\d{1,3})?[ \.-]?[\(]?\d{3}[\)]?)?[ \.-]?\d{3}[ \.-]?\d{4}$)/", $str))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    /**
     * Determines if a string is an email.
     * @param type $str string to analize.
     * @return bool true if successful.
     * 
     * @author ILoveJackDaniel. 
     * 
     */
    static function is_email($email)
    {
         // First, we check that there's one @ symbol, 
        // and that the lengths are right.
        if (!ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) {
            // Email invalid because wrong number of characters 
            // in one section or wrong number of @ symbols.
            return false;
        }
        // Split it into sections to make life easier
        $email_array = explode("@", $email);
        $local_array = explode(".", $email_array[0]);
        for ($i = 0; $i < sizeof($local_array); $i++) 
        {
            if(
                !ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&
                ↪'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$",
                $local_array[$i])) 
            {
                return false;
            }
        }
        // Check if domain is IP. If not, 
        // it should be valid domain name
        if (!ereg("^\[?[0-9\.]+\]?$", $email_array[1])) 
         {
            $domain_array = explode(".", $email_array[1]);
            if (sizeof($domain_array) < 2) 
            {
                return false; // Not enough parts to domain
            }
            for ($i = 0; $i < sizeof($domain_array); $i++) 
            {
                if(!ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|
                ↪([A-Za-z0-9]+))$", $domain_array[$i])) 
                {
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * checks if a string matches with the dominican
     * id format.
     * 
     * @param type $str string with the dominican id
     * @return boolean true if is a valid id
     * 
     * @author Alvin Durán
     */
    static function is_dominican_id($str)
    {
        $cedula = substr($str, 0, 3) . substr($str, 4, 7);
        $Verificador = $str[12];
        $suma = 0;
        for ($i = 0; $i < strlen($cedula); $i++)
        {
                $mod = "";
                if(($i % 2) == 0) 
                {
                    $mod = 1;
                } 
                else 
                {
                    $mod = 2;
                }
                $res = substr($cedula,$i,1) * $mod;
                if ($res > 9)
                {
                    $uno = substr($res,0,1);
                    $dos = substr($res,1,1);
                    $res = $uno + $dos;
                }
                $suma += $res;
        }

        $el_numero = (10 - ($suma % 10)) % 10;
        if ($el_numero == $Verificador && substr($cedula,0,3) != "000")
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}

?>
