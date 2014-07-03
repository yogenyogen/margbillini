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
$path = "";
foreach($ro->getPathwayNames() as $p)
{
    $path.=DS.AuxTools::SEFReady($p);
}
$cat = new bll_category($cid);
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$catlv = $cat->getLanguageValue($LangId);
$product = new bll_product(0);

$limitstart=0;
if(isset($_POST['limitstart']))
    $limitstart=$_POST['limitstart'];

$elements_by_page=6;
$total = count($product->findAll('CategoryId', $cat->Id, true, 'Id'));
$page_displayed=1;
if(($limitstart)!= 0)
{
    $page_displayed=($elements_by_page+$limitstart)/$elements_by_page;   
}

$products = $product->findAll('CategoryId', $cat->Id, true, 'Id', $limitstart, $elements_by_page);
?>
<h1><?php echo JText::_('COM_CATALOG_CATALOG'); ?></h1>
<div class="products-holder">
    <h4>
        <?php echo $catlv->Name; ?>
    </h4>
    <p>
        <?php echo $catlv->Description; ?>
    </p>
    <div class="product-list">
            <?php foreach($products as $product): ?>
            <div class="product">
                <?php 
                $img = $product->getMainImage();
                $lval = $product->getLanguageValue($LangId);
                if(strlen($img->ImageUrl)> 4)
                    $image = $img->ImageUrl;
                else
                    $image='./components/com_catalog/images/no-image-listing.jpg';

                ?>
                <img class="lis-image" src="<?php echo $image; ?>" />
                <h3><?php echo $lval->Name; ?></h3>
                <form action="<?php echo $path.DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>" method="POST">
                    <input name="pid" type="hidden" value="<?php echo $product->Id; ?>"/>
                    <input type="submit" value="<?php echo JText::_('COM_CATALOG_PRODUCT_DETAIL'); ?>"/>
                </form>
            </div>
            <?php endforeach; ?>
    </div>
</div>
<?php 

echo HtmlGenerator::GeneratePagination($product->getObjectName(), "", 
                        $total, $page_displayed, $elements_by_page, array('cid'=>$cat->Id));
?>