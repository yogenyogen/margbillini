<?php
/**
 * @version		$Id: controller.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');

/**
 * Hello World Component Controller
 */
class CatalogController extends JControllerLegacy
{
    function setproduct()
    {
        
        $jinput = JFactory::getApplication()->input;
        $qpid=$jinput->get('pid', null);
        $qcant=$jinput->get('cant', null);
        if($qpid > 0)
        {
            $sess=JFactory::getApplication()->getSession();
            $productsid = $sess->get('products', array());
            $found=false;
            foreach($productsid as $id => &$cant)
            {
                if($qpid == $id)
                {
                    if($qcant>0)
                        $cant+=$qcant;
                    else
                        $cant++;
                    
                    $found=true;
                    break;
                }
            }
            if($found ===false)
            {
                if($qcant > 0)
                    $productsid[$qpid]+=$qcant;
                else
                    $productsid[$qpid]=1;
            }
            $sess->set('products', $productsid);

            $this->get_cart_summary();
        }
        else
        {            
            ob_clean();
            echo 0;
        }
        exit();
    }
    
    function setproducts()
    {
        $jinput = JFactory::getApplication()->input;
        $qpid = null;
        $qcant=null;
        if(isset($_GET['pid']))
            $qpid=$_GET['pid'];
        if(isset($_GET['cant']))
            $qcant=$_GET['cant'];
        if(is_array($qpid) && is_array($qcant))
        {
            $sess=JFactory::getApplication()->getSession();
            $productsid = array();
            for($i =0; $i<count($qpid); $i++)
            {
                if($qcant[$i] > 0)
                    $productsid[$qpid[$i]]=$qcant[$i];
            }
            $sess->set('products', $productsid);
            echo 1;
        }
        else if($qpid!== null)
        {
            $sess=JFactory::getApplication()->getSession();
            $tcant = 1;
            if($qcant !==null)
                $tcant=$qcant;
            $productsid = array($qpid=>$tcant);
            $sess->set('products', $productsid);
        }
        else
        {
            echo 0;
        }
        exit();
    }
    
    function coupon()
    {
        // Get the application object. 
        $code="";
        if(isset($_GET['str']))
            $code=$_GET['str'];
        $coupon = new catalogcoupon(0);
        $coupon = $coupon->find('Code', $code);
        if($coupon->Id > 0)
        {
            $obj = new bll_sale(0);
            $sales = $obj->findAll('CouponId', $coupon->Id);
            $uses = count($sales);
            $dttz = new DateTimeZone(LIB_TIMEZONE);
            $currdate = new DateTime('now', $dttz);
            $finishdate = new DateTime($coupon->Date, $dttz);
            if($coupon->Enable==0 || $uses >= $coupon->Uses  || 
                    ($currdate >= $finishdate)
              )
            {
                $response=json_encode(array(-1,ob_get_contents()));
            }
            else
            {
                $response= json_encode($coupon);
            }
        }
        else
        {
            $response= json_encode(array(-1, ob_get_contents()));
        }
        ob_clean();
        // Get the application object.  
        header('Content-type: application/json');
        echo( $response );
        exit;
    }
    
