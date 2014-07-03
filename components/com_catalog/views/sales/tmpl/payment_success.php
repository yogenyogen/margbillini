<?php

$sess=JFactory::getApplication()->getSession();
$sess->set('products', array());
$input = JFactory::getApplication()->input->getArray();
$productsid=array();
$productscant = array();

$total=0;

$cid=null;

$smid=0;

$pmid=0;

$LangId = AuxTools::GetCurrentLanguageIDJoomla();

if(isset($input['p']))
    $productsid=$input['p'];

if(isset($input['pc']))

    $productscant=$input['pc'];

if(isset($input['total']))

    $total=$input['total'];

if(isset($input['cid']))

    $cid=$input['cid'];

if(isset($input['pmid']))

    $pmid=$input['pmid'];

if(isset($input['smid']))

    $smid=$input['smid'];

if(isset($input['sid']))

    $sid=$input['sid'];

$shipping = new bll_shippingmethod($smid);

$sale = new bll_sale($sid);

if(($sale->UserId != JFactory::getUser()->id) && !JFactory::getUser()->guest)
{

    JFactory::getApplication()->enqueueMessage(JText::_('COM_CATALOG_INVALID_USER'), 'error');
    JFactory::getApplication()->redirect('/');

}
if(($sale->Total-$shipping->Price) != $total)
{

    JFactory::getApplication()->enqueueMessage(JText::_('COM_CATALOG_INVALID_SALE_DETAIL'), 'error');
    JFactory::getApplication()->redirect('/');
}

$payment  = new bll_paymentmethod(($pmid));

$desc=$payment->getLanguageValue($LangId)->Description;

$coupon = new catalogcoupon($cid);

$reduce=0;

if($coupon->Id > 0)

{

    $reduce = $total*($coupon->Discount/100);

}

$html="<div style=\"background-color:#EDEDED; padding:20px; font-family:Arial !important;\"><div style=\"background-color:#fff; overflow: hidden; border:1px solid #ccc; margin:0 auto; width:500px; padding:20px 30px;\">";

$html.="
<table width=\"100%\" border=\"0\" cellpadding=\"0\"  cellspacing=\"0\" >
<tr>
	<td align=\"left\"><img src=\"https://www.freeroamingpanama.com/images/logo_bill.jpg\" /></td>
	<td align=\"right\">
		<h3 style=\"font-family:Arial !important; font-size:14px; text-transform:uppercase; margin:0;\">".JText::_('COM_CATALOG_SALE_CONFIRMATION')."</h3>
		<h3 style=\"font-family:Arial !important; font-size:17px; margin:0;\">Orden # $sale->Id</h3>
	</td>
</tr>
<tr>
	<td colspan=\"2\">
		<img src=\"https://www.freeroamingpanama.com/images/pointer.jpg\" />
		<p style=\"margin:15px 0 15px 0; font-family:Arial !important;\">Gracias por utilizar nuestros servicios.<br/>
		Hemos recibido su orden, en pocos momentos, procederemos a procesarlo.</p>

		<p style=\"margin-bottom:15px; font-family:Arial !important;\">A continuación el detalle de la orden:</p>
		<p style=\"margin-bottom:5px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_DATE').":</strong>". $sale->Date."</p>
        <p style=\"margin-bottom:5px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_PAYMENT_METHOD').":</strong>". $payment->getLanguageValue($LangId)->Name."</p>
        <p style=\"margin-bottom:20px; font-family:Arial !important;\"><strong>".JText::_('COM_CATALOG_SHIPPING_METHOD').":</strong>". $shipping->getLanguageValue($LangId)->Name."</p>

		
		<table width=\"100%\" border=\"1\" cellpadding=\"5\" style=\"background:#fff; border:1px solid #ccc;\">
			<tr style=\"background:#efefef; text-align:left; font-size:12px;\" >
				<th>Cantidad</th>
				<th>Descripción</th>
				<th>Precio</th>
				<th>Precio Total</th>
			</tr>";
			for($i=0; $i< count($productsid); $i++):
		            $product = new bll_product($productsid[$i]);
		            $preduce=0;
		            if($coupon->Id)
		                $preduce = $product->SalePrice*($coupon->Discount/100);
		            $ptotal = AuxTools::MoneyFormat(($product->SalePrice-$preduce)*$productscant[$i]);
					$punit = AuxTools::MoneyFormat($product->SalePrice);
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
                <td align=\"left\"><p style=\"margin-bottom:0px; font-family:Arial !important;\" id=\"sub-total\">".  AuxTools::MoneyFormat($total)."</p></td>
			</tr>
			<tr>
				<td align=\"right\"><p style=\"margin-bottom:0px; text-align:right; font-family:Arial !important;\">". JText::_('COM_CATALOG_SHIPPING').":</p></td>
				<td align=\"left\"><p style=\"margin-bottom:0px; font-family:Arial !important;\" id=\"shi-total\">".  AuxTools::MoneyFormat($shipping->Price)."</p></td>
			</tr>
			<tr style=\"border-top:1px solid #ccc; font-size:16px;\" class=\"total_bill\">
				<td align=\"right\"><p style=\"margin-bottom:0px; font-size:16px; text-align:right; font-family:Arial !important;\">". JText::_('COM_CATALOG_TOTAL').":</p></td>
				<td align=\"left\"><p style=\"margin-bottom:0px; font-size:16px; font-family:Arial !important;\" id=\"total_bill\"><strong>".  AuxTools::MoneyFormat($total+$shipping->Price)."</strong></p></td>
			</tr>
   			</table><img src=\"https://www.freeroamingpanama.com/images/pointer.jpg\" /><div style=\"font-family:Arial !important;\" class=\"desc-pag\">$desc</div>";
  

     

     $html.="</div>";
     $user = JFactory::getUser($sale->UserId);
     $config = new JConfig();
     $mailer=    JFactory::getMailer();
     $mailer->isHtml(true);
     $mailer->setBody($html);
     $mailer->setSubject("Detalle de la compra #".$sale->Id);
     $mailer->setSender(array("Free Roaming Panamá", "ordenes@freeroamingpanama.com"));
     $mailer->SetFrom("ordenes@freeroamingpanama.com", "Free Roaming Panamá");
     $mailer->AddAddress($user->email);
     $mailer->addCC("ordenes@freeroamingpanama.com");
     $db = new dbprovider();
     $query = "SELECT DISTINCT user_id FROM `#__user_usergroup_map` WHERE group_id = 7 OR group_id = 8";
     $db->Query($query);
     $admins=$db->getNextObjectList();
     foreach($admins as $admin)
     {
         $usr= JFactory::getUser($admin->user_id);
         $mailer->addCC($usr->email);
     }
     $mailer->Send();

echo $html;

