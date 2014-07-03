<?php

/**
 * @version		$Id: hello.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');

require_once JPATH_ROOT.'/libs/defines.php';
require_once BASE_DIR.LIBS.INCLUDES;

oDirectory::loadClassesFromDirectory(JPATH_COMPONENT_ADMINISTRATOR.DS.MODELS.DS.DATA);
oDirectory::loadClassesFromDirectory(JPATH_COMPONENT_ADMINISTRATOR.DS.MODELS.DS.LOGIC);

$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, JPATH_COMPONENT_ADMINISTRATOR, $language_tag, $reload);
$document = JFactory::getDocument();

// Get an instance of the controller prefixed by Catalog
$controller = JControllerLegacy::getInstance('Catalog');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
//AuxTools::DatabaseDebugging();