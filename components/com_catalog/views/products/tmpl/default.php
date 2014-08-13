<?php

/**
 * @version		$Id: default.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$cid=$app->input->get('cid',0);

$ro=$app->getPathway();

$cat = new bll_category($cid);
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$catlv = $cat->getLanguageValue($LangId);
$product = new bll_product(0);

$limitstart=0;
if(isset($_POST['limitstart']))
    $limitstart=$_POST['limitstart'];

$elements_by_page=12;
$total = count(bll_product::find_products($cat->Id));

$document = JFactory::getDocument();
$document->setTitle($catlv->Name. ' - '.$app->get('sitename'));
$document->setDescription(strip_tags($catlv->Description));
$products = bll_product::find_products($cat->Id,$limitstart, $elements_by_page);

$curr = bll_currencies::getActiveCurrency();

if(strlen($cat->ImageUrl)> 4)
    $catimage = $cat->ImageUrl;
else
    $catimage='./components/com_catalog/images/no-image-listing-detail.jpg';
$base_uri = JUri::base();
$current_uri  =  JUri::current();
$document->addHeadLink($current_uri,'canonical');
$ctags = '
<meta property="og:title" content="'.$catlv->Name.'" />
<meta property="og:description" content="'.strip_tags($catlv->Description).'" />
<meta property="og:image" content="'.$base_uri.DS.$catimage.'" />
<meta property="og:site_name" content="'.$base_uri.'" />
<meta property="og:url" content="'.$current_uri.'" />';
$document->addCustomTag($ctags);

if($total > 0){
?>
    <h1 class="span12">
        <?php echo $catlv->Name; ?>
    </h1>
    <div class="span12 no-margin-left">
            <?php 
            $index=0;
            foreach($products as $product): 
            if($index%4 == 0 && $index > 0):    
                ?>
             </div>
             <div class="clearfix span12  no-margin-left"><hr></div>
             <div class="span12  no-margin-left">
            <?php 
            endif;
            $index++;
            ?>
            <div class="span3">
                <?php 
                $img = $product->getMainImage();
                $lval = $product->getLanguageValue($LangId);
                if(is_file(JPATH_ROOT.DS.$img->ImageThumb))
                    $image = $img->ImageThumb;
                else
                    $image='./components/com_catalog/images/no-image-listing.jpg';

                ?>
                <a href="<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><img class="lis-image" src="<?php echo $image; ?>" /></a>
                <div>
                    <h3><a href="<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><?php echo $lval->Name; ?></a></h3>
                </div>
                <div class="price">
                    <?php 
                    $sale_price=AuxTools::MoneyFormat($product->SalePrice, $curr->CurrCode, $curr->Rate);
                    if($product->have_offer_price()==true)
                    {
                        $offer_price=AuxTools::MoneyFormat($product->OfferPrice, $curr->CurrCode, $curr->Rate);
                        $percent = ($product->SalePrice*100) / $product->OfferPrice;
                        ?>
                            <span class="line-through smaller-text"><?php echo $sale_price; ?></span> <span class="red"><?php echo $offer_price; ?></span> 
                        
                        <?php
                    }
                    else
                    {
                        ?>
                            <span class="red"><?php echo $sale_price; ?></span>
                        
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
    </div>
<div class="span12">
<?php 
echo HtmlGenerator::GeneratePagination($product->getObjectName(), "", 
                       $total, $limitstart,$elements_by_page, array('cid'=>$cat->Id));
?>
</div>
<?php
}
else
{
    echo "<p>".JText::_('COM_CATALOG_NO_PRODUCTS_AVAILABLE')."</p>";
}