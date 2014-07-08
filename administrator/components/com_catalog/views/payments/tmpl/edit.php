<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new bll_paymentmethod($id);
$lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());

$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 

?>

<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY19; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . TINYMCE . TINYMCE_JQUERY; ?>"></script>



<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=payments&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_PAYMENT_METHOD'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php
    $languages = languages::GetLanguages();
    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    $enable=array();
    if($obj->Enable == 1)
        $enable=array('#__1'=>  JText::_('COM_CATALOG_YES'), '0'=>  JText::_('COM_CATALOG_NO'));
    else
        $enable=array('1'=>  JText::_('COM_CATALOG_YES'), '#__0'=>  JText::_('COM_CATALOG_NO'));
    $form->Label(JText::_('COM_CATALOG_ENABLE'), 'Enable');
    $form->SelectBox('Enable', $enable);
    
    $external=array();
    if($obj->External == 1)
        $external=array('#__1'=>  JText::_('COM_CATALOG_YES'), '0'=>  JText::_('COM_CATALOG_NO'));
    else
        $external=array('1'=>  JText::_('COM_CATALOG_YES'), '#__0'=>  JText::_('COM_CATALOG_NO'));
    $form->Label(JText::_('COM_CATALOG_EXTERNAL'), 'External');
    $form->SelectBox('External', $external);
        
    foreach($languages as $lang):
        $langval = $obj->getLanguageValue($lang->lang_id);
        $form->Label(JText::_('COM_CATALOG_NAME')."($lang->title_native)", 'Name_'.$lang->lang_id);
        $form->Text('Name_'.$lang->lang_id, $langval->Name, '', 'Labels', true);
        $form->Label(JText::_('COM_CATALOG_DESCRIPTION')."($lang->title_native)", 'Description_'.$lang->lang_id);
        $form->Editor('Description_'.$lang->lang_id, $langval->Description, 'default_tiny_id_'.$lang->lang_id, 'default_tiny_class_'.$lang->lang_id);
    endforeach;


    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    echo $form->Render('./index.php?option=com_catalog&view=payments&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

