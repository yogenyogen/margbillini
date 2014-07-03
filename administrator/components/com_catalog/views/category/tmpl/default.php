<?php

$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$category = new bll_category(0);
$root_categories = bll_category::getRootCategories();
?>

<div id="j-main-container" class="span10"> 
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">

                <a class="btn btn-small btn-success" href="./index.php?option=com_catalog&view=category&layout=edit">

                        <?php echo JText::_('COM_CATALOG_NEW')?>

                    <span class="icon-new icon-white"></span>

                </a>

        </div>
        <div class="btn-wrapper" id="toolbar-cancel">
            <a class="btn btn-small" href="./index.php?option=com_catalog&view=category&layout=default">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
            </a>
        </div>
    </div>
    <h3>
        <?php echo JText::_('COM_CATALOG_CATEGORY')." ". JText::_('COM_CATALOG_MANAGER'); ?>
    </h3>
    <?php
    echo bll_category::getCategoriesTreeHtmlAdmin($root_categories);
    ?>
</div>