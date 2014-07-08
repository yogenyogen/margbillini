<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
if(isset($_POST['id']))
    $id=$_POST['id'];

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new catalogcoupon($id);
$lang = new languages(AuxTools::GetCurrentLanguageIDJoomla());

$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 

?>

<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . TINYMCE . TINYMCE_JQUERY; ?>"></script>

<script>
var codelength=12;
    function randomString(length) {
        var chars='0123456789:.-_abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var result = '';
        for (var i = length; i > 0; --i) result += chars[Math.round(Math.random() * (chars.length - 1))];
        return result;
    }
    


function is_num(evt)
    {
        var theEvent = evt || window.event;
      var key = theEvent.keyCode || theEvent.which;
      var regex = /[0-9]|\./;
        switch(key)
        {
           case 8:
            
           break; 
           case 46:
            
           break;
           default:
               key = String.fromCharCode( key );
               if( !regex.test(key) ) {
                theEvent.returnValue = false;
                if(theEvent.preventDefault) theEvent.preventDefault();
              }
           break;
        }
      
  }

    function change (el, max_len) 
    {
        if(max_len > 0)
            max_len = max_len-1;
        if (el.value.length > max_len) {
        el.value = el.value.substr(0, max_len+1);
        return false;
        }
        
        return true;
    }

$(document).ready(function() 
{
        $( "#datepicker" ).datepicker({ minDate: 0, dateFormat:'yy-mm-dd'});
        $('#Gen').click(function(){
            $('#Code').val(randomString(codelength));
            return false; 
        });
        
        $( "#Code" ).keypress(function() {
            return change(this,codelength);
        });
        $( "#Dis" ).keypress(function() {
            return change(this,2);
        });
        $( "#Dis" ).keydown(function(event) {
            return is_num(event);
        });
        $( "#Use" ).keydown(function(event) {
            return is_num(event);
        });
});
</script>


<div id="j-main-container" class="span10">   
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=coupons&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_COUPON'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 
    </h3>

    <?php
    $cal = "<label>Promotion end date: </label><input id='datepicker' type='text' name='Date' value='$obj->Date' />";
    if($obj->Enable == 1)
        $enable=array('#__1'=>  JText::_('COM_CATALOG_YES'), '0'=>  JText::_('COM_CATALOG_NO'));
    else
        $enable=array('1'=>  JText::_('COM_CATALOG_YES'), '#__0'=>  JText::_('COM_CATALOG_NO'));
    
    $form= form::getInstance();
    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);
    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);
    $form->Hidden('action', 'edit', '', '');
    
    $form->Label(JText::_('COM_CATALOG_CODE'), 'Code');
    $form->Text('Code', $obj->Code, 'Code');
    $form->Button(JText::_('COM_CATALOG_GENERATE_CODE'), 'Gen', 'Gen');
    $form->Label(JText::_('COM_CATALOG_DATE'), 'Date');
    $form->HTML($cal);
    $form->Label(JText::_('COM_CATALOG_ENABLE'), 'Enable');
    $form->SelectBox('Enable', $enable);
    $form->Label(JText::_('COM_CATALOG_DISCOUNT'), 'Discount');
    $form->Text('Discount', $obj->Discount, 'Dis', 'Dis', true);
    $form->Label(JText::_('COM_CATALOG_USES'), 'Uses');
    $form->Text('Uses', $obj->Uses, 'Use', 'Use', true);
    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    echo $form->Render('./index.php?option=com_catalog&view=coupons&limitstart='.$lower_limit, array('onSubmit'=>''));

    ?>

</div>

