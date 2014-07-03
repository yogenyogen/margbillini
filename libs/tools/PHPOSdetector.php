<?php

/**
 * Description of OSdetector
 *
 * @author Gabriel Elias González Disla
 */
class PHPOSdetector 
{
    
    var $IS_WIN=false;
    var $IS_MAC=false;
    var $IS_UNIX=false;
    var $IS_ON="";
    
    private function __construct() 
    {
        // Detect the native operating system type.
        $os = strtoupper(substr(PHP_OS, 0, 3));
        if (!defined('IS_WIN'))
        {
                $this->IS_WIN = (($os === 'WIN') ? true : false);
                $this->IS_ON = "WIN";
        }
        if (!defined('IS_MAC'))
        {
                $this->IS_MAC = (($os === 'MAC') ? true : false);
                $this->IS_ON = "MAC";
        }
        if (!defined('IS_UNIX'))
        {
                $this->IS_UNIX = ((($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
                $this->IS_ON = "UNIX";
        }
    }
    
    /**
     * Gets the operative system of the server
     * 
     * @return string name of the operative system
     */
    public static function getOS()
    {
        $m = new PHPOSdetector();
        return $m->IS_ON;
    }
    
    /**
     * Gets the client operative system
     * 
     * @return string client operative System
     */
    public static function getUserOS() 
    { 
        $user_agent     =   filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        $os_platform    =   "Unknown OS Platform";
        $os_array       =   array(
                                '/windows nt 6.3/i'     =>  'Windows 8.1',
                                '/windows nt 6.2/i'     =>  'Windows 8',
                                '/windows nt 6.1/i'     =>  'Windows 7',
                                '/windows nt 6.0/i'     =>  'Windows Vista',
                                '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                                '/windows nt 5.1/i'     =>  'Windows XP',
                                '/windows xp/i'         =>  'Windows XP',
                                '/windows nt 5.0/i'     =>  'Windows 2000',
                                '/windows me/i'         =>  'Windows ME',
                                '/win98/i'              =>  'Windows 98',
                                '/win95/i'              =>  'Windows 95',
                                '/win16/i'              =>  'Windows 3.11',
                                '/macintosh|mac os x/i' =>  'Mac OS X',
                                '/mac_powerpc/i'        =>  'Mac OS 9',
                                '/linux/i'              =>  'Linux',
                                '/ubuntu/i'             =>  'Ubuntu',
                                '/iphone/i'             =>  'iPhone',
                                '/ipod/i'               =>  'iPod',
                                '/ipad/i'               =>  'iPad',
                                '/android/i'            =>  'Android',
                                '/blackberry/i'         =>  'BlackBerry',
                                '/webos/i'              =>  'Mobile'
                            );

            foreach ($os_array as $regex => $value) 
            { 
                if (preg_match($regex, $user_agent)) 
                {
                    $os_platform    =   $value;
                }
            }   
            return $os_platform;
    }

    /**
     * Gets the client browser
     * 
     * @return string client browser
     */
    public static function getUserBrowser() 
    {
        $user_agent     =   filter_input(INPUT_SERVER,'HTTP_USER_AGENT');
        $browser        =   "Unknown Browser";
        $browser_array  =   array(
                                '/msie/i'       =>  'Internet Explorer',
                                '/firefox/i'    =>  'Firefox',
                                '/safari/i'     =>  'Safari',
                                '/chrome/i'     =>  'Chrome',
                                '/opera/i'      =>  'Opera',
                                '/netscape/i'   =>  'Netscape',
                                '/maxthon/i'    =>  'Maxthon',
                                '/konqueror/i'  =>  'Konqueror',
                                '/mobile/i'     =>  'Handheld Browser'
                            );
        foreach ($browser_array as $regex => $value) 
        { 
            if (preg_match($regex, $user_agent)) 
            {
                $browser    =   $value;
            }
        }
        return $browser;
    }
    
}

?>
