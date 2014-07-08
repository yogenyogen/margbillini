<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];
$cid=0;
if(isset($_REQUEST['cid']))
    $cid=$_REQUEST['cid'];

$pre_c = new country($cid);

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new province($id);
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
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=provinces&limitstart=<?php echo $lower_limit; ?>&cid=<?php echo $cid; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_PROVINCE'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php
    $country = new country($obj->CountryId);
    $countries  = $country->setSelectValues('Name', 'Id', $country->Id);
    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    
    $form->Label(JText::_('COM_CATALOG_NAME'), 'Name');
    $form->Text('Name', $obj->Name, '', 'Labels', true);
    if($pre_c->Id > 0)
    {
        $form->Hidden('CountryId', $pre_c->Id);
        $form->Hidden('cid', $pre_c->Id);
    }
    else{
        $form->Label(JText::_('COM_CATALOG_COUNTRY'), 'CountryId');
        $form->SelectBox('CountryId', $countries, '', 'Labels', true);
    }

    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    echo $form->Render('./index.php?option=com_catalog&view=provinces&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

