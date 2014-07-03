<?php
if (!defined('_EXEC')) 
{
    die("Restricted Access");
}

/**
 * Object for getting information about directories
 *
 * @author Gabriel
 */
class oDirectory
{

    /**
     * gets the directory path from a file
     *
     * @param string $path path of the file
     * @return string|boolean string with the directory path or false otherwise.
     */
    public static function getDirectory($path) 
    {
        if (is_file($path) == true)
        {
            return dirname($path);
        }
        else
        {
            return false;
        }
    }

    /**
     * Gets an hash of files=>path of files in a directory
     *
     * @param type $path
     * @return array|boolean array with the directory files or false otherwise.
     */
    public static function getFilesFromDirectory($path) 
    {
        if (is_dir($path) == true) 
        {
            $files = array();
            $dirhandler = opendir($path);
            if ($dirhandler != false) 
            {
                /* This is the correct way to loop over the directory. */
                while (($entry = readdir($dirhandler)) !== false) 
                {
                    if ($entry != "." && $entry != "..") 
                     {
                        if (strripos($entry, ".") !== false)
                        {
                            $files[$entry] = $path . DS . $entry;
                        }
                    }
                }
                return $files;
            }
            return false;
        }
        return false;
    }

    /**
     * Gets an hash of files=>path of directories in a directory
     *
     * @param type $path
     * @return array|boolean array with the directory files or false otherwise.
     */
    public static function getDirectoriesFromDirectory($path) 
    {
        if (is_dir($path) == true) 
        {
            $files = array();
            $dirhandler = opendir($path);
            if ($dirhandler != false) 
            {
                /* This is the correct way to loop over the directory. */
                while (false !== ($entry = readdir($dirhandler))) 
                {
                    if ($entry != "." && $entry != "..") 
                    {
                        if (strripos($entry, ".") === false)
                        {
                            $files[$entry] = $path . DS . $entry;
                        }
                    }
                }
                return $files;
            }
            return false;
        }
        return false;
    }
    
    /**
     * Array of views per component
     * @param string $components_root path to the components folder
     * @return array 
     */
    public static function getAllViewsFromComponents($components_root)
    {
        $views=array();
        $components = array();
        $components = self::getDirectoriesFromDirectory($components_root);
        foreach($components as $entry => $path)
        {
            $views[$entry]=self::getViewsFromComponent($path);
        }
        return $views;
    }
    
    private static function getViewsFromComponent($comp_root)
    {
        $result=array();
        $views_root = $comp_root.VIEWS;
        $views=self::getDirectoriesFromDirectory($views_root);
        foreach($views as $view => $path)
        {
            $tmpl_path = $path.DS.TMPL;
            $layouts = self::getFilesFromDirectory($tmpl_path);
            $final_layouts = array();
            foreach($layouts as $entry => $path)
            {
                if(strpos($path, 'index.html')!== false)
                {
                    unset($layouts[$entry]);
                }
                else
                {
                    $final_layouts[$entry]=$path;
                }
            }
            if(count($final_layouts)>0)
            {
                $result[$view]=$final_layouts;
            }
        }
        return $result;
    }

    /**
     * Gets an hash of files=>path of directories in a directory
     *
     * @param type $path
     * @return array|boolean array with the directory files or false otherwise.
     */
    public static function getExtFilesFromDirectory($path) 
    {
        if (is_dir($path) == true) 
        {
            $files = array();
            $dirhandler = opendir($path);
            if ($dirhandler != false) 
            {
                /* This is the correct way to loop over the directory. */
                while (false !== ($entry = readdir($dirhandler))) 
                {
                    if ($entry != "." && $entry != "..") 
                    {
                        if (strripos($entry, ".") !== false)
                        {
                            $files[$entry] = $path . DS . $entry;
                        }
                    }
                }
                return $files;
            }
            return false;
        }
        return false;
    }

    /**
     * Path of the directory to load the classes
     * @param type $opath
     * @return string
     */
    public static function loadClassesFromDirectory($opath) 
    {
        $directories = self::getDirectoriesFromDirectory($opath);
        $files = self::getFilesFromDirectory($opath);
        $loaded_files = array();
        if ($files) 
        {
            foreach ($files as $fname => $path) 
            {
                require_once $path;
                $loaded_files[$fname] = '';
            }
        }
        if (count($directories) > 0)
        {
            foreach ($directories as $fname => $path) 
            {
                array_merge($loaded_files, self::loadClassesFromDirectory($path));
            }
        }
        return $loaded_files;
    }

}

?>
