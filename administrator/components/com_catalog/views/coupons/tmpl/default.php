<?php 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$ob = new catalogcoupon(0);
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
                <a class="btn btn-small btn-success" href="./index.php?option=com_catalog&view=coupons&layout=edit">
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
        <?php  echo JText::_('COM_CATALOG_COUPONS')." ". JText::_('COM_CATALOG_MANAGER'); ?>
    </h3>
    <table class="table table-striped">
        <tr>
              <th><?php echo JText::_('COM_CATALOG_ID')?></th>
              <th><?php echo JText::_('COM_CATALOG_CODE')?></th>
              <th><?php echo JText::_('COM_CATALOG_DISCOUNT')?></th>
              <th><?php echo JText::_('COM_CATALOG_USES')?></th>
              <th><?php echo JText::_('COM_CATALOG_DATE')?></th>
              <th><?php echo JText::_('COM_CATALOG_ENABLE')?></th>
              <th><?php echo JText::_('COM_CATALOG_ACTIONS')?></th>
        </tr>
<?php
foreach($objs as $obj)
{
    $sale = new bll_sale(0);
    $sales=$sale->findAll('CouponId', $obj->Id);
?>

    <tr>
        <td> <?php echo $obj->Id; ?></td>
        <td> <?php echo $obj->Code; ?></td>
        <td> <?php echo $obj->Discount; ?></td>
        <td> <?php echo $obj->Uses; ?></td>
        <td> <?php echo $obj->Date; ?></td>
        <td> <?php echo $obj->Enable; ?></td>
         <td>

            <form method="POST" action="./index.php?option=com_catalog&view=coupons&layout=edit">
                 <input type="hidden"  name="id" value="<?php echo $obj->Id; ?>"/>
                 <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>" />
                 <button><?php echo JText::_('COM_CATALOG_EDIT')?></button>
            </form>
            <?php if(count($sales)<=0): ?>
                <a class="button" href="./index.php?option=com_catalog&view=coupons&action=delete&id=<?php echo $obj->Id; ?>">
                <?php echo JText::_('COM_CATALOG_DELETE')?>
                </a>
            <?php endif; ?>
        </td>    
    </tr>
<?php
}
?>
      </table>
    <?php
    echo HtmlGenerator::GeneratePagination($ob->getObjectName(), './index.php?option=com_catalog&view=coupons', $total, $lower_limit, $nelementsbypage);
    ?>
</div>