    function checkout()
    {
        //pending sale by default
        $stateid=2;
        $uid=0;
        $input=JFactory::getApplication()->input;
        //from id location class to name
        $country= $input->getInt('country', 0);
        $coun_object = new country($country);
        $input->set('country', $coun_object->Name);
        $city = $input->getInt('city', 0);
        $ci_object = new cities($city);
        $input->set('city', $ci_object->Name);
        $region=$input->getInt('region', 0);
        $re_object = new province($region);
        $input->set('region', $re_object->Name);
        $sector=$input->getInt('sector', 0);
        $se_object = new sector($sector);
        $input->set('sector', $se_object->Name);
        //end setting the location values
        $intertal_sale_fail_redirect=$input->getString("internal_sale_fail_redirect",
                "");
        $sale_success_redirect=$input->getString("sale_success_redirect",
                "");
        if(JFactory::getUser()->guest==false && JFactory::getUser()->id > 0)
        {
            $us = JFactory::getUser();
            //getting profile info
            $phone = $input->getString('phone', "");
            $country= $input->getString('country', "");
            $city = $input->getString('city', "");
            $dob = $input->getString('dob', "");
            $region=$input->getString('region', "");
            $sector=$input->getString('sector', "");
            $address1 = $input->getString('address1', "");
            $address2 = $input->getString('address2', "");
            $postal_code = $input->getString('postal_code', "");
            $profile=array();
            $profile['profile'] =array(
                'phone'=>$phone,
                'country'=>$country,
                'city'=>$city,
                'dob'=>$dob,
                'region'=>$region,
                'sector'=>$sector,
                'address1'=>$address1,
                'address2'=>$address2,
                'postal_code'=>$postal_code,
            );
            $us->setProperties($profile);
            if(!$us->save())
            {
                $this->setMessage(JText::sprintf('COM_CATALOG_REGISTRATION_SAVE_FAILED', $us->getError()));
                $this->setRedirect($intertal_sale_fail_redirect);
                $this->redirect();
                return;
            }
            $uid = $us->id;
            //end of getting profile info
        }
        else {
             //create user account
              $password = $this->generatePassword();
              $input->set("password", $password);
              $input->set("password2", $password);
              $input->set("mail", $input->getString("email", ""));
              
              //if a problem occurs during registration
              //function registerUser will redirect and report error.
              $return = $this->RegisterUser();
              if($return===false)
                  return;
              $user = JFactory::getUser($return);
              
              //Set free plan as default one on register
              $app = JFactory::getApplication();
              $jdb = JFactory::getDbo();
              // Mark the user as logged in
              $user->set('guest', 0);

              // Register the needed session variables
              $session = JFactory::getSession();
              $session->set('user', $user);

              // Check to see the the session already exists.
              $app->checkSession();
              $app->login( Array( 'username' => $user->username, 'password' => $password ));
              // Update the user related fields for the Joomla sessions table.
              $query = $jdb->getQuery(true)
                      ->update($jdb->quoteName('#__session'))
                      ->set($jdb->quoteName('guest') . ' = ' . $jdb->quote($user->guest))
                      ->set($jdb->quoteName('username') . ' = ' . $jdb->quote($user->username))
                      ->set($jdb->quoteName('userid') . ' = ' . (int) $user->id)
                      ->where($jdb->quoteName('session_id') . ' = ' . $jdb->quote($session->getId()));
              $jdb->setQuery($query)->execute();

              // Hit the user last visit field
              $user->setLastVisit();
              $uid = $user->id;
        }
        $city = $input->getString('city', "");
        $c = new cities(0);
        $c= $c->find('Name', $city);
        //products
        $pmid=$input->get('pmid',"");
        $total=$input->get('total',"");
        $smid=$input->get('smid',"");
        $cid=$input->get('cid',null);
        $cityid=$c->Id;
        $arrs=$input->getArray();
        $p = $arrs['p'];
        $pc  = $arrs['pc'];
        $Products=array();
        for($i=0; $i<count($p); $i++)
        {
            $Products[$p[$i]]=$pc[$i];
        }
        //1 if sale is created successfully, redirect if payment is
        //processed by a third party
        $ret=bll_sale::createSale($Products, $uid, $pmid, $stateid, 
                $smid, $cityid, $cid);
        $sale = new bll_sale();
        $lastInsertedSale = $sale->find(null,null,true,'Id');
        $msg="";
        switch($ret)
        {
            case 1:
                $vars = http_build_query(array('total'=>$total,'p'=>$p, 'pc'=>$pc, 'pmid'=>$pmid, 'smid'=>$smid, 'cid'=>$cid, 'sid'=>$lastInsertedSale->Id));
                $this->setRedirect($sale_success_redirect.'&'.$vars);
            break;
            case 0:
                $msg=  JText::_('COM_CATALOG_ERROR_CREATING_SALE');
            break;
            case -1:
                $msg=  JText::_('COM_CATALOG_INVALID_PAYMENT_METHOD');
            break;
            case -2:
                $msg=  JText::_('COM_CATALOG_INVALID_SALE_STATE');
            break;
            case -3:
                $msg=  JText::_('COM_CATALOG_INVALID_COUPON');
            break;
            case -4:
                $msg=  JText::_('COM_CATALOG_INVALID_SHIPPING_METHOD');
            break;
            case -5:
                $msg=  JText::_('COM_CATALOG_INVALID_CITY_SHIPPING');
            break;
            case -6:
                $msg=  JText::_('COM_CATALOG_INVALID_PRODUCTS');
            break;
        }
        if($msg!="")
        {
            $temp=$input->getArray();
            unset($temp['task']);
            $vars = http_build_query($temp);
            $this->setRedirect($intertal_sale_fail_redirect."?".$vars);
            JFactory::getApplication()->enqueueMessage($msg,'error');
        }
        $this->redirect();
    }
    
