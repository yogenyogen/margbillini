<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');

JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$CA = JPATH_ADMINISTRATOR.'/components/com_catalog';
require_once $CA.'/libs/defines.php';
require_once BASE_DIR.LIBS.INCLUDES;
oDirectory::loadClassesFromDirectory($CA.MODELS.DATA);
oDirectory::loadClassesFromDirectory($CA.MODELS.LOGIC);

$city = new cities(0);
$cities=$city->findAll(null,null,false,'Name');
$tarr = array();
foreach($cities as $obj)
{
    $tarr[]=$obj->Name;
}
$cities=$tarr;
$country = new country(0);
$countries = $country->findAll(null,null,false,'Name');
$tarr2 = array();
foreach($countries as $obj)
{
    $tarr2[]=$obj->Name;
}
$countries=$tarr2;
$sector = new sector(0);
$sectors=$sector->findAll(null,null,false,'Name');
$tarr3 = array();
foreach($sectors as $obj)
{
    $tarr3[]=$obj->Name;
}
$sectors=$tarr3;
$province = new province(0);
$provinces=$province->findAll(null,null,false,'Name');
$tarr4 = array();
foreach($provinces as $obj)
{
    $tarr4[]=$obj->Name;
}
$provinces=$tarr4;

$lang = JFactory::getLanguage();
$extension = 'com_catalog';
$language_tag = AuxTools::GetCurrentLanguageJoomla();
$reload = true;
$lang->load($extension, $CA, $language_tag, $reload);



//load user_profile plugin language
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);

?>
<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h4><i class="fa fa-heart fa-rotate-270"></i><span>?php echo $this->escape($this->params->get('page_heading')); ?></span><i class="fa fa-heart fa-rotate-90"></i>
		</h4>
	</div>
<?php endif; ?>

<script type="text/javascript">
	Joomla.twoFactorMethodChange = function(e)
	{
            
        jQuery.noConflict();
		var selectedPane = 'com_users_twofactor_' + jQuery('#jform_twofactor_method').val();

		jQuery.each(jQuery('#com_users_twofactor_forms_container>div'), function(i, el) {
			if (el.id != selectedPane)
			{
				jQuery('#' + el.id).hide(0);
			}
			else
			{
				jQuery('#' + el.id).show(0);
			}
		});
	}
        
</script>
<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><?php echo JText::_($fieldset->label); ?></legend>
		<?php endif;?>
		<?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<div class="control-group">
					<div class="controls">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php else:?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
						<?php if (!$field->required && $field->type != 'Spacer') : ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
						<?php endif; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endif;?>
		<?php endforeach;?>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>

<?php if (count($this->twofactormethods) > 1): ?>
	<fieldset>
		<legend><?php echo JText::_('COM_USERS_PROFILE_TWO_FACTOR_AUTH') ?></legend>

		<div class="control-group">
			<div class="control-label">
				<label id="jform_twofactor_method-lbl" for="jform_twofactor_method" class="hasTooltip"
					   title="<strong><?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL') ?></strong><br/><?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_DESC') ?>">
					<?php echo JText::_('COM_USERS_PROFILE_TWOFACTOR_LABEL'); ?>
				</label>
			</div>
			<div class="controls">
				<?php echo JHtml::_('select.genericlist', $this->twofactormethods, 'jform[twofactor][method]', array('onchange' => 'Joomla.twoFactorMethodChange()'), 'value', 'text', $this->otpConfig->method, 'jform_twofactor_method', false) ?>
			</div>
		</div>
		<div id="com_users_twofactor_forms_container">
			<?php foreach($this->twofactorform as $form): ?>
			<?php $style = $form['method'] == $this->otpConfig->method ? 'display: block' : 'display: none'; ?>
			<div id="com_users_twofactor_<?php echo $form['method'] ?>" style="<?php echo $style; ?>">
				<?php echo $form['form'] ?>
			</div>
			<?php endforeach; ?>
		</div>
	</fieldset>

	<fieldset>
		<legend>
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS') ?>
		</legend>
		<div class="alert alert-info">
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS_DESC') ?>
		</div>
		<?php if (empty($this->otpConfig->otep)): ?>
		<div class="alert alert-warning">
			<?php echo JText::_('COM_USERS_PROFILE_OTEPS_WAIT_DESC') ?>
		</div>
		<?php else: ?>
		<?php foreach ($this->otpConfig->otep as $otep): ?>
		<span class="span3">
			<?php echo substr($otep, 0, 4) ?>-<?php echo substr($otep, 4, 4) ?>-<?php echo substr($otep, 8, 4) ?>-<?php echo substr($otep, 12, 4) ?>
		</span>
		<?php endforeach; ?>
		<div class="clearfix"></div>
		<?php endif; ?>
	</fieldset>
<?php endif; ?>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<a class="btn" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
<style>
  .ui-autocomplete {
    max-height: 100px;
    overflow-y: auto;
    /* prevent horizontal scrollbar */
    overflow-x: hidden;
  }
  /* IE 6 doesn't support max-height
   * we use height instead, but this forces the menu to always be this tall
   */
  * html .ui-autocomplete {
    height: 100px;
  }
  </style>
<script>

var xj_qu =$.noConflict(true);
xj_qu(document).ready(function() 
{
    xj_qu("#jform_profile_dob").datepicker({
      changeMonth: true,
      changeYear: true,
      showOn: "both",
    });
    
    function dissapear(selector)
    {
       var elem = selector[0];
       
       var granpa = elem.parentNode.parentNode;
       granpa.style.display="none";
    };
    dissapear(xj_qu( "#jform_profile_city" ));
    dissapear(xj_qu( "#jform_profile_country"));
    dissapear(xj_qu( "#jform_profile_sector" ));
    dissapear(xj_qu( "#jform_profile_region" ));
});
</script>