<?php
// No direct access to this file

// import joomla controller library
jimport('joomla.application.component.controller');

require_once JPATH_ROOT.'/libs/defines.php';
require_once BASE_DIR.LIBS.INCLUDES;

oDirectory::loadClassesFromDirectory(JPATH_COMPONENT_ADMINISTRATOR.DS.MODELS.DS.DATA);
oDirectory::loadClassesFromDirectory(JPATH_COMPONENT_ADMINISTRATOR.DS.MODELS.DS.LOGIC);

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('Catalog');
$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, JPATH_COMPONENT_ADMINISTRATOR, $language_tag, $reload);

JToolbarHelper::title(JText::_('Catalog Manager'), 'Catalog');
$menu_elements = array(
		JText::_('COM_CATALOG_CONTROL_PANEL')=>'./index.php?option=com_catalog',
                JText::_('COM_CATALOG_CATEGORIES')=>'./index.php?option=com_catalog&view=category',
                JText::_('COM_CATALOG_PRODUCTS')=>'./index.php?option=com_catalog&view=products',
                JText::_('COM_CATALOG_SALES')=>'./index.php?option=com_catalog&view=sales',
                JText::_('COM_CATALOG_SHIPPING_METHODS')=>'./index.php?option=com_catalog&view=shippings',
                JText::_('COM_CATALOG_PAYMENT_METHODS')=>'./index.php?option=com_catalog&view=payments',
                JText::_('COM_CATALOG_SALE_STATES')=>'./index.php?option=com_catalog&view=states',
                JText::_('COM_CATALOG_COUNTRIES')=>'./index.php?option=com_catalog&view=countries',
                JText::_('COM_CATALOG_CITIES')=>'./index.php?option=com_catalog&view=cities',
                JText::_('COM_CATALOG_COUPONS')=>'./index.php?option=com_catalog&view=coupons',
                JText::_('COM_CATALOG_CURRENCIES')=>'./index.php?option=com_catalog&view=currencies'

        );

$element = "";

if(isset($_GET['view']))
    $element = $_GET['view'];

JHtml::stylesheet(JPATH_COMPONENT_ADMINISTRATOR.DS.STYLES.STYLE);
HtmlGenerator::GenerateJoomlaSideBarMenu($menu_elements, $element);

// Get the task
$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', "", 'STR' );

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
