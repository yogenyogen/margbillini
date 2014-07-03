<?php 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$ob = new bll_product(0);
$nelementsbypage =NUMBER_ELEMENTS_BY_PAGE;
$total = count($ob->findAll(null,null,true, $ob->getPrimaryKeyField()));
$lower_limit=0;

if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$objs= $ob->findAll(null,null,true, $ob->getPrimaryKeyField(), $lower_limit, $nelementsbypage);
$LangId=AuxTools::GetCurrentLanguageIDJoomla();
$lang = new languages($LangId);
?>

<div id="j-main-container" class="span10"> 

    <div class="btn-toolbar" id="toolbar">

        <div class="btn-wrapper" id="toolbar-new">

                <a class="btn btn-small btn-success" href="./index.php?option=com_catalog&view=products&layout=selectcategory">

                        <?php echo JText::_('COM_CATALOG_NEW')?>

                    <span class="icon-new icon-white"></span>

                </a>

        </div>

        <div class="btn-wrapper" id="toolbar-new">

                <a class="btn btn-small" href="./index.php?option=com_catalog">

                <span class="icon-cancel"></span>

                <?php echo JText::_('COM_CATALOG_CANCEL')?>

                </a>

        </div>

    </div>

    <h3>

        <?php  echo JText::_('COM_CATALOG_PRODUCTS')." ". JText::_('COM_CATALOG_MANAGER'); ?>

    </h3>

    <table class="table table-striped">

        <tr>

              <th><?php echo JText::_('COM_CATALOG_ID')?></th>

              <th><?php echo JText::_('COM_CATALOG_NAME')."($lang->title_native)"?></th>

              <th><?php echo JText::_('COM_CATALOG_DESCRIPTION')."($lang->title_native)"?></th>

              <th><?php echo JText::_('COM_CATALOG_CATEGORY')."($lang->title_native)"?></th>

              <th><?php echo JText::_('COM_CATALOG_ACTIONS')?></th>

        </tr>

        

<?php
foreach($objs as $obj)
{
    $category = new bll_category($obj->CategoryId);
?>

    <tr>

        <td> <?php echo $obj->Id; ?></td>
        <td> <?php echo $obj->getLanguageValue($LangId)->Name; ?></td>
        <td> <?php echo $obj->getLanguageValue($LangId)->Description; ?></td>
        <td> 
        <?php echo $category->getLanguageValue($LangId)->Name; ?> 
        </td>
        <td>

            <form method="POST" action="./index.php?option=com_catalog&view=products&layout=edit">
                 <input type="hidden"  name="id" value="<?php echo $obj->Id; ?>"/>
                 <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>" />
                 <button><?php echo JText::_('COM_CATALOG_EDIT')?></button>
            </form>
            <form method="POST" action="./index.php?option=com_catalog&view=products&layout=changecategory">
                 <input type="hidden"  name="id" value="<?php echo $obj->Id; ?>"/>
                 <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>" />
                 <button><?php echo JText::_('COM_CATALOG_CHANGE_CATEGORY')?></button>
            </form>
            <a class="button" href="./index.php?option=com_catalog&view=products&action=delete&id=<?php echo $obj->Id; ?>">
            <?php echo JText::_('COM_CATALOG_DELETE')?>
            </a>
        </td>    
    </tr>
<?php
}
?>

      </table>
    <?php

    echo HtmlGenerator::GeneratePagination($ob->getObjectName(), './index.php?option=com_catalog&view=products', $total, $lower_limit, $nelementsbypage);

    ?>

</div>