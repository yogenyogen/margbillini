<?php

$lower_limit=0;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$root_categories = bll_category::getRootCategories();
?>


<div id="j-main-container" class="span10"> 
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
            <a class="btn btn-small" href="./index.php?option=com_catalog&view=products&layout=products&limitstart=<?php echo $lower_limit; ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
            </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_SELECT_CATEGORY'); ?>
    </h3>
    <?php
    echo bll_category::getCategoriesTreeHtmlProductSelection($root_categories);
    ?>
</div>