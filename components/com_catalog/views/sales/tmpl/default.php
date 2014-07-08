<?php
$jspath = AuxTools::getJSPathFromPHPDir(BASE_DIR); 
/**
 * @version		$Id: default.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$ro=JFactory::getApplication()->getPathway();
$path = "";
foreach($ro->getPathwayNames() as $p)
{
    $path.=DS.AuxTools::SEFReady($p);
}
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$category = new bll_category(0);
$root_categories = bll_category::getRootCategories();

$sess=JFactory::getApplication()->getSession();
$productsid = $sess->get('products', array());

$cid="";
if(isset($_GET['cid']))
{
    $coupon = new catalogcoupon($_GET['cid']);
    if($coupon->Id > 0)
    {
        $sales = $coupon->findAll('CouponId', $coupon->Id);
        $uses = count($sales);
        $dttz = new DateTimeZone(LIB_TIMEZONE);
        $currdate = new DateTime('now', $dttz);
        $finishdate = new DateTime($coupon->Date, $dttz);
        if($coupon->Enable==0 || $uses >= $coupon->Uses  || 
                ($currdate >= $finishdate)
          )
        {
            $cid="";
        }
        else
        {
            $cid = $coupon->Id;
        }
    }
}


?>

<script>
  $(function() {
      var min = 0;
      var max = 1000;
    $( ".spinner" ).spinner({
      spin: function( event, ui ) {
        if ( ui.value > max ) {
          $( this ).spinner( "value", min );
            return false;
        } else if ( ui.value < min ) {
          $( this ).spinner( "value", max );
            return false;
        }
      },
      stop: function(event,ui)
      {
            var prices = document.getElementsByName('price[]');
            var cant = document.getElementsByName('pc[]');
            var cur = '<?php echo DEFAULT_CURRENCY ?>';
            var total=0;
            for(var i =0; i<prices.length;i++)
            {
                var ids = prices[i].id.split('_');
                if(ids.length > 0)
                var id = ids[ids.length-1];
                var ptotal = prices[i].value*cant[i].value;
                total+=ptotal;
                $('#total_'+id).html(cur+ptotal);
            }
            $('#total').html(cur+total);
            $('#totalinput').val(total);
            var Discount = parseInt($('#cdis').val());
            if(Discount > 0)
                {
                    var total=parseFloat($('#totalinput').val());
                    $('#tdis').html('<?php echo JText::_('COM_CATALOG_TOTAL_DISCOUNT'); ?>('+Discount+'%):');
                    var tdis=(total-(total*(Discount/100)));
                    var h =cur+tdis;
                    $('#totaldis').html(h);
                }
            return false;
      }
    });
    <?php 
    if(isset($_GET['redirect']))
        echo "$(\"#cart\").submit();";
    ?>
  });
  
</script>
<div class="product-list">
<h3><i class="fa fa-heart fa-rotate-270"></i><span><?php echo JText::_('COM_CATALOG_CART_DETAIL'); ?></span><i class="fa fa-heart fa-rotate-90"></i>
</h3>
<div class="label_cart">
    <div class="span5"></div>
    <div class="span2"><?php echo JText::_('COM_CATALOG_PRICE'); ?></div>
    <div class="span2"><?php echo JText::_('COM_CATALOG_QUANTITY'); ?></div>
    <div class="span3">Total</div>
</div>
<div class="content_cart">
    <form method="POST" id="cart" action="<?php echo JRoute::_('index.php?option=com_catalog&view=sales&layout=payment');?>">
    <?php
    $total=0;
    foreach($productsid as $pid => $cant):
        $p = new bll_product($pid);
        $ptotal=0;
        if($p->Id > 0)
        {
            $lval = $p->getLanguageValue($LangId);
            $price=$p->SalePrice;
            $ptotal+= $p->SalePrice*$cant;
            ?>
            <div class="content_cart">
             <div class="span5">
	
				 <?php 
			        $img = $p->getMainImage();
			        $lval = $p->getLanguageValue($LangId);
			        if(strlen($img->ImageUrl)> 4)
			            $image = $img->ImageThumb;
			        else
			            $image='./components/com_catalog/images/no-image-product.jpg';
			        ?>
				 <img src="<?php echo $image; ?>" />
                 <h4><?php echo $lval->Name; ?></h4>
                 <input type="hidden" name="p[]" value="<?php echo $p->Id ?>" />
                 <input type="hidden" id="price_<?php echo $p->Id ?>" name="price[]" value="<?php echo $p->SalePrice; ?>" />
             </div>
             <div class="span2 price"><?php echo AuxTools::MoneyFormat($price); ?></div>
             <div class="span2">
               <input id="p_<?php echo $p->Id ?>" class="spinner" name="pc[]" value="<?php echo $cant; ?>">
            
            <button type="button" onclick="return addproducts(null);" >
                <i class="fa fa-refresh fa-2x"></i>
            </button>
            <button type="button" onclick="return addproducts('<?php echo $p->Id ?>');" >
                <i class="fa fa-trash-o fa-2x"></i>
            </button>
             </div>
             <div id="total_<?php echo $p->Id ?>" class="span2 price"><?php echo AuxTools::MoneyFormat($ptotal); ?></div>
            </div>
            <?php
            $total+=$ptotal;
        }
    endforeach;
    ?>
        <div class="span_total">
            <div class="right">
		    <div class="coupon_code">
	                <label><?php echo JText::_('COM_CATALOG_COUPON_CODE'); ?></label>
	                <input type="text" id="check" name="_c_c_c" value="" />
	                <button class="btn_blue" type="button" onclick="checkcoupon()">
	                    <?php echo JText::_('COM_CATALOG_CHECK'); ?>
	                </button>
	            </div>
                <div class="total_all">
                    <p><?php echo JText::_('COM_CATALOG_TOTAL'); ?>:
                    <span id="total"><?php echo AuxTools::MoneyFormat($total); ?></span>
                    </p>
                </div>
                <div class="total_all2">
                    <p>
                        <span id="tdis"></span>
                        <span id="totaldis"></span>
                    </p>
                </div>
                <input type="hidden" id="cid" name="cid" value="<?php echo $cid ?>" />
                <input type="hidden" id="totalinput" name="total" value="<?php echo $total ?>" />
                <input type="hidden" id="cdis" name="cdis" value="" />
                
                <button type="submit" class="buy_it">
                    Procesar compra<i class="fa fa-shopping-cart fa-3x"></i>
                </button>
            </div>
            
        </div>
        
    </form>
</div>    
<script type="text/javascript">
function addproducts(pid)
{
    var cant = document.getElementsByName('pc[]');
    var p = document.getElementsByName('p[]');
    var products=[];
    var q=[];
    for(var i =0; i<cant.length;i++)
    {
        
        if(p[i].value === pid)
            {
                products.push(p[i].value);
                q.push(0);
            }
            else
                {
                    products.push(p[i].value);
                    q.push(cant[i].value);
                }
    }
    
    $.ajax({ 
        url:"index.php?option=com_catalog&task=setproducts", 
        data:{pid:products, cant:q}
        }).done(function( data ) {
        window.location="index.php?option=com_catalog&view=sales";
        return false;
      });
      
    return false;
}

function checkcoupon()
{
    var x = jQuery('#check').val();
    jQuery.ajax({ 
        url:"/index.php?option=com_catalog&task=coupon&format=json", 
        data:{str:x},
        dataType:'json'
        }).done(function( data ) {
        var c=data;
        if(c===undefined || c[0] <= 0)
            {
                alert('<?php echo JText::_('COM_CATALOG_COUPON_UNAVAILABLE')?>');
            }
            else
                {
                    alert('<?php echo JText::_('COM_CATALOG_COUPON_AVAILABLE')?>');
                    jQuery('#cid').val(c.Id);
                    jQuery('#cdis').val(c.Discount);
                    var total=parseFloat($('#totalinput').val());
                    var cur="<?php echo DEFAULT_CURRENCY ?>";
                    jQuery('#tdis').html('<?php echo JText::_('COM_CATALOG_TOTAL_DISCOUNT'); ?>('+c.Discount+'%):');
                    var tdis=(total-(total*(c.Discount/100)));
                    var h =cur+tdis;
                    jQuery('#totaldis').html(h);
                }
        return false;
      });
}
</script>
   </div>     