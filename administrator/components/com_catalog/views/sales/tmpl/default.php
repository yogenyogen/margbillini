<?php 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$ob = new bll_sale(null);
$nelementsbypage =NUMBER_ELEMENTS_BY_PAGE;
$lower_limit=0;
$pmid=0;
$user_id="";
$sale_id="";

$array_pag_params=array();
$field_params=null;
$value_params=null;
if(isset($_REQUEST['limitstart']))
    $lower_limit=$_REQUEST['limitstart'];
if(isset($_REQUEST['pmid']) && $_REQUEST['pmid'] != "")
{
    $pmid=$_REQUEST['pmid'];
    $array_pag_params['pmid']=$pmid;
    $field_params[]=array('PaymentMethodId' , '=');
    $value_params[]=array($pmid , 'AND');
}
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != "")
{
    $user_id=$_REQUEST['user_id'];
    $array_pag_params['user_id']=$user_id;
    $field_params[]=array('UserId', '=');
    $value_params[]=array($user_id, 'AND');
}
if(isset($_REQUEST['sale_id']) && $_REQUEST['sale_id'] != "")
{
    $sale_id=$_REQUEST['sale_id'];
    $array_pag_params['sale_id']=$sale_id;
    $field_params[]=array('Id', '=');
    $value_params[]=array($sale_id, 'AND');
}

$total = count($ob->findAll($field_params,$value_params,true, $ob->getPrimaryKeyField()));
$objs= $ob->findAll($field_params,$value_params,true, $ob->getPrimaryKeyField(), $lower_limit, $nelementsbypage);
$LangId=AuxTools::GetCurrentLanguageIDJoomla();
$lang = new languages($LangId);

