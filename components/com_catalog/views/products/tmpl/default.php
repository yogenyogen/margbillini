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
$cid =0;
if(isset($_REQUEST['cid']))
{
    $cid=$_REQUEST['cid'];
}
$ro=JFactory::getApplication()->getPathway();

$cat = new bll_category($cid);
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$catlv = $cat->getLanguageValue($LangId);
$product = new bll_product(0);

$limitstart=0;
if(isset($_POST['limitstart']))
    $limitstart=$_POST['limitstart'];

$elements_by_page=12;
$total = count(bll_product::find_products($cat->Id));
$page_displayed=1;
if(($limitstart)!= 0)
{
    $page_displayed=($elements_by_page+$limitstart)/$elements_by_page;   
}
$document = JFactory::getDocument();
$document->setTitle($cat->getLanguageValue($LangId)->Name);
$document->setDescription(strip_tags($cat->getLanguageValue($LangId)->Description));
$products = bll_product::find_products($cat->Id,$limitstart, $elements_by_page);

$curr = bll_currencies::getActiveCurrency();
?>
<h1><?php echo JText::_('COM_CATALOG_CATALOG'); ?></h1>
<div class="products-holder" class="row-fluid">
    <h4>
        <?php echo $catlv->Name; ?>
    </h4>
    <div class="span12">
            <?php foreach($products as $product): ?>
            <div class="span3">
                <?php 
                $img = $product->getMainImage();
                $lval = $product->getLanguageValue($LangId);
                if(is_file(JPATH_ROOT.DS.$img->ImageThumb))
                    $image = $img->ImageThumb;
                else
                    $image='./components/com_catalog/images/no-image-listing.jpg';

                ?>
                <a href="./index.php/<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><img class="lis-image" src="<?php echo $image; ?>" /></a>
                <div>
                    <h3><a href="./index.php/<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><?php echo $lval->Name; ?></a></h3>
                </div>
                <div>
                    <?php 
                    echo AuxTools::MoneyFormat($product->SalePrice, $curr->CurrCode, $curr->Rate);
                    ?>
                </div>
            </div>
            <?php endforeach; ?>
    </div>
</div>
<?php 

echo HtmlGenerator::GeneratePagination($product->getObjectName(), "", 
                        $total, $page_displayed, $elements_by_page, array('cid'=>$cat->Id));
?>