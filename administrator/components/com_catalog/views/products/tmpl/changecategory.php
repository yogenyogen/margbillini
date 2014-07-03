<?php
$jspath = AuxTools::getJSPathFromPHPDir(BASE_DIR); 

$lid=0;
if(isset($_REQUEST['id']))
    $lid=$_REQUEST['id'];
$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];
$root_categories = bll_category::getRootCategories();

$product = new bll_product($lid);
$cat = new bll_category($product->CategoryId);
?>


<div id="j-main-container" class="span10"> 
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
            <a class="btn btn-small" href="./index.php?option=com_catalog&view=products&layout=default&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
            </a>
        </div>
    </div>
    <p><?php echo JText::_('COM_CATALOG_CATEGORY'); ?>: <?php echo $cat->getLanguageValue(AuxTools::GetCurrentLanguageIDJoomla())->Name; ?></p>
    <h3>
        <?php echo JText::_('COM_CATALOG_SELECT_CATEGORY'); ?>
    </h3>
    <?php
    echo bll_category::getCategoriesTreeHtmlChangeProductCategory($root_categories,0, $lid);
    ?>
</div>