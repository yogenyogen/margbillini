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
<div class="products-holder" span="span12">
    <h4>
        <?php echo $catlv->Name; ?>
    </h4>
    <div class="product-list">
            <?php foreach($products as $product): ?>
            <div class="product" class="span3">
                <?php 
                $img = $product->getMainImage();
                $lval = $product->getLanguageValue($LangId);
                if(is_file(JPATH_ROOT.DS.$img->ImageThumb))
                    $image = $img->ImageThumb;
                else
                    $image='./components/com_catalog/images/no-image-listing.jpg';

                ?>
                <a href="./index.php/<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><img class="lis-image" src="<?php echo $image; ?>" /></a>
                <h3><a href="./index.php/<?php echo DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html" ?>"><?php echo $lval->Name; ?></a></h3>
            </div>
            <?php endforeach; ?>
    </div>
</div>
<?php 

echo HtmlGenerator::GeneratePagination($product->getObjectName(), "", 
                        $total, $page_displayed, $elements_by_page, array('cid'=>$cat->Id));
?>