    function RegisterUser()
    {
        jimport( 'joomla.user.helper' );
        JPluginHelper::importPlugin('user');
        $jinput = JFactory::getApplication()->input;
        $language = new languages(AuxTools::GetCurrentLanguageIDJoomla());
        $lang = JFactory::getLanguage();
        $lang->load('com_users', JPATH_SITE, $language->lang_code, true);
        
        $id=0;
        $us = null;
        $isNew = false;
        $dbo = new dbprovider();
        $value = $jinput->getString("username", "");
        $temp=$jinput->getArray();
        unset($temp['task']);
        $vars = http_build_query($temp);
        $user_creation_fail_redirect=$jinput->get("user_creation_fail_redirect",
                "");
        $this->setRedirect($user_creation_fail_redirect."?".$vars);
        //validates if the username field is empty
        if ($value == "") {
          JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_INVALID_USERNAME"), 'error');
          $this->redirect();
          return false;
        }
        //validates if is a new or old user.
        if ($id <= 0) {
          $isNew = true;
          $us = JFactory::getUser();
          //Checking if user exist
          $query = "select * from #__users where username='" . $dbo->escape_string($value) . "'";
          $dbo->Query($query);
          $r = $dbo->getNextObject();

          //if user exists we cannot procced
          if ($r != null) {
            JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_USERNAME_EXISTS"), 'error');
            $this->redirect();
            return false;
          }
          $cache = JFactory::getCache();
          $cache->clean();
        } else {
          $us = JFactory::getUser($id);
          //Checking if user exist
          $query = "select * from #__users where username='" . $dbo->escape_string($value) . "'";
          $dbo->Query($query);
          $r = $dbo->getNextObject();
          //if user does not exists we cannot procced
          if ($r == null) {
            session_destroy();
            JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_LOGOUT_CLEAN_COOKIES"), 'error');
            $this->redirect();
            return false;
          }
        }

        $params = JComponentHelper::getParams('com_users');
        $us->username = $value;
        $us->guest=0;
        $regis_group=2;
        $us->groups=array("$regis_group"=>$regis_group);
        $us->activation = "";
        $us->block = 0;
        $pass = $jinput->getString("password", "");
        $pass2 = $jinput->getString("password2", "");

        //password fields must not be an empty string
        if ($pass == "" || $pass2 == "")
        {
            JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_ERROR_CREATING_PASSWORD"), 'error');
            $this->redirect();
            return false;
        }
        $salt = JUserHelper::genRandomPassword(32);
        $crypt = JUserHelper::getCryptedPassword($pass, $salt);
        $pass = $crypt . ':' . $salt;
        $us->password = $pass;
        if ($us->id <= 0)
          $us->password_clear = $jinput->getString("password", "");;


        $us->email = $jinput->getString("mail", "");
        $re = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
        if(preg_match($re, $us->email) !==1)
        {
            JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_INVALID_EMAIL"), 'error');
            $this->redirect();
            return false;
        }
        $us->name = $jinput->getString("name", "");

        if ($us->name == "" || $us->email == "")
        {
            JFactory::getApplication()->enqueueMessage(JText::_("COM_CATALOG_INVALID_USERNAME_EMAIL"), 'error');
            $this->redirect();
            return false;
        }
        //getting profile info
        $phone = $jinput->getString('phone', "");
        $country= $jinput->getString('country', "");
        $city = $jinput->getString('city', "");
        $dob = $jinput->getString('dob', "");
        $region=$jinput->getString('region', "");
        $sector=$jinput->getString('sector', "");
        $address1 = $jinput->getString('address1', "");
        $address2 = $jinput->getString('address2', "");
        $postal_code = $jinput->getString('postal_code', "");
        $profile=array();
        $profile['profile'] =array(
            'phone'=>$phone,
            'country'=>$country,
            'city'=>$city,
            'dob'=>$dob,
            'region'=>$region,
            'sector'=>$sector,
            'address1'=>$address1,
            'address2'=>$address2,
            'postal_code'=>$postal_code,
        );
        $us->setProperties($profile);
        //end of getting profile info
        if (!$us->save()) {
          $this->setMessage(JText::sprintf('COM_CATALOG_REGISTRATION_SAVE_FAILED', $us->getError()));
          $this->redirect();
          return false;
        }
        
        
        
        // Compile the notification mail values.
        $data = $us->getProperties();
        $config = new JConfig();
        $data['fromname'] = $config->fromname;
        $data['mailfrom'] = $config->mailfrom;
        $data['sitename'] = $config->sitename;
        $data['siteurl'] = JUri::root();
        

        $emailSubject = JText::sprintf('COM_CATALOG_EMAIL_ACCOUNT_DETAILS', $data['name'], $data['sitename']);
        $emailBody = JText::sprintf('COM_CATALOG_EMAIL_REGISTERED_BODY_PW', $data['name'], $data['sitename'], $data['siteurl'], $data['username'], $data['password_clear']);
        $mailer = JFactory::getMailer();
        $mailer->isHtml(true);
        $mailer->useSMTP($config->smtpauth, $config->smtphost,
                $config->smtpuser, $config->smtppass, $config->smtpsecure, $config->smtpport );

        // Send the registration email.
        $mailer->sendMail(
                $data['mailfrom'], $data['fromname'], 
                $data['email'], $emailSubject, $emailBody);
        $mailer->ClearAllRecipients();
        $this->setRedirect("");
        if ($isNew == true) {
          $dbo = new dbprovider();
          $query = "INSERT INTO `#__user_usergroup_map` (`user_id`, `group_id`) VALUES($us->id, $regis_group) ";
          $dbo->Query($query);
        }
        JFactory::getApplication()->enqueueMessage(JText::_('COM_CATALOG_ACCOUNT_CREATED'));
        return $us->id;
    }
    
    function generatePassword() 
    {
        $alpha = "abcdefghijklmnopqrstuvwxyz";
        $alpha_upper = strtoupper($alpha);
        $numeric = "0123456789";
        $chars = "";

        $chars = $alpha . $alpha_upper . $numeric;
        $length = 8;

        $len = strlen($chars);
        $pw = '';

        for ($i = 0; $i < $length; $i++)
          $pw .= substr($chars, rand(0, $len - 1), 1);

        $pw = str_shuffle($pw);
        return $pw;
   }
   
    function payulatam_confirmation()
    {
        $payu = new payulatam();
        $ApiKey=$payu->ApiKey;//llave de usuario de pruebas 2  6u39nqhq8ftd0hlvnjfs66eh8c
        $merchant_id=$_REQUEST['merchantId'];
        $referenceCode=$_REQUEST['referenceCode'];
        $TX_VALUE=$_REQUEST['TX_VALUE'];
        $New_value=number_format($TX_VALUE, 1, '.', '');
        //Se debe aproximar el valor siempre a un decimal.
        $currency=$_REQUEST['currency'];
        $transactionState=$_REQUEST['transactionState'];
        $firma_cadena= "$ApiKey~$merchant_id~$referenceCode~$New_value~$currency~$transactionState";
        $firmacreada = md5($firma_cadena);//firma que generaron ustedes
        $firma =$_REQUEST['signature'];//firma que envía nuestro sistema 
        
        $reference_pol=$_REQUEST['reference_pol'];
        $cus=$_REQUEST['cus'];
        $extra1=$_REQUEST['description'];
        $pseBank=$_REQUEST['pseBank'];
        $lapPaymentMethod=$_REQUEST['lapPaymentMethod'];
        $transactionId=$_REQUEST['transactionId'];
        if($_REQUEST['transactionState'] == 6 && $_REQUEST['polResponseCode'] == 5)
        {$estadoTx = "Transacci&oacute;n fallida";}
        else if($_REQUEST['transactionState'] == 6 && $_REQUEST['polResponseCode'] == 4)
        {$estadoTx = "Transacci&oacute;n rechazada";}
        else if($_REQUEST['transactionState'] == 12 && $_REQUEST['polResponseCode'] == 9994)
        {$estadoTx = "Pendiente, Por favor revisar si el d&eacute;bito fue realizado en el Banco";}
        else if($_REQUEST['transactionState'] == 4 && $_REQUEST['polResponseCode'] == 1)
        {$estadoTx = "Transacci&oacute;n aprobada";}
        else
        {$estadoTx=$_REQUEST['mensaje'];}
        if( $_REQUEST['transactionState'] == 4 && 
            $_REQUEST['polResponseCode'] == 1 && 
            strtoupper($firma)==strtoupper($firmacreada))
        {
            $products = array();
            $cant=array();
            $sale = new bll_sale($referenceCode);
            $details=bll_sale::getProductsFromSale($sale->Id);
            if(isset($details[0]))
                $products = $details[0];
            if(isset($details[1]))
                $cant=$details[1];
            $sale->SaleStateId=1;
            $sale->update();
            $total = $sale->Total;
            $shipping = new bll_shippingmethod($sale->ShippingMethodId);
            $this->setMessage(JText::_('COM_CATALOG_TWO_CHECKOUT_ORDER_COMPLETED'));
            $vars = http_build_query(array('total'=>($total-$shipping->Price),'p'=>$products, 'pc'=>$cant, 'pmid'=>$sale->PaymentMethodId, 'smid'=>$sale->ShippingMethodId, 'cid'=>$sale->CouponId, 'sid'=>$sale->Id));
            $this->setRedirect('/index.php?option=com_catalog&view=sales&layout=payment_success'.'&'.$vars);
        }
        else{
        
            $this->setMessage(JText::_('COM_CATALOG_TWO_CHECKOUT_CARD_NOT_PROCESSED'));
            $this->setRedirect("/index.php?option=com_catalog&view=sales&layout=sales");
            
        }
        $this->redirect();
    }
    
    function get_cart_summary()
    {
        $sess=JFactory::getApplication()->getSession();
        $productsid = $sess->get('products', array());
        $ptotal=0;
        $total=0;
        foreach($productsid as $id => $cant):
            $product = new bll_product($id);
            $total+=$product->SalePrice*$cant;
            $ptotal+=$cant;
        endforeach;
        ob_end_clean();
        ob_start();
        // Get the application object.  
        header('Content-type: application/json');
        echo json_encode(array($ptotal, AuxTools::MoneyFormat($total), $productsid));
        exit();
    }
}
