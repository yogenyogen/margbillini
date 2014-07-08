<?php

// No direct access to this file

defined('_JEXEC') or die('Restricted access');



$id=0;

if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$category = new bll_category($id);

$language = new languages(AuxTools::GetCurrentLanguageIDJoomla());
$languages = $language->findAll();



$db= new dbprovider(true);
$id=$db->escape_string($id);
$query="SELECT C.Id as Id, CL.Name as Name FROM  `#__catalogcategory` AS `C` 
    INNER JOIN `#__catalogcategorylang`  AS CL  ON  C.Id = CL.CategoryId 
    WHERE  C.Id <> '$id' ";
$db->Query($query);
$re =$db->getNextObjectList();

$categories  = $category->setSelectValues("Name", "Id", 
               $category->CategoryId, $re);

$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 

?>

<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . TINYMCE . TINYMCE_JQUERY; ?>"></script>

<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=category&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_CATEGORY'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php
    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
    $form->Hidden('Id', $category->Id);
    $form->Hidden('action', 'edit', '', '');
    $form->Label(JText::_('COM_CATALOG_PARENT_CATEGORY'), 'CategoryId');
    $form->SelectBox('CategoryId', $categories); 

    foreach($languages as $lang)
    {
        $langval = $category->getLanguageValue($lang->lang_id);
        $form->Label(JText::_('COM_CATALOG_NAME')."($lang->title_native)", 'Name_'.$lang->lang_id);
        $form->Text('Name_'.$lang->lang_id, $langval->Name, '', 'Labels', true);
        $form->Label(JText::_('COM_CATALOG_DESCRIPTION')."($lang->title_native)", 'Description_'.$lang->lang_id);
        $form->Editor('Description_'.$lang->lang_id, $langval->Description, 'Description_'.$lang->lang_id, 'Description_class_'.$lang->lang_id, true);
    }

    $form->Label(JText::_('COM_CATALOG_IMAGE'), 'ImageUrl');
    $form->JMediaField('ImageUrl', $category->ImageUrl);
    $form->Label(JText::_('COM_CATALOG_THUMB'), 'ThumbUrl');
    $form->JMediaField('ThumbUrl', $category->ThumbUrl);
    $form->Submit(JText::_('COM_CATALOG_SAVE'));
    echo $form->Render('./index.php?option=com_catalog&view=category&limitstart='.$lower_limit, array('onSubmit'=>'return fieldname();'));
    ?>
</div>

