<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_catalog_products
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if(!defined('DS'))
    define('DS', '/');
$component =JPATH_ADMINISTRATOR.DS.'components'.DS.'com_catalog/';
require_once JPATH_ROOT.'/libs/defines.php';
require_once BASE_DIR.LIBS.INCLUDES;

oDirectory::loadClassesFromDirectory($component.DS.MODELS.DS.DATA);
oDirectory::loadClassesFromDirectory($component.DS.MODELS.DS.LOGIC);

$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, $component, $language_tag, $reload);
$categoryid = $params->get('category');
$layout = "default";
require JModuleHelper::getLayoutPath('mod_catalog_products', $params->get('layout', $layout));