$pmethods = new bll_paymentmethod(0);
$pmethods = $pmethods->findAll();
?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.9.1.js"></script>
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<div id="j-main-container" class="span10"> 
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <form method="POST">
    <table>
        <tr>
            <td>
                <label><?php echo JText::_('COM_CATALOG_PAYMENT_METHOD')?></label>
                <select name="pmid">
                    <option value=""></option>
                    <?php 
                    foreach($pmethods as $pm)
                    {
                        if($pm->Id == $pmid)
                            echo "<option selected=\"selected\" value=\"$pm->Id\">".$pm->getLanguageValue($LangId)->Name."</option>";
                        else
                            echo "<option value=\"$pm->Id\">".$pm->getLanguageValue($LangId)->Name."</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <label><?php echo JText::_('COM_CATALOG_USER')." ". JText::_('COM_CATALOG_ID');?></label>
                <input type="text" name="user_id" value="<?php echo $user_id ?>" />
            </td>
            <td>
                <label><?php echo JText::_('COM_CATALOG_SALE')." ". JText::_('COM_CATALOG_ID');?></label>
                <input type="text" name="sale_id" value="<?php echo $sale_id ?>" />
            </td>
            <td>
                <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>"/>
                <input type="submit" value="Filtrar"/>
            </td>
        </tr>
    </table>
    </form>
    <h3>
        <?php  echo JText::_('COM_CATALOG_CITIES')." ". JText::_('COM_CATALOG_MANAGER'); ?>
    </h3>
    <table class="table table-striped">
        <tr>
              <th><?php echo JText::_('COM_CATALOG_ID')?></th>
              <th><?php echo JText::_('COM_CATALOG_NAME')?></th>
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
    $curr = new bll_currencies($obj->CurrencyId);
    $user = JFactory::getUser($obj->UserId);
    $profile=JUserHelper::getProfile($user->id)->getProperties();
    $profile=$profile['profile'];
    $products = bll_sale::getProductsFromSale($obj->Id);
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
        <td> <?php echo $user->name; ?></td>
        <td> <?php echo AuxTools::MoneyFormat($obj->Total); ?></td>
        <td> <?php echo $obj->Date; ?></td>
        <td><?php echo $payment->getLanguageValue($LangId)->Name; ?></td>
        <td><?php echo $shipping->getLanguageValue($LangId)->Name; ?></td>
        <td><?php echo $salestate->getLanguageValue($LangId)->Name; ?></td>
         <td>
            <form method="POST" action="./index.php?option=com_catalog&view=sales&layout=edit">
                 <input type="hidden"  name="id" value="<?php echo $obj->Id; ?>"/>
                 <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>" />
                 <button class="btn">
                     <span class="icon-edit"></span>
                     <?php echo JText::_('COM_CATALOG_CHANGE_SALE_STATE')?></button>
            </form>
             <script>
             $(function() {
              $( "#dialog-<?php echo $obj->Id; ?>" ).dialog({
                autoOpen: false,
                modal: true,
                width:'auto',
                show: {
                  effect: "blind",
                  duration: 300
                },
                hide: {
                  effect: "blind",
                  duration: 300
                }
              });

              $( "#detail-<?php echo $obj->Id; ?>" ).click(function() {
                $( "#dialog-<?php echo $obj->Id; ?>" ).dialog( "open" );
              });
            });
            </script>
             <div id="dialog-<?php echo $obj->Id; ?>" title="<?php echo JText::_('COM_CATALOG_DETAILS'); ?>">
                 <?php
                $desc="<p>".JText::_('COM_CATALOG_SHIPPING')."</p>";
                $desc.="<p>".JText::_('COM_CATALOG_COUNTRY').": ".$profile['country']."</p>";
                $desc.="<p>".JText::_('COM_CATALOG_REGION').": ".$profile['region']."</p>";
                $desc.="<p>".JText::_('COM_CATALOG_CITY').": ".$profile['city']."</p>";
                $desc.="<p>".JText::_('COM_CATALOG_ADDRESS').": ".$profile['address1']." ".$profile['address2']."</p>";
                $sub_total=0;
                $html="<div style=\"background-color:#EDEDED; padding:20px; font-family:Arial !important;\">
                    <div style=\"background-color:#fff; overflow: hidden; border:1px solid #ccc; margin:0 auto; width:500px; padding:20px 30px;\">";

                $html.="
                <table width=\"100%\" border=\"0\" cellpadding=\"0\"  cellspacing=\"0\" >
                <tr>
                        <td align=\"left\"><img src=\"http://www.margbillini.com/images/logo_bill.png\" /></td>
                        <td align=\"right\">
                                <h3 style=\"font-family:Arial !important; font-size:14px; text-transform:uppercase; margin:0;\">".JText::_('COM_CATALOG_SALE_CONFIRMATION')."</h3>
                                <h3 style=\"font-family:Arial !important; font-size:17px; margin:0;\">".JText::_('COM_CATALOG_ORDER')." # $obj->Id</h3>
                                <p>".JText::_('COM_CATALOG_NAME').": $user->name - ".JText::_('COM_CATALOG_ID').":$user->id</p>
                        </td>
                </tr>
                <tr>
                        <td colspan=\"2\">
                                <img src=\"http://www.margbillini.com/images/pointer.jpg\" />
                                <p style=\"margin:15px 0 15px 0; font-family:Arial !important;\">".JText::_('COM_CATALOG_PURCHASE_THANK_YOU_MESSAGE')."</p>

                                <p style=\"margin-bottom:15px; font-family:Arial !important;\">".JText::_('COM_CATALOG_ORDER_DETAIL').":</p>
                                <p style=\"margin-bottom:5px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_DATE').":</strong>". $obj->Date."</p>
                        <p style=\"margin-bottom:5px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_PAYMENT_METHOD').":</strong>". $payment->getLanguageValue($LangId)->Name."</p>
                        <p style=\"margin-bottom:20px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_SHIPPING_METHOD').":</strong>". $shipping->getLanguageValue($LangId)->Name."</p>
                        <p style=\"margin-bottom:20px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_SALE_STATE').":</strong>". $salestate->getLanguageValue($LangId)->Name."</p>


                                <table width=\"100%\" border=\"1\" cellpadding=\"5\" style=\"background:#fff; border:1px solid #ccc;\">
                                        <tr style=\"background:#efefef; text-align:left; font-size:12px;\" >
                                                <th>".JText::_('COM_CATALOG_QUANTITY')."</th>
                                                <th>".JText::_('COM_CATALOG_DESCRIPTION')."</th>
                                                <th>".JText::_('COM_CATALOG_PRICE')."</th>
                                                <th>".JText::_('COM_CATALOG_TOTAL')."</th>
                                        </tr>";
                                        for($i=0; $i< count($productsid); $i++):
                                            $product = new bll_product($productsid[$i]);
                                            $preduce=0;
                                            if($coupon->Id)
                                                $preduce = $product->SalePrice*($coupon->Discount/100);
                                            $temp_total = ($product->SalePrice-$preduce)*$productscant[$i];
                                            $sub_total+=$temp_total;
                                            $ptotal = AuxTools::MoneyFormat($temp_total, $curr->CurrCode, $obj->CurrencyRate);
                                                        $punit = AuxTools::MoneyFormat($product->SalePrice, $curr->CurrCode, $obj->CurrencyRate);
                                            $html.="<tr style=\"font-size:12px;\"><td>".$productscant[$i]."</td><td><p style=\"margin:0; font-family:Arial !important;\">".$product->getLanguageValue($LangId)->Name."</p></td><td>".$punit."</td><td>".$ptotal."</td></tr>";
                                        endfor;
                                        $html.="
                                </table>
                        </td>
                        </tr>
                </table>";
                         $html.="    
                            <table align=\"right\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">
                                        <tr>
                                                <td align=\"right\"><p style=\"margin-bottom:0px; text-align:right; font-family:Arial !important;\">". JText::_('COM_CATALOG_SUB_TOTAL').":</p></td>
                                <td align=\"left\"><p style=\"margin-bottom:0px; font-family:Arial !important;\" id=\"sub-total\">".  AuxTools::MoneyFormat($temp_total)."</p></td>
                                        </tr>
                                        <tr>
                                                <td align=\"right\"><p style=\"margin-bottom:0px; text-align:right; font-family:Arial !important;\">". JText::_('COM_CATALOG_SHIPPING').":</p></td>
                                                <td align=\"left\"><p style=\"margin-bottom:0px; font-family:Arial !important;\" id=\"shi-total\">".  AuxTools::MoneyFormat($shipping->Price)."</p></td>
                                        </tr>
                                        <tr style=\"border-top:1px solid #ccc; font-size:16px;\" class=\"total_bill\">
                                                <td align=\"right\"><p style=\"margin-bottom:0px; font-size:16px; text-align:right; font-family:Arial !important;\">". JText::_('COM_CATALOG_TOTAL').":</p></td>
                                                <td align=\"left\"><p style=\"margin-bottom:0px; font-size:16px; font-family:Arial !important;\" id=\"total_bill\"><strong>".  AuxTools::MoneyFormat($temp_total+$shipping->Price)."</strong></p></td>
                                        </tr>
                                        </table><img src=\"http://www.margbillini.com/images/pointer.jpg\" /><div style=\"font-family:Arial !important;\" class=\"desc-pag\">$desc</div>";
                     $html.="</div></div>";
                     echo $html;
                ?> 
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
    echo HtmlGenerator::GeneratePagination($ob->getObjectName(), './index.php?option=com_catalog&view=sales', $total, $lower_limit, $nelementsbypage, $array_pag_params);
    ?>
</div>