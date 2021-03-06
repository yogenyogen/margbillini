<?php

defined('_JEXEC') or die;

if (!defined('DS'))
  define('DS', DIRECTORY_SEPARATOR);

$admin_root=JPATH_ROOT.'/administrator/components/com_catalog/';

require_once JPATH_ROOT.DS.'libs/defines.php';
require_once BASE_DIR.DS.LIBS.INCLUDES;
oDirectory::loadClassesFromDirectory($admin_root.MODELS.DATA);
oDirectory::loadClassesFromDirectory($admin_root.MODELS.LOGIC);

$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, $admin_root, $language_tag, $reload);

function catalogBuildRoute($query) {
   return array();
}

function CatalogParseRoute($segments) 
{
    $search_view_needle= strtolower(JText::_('COM_CATALOG_SEARCH_NEEDLE'));
    
    $vars = array();
    $search_view = false;
    if(isset($segments[0]))
    {
       if($segments[0] == $search_view_needle)
           $search_view=true;
    }
    $definer = $segments[count($segments)-1];
    //preparing for search
    $definer=(str_replace(':', '-', $definer));
    $arr = explode('-', $definer);
    $view_layout=$arr[count($arr)-1];
    $arr = explode('.', $view_layout);
    $view_layout=$arr[0];
    $set_layout="";
    if($search_view==true)
    {
        $set_layout="search_detail";
    }
    else if(is_numeric($view_layout))//business detail
    {
        $set_layout="product_detail";
    }
    else if(!is_numeric($view_layout))//category detail
    {
        $set_layout="category_detail";
    }
    
    switch($set_layout)
    {
        case "category_detail":
            $vars['option'] = "com_catalog";
            $vars['view'] = "products";
            $vars['layout'] = "default";
            $arr = explode('.', $definer);
            $needle=$arr[0];
            $needle=  ucwords(str_replace('-', ' ', $needle));
            $cat = new catalogcategorylang(0);
            $cat=$cat->find(array(array('Name','Like')), array(array($needle,null)));
            $id=0;
            if( $cat->CategoryId > 0)
                $id=$cat->CategoryId;
            $vars['cid']=$id;
        break;
        case "product_detail":
            $id=$view_layout;
            $vars['option'] = "com_catalog";
            $vars['view'] = "products";
            $vars['layout'] = "detail";
            $vars['pid']=$id;
        break;
        case "search_detail":
            $id=$view_layout;
            $vars['option'] = "com_catalog";
            $vars['view'] = "search";
            $vars['layout'] = "detail";
            if(isset($segments[1]))
                $arr = explode('.', $segments[1]);
            else 
                $arr = array('');
            $needle=$arr[0];
            $elems=explode('_', $needle);
            if(isset($elems[0]))
            $vars['key']=str_replace(':', '-', $elems[0]);
            if(isset($elems[1]))
            $vars['location']=str_replace(':', '-', $elems[1]);
        break;
        default:
            //default view
            $lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());
            $redirect=$lang->sef.DS.JText::_('COM_CATALOG_CATALOG_NEEDLE');
            //die($redirect);
            JFactory::getApplication()->redirect($redirect,true);
        break;
    }
    
    
    return $vars;
}