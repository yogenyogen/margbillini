<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new bll_sale($id);
$lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());

?>

<script type="text/javascript" src="../<?php echo  LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="../<?php echo  LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="../<?php echo  LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  

<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=sales&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_SALE'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php

    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    $arr=array();
    $saleState = new bll_salestate($obj->SaleStateId);
    $dbarr=array();
    foreach($saleState->findAll() as $ss)
    {
        $dbarr[]= $ss->getLanguageValue($lang->lang_id);
    }
    $arr=$saleState->setSelectValues('Name', 'SaleStateId', $obj->SaleStateId, $dbarr);
    $form->Label(JText::_('COM_CATALOG_SALE_STATE'), 'SaleStateId');
    $form->SelectBox('SaleStateId', $arr, '', 'Labels', true);


    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    echo $form->Render('./index.php?option=com_catalog&view=sales&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

