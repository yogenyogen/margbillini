<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new bll_shippingmethod($id);
$lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());
$languages = languages::GetLanguages();
$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 
$cities = bll_shippingmethod::getCitiesFromMethod($obj->Id);

$city = new cities(0);
$all_cities = $city->findAll(null,null, false, 'Name');
$carr = array();
foreach($all_cities as $c):
    if(array_search($c->Id, $cities)!==false)
       $carr['#__'.$c->Id]=$c->Name;    
    else
        $carr[$c->Id]=$c->Name;
endforeach;

$yes_no = array('1'=>JText::_('COM_CATALOG_YES'), '#__0'=>JText::_('COM_CATALOG_NO'));
if($obj->Global == 1)
{
    $yes_no = array('#__1'=>JText::_('COM_CATALOG_YES'), '0'=>JText::_('COM_CATALOG_NO'));
}

?>
<script type="text/javascript" src="../<?php echo LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="../<?php echo LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="../<?php echo LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  

<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=shippings&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_SHIPPING_METHOD'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php

    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    $form->Label(JText::_('COM_CATALOG_MINDAYS'), 'MinDays');
    $form->Text('MinDays', $obj->MinDays);
    $form->Label(JText::_('COM_CATALOG_GLOBAL'), 'Global');
    $form->SelectBox('Global', $yes_no);
    $form->Label(JText::_('COM_CATALOG_MAXDAYS'), 'MaxDays');
    $form->Text('MaxDays', $obj->MaxDays);
    $form->Label(JText::_('COM_CATALOG_PRICE')."(".DEFAULT_CURRENCY.")", 'Price');
    $form->Text('Price', $obj->Price);
    $form->Label(JText::_('COM_CATALOG_CITIES'), 'Cities');
    $form->Checkboxes('Cities', $carr);
    foreach($languages as $lang):
        $langval = $obj->getLanguageValue($lang->lang_id);
        $form->Label(JText::_('COM_CATALOG_NAME')."($lang->title_native)", 'Name_'.$lang->lang_id);
        $form->Text('Name_'.$lang->lang_id, $langval->Name, '', 'Labels', true);
        $form->Label(JText::_('COM_CATALOG_DESCRIPTION')."($lang->title_native)", 'Description_'.$lang->lang_id);
        $form->JEditor('Description_'.$lang->lang_id, $langval->Description, 300,400,30,30);
    endforeach;
    $form->Submit(JText::_('COM_CATALOG_SAVE'));
    echo $form->Render('./index.php?option=com_catalog&view=shippings&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

