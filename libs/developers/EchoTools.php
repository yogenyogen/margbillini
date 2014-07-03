<?php

class EchoTools
{
    static function PrintGlobal($global)
    {
        switch(strtolower($global))
        {
            case "post":
                self::printArrayLevel2($_POST);
                break;
             case "get":
                self::printArrayLevel2($_GET);
                break;
                
            case "server":
                self::printArrayLevel2($_SERVER);
                break;
           case "session":
                self::printArrayLevel2($_SESSION);
                break;
        }
        
    }
    
    private static function printArrayLevel2($arr)
    {
        foreach($arr as $k => $v)
        {
            if(is_array($v)== true)
            {
                echo "$k => ";
                foreach($v as $e)
                    echo " [$e] ";
                echo "<br/>";
            }
            else
                echo "$k => $v";

            echo "<br/>";
        }
    }
    
}
?>

