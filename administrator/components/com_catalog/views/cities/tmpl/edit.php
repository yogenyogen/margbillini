<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new cities($id);
$lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());

$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 

?>

<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . TINYMCE . TINYMCE_JQUERY; ?>"></script>



<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=cities&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_CITY'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php
    $province = new province($obj->ProvinceId);
    $provinces  = $province->setSelectValues('Name', 'Id', $province->Id);
    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    
    $form->Label(JText::_('COM_CATALOG_NAME'), 'Name');
    $form->Text('Name', $obj->Name, '', 'Labels', true);
    $form->Label(JText::_('COM_CATALOG_PROVINCE'), 'ProvinceId');
    $form->SelectBox('ProvinceId', $provinces, '', 'Labels', true);

    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    echo $form->Render('./index.php?option=com_catalog&view=cities&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

