<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$CA = JPATH_ADMINISTRATOR.'/components/com_catalog';
require_once JPATH_ROOT.'/libs/defines.php';
require_once BASE_DIR.LIBS.INCLUDES;
$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, $CA, $language_tag, $reload);

?>
<div class="profile <?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
<div class="page-header">
	<h4><i class="fa fa-heart fa-rotate-270"></i><span><?php echo JText::_($fieldset->label); ?></span><i class="fa fa-heart fa-rotate-90"></i>
	</h4>
</div>
<?php endif; ?>
<?php if (JFactory::getUser()->id == $this->data->id) : ?>
<ul class="btn-toolbar pull-right">
	<li class="btn-group">
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
			<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
                <a class="btn" href="<?php echo JRoute::_('/index.php?option=com_catalog&view=sales&layout=sales'); ?>">
			<span class="icon-cart"></span> <?php echo JText::_('COM_CATALOG_VIEW_ORDERS'); ?></a>
	</li>
</ul>
<?php endif; ?>
<?php echo $this->loadTemplate('core'); ?>

<?php echo $this->loadTemplate('params'); ?>

<?php echo $this->loadTemplate('custom'); ?>

</div>
