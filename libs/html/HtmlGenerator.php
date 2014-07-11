<?php

if (!defined('_EXEC')) {
    die("Restricted Access");
}
require_once BASE_DIR . LIBS . TOOLS . CONSTANTS;
require_once BASE_DIR . LIBS . DB . DBOBJECT;
require_once BASE_DIR . LIBS . LANGUAGES;

/**
 * Object for html generation
 *
 * @author Gabriel
 */
class HtmlGenerator {

    /**
     * Generates an list
     *
     * @param array $menuobjref list of menu elements objects
     * @param int  $mode integer different from 1 that indicates if the list
     * is ordered (<ol>) and 1 if is an ordered one.
     *
     * @example THIS FUNCTION MUST BE USED ON MENU ELEMENTS OBJECTS. with the
     * following form
     *
     * class temp extends dbobject{
     *
     *      var $Id;
     *      var $name;
     *      var $action;
     * }
     *
     * $elem=new temp();
     * $menuelems=$elem->getAll();
     *
     * htmlgenobj::menulistgenerator($menuelems);
     *
     * @return string An HTML output generated list
     */
    static function GenerateMenuList($menuobjref, $mode = 1) {
        $count = 0;
        $html = "";
        if (is_array($menuobjref) == false) {
            die("The reference must be a list or an array of a menu object!");
        }
        foreach ($menuobjref as $obj) {
            $cname = get_class($obj);
            break;
        }
        $html.="<div id=\"$cname\" class=\"$cname\">";
        if ($mode == 1) {
            $html.="<ul>";
        } 
        else 
        {
            $html.="<ol>";
        }
        $i = 0;
        foreach ($menuobjref as $obj) {
            $i++;
            $html.="
                <li class=\"menu-elem$i\">
                    <a
                ";
            $tmp = "";
            $tmp2 = "";
            foreach ($obj as $fn => $val) 
            {
                if ($fn != "Id") 
                {
                    switch ($count) 
                    {
                        //name field
                        case 0:
                            $tmp2 = " $val";
                            $count++;
                            break;

                        //href field
                        case 1:
                            $tmp = " href=\"$val\" >";
                            $count++;
                            break;

                        //else nothing this obj must be used with concience
                        default:
                            $count+=2;
                            break;
                    }
                }
            }
            $count = 0;
            $html.= $tmp;
            $html.= $tmp2;
            $html.="
                    </a>
                </li>
                ";
        }
        if ($mode == 1) 
        {
            $html.="</ul>";
        } 
        else 
        {
            $html.="</ol>";
        }
        $html.="</div>";
        return $html;
    }

    /**
     * Displays a joomla side menu from the hash of the elements
     * of the menu
     *
     * @param type $hash of the form array($k => $v )
     * where $k is Label of the link and the $v the link that the sections goes
     * through.
     * @param string  $active_element name of vista displaying
     *
     * @return boolean true on success, false otherwise
     */
    static function GenerateJoomlaSideBarMenu($hash = array(), $active_element = '') 
    {
        if (!defined('_EXEC')) 
        {
            return false;
        }
        $keys = array_keys($hash);

        foreach ($keys as $key):
            if (is_numeric($key) === true):
                return false;
            endif;
        endforeach;
        $ind=0;
        foreach ($hash as $k => $v):
            $flag = false;
            $needle = '&view=' . $active_element;
            $count = strlen($needle);
            $pos = strrpos($v, $needle);
            $f_pos = $count + $pos;
            if (($f_pos) == strlen($v)) 
            {
                $flag = true;
            } 
            else if (($f_pos) < strlen($v)) 
            {
                if (isset($v[$f_pos])) 
                {
                    if ($v[$f_pos] == '&')
                    {
                        $flag = true;
                    }
                }
            }
            
            if ($flag == true)
            {
                JHtmlSidebar::addEntry($k, $v, true);
            }
            else
            {    
                JHtmlSidebar::addEntry($k, $v);
            }
            $ind++;
        endforeach;
        
        $html = JHtmlSidebar::render();
        
        echo '<div id="j-sidebar-container" class="span2">' .
        $html .
        '</div>';
        return true;
    }

    /**
     * Returns the pagination html
     * @param string $name name of the form to submit
     * @param string $uri  string with the uri to the pagination
     * @param integer $number_of_elements maximun number of elements
     * @param integer $elements_by_page number of elements displayed by page
     * @param array   $data hash of data to save in the pagination form
     *
     * @return string pagination html
     */
    static function GeneratePagination($name, $uri, $number_of_elements, $limitstart, $elements_by_page, $data=array()) 
    {
        $html = '';
        $datahtml='';
        $n_int_pages = intval(($number_of_elements) / $elements_by_page);
        $n_pages = (($number_of_elements) / $elements_by_page);
        if ($n_pages > $n_int_pages)
            $n_pages = $n_int_pages + 1;
        else
            $n_pages = $n_int_pages;

        if ($n_pages <= 1)
            return $html;
        
        
        $page_displayed=($elements_by_page+$limitstart)/$elements_by_page; 
        
        foreach($data as $k => $v)
        {
            if($k != "limitstart")
            $datahtml.='<input type="hidden" name="'.$k.'" value="' . $v . '" />';
        }
        
        $html = '
        <div class="pagination pagination-toolbar">
        <form name=' . $name . ' method="POST" action="' . $uri . '">
            <ul class="pagination-list">';

        $prev = '<li class="disabled"><button disabled="disabled"><i class="icon-previous"></i></button></li>';
        $next = '<li class="disabled"><button disabled="disabled"><i class="icon-next"></i></button></li>';

        $pages = '';

        for ($index = 1; $index <= $n_pages; $index++) {
            if ($index !== $page_displayed)
            {
                $pages.='<li><button title="' . ($index) . '" onclick="document.' . $name . '.limitstart.value=' . (($index - 1) * $elements_by_page) . '; ">' . ($index) . '</button></li>';
            }
            else
            {
                $pages.='<li class="disabled"><button disabled="disabled">' . ($index) . '</button></li>';
            }
        }

        if ($page_displayed > 1) 
        {
            $go_to_first_page = '<li><button title="Primero" onclick="document.' . $name . '.limitstart.value=0;"><i class="icon-first"></i></button></li>';
            $prev = '<li><button title="Atras" onclick="document.' . $name . '.limitstart.value=' . (($page_displayed - 1 - 1) * $elements_by_page) . '; "><i class="icon-previous"></i></button></li>';
        }
        else
        {
            $go_to_first_page = "<li class=\"disabled\"><button disabled=\"disabled\"><i class=\"icon-first\"></i></button></li>";
        }
        if ($page_displayed < $n_pages) 
        {
            $last_limit = ($n_pages - 1) * $elements_by_page;
            $go_to_last_page = '<li><button title="Final" onclick="document.' . $name . '.limitstart.value=' . $last_limit . ';"><i class="icon-last"></i></button></li>';
            $next = '<li><button title="Siguiente" onclick="document.' . $name . '.limitstart.value=' . (($page_displayed + 1 - 1) * $elements_by_page) . '; "><i class="icon-next"></i></button></li>';
        } 
        else 
        {
            $go_to_last_page = "<li class=\"disabled\"><button disabled=\"disabled\"><i class=\"icon-last\"></i></buttona></li>";
        }

        $html.=($go_to_first_page . $prev . $pages . $next . $go_to_last_page);

        $html.='
            </ul>
            <input type="hidden" name="limitstart" value="' . $limitstart . '" />
            '.$datahtml.'
        </form>
        </div>';

        return $html;
    }

}

?>
