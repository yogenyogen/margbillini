<?php 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
if (JFactory::getUser()->guest)
{
    JFactory::getApplication()->enqueueMessage(JText::_('COM_CATALOG_INVALID_USER'), 'error');
    JFactory::getApplication()->redirect('/');
}
$ob = new bll_sale(0);
$uid=JFactory::getUser()->id;
$nelementsbypage =NUMBER_ELEMENTS_BY_PAGE;
$total = count($ob->findAll('UserId', $uid,true, $ob->getPrimaryKeyField()));
$lower_limit=0;
$curr = bll_currencies::getActiveCurrency();
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];

$objs= $ob->findAll('UserId', $uid,true, $ob->getPrimaryKeyField(), $lower_limit, $nelementsbypage);
$LangId=AuxTools::GetCurrentLanguageIDJoomla();
$lang = new languages($LangId);
?>
<div class="product-list">
<div id="j-main-container" class="orders"> 
    <?php if(!isset($_GET['tmpl']) || (isset($_GET['tmpl']) && $_GET['tmpl']!='component')): ?>
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="<?php echo JRoute::_('/index.php?option=com_users&view=profile'); ?>">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_BACK')?>
                </a>
        </div>
    </div>
    <?php endif; ?>
    <h3><i class="fa fa-heart fa-rotate-270"></i><span>
        <?php  echo JText::_('COM_CATALOG_ORDERS'); ?></span><i class="fa fa-heart fa-rotate-90"></i>
    </h3>
    <table class="table table-striped">
        <tr>
              <th><?php echo JText::_('COM_CATALOG_ID')?></th>
              <th><?php echo JText::_('COM_CATALOG_TOTAL')?></th>
              <th><?php echo JText::_('COM_CATALOG_DATE')?></th>
              <th><?php echo JText::_('COM_CATALOG_PAYMENT_METHOD')?></th>
              <th><?php echo JText::_('COM_CATALOG_SHIPPING_METHOD')?></th>
              <th><?php echo JText::_('COM_CATALOG_SALE_STATE')?></th>
              <th><?php echo JText::_('COM_CATALOG_ACTIONS')?></th>
        </tr>
<?php
foreach($objs as $obj)
{
    $payment = new bll_paymentmethod($obj->PaymentMethodId);
    $shipping = new bll_shippingmethod($obj->ShippingMethodId);
    $salestate = new bll_salestate($obj->SaleStateId);
    $products = bll_sale::getProductsFromSale($obj->Id);
    $curr= new bll_currencies($obj->CurrencyId);
    $productsid = array();
    $productscant = array();
    if(isset($products[0]))
        $productsid = $products[0];
    if(isset($products[1]))
        $productscant = $products[1];
    $coupon = new catalogcoupon($obj->CouponId);
    $reduce=0;
?>

    <tr>
        <td> <?php echo $obj->Id; ?></td>
        <td> <?php echo AuxTools::MoneyFormat($obj->Total, $curr->CurrCode, $obj->CurrencyRate); ?></td>
        <td> <?php echo $obj->Date; ?></td>
        <td><?php echo $payment->getLanguageValue($LangId)->Name; ?></td>
        <td><?php echo $shipping->getLanguageValue($LangId)->Name; ?></td>
        <td><?php echo $salestate->getLanguageValue($LangId)->Name; ?></td>
        <td>
            <script>
             jQuery(function() {
              jQuery( "#dialog-<?php echo $obj->Id; ?>" ).dialog({
                autoOpen: false,
                modal:true,
                show: {
                  effect: "blind",
                  duration: 300
                },
                hide: {
                  effect: "blind",
                  duration: 300
                }
              });

              jQuery( "#detail-<?php echo $obj->Id; ?>" ).click(function() {
                jQuery( "#dialog-<?php echo $obj->Id; ?>" ).dialog( "open" );
              });
            });
            </script>
             
             <div id="dialog-<?php echo $obj->Id; ?>" title="<?php echo JText::_('COM_CATALOG_DETAILS'); ?>">
                 <?php 
                 $sale_total=0;
                for($i=0; $i< count($productsid); $i++):
                    $product = new bll_product($productsid[$i]);
                    $preduce=0;
                    if($coupon->Id)
                        $preduce = $product->SalePrice*($coupon->Discount/100);
                    $ptotal = ($product->SalePrice-$preduce)*$productscant[$i];
                    $sale_total+=$ptotal;
                    ?>
                    <p><?php echo JText::_('COM_CATALOG_PRODUCT'); ?></p>
                    <div class="span12">
                        <?php echo $product->getLanguageValue($LangId)->Name; ?> x <?php echo $productscant[$i]; ?>(<?php echo AuxTools::MoneyFormat($ptotal, $curr->CurrCode, $curr->Rate); ?>)
                    </div>
                    <?php

                endfor;
                ?>

                    <p>
                        <?php echo JText::_('COM_CATALOG_PAYMENT_METHOD'); ?>:
                        <?php  echo $payment->getLanguageValue($LangId)->Name; ?>
                    </p>
                    <p><?php echo JText::_('COM_CATALOG_SUB_TOTAL'); ?>:
                        <span id="sub-total"><?php echo AuxTools::MoneyFormat($sale_total, $curr->CurrCode, $obj->CurrencyRate); ?></span>
                    </p>
                    <p><?php echo JText::_('COM_CATALOG_SHIPPING'); ?>:
                        <span id="shi-total"><?php echo AuxTools::MoneyFormat($shipping->Price, $curr->CurrCode, $obj->CurrencyRate); ?></span>
                    </p>
                    <p><?php echo JText::_('COM_CATALOG_TOTAL'); ?>:
                        <span id="total"><?php echo AuxTools::MoneyFormat($sale_total+$shipping->Price, $curr->CurrCode, $obj->CurrencyRate); ?></span>
                    </p>
             </div>
            
            <button class="btn" id="detail-<?php echo $obj->Id; ?>">
                <span class="icon-list"></span>
                <?php echo JText::_('COM_CATALOG_DETAILS'); ?>
            </button>
        </td>
    </tr>
<?php
}
?>
      </table>
    <?php
    echo HtmlGenerator::GeneratePagination($ob->getObjectName(), './index.php?option=com_catalog&view=sales&layout=sales', $total, $lower_limit, $nelementsbypage);
    ?>
</div>
</div>