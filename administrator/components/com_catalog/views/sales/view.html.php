<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
 

/**
 * CatalogProducts View
 */
class CatalogViewSales extends JViewLegacy
{
        /**
         * HelloWorlds view display method
         * @return void
         */
        function display($tpl = null) 
        {
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                $this->actions();
                // Display the template
                parent::display($tpl);

        }

        function actions()
        {
            if(isset($_POST['action']) || isset($_GET['action']))
            {
                if(isset($_GET['action']))
                    $action=$_GET['action'];
                if(isset($_POST['action']))
                    $action=$_POST['action'];
                
                $type_msg='message';
                switch ($action)
                {
                    case 'edit':
                        $id=0;
                        $obj=new bll_sale(0);
                    
                        if(isset($_POST['Id']))
                        {
                            $id=$_POST['Id'];
                            $obj=new bll_sale($id);
                            $sendmail=0;
                            if($obj->SaleStateId != 1)
                            {
                                $sendmail++;
                            }
                            $obj->setAttributes($_POST);
                            
                            $obj=$obj->update();
                            if($obj !== false)
                            {
                                $action_msg =JText::_('COM_CATALOG_EDITED');
                                if($obj->SaleStateId == 1)
                                {
                                    $sendmail++;
                                }
                                if($sendmail > 1)
                                {
                                    $this->sendApprovedSaleMsg($obj);
                                }
                            }
                            else 
                            {
                                $type_msg='error';
                                $action_msg =JText::_('COM_CATALOG_ERROR_EDITING');
                            }
                        }
                        else
                        { 
                            $obj->setAttributes($_POST);
                            $obj=$obj->insert();
                            if($obj !== false)
                                $action_msg =JText::_('COM_CATALOG_CREATED');
                            else
                            {

                                $action_msg =JText::_('COM_CATALOG_ERROR_CREATING');
                                $type_msg='error';
                            }

                            $what_msg  =" ". JText::_('COM_CATALOG_SALE'); 
                            JFactory::getApplication()->enqueueMessage(
                                    $action_msg.$what_msg,
                                    $type_msg

                            );

                        }

                    break;



                    case 'delete':
                        if(isset($_GET['id']))
                        {
                            $id=$_GET['id'];
                            $obj=new bll_sale($id);

                            if($obj->delete() == true)
                                $action_msg =JText::_('COM_CATALOG_DELETED');

                            else
                            {
                                $type_msg='error';
                                $action_msg =JText::_('COM_CATALOG_ERROR_DELETING');
                            }

                            $what_msg  =" ". JText::_('COM_CATALOG_SALE'); 
                            JFactory::getApplication()->enqueueMessage(
                                    $action_msg.$what_msg,
                                    $type_msg

                            );
                        }
                    break;
                 
                }
            }
        }
        
        /**
         * 
         * @param bll_sale $obj
         */
        private function sendApprovedSaleMsg($obj)
        {
            $payment = new bll_paymentmethod($obj->PaymentMethodId);
            $shipping = new bll_shippingmethod($obj->ShippingMethodId);
            $salestate = new bll_salestate($obj->SaleStateId);
            $LangId = $obj->LangId;
            $user = JFactory::getUser($obj->UserId);
            $curr = new bll_currencies($obj->CurrencyId);
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
                 $user = JFactory::getUser($obj->UserId);
                $config = new JConfig();
                $mailer=    JFactory::getMailer();
                $mailer->isHtml(true);
                $mailer->setBody($html);
                $mailer->setSubject(JText::_('COM_CATALOG_ORDER')." #".$obj->Id." ".JText::_('COM_CATALOG_ORDER_COMPLETED'));
                $mailer->setSender(array("Marg Billini", "ordenes@margbillini.com"));
                $mailer->SetFrom("ordenes@margbillini.com", "Marg Billini");
                $mailer->AddAddress($user->email);
                $mailer->addCC("ordenes@margbillini.com");
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
        }
}