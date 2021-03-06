<?php
$id =0;
$app = JFactory::getApplication();
if(isset($_REQUEST['pid']))
{
    $id=$_REQUEST['pid'];
}
$product = new bll_product($id);
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$language = new languages($LangId);
$img = $product->getMainImage();
$images = $product->getImages();
$lval = $product->getLanguageValue($LangId);

$document = JFactory::getDocument();
$document->setTitle($lval->Name. ' - '.$app->get('sitename') );
$document->setDescription(strip_tags($lval->Description));
if(strlen($img->ImageUrl)> 4)
    $image = $img->ImageUrl;
else
    $image='./components/com_catalog/images/no-image-listing-detail.jpg';

$curr = bll_currencies::getActiveCurrency();
$document->addStyleSheet('./templates/marg/css/pikachoose/bottom.css');
$base_uri = JUri::base();
$current_uri  =  JUri::current();
$document->addHeadLink($current_uri,'canonical');
$ctags = '
<meta property="og:title" content="'.$lval->Name.'" />
<meta property="og:description" content="'.strip_tags($lval->Description).'" />
<meta property="og:image" content="'.$base_uri.DS.$image.'" />
<meta property="og:site_name" content="'.$base_uri.'" />
<meta property="og:url" content="'.$current_uri.'" />';
$document->addCustomTag($ctags);

?>
<script type="text/javascript" src="./pikachoose/lib/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="./pikachoose/lib/jquery.pikachoose.min.js"></script>
<script type="text/javascript" src="./pikachoose/lib/jquery.touchwipe.min.js"></script>
<script language="javascript">
        jQuery(document).ready(function (){
                jQuery("#pikame").PikaChoose({carousel:true,transition:[0]});
        });
</script>
<script type="text/javascript">
jQuery(function() 
{
    jQuery( "#dialog" ).dialog({
      autoOpen: false,
      modal:true,
      width: 'auto',
      position:{ my: "center", at: "center", of: jQuery('body') },
      show: {
        effect: "blind",
        duration: 500
      },
      hide: {
        effect: "blind",
        duration: 500
      }
    });
});
            
function addproduct(pid)
{
    
    var cant=jQuery("#quantity").val();
    jQuery.ajax({
        url:"/<?php echo $language->sef; ?>?option=com_catalog&task=setproduct&format=json", 
        data:{ pid: pid, cant: jQuery("#quantity").val() },
        dataType:'json'
        }
    ).done(function( data, textStatus, jqXHR) {
            var c=data;
            
            <?php 
            if($product->have_offer_price()==true)
            {
                ?>
                var price = "<?php echo $product->OfferPrice; ?>";
                <?php
            }
            else 
            {
                ?>
                var price = "<?php echo $product->SalePrice; ?>";
                <?php
            }
            ?>
            
            
            var curr = "<?php echo $curr->CurrCode; ?>";
            var curr_rate=<?php echo $curr->Rate ?>;
            var str=cant+" x "+curr+((cant*price)*curr_rate);
            jQuery("#cantelem").html("");
            jQuery("#cantelem").html(str);
            var html= c[0] +" <?php echo JText::_('COM_CATALOG_CART_ARTICLES_FOR')." " ?>"+c[1];
            jQuery( "#dialog" ).dialog( "open" );
            jQuery( "#cart_detail_str" ).html(html);
            jQuery('#dialog-product-detail').html(jQuery('#list-product-detail-'+pid).html()); 
            return true;
      }
    ).fail(function( jqXHR, textStatus, errorThrown ) {
        alert(textStatus+", Error:"+errorThrown);
    });
    return false;
}
</script>
<div id="dialog" title="<?php echo JText::_('COM_CATALOG_PRODUCT_ADDED') ?>">
    <div id="dialog-product-detail">
        
    </div>
    <a class="button" onclick="$( '#dialog' ).dialog( 'close' );"><?php echo JText::_('COM_CATALOG_KEEP_BUYING') ?></a>
    <a class="button" href="<?php echo JRoute::_('index.php?option=com_catalog&view=sales'); ?>"><?php echo JText::_('COM_CATALOG_CHECKOUT') ?></a>
</div>
 <div style="display:none;" id="list-product-detail-<?php echo $product->Id ?>">
     <img class="span4" id="image-popup" src="<?php echo $image; ?>" />
     <p><?php echo $product->getLanguageValue($LangId)->Name; ?></p>
     <p id="cantelem"></p>
 </div>
<div class="span12 row-fluid">
	<h4 class="span12">
            <i class="fa fa-heart fa-rotate-270"></i>
            <span>
    <?php 
    echo $lval->Name;
    ?></span>
            <i class="fa fa-heart fa-rotate-90"></i>
    </h4>
    <div class="span5">
        <?php 
        if(count($images) <= 0)
        {
          ?>
          <img class="lis-image" src="<?php echo $image; ?>" />    
          <?php  
        }
        else
        {
        ?>
        <ul id="pikame" class="jcarousel-skin-pika">
        <?php foreach($images as $imgobj):
            if(strlen($imgobj->ImageUrl)> 4)
            {
                $imar = $imgobj->ImageUrl;
            }
            else
            {
                $imar='./components/com_catalog/images/no-image-listing-detail.jpg';
            }
            ?>
            <li><a><img class="lis-image" src="<?php echo $imar ?>" /></a><span></span></li>
        <?php endforeach; ?>
        </ul>
        <?php
        }
        ?>
    </div>
    <div class="span6">
        <div class="description span12">
            <p>
            <?php 
            echo $lval->Description;
            ?>
            </p>
        </div>
        <div class="span12">
                <input id="quantity" class="number_qty" value="1" type="hidden" name="quantity" min="1" max="15">
        
        <hr/>        
        <div class="price">
                <?php 
                    $sale_price=AuxTools::MoneyFormat($product->SalePrice, $curr->CurrCode, $curr->Rate);
                    if($product->have_offer_price()==true)
                    {
                        $offer_price=AuxTools::MoneyFormat($product->OfferPrice, $curr->CurrCode, $curr->Rate);
                        $percent = number_format( ($product->OfferPrice / ($product->SalePrice) ) * 100, 2);
                        ?>
                            <div class="smaller-text"><span class="line-through "><?php echo $sale_price; ?></span></div> 
                            <div class="red"><?php echo $offer_price; ?></div> 
                            <div class="red smaller-text"><?php echo JText::_('COM_CATALOG_YOU_SAVE').": ".AuxTools::MoneyFormat($product->SalePrice-$product->OfferPrice, $curr->CurrCode, $curr->Rate)."($percent%)"; ?></div>
                        <?php
                    }
                    else
                    {
                        ?>
                            <span class="red"><?php echo $sale_price; ?></span>
                        
                        <?php
                    }
                    ?>
        </div>
        <?php if(count(bll_product::check_product_sales($product->Id)) <= 0): ?>
        <button type="button" id="order" href="#" onclick="return addproduct(<?php echo $product->Id; ?>);">
                <?php echo JText::_('COM_CATALOG_ORDER') ?><i class="fa fa-shopping-cart fa-3x"></i>
        </button>
        <?php endif; ?>
        </div>
    </div>
    <hr>
    <div class="span12 note">
            <?php 
            echo $lval->Note;
            ?>
    </div>
</div>