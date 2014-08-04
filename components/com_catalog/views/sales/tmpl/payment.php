<?php
$user= JFactory::getUser();
$profile=JUserHelper::getProfile($user->id)->getProperties();
$guest_disable = '';
$jspath = AuxTools::getJSPathFromPHPDir(JPATH_ROOT);

if($user->guest == false && $user->id > 0)
    $guest_disable = 'disabled';

if(isset($profile['profile']))
{
    $profile=$profile['profile'];
}
else {
    $profile=array('city'=>'','address1'=>'','address2'=>'',
        'country'=>'','phone'=>'', 'postal_code'=>'', 'dob'=>'', 'region'=>'', 'website'=>'');
}
$input = JFactory::getApplication()->input->getArray();
$productsid=array();
$productscant = array();
$total=0;
$cid=null;

if(isset($input['p']))
    $productsid=$input['p'];

if(isset($input['pc']))
    $productscant=$input['pc'];

if(isset($input['total']))
    $total=$input['total'];

if(isset($input['cid']))
    $cid=$input['cid'];

if(isset($input['name']))
    $user->name=$input['name'];

if(isset($input['username']))
    $user->username=$input['username'];

if(isset($input['email']))
    $user->email=$input['email'];

if(isset($input['city']))
    $profile['city']=$input['city'];

if(isset($input['address1']))
    $profile['address1']=$input['address1'];

if(isset($input['address2']))
    $profile['address2']=$input['address2'];

if(isset($input['country']))
    $profile['country']=$input['country'];

if(isset($input['region']))
    $profile['region']=$input['region'];

if(isset($input['phone']))
    $profile["phone"]=$input['phone'];

if(isset($input['postal_code']))
    $profile["postal_code"]=$input['postal_code'];
if(isset($input['dob']))
    $profile['dob']=$input['dob'];


$curr = bll_currencies::getActiveCurrency();

$cangoon=false;
for($i=0; $i< count($productsid); $i++):
    $temp_pro = new bll_product($productsid[$i]);
    if($productscant[$i] > 0 && $temp_pro->Id > 0)
    {
        $cangoon=true;
        break;
    }
endfor;

if($cangoon == false)
{
    JFactory::getApplication()->enqueueMessage('No hay ningun elemento que comprar.');
    JFactory::getApplication()->redirect('/');
}
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$city = new cities(0);
$s = new bll_shippingmethod(0);
$cities=$city->findAll(null,null,false,'Name');
$smethods = $s->findAll(null,null,false);
$global_shipping_methods = $s->findAll('Global', 1);
$arr=array();
foreach($cities as $c)
{
    $arr[$c->Id]=bll_shippingmethod::getMethodsFromCityId($c->Id);
}
$payment_methods = new bll_paymentmethod(0);
$payment_methods= $payment_methods->findAll('Enable', 1);
$coupon = new catalogcoupon($cid);
$reduce=0;
if($coupon->Id > 0)
{
    $reduce = $total*($coupon->Discount/100);
}
$country = new country(0);
$countries = $country->findAll(null,null,false,'Name');
$locations= country::getLocationTree();

?>
<?php if($user->guest): ?>
    <div id="dialog-login" title="<?php echo JText::_('COM_CATALOG_LOGIN'); ?>">

    </div>
<?php endif; ?>
<style>
    form ul li{
        list-style: none;
    }
</style>
<script>
    jQuery(document).ready(function(){
      <?php  if($user->guest)
        {
        ?>
        jQuery.ajax(
            {url:'/index.php?option=com_users&view=login&login_redirect_uri=<?php echo urlencode('/index.php?option=com_catalog&view=sales&redirect=1&cid='.$cid);?>&tmpl=component'}
          ).done(function(data){
              var str=data;
              var arr=str.split("</head>");
              var html = arr[1];
              jQuery("#dialog-login").html(html);
          });
        jQuery( "#dialog-login" ).dialog({
                  autoOpen: false,
                  modal:true,
                  width: 'auto',
                  position:{ my: "center", at: "center", of: jQuery('body') },
                  show: {
                    effect: "explode",
                    duration: 500
                  },
                  hide: {
                    effect: "explode",
                    duration: 500
                  }
                });

                jQuery( "#opener-login" ).click(function() {
                  jQuery( "#dialog-login" ).dialog( "open" );
                 });    
        <?php
    }
    ?>
    });
</script>
<form id="payment-form" name="payment-form" method="POST" action="<?php echo JRoute::_('index.php?option=com_catalog&task=checkout'); ?>">
<div id="accordion">
    <h3>1. <?php echo JText::_('COM_CATALOG_USER_INFORMATION'); ?></h3>
    <div>
        <fieldset>
	<div class="left_module_login">
            <?php if($user->guest): ?>
            
            <h3><?php echo JText::_('COM_CATALOG_HAVE_ACCOUNT_LOGIN'); ?></h3>
            <p>
                <?php echo JText::_('COM_CATALOG_LOGIN_CLICK_HERE'); ?>: <button id="opener-login" type="button"><?php echo JText::_('COM_CATALOG_LOGIN'); ?></button>
            </p>
            <?php endif; ?>
	</div>        
<script>
  var cities_method = <?php echo json_encode($arr, JSON_UNESCAPED_UNICODE); ?>;
  var smethods=<?php echo json_encode($smethods, JSON_UNESCAPED_UNICODE); ?>;
  var location_tree= <?php echo json_encode($locations, JSON_UNESCAPED_UNICODE); ?>;
  var globas_smethods  = <?php echo json_encode($global_shipping_methods, JSON_UNESCAPED_UNICODE); ?>;
  jQuery(function() {

    jQuery( "#accordion" ).accordion({ disabled: true });

    <?php 
    if($user->guest!=true && $user->id > 0)
    {
        echo "jQuery('#accordion').accordion({active:1});";
    }
    ?>
    jQuery('#submit').click(function() {

        if( validateForm()===true)
            {
                jQuery('form[name="payment-form"]').trigger("submit");
                return true;
            }
        else 
            return false;
    });
    
    
          jQuery( "#datepicker" ).datepicker(
            {
                dateFormat:"yy-mm-dd",
                showOn: "both",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:-19" // this is the option you're looking for

            }
          );

  });

  function validateForm(until)
  {

      var form = jQuery('#payment-form')[0];
        var fsetinit=0;
        var active_tab =0;
        var isvalid=true;

        for(var index=fsetinit; index<form.length; index++)
            {

                var field=form[index];
                if(field.tagName.toLowerCase() === "fieldset")
                    active_tab++;

                if(until!==null)
                    if(active_tab > until)
                        break;

                if(!field.validity.valid)
                    {
                       isvalid=false;
                       jQuery('#accordion').accordion({active:active_tab-1});
                       field.focus();
                       return false;
                    }
            }
       if(isvalid === true)
       {
          if(validateEmail(jQuery('#email').val()) ===false)
          {
            alert('<?php echo JText::_('COM_CATALOG_INVALID_EMAIL') ?>');
            jQuery('#accordion').accordion({active:0});
            jQuery('#email').focus();
            return false;
          }
          if(active_tab === 1)
              {
          if(jQuery('#city').val() === "")
              {
                  jQuery('#accordion').accordion({active:1});
                  jQuery('#city').focus();
                  return false;
              }
          if(jQuery('#country').val() === "")
              {
                  jQuery('#accordion').accordion({active:1});
                  jQuery('#country').focus();
                  return false;
              }

          if(jQuery('#region').val() === "")
              {
                  jQuery('#accordion').accordion({active:1});
                  jQuery('#region').focus();
                  return false;
              }
        }
       }
       return true;
  }
  
  function validateEmail(email) { 
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    } 
  
  function changeMethods(val)
  {
      var countries = location_tree;
      var cid = jQuery('#country').val();
      var pid = jQuery('#region').val();
      var provinces=[];
      var cities=[];
      var sectors=[];

      for(var i=0; i < countries.length; i++)
          {
              var c = countries[i];
              if(c.id === cid)
              {
                  provinces = c.provinces;
                  break;
              }
          }
     for(var i=0; i < provinces.length; i++)
          {
              var p = provinces[i];
              if(p.id === pid)
              {
                  cities = p.cities;
                  break;
              }
          }
      for(var k in cities_method)
      {
          if(val === k)
            {
                var arr=cities_method[k];
                for(var sm in smethods)
                    {
                        var av = false;
                        var elem=smethods[sm];
                        var context=jQuery('#sm_'+elem.Id);
                        
                        if(elem.Global === '1')
                        {
                           av = true;   
                        }    
                        else
                        {
                            for(var n in arr)
                            {
                                if(arr[n] === elem.Id)
                                    {
                                        av=true;
                                        break;
                                    }
                            }
                        }
                        if(av ===true)
                            context[0].disabled=false;
                        else
                            {
                                context[0].disabled=true;
                                context[0].checked=false;
                            }
                    }
               return true;
            }
      }
    for(var sm in smethods)
            {
                var elem=smethods[sm];
                var context=jQuery('#sm_'+elem.Id);
                var input =context[0]; 
                if(input !== undefined)
                {
                    if(elem.Global !== '1')
                    {
                        input.disabled=true;
                        input.checked=false;
                    }
                    else
                    {
                        input.disabled=false;
                    }
                }
            }
  }
 
  function shipping()
  {

      var radios = document.getElementsByName('smid');
      var cur = '<?php echo $curr->CurrCode ?>';
      var sprice=0;
      for(var i=0;  i<radios.length; i++)
          {

             var radio = radios[i];
             var found=false;
             if(radio.checked === true)
                 {
             for(var index in smethods)
                 {

                     var elem=smethods[index];
                     if(elem.Id == radio.value)
                         {
                             sprice = parseInt(elem.Price);
                             found=true;
                             break;
                         }
                 }
             if(found === true)
                 break;
                 }          
          }
          var total =parseInt(jQuery('#itotal').val());
          var totalstr=(sprice+total);
          jQuery('#shi-total').html(cur+sprice);
          jQuery('#total').html(cur+totalstr);
  }
  
  function changeProvinces(cid)
  {
      var countries = location_tree;
      var provinces;
      for(var i=0; i < countries.length; i++)
          {
              var c = countries[i];
              if(c.id === cid)
              {
                  provinces = c.provinces;
                  break;
              }
          }
          jQuery('#sector').html('<option></option>');
          jQuery('#city').html('<option></option>');
          jQuery('#city').val(null);
          changeMethods(jQuery('#city').val());
          var prohtml="<option></option>";
      if(provinces !== undefined)
          {
              for(var i=0; i < provinces.length; i++)
                  {
                      prohtml+="<option value=\""+provinces[i].id+"\">"+provinces[i].name+"</option>";
                  }
          }
          jQuery('#region').html(prohtml);
  }
  
  function changeCities(pid)
  {
      var countries = location_tree;
      var cid = jQuery('#country').val();
      var provinces=[];
      var cities=[];
      for(var i=0; i < countries.length; i++)
          {
              var c = countries[i];
              if(c.id === cid)
              {
                  provinces = c.provinces;
                  break;
              }
          }
     for(var i=0; i < provinces.length; i++)
          {
              var p = provinces[i];
              if(p.id === pid)
              {
                  cities = p.cities;
                  break;
              }
          }
          jQuery('#city').val(null);
          var cihtml="<option></option>";
          changeMethods(jQuery('#city').val());
      for(var i=0; i < cities.length; i++)
          {
              cihtml+="<option value=\""+cities[i].id+"\">"+cities[i].name+"</option>";
          }
          jQuery('#city').html(cihtml);
  }

  function f_continue()
  {
      var at=jQuery('#accordion').accordion("option",'active');
      if(at === undefined)
          at=0;
      if( validateForm(at)===true)
          {
              jQuery('#accordion').accordion({active:jQuery( '#accordion' ).accordion( 'option', 'active' )+1});
          }
  }
</script>
	<div class="right_module_login">
	<h3><?php echo JText::_('COM_CATALOG_NEW_USER_REGISTER'); ?></h3>
	<ul class="registro_field">
	<li>
            <label><?php echo JText::_('COM_CATALOG_USERNAME'); ?> *</label>
            <input required="required" type="text" name="username" <?php echo $guest_disable ?> value="<?php echo $user->username ?>" />
        </li>
        <li>
        <label><?php echo JText::_('COM_CATALOG_NAME'); ?> *</label>
        <input required="required" type="text" name="name" <?php echo $guest_disable ?> value="<?php echo $user->name ?>" />
        </li>
		<li>
        <label><?php echo JText::_('COM_CATALOG_EMAIL'); ?> *</label>
        <input required="required" id="email" type="text" name="email" <?php echo $guest_disable ?>  value="<?php echo $user->email ?>" />
        </li>
		<li>
        <label><?php echo JText::_('COM_CATALOG_PHONE'); ?> *</label>
        <input required="required" type="text" name="phone" value="<?php echo $profile['phone'] ?>" />       
        </li>
		<li>
        <label><?php echo JText::_('COM_CATALOG_BIRTHDAY_DATE'); ?></label>
            
                <input id="datepicker" type="text" name="dob" value="<?php echo $profile['dob'] ?>" />       
            
                </li>
		<li>
        <button type="button" onclick="return f_continue();">
            <?php echo JText::_('COM_CATALOG_NEXT'); ?>
        </button>
		</li>
		</ul>
		
		</div>
        </fieldset>
    </div>
    <h3>2. <?php echo JText::_('COM_CATALOG_SHIPPING_INFORMATION'); ?></h3>
    <div>
        <fieldset>
		<div class="left_module_register">
			<ul class="registro_field">
				<li>
        <label><?php echo JText::_('COM_CATALOG_COUNTRY'); ?> *</label>
        <select id="country" name="country" onchange="changeProvinces(this.value)">
            <option></option>
            <?php 
            $selcoun="";
            foreach($countries as $coun)
            {

                $comparisor = "";
                if($profile['country'] != "")
                    $comparisor = $profile['country'];
                
                if($coun->Name == $comparisor)
                {
                    $selcoun=$coun->Name;
                    echo '<option value="'.$coun->Id.'" selected>'.$coun->Name.'</option>';  
                    $coun_script=true;
                }
                else
                    echo '<option value="'.$coun->Id.'" >'.$coun->Name.'</option>';  
           

            }
            ?>
        </select>
        <?php 

            $country = $country->find('Name', $selcoun);
            $province = new province(0);
            $provinces=array();
            if($country->Id > 0)
            {
                $provinces = $province->findAll('CountryId', $country->Id);
            }
        ?>
		</li>
		<li>
        <label><?php echo JText::_('COM_CATALOG_REGION'); ?> *</label>
        <select id="region" name="region" onchange="changeCities(this.value)">
            <option></option>
            <?php 
            $selpro="";
            foreach($provinces as $obj)
            {
                $comparisor = "";
                if($profile['region'] != "")
                    $comparisor = $profile['region'];
                
                if($obj->Name == $comparisor)
                {
                    $selpro=$obj->Name;
                    echo '<option value="'.$obj->Id.'" selected>'.$obj->Name.'</option>';  
                    $pro_script=true;
                }
                else
                    echo '<option value="'.$obj->Id.'" >'.$obj->Name.'</option>';  
            
            }
            ?>
        </select>
        <?php 
            $province = $province->find('Name', $selpro);
            $city = new cities(0);
            $cities=array();
            if($province->Id > 0)
            {
                $cities = $city->findAll('ProvinceId', $province->Id);
            }
        ?>
		</li>
		<li>

        <label><?php echo JText::_('COM_CATALOG_CITY'); ?> *</label>

        <select id="city" name="city" onchange="changeMethods(this.value)">

            <option></option>

            <?php 

            $script=false;
            $selcity=new cities(0);
            foreach($cities as $city)
            {
                $comparisor = "";
                if($profile['city'] != "")
                    $comparisor = $profile['city'];
               
                if($city->Name == $comparisor)
                {
                    $selcity=$city;
                    echo '<option value="'.$city->Id.'" selected>'.$city->Name.'</option>';  
                    $script=true;
                }
                else
                    echo '<option value="'.$city->Id.'" >'.$city->Name.'</option>';  
           
            }
            
            ?>
        </select>
        <?php 
            if($script === true)
            {
                echo '<script>
                    jQuery(function() {
                        changeMethods("'.$selcity->Id.'");
                     });
                </script>';
            }
            else
            {
                echo '<script>
                    jQuery(function() {
                        changeMethods("0");
                     });
                </script>';
            }
        ?>
		</li>
		<li>
        <label><?php echo JText::_('COM_CATALOG_ADDRESS'); ?> *</label>
        <input required="required" type="text" name="address1" value="<?php echo $profile['address1'] ?>" />
		</li>

		<li>

        <label><?php echo JText::_('COM_CATALOG_ADDRESS'); ?> 2</label>
        <input type="text" name="address2" value="<?php echo $profile['address2'] ?>" />
		</li>

		<li>

        <label><?php echo JText::_('COM_CATALOG_POSTAL_CODE'); ?></label>

        <input type="text" name="postal_code" value="<?php echo $profile['postal_code'] ?>" />        

		</li>

		<li>

        <label><?php echo JText::_('COM_CATALOG_SHIPPING_METHODS'); ?> *</label>

        <div class='shipping-method-holder'>

            <?php 

            foreach($smethods as $shipping)
            {

                echo '<input disabled="disabled" required="required" onchange="shipping()" id="sm_'.$shipping->Id.'" type="radio" name="smid" value="'.$shipping->Id.'" /> <label>'.$shipping->getLanguageValue($LangId)->Name.''.AuxTools::MoneyFormat($shipping->Price,$curr->CurrCode, $curr->Rate).''.'</label><div class="desc_ship">'.$shipping->getLanguageValue($LangId)->Description.'</div><hr/>';

            }

            ?>

        </div>

		

		</li>

		<li>

        <button class="back" type="button" onclick="jQuery('#accordion').accordion({active:jQuery( '#accordion' ).accordion( 'option', 'active' )-1});">

            <?php echo JText::_('COM_CATALOG_PREVIOUS'); ?>

        </button>

        <button type="button" onclick="return f_continue();">

            <?php echo JText::_('COM_CATALOG_NEXT'); ?>

        </button>

		

		</li>

		</ul>

		</div>

		<div class="right_module_register">

			<img src="/images/bg_free_people.jpg" />

		</div>

        </fieldset>

    </div>

    <h3>3. <?php echo JText::_('COM_CATALOG_CONFIRMATION'); ?></h3>

    <div>

        <fieldset>

        <input type="hidden" name="cid" value="<?php echo $cid; ?>" />

        <input type="hidden" id="itotal" name="total" value="<?php echo $total-$reduce; ?>" />
		<div class="left_module_register">

		<table class="confirmation_payment">

			<tr>

			<th width="40%"><p><?php echo JText::_('COM_CATALOG_PRODUCTS'); ?></p></th>
			<th><p><?php echo JText::_('COM_CATALOG_QUANTITY'); ?></p></th>
			<th><p><?php echo JText::_('COM_CATALOG_PRICE'); ?></p></th>
			<th><p><?php echo JText::_('COM_CATALOG_TOTAL'); ?></p></th>

			</tr>

			

					<?php 

			        for($i=0; $i< count($productsid); $i++):

			            $product = new bll_product($productsid[$i]);

                                    if($productscant[$i] <= 0)

                                        continue;

			            $preduce=0;

			            if($coupon->Id)

			                $preduce = $product->SalePrice*($coupon->Discount/100);

			            $ptotal = AuxTools::MoneyFormat(($product->SalePrice-$preduce)*$productscant[$i], $curr->CurrCode, $curr->Rate);

						$punit = AuxTools::MoneyFormat($product->SalePrice-$preduce, $curr->CurrCode, $curr->Rate);

			            ?>

						<tr>

							<td>

			            <input type="hidden" name="p[]" value="<?php echo $productsid[$i]; ?>" />

			            <input type="hidden" name="pc[]" value="<?php echo $productscant[$i]; ?>" />



			            <p class="title_plan">

			                <?php echo $product->getLanguageValue($LangId)->Name; ?>

			            </p>

						</td>

						<td><?php echo $productscant[$i]; ?></td>

						<td class="price"><?php echo $punit ?></td>

						<td class="price"><?php echo $ptotal ?></td>

					</tr>

			            <?php

						

			        endfor;

			        ?>

			<tr>

				<td></td>

				<td></td>

				<td class="grey"><p><?php echo JText::_('COM_CATALOG_SUB_TOTAL'); ?>:</p></td>

				<td class="grey price"><span id="sub-total"><?php echo AuxTools::MoneyFormat($total-$reduce, $curr->CurrCode, $curr->Rate); ?></span></td>

			</tr>

			

			<tr>

				<td></td>

				<td></td>

				<td class="white"><p><?php echo JText::_('COM_CATALOG_SHIPPING'); ?>:</p></td>

				<td class="white price"><span id="shi-total"><?php echo AuxTools::MoneyFormat(0, $curr->CurrCode, $curr->Rate); ?></span></td>

			</tr>

			

			<tr>

				<td></td>

				<td></td>

				<td class="grey"><p><?php echo JText::_('COM_CATALOG_TOTAL'); ?>:</p></td>

				<td class="grey price"><span id="total"><?php echo AuxTools::MoneyFormat($total-$reduce, $curr->CurrCode, $curr->Rate); ?></span></td>

			</tr>

			<tr>

				<td colspan="4">

					<h3 class="page-header_border left_align"><i class="fa fa-heart fa-rotate-270"></i><span>

					<?php echo JText::_('COM_CATALOG_PAYMENT_METHOD'); ?></span><i class="fa fa-heart fa-rotate-90"></i>

					</h3>

		                

		                <?php 



		                foreach($payment_methods as $pm)

		                {

		                    echo "<input name=\"pmid\" type=\"radio\" value=\"$pm->Id\" required=\"required\"/>

		                         <label>".$pm->getLanguageValue($LangId)->Name.'<div class="desc_payment">'.$pm->getLanguageValue($LangId)->Description.

                                        "</div></label><hr/>";

		                }

		                ?>

		               

		             

				</td>

			</tr>

			

			<tr>

				<td></td>

				<td></td>

				<td colspan="2">

					 <input type="hidden" name="internal_sale_fail_redirect" 

		                       value="/index.php?option=com_catalog&view=sales&layout=payment" />

		                <input type="hidden" name="sale_success_redirect" 

		                       value="<?php echo JRoute::_('/index.php?option=com_catalog&view=sales&layout=payment_success'); ?>" />

		                <input type="hidden" name="user_creation_fail_redirect" 

		                       value="/index.php?option=com_catalog&view=sales&layout=payment" />

		            <button class="back" type="button" onclick="jQuery('#accordion').accordion({active:jQuery( '#accordion' ).accordion( 'option', 'active' )-1});">

		                <?php echo JText::_('COM_CATALOG_PREVIOUS'); ?>
		            </button>
                            <button type="submit" id="submit" >
                                <?php echo JText::_('COM_CATALOG_BUY'); ?>
                            </button>
				</td>

			</tr>

		</table>

                </div>

		<div class="right_module_register">

			<img src="/images/carrito.jpg" />
		</div>
            </fieldset>

    </div>

</div>

</form>

