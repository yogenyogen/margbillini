<?php 
$LangId=  AuxTools::GetCurrentLanguageIDJoomla();
$products  = bll_product::getProductsByCategory($categoryid);

?>
<script type="text/javascript">
jQuery(function() 
{
    jQuery( "#dialog" ).dialog({
      autoOpen: false,
      modal:true,
      width: 'auto',
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
</script>

<div id="dialog" title="El producto fue anadido correctamente">
    <div id="dialog-product-detail">
        
    </div>
    <a class="button" onclick="jQuery( '#dialog' ).dialog( 'close' );">Seguir comprando</a>
    <a class="button" href="index.php?option=com_catalog&view=sales">Ir al carrito</a>
</div>
<div class="products-holder">
    <?php foreach($products as $product): 
        $img = $product->getMainImage();
        $lval = $product->getLanguageValue($LangId);
        if(strlen($img->ImageThumb)> 4)
                    $image = $img->ImageThumb;
                else
                    $image='./components/com_catalog/images/no-image-listing.jpg';
        ?>
    <div class="product_holder span4">
        <img src="<?php echo $image; ?>"/>
        <div class="overlay"></div>
        <div class="product_holder_icon_holder" align="middle">
            <div class="product_holder_icon_base">
                <a href="<?php echo JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.AuxTools::SEFReady($lval->Name)."-$product->Id.html"  ?>" title="Ver detalle"><i class="fa fa-search fa-3x"></i></a>
            </div>
            <div class="product_holder_icon_base">
                <a onclick="addproduct(<?php echo $product->Id ?>);" href="#" title="Comprar" ><i class="fa fa-shopping-cart fa-3x"></i></a>
            </div>
        </div>
         <div style="display:none;" id="list-product-detail-<?php echo $product->Id ?>">
             <img id="image-popup" src="<?php echo $image; ?>" />
             <p><?php echo $product->getLanguageValue($LangId)->Name; ?></p>
             <p>1 x <?php echo AuxTools::MoneyFormat($product->SalePrice); ?></p>
         </div>
    </div>
    <?php endforeach; ?>
</div>
<script type="text/javascript">
function addproduct(pid)
{
    jQuery('#dialog-product-detail').html(jQuery('#list-product-detail-'+pid).html()); 
    jQuery.ajax({
        url:"index.php?option=com_catalog&task=setproduct&format=json", 
        data:{ pid: pid, cant: null }
        }
    ).done(function( data ) {
            var c=data;
            var html= c[0] +" <?php echo JText::_('COM_CATALOG_CART_ARTICLES_FOR')." " ?>"+c[1];
            jQuery( "#dialog" ).dialog( "open" );
            jQuery( "#cart_detail_str" ).html( html);
            return true;
      }
    );
      
    
    return false;
}
</script>
