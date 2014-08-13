<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$id=0;
$cfid = "";
if(isset($_POST['id']))
    $id=$_POST['id'];
if(isset($_POST['cfid']) && $_POST['cfid'] != "")
{
    $cfid=$_POST['cfid'];
}
$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$obj = new bll_product($id);
$category = new bll_category((0));
$cancel_path="./index.php?option=com_catalog&view=products&limitstart=$lower_limit";

if($obj->Id <= 0)
{
    if( isset($_POST['cid']))
    {
        $category = new bll_category($_POST['cid']);
        if($category->Id > 0)
        {
            $obj->CategoryId=$category->Id;
            $cancel_path="./index.php?option=com_catalog&view=products&layout=selectcategory&limitstart=$lower_limit";
        }
        else
            JFactory::getApplication()->enqueueMessage(
            JText::__('COM_CATALOG_INVALID_CATEGORY_SELLER_FOR_PRODUCT_CREATION'),
            'error'

            );
    }

}

$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$language = new languages(0);
$languages = $language->findAll(null,null, false);
$images=$obj->getImages();
$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT); 

$name='#__name';
$ids='#__id';
$dir='stories';

$xmlfile = new SimpleXMLElement('<field name="'.$name.'" type="media" directory="'.$dir.'" />');
$f = new JForm('temp');
$f->load($xmlfile);
$f->setField($xmlfile);
$f->setFieldAttribute($name, 'id', $ids);
$yesarray = array("#__1"=>"Yes", "0"=>"No");
$noarray = array("1"=>"Yes", "#__0"=>"No");

if($obj->Feature == 1)
    $feature = $yesarray;
else
    $feature = $noarray;

?>
<style>
    .addimg
    {
        border:1px solid #bbb; padding:6px; background: #ddd;
    }
    .addimg:hover{
        cursor:pointer;
    }
</style>
<script type="text/javascript" src="../<?php echo LIBS . JS . JQUERY; ?>"></script>
<script type="text/javascript" src="../<?php echo LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="../<?php echo LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" />  
<script type="text/javascript" src="../<?php echo LIBS . JS . MASKED_INPUTS_JQUERY; ?>"></script>
<script type="text/javascript" >

    var nimg = <?php echo count($images); ?>;

    var imgfield =<?php echo json_encode($f->getInput($name)); ?>;

    var imglabel=<?php echo json_encode('<label for="Images_#__nimg" id="imalabel_#__nimg" >'. JText::_('COM_CATALOG_IMAGE').' #__nimg</label>');?>;
    var imgtlabel=<?php echo json_encode('<label for="ImagesThumb_#__nimg" id="imalabelthumb_#__nimg" >'. JText::_('COM_CATALOG_IMAGE_THUMB') .' #__nimg</label>');?>;
    var btndel=<?php echo json_encode("<br/><label>".JText::_('COM_CATALOG_MAIN_IMAGE').":</label><input type=\"radio\" value=\"#__nimg\" name=\"mainimg\" /><br/><a href=\"#\" onclick=\"return removeimg(#__nimg);\">". JText::_('COM_CATALOG_REMOVE_IMAGE')."</a>")?>;
    function addimg()
    {
        nimg++;
        var name='Images[]';
        var ni="";
        ni+=nimg;
        var idf = 'image_'+ni;
        var label = imglabel.replace('#__nimg',ni);
        label =label.replace('#__nimg',ni);
        label =label.replace('#__nimg',ni);
        var field = imgfield.replace('___id', idf);
        var btdel =btndel.replace('#__nimg',ni);
        btdel =btdel.replace('#__nimg',ni);
        field = field.replace('___id', idf);
        field = field.replace('___id', idf);
        field = field.replace('___id', idf);
        field = field.replace('___id', idf);
        field = field.replace('___id', idf);
        field = field.replace('#__name', name);
        field = field.replace('#__name', name);
        field = field.replace('#__name', name);
        var label2 =imgtlabel.replace('#__nimg',ni);
        label2 =label2.replace('#__nimg',ni);
        label2 =label2.replace('#__nimg',ni);
        var idf2 = 'imagethumb_'+ni;
        var name2='ImagesThumb[]';
        var field2 = imgfield.replace('___id', idf2);
        field2 = field2.replace('___id', idf2);
        field2 = field2.replace('___id', idf2);
        field2 = field2.replace('___id', idf2);
        field2 = field2.replace('___id', idf2);
        field2 = field2.replace('___id', idf2);
        field2 = field2.replace('#__name', name2);
        field2 = field2.replace('#__name', name2);
        field2 = field2.replace('#__name', name2);
        jQuery('.newimages').append("<div>"+label+field+label2+field2+btdel+"</div>");
        window.addEvent('domready', function() {
			SqueezeBox.initialize({});
			SqueezeBox.assign($$('a.modal'), {
				parse: 'rel'
			});
        });
        return false;

    }

    

    function removeimg(index)
    {
        var label=jQuery('#imalabel_'+index)[0];
        var holder = label.parentNode;
        if(holder.tagName=="LI")
            {
                var sibling = holder.nextSibling;
                var sibling2 = holder.nextSibling.nextSibling;
                var sibling3 = holder.nextSibling.nextSibling.nextSibling;
                var sibling4 = holder.nextSibling.nextSibling.nextSibling.nextSibling;
                sibling.outerHTML="";
                sibling2.outerHTML="";
                sibling3.outerHTML="";
                sibling4.outerHTML="";
            }
        holder.outerHTML="";
        
        nimg--;
        return false;
    }

</script>
<div id="j-main-container" class="span10">   

    <div class="btn-toolbar" id="toolbar">

        <div class="btn-wrapper" id="toolbar-new">

                <a class="btn btn-small" href="<?php echo $cancel_path ?>">

                <span class="icon-cancel"></span>

                <?php echo JText::_('COM_CATALOG_CANCEL')?>

                </a>

        </div>

    </div>

    <h3>

        <?php echo JText::_('COM_CATALOG_PRODUCT'). " ".JText::_('COM_CATALOG_DETAILS'); ?> 

    </h3>

    <?php

    $form= form::getInstance();

    $form->setLayout(FormLayouts::FORMS_UL_LAYOUT);

    $userdetail_html="";

    $form->HTML($userdetail_html);

    if(isset($_POST['id']))
        $form->Hidden('Id', $obj->Id);

    $form->Hidden('CategoryId', $obj->CategoryId);
    $form->Hidden('cfid', $cfid);
    $form->Hidden('action', 'edit', '', '');
    $tab_top_html='<script type="text/javascript">
  jQuery(function() {
    jQuery( "#tabs" ).tabs();
  });
  </script>
  <div id="tabs">
        <ul>';
    foreach($languages as $lang)
    {
        $tab_top_html.='<li><a href="#tabs-'.$lang->lang_id.'">'.$lang->title_native.'</a></li>';
    }
    $tab_top_html.='</ul>';
    foreach($languages as $lang)
    {
        $langval = $obj->getLanguageValue($lang->lang_id);
        $forml = Form::getInstance('val');
        $forml->Label(JText::_('COM_CATALOG_NAME')."($lang->title_native)", 'Name_'.$lang->lang_id);
        $forml->Text('Name_'.$lang->lang_id, $langval->Name, '', 'Labels', true);
       
        $forml->Label(JText::_('COM_CATALOG_DESCRIPTION')."($lang->title_native)", 'Description_'.$lang->lang_id);
        $forml->JEditor('Description_'.$lang->lang_id, $langval->Description, 300,23200,40,40);
        
        $forml->Label(JText::_('COM_CATALOG_NOTE')."($lang->title_native)", 'Note_'.$lang->lang_id);
        $forml->JEditor('Note_'.$lang->lang_id, $langval->Note,300,200,40,40);
        
        $tab_top_html.="<div id=\"tabs-$lang->lang_id\">";
        $tab_top_html.=$forml->renderFields();
        $tab_top_html.="</div>";
        $forml->clear();
    }
    $tab_top_html.="</div>";
    $form->HTML($tab_top_html);
    $index = 1;
//    $form->Label(JText::_('COM_CATALOG_ADDRESS'), 'Address');
//    $form->TextArea('Address', $obj->Address, 80, 5);
//    $form->Label(JText::_('COM_CATALOG_RENT_PRICE'), 'RentPrice');
//    $form->Text('RentPrice', $obj->RentPrice);

    $form->Label(JText::_('COM_CATALOG_SALE_PRICE')."(".DEFAULT_CURRENCY.")", 'SalePrice');
    $form->Text('SalePrice', $obj->SalePrice);
    $form->Label(JText::_('Offer price')."(".DEFAULT_CURRENCY.")", 'OfferPrice');
    $form->Text('OfferPrice', $obj->OfferPrice);
    $form->HTML($obj->GenerateFormFields());
    foreach($images as $image)
    {
        $_imgsel = "";
        if($image->Main == 1)
        {
            $_imgsel = "checked";
        }
        $form->HTML("<span>".JText::_('COM_CATALOG_MAIN_IMAGE').":</span><input type=\"radio\" value=\"$index\" $_imgsel name=\"mainimg\" />");
        
        $form->Label(JText::_('COM_CATALOG_IMAGE')." ".$index, 'images_'.$index, 'imalabel_'.$index);
        $form->JMediaField('Images[]', $image->ImageUrl, 'stories', 'image_'.$index );
        $form->Label(JText::_('COM_CATALOG_IMAGE_THUMB')." ".$index, 'imagesthumb_'.$index, 'imalabelthumb_'.$index);
        $form->JMediaField('ImagesThumb[]', $image->ImageThumb, 'stories', 'imagesthumb_'.$index );
        $form->HTML("<a href=\"#\" onclick=\"return removeimg($index);\">".JText::_('COM_CATALOG_REMOVE_IMAGE')."</a><hr>");
        $index++;
    }

    $form->HTML("<div class=\"newimages\"></div><a class=\"addimg\" onclick=\"return addimg()\">".JText::_('COM_CATALOG_ADD_IMAGE')."</a><br><hr>");
    $form->Submit(JText::_('COM_CATALOG_SAVE'));

    $ll=0;

    if($obj->Id > 0)
        $ll = $lower_limit;

    echo $form->Render('./index.php?option=com_catalog&view=products&limitstart='.$ll, array(), false, 'product');
    ?>
</div>

