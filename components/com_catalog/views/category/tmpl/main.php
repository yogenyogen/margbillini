<?php

$ro=JFactory::getApplication()->getPathway();

$path = "";
foreach($ro->getPathwayNames() as $p)
{
    $path.=DS.AuxTools::SEFReady($p);
}
$LangId= AuxTools::GetCurrentLanguageIDJoomla();
$l = new bll_product(-1);
$products_date = $l->findAll(null,null, true);
?>

<style>
.product_holder:hover .overlay {
    opacity:0.4;
}
.overlay {
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:#fff;
    opacity:0;
}
.product_holder:hover .product_holder_icon_holder {
    opacity:1;
}
.product_holder .product_holder_icon_base
{
   
    height: 36px;
    width: 36px;
}

.product_holder_icon_holder
{
    width:100%;
    height:100%;
    margin: 30px auto 10px auto;
    opacity:0;
}
</style>

<script type="text/javascript">
$(function() 
{
    $( "#dialog" ).dialog({
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
            
function addproduct(pid)
{
    $('#dialog-product-detail').html($('#list-product-detail-'+pid).html()); 

    $.ajax({
        url:"index.php?option=com_catalog&task=setproduct&format=json", 
        data:{ pid: pid, cant: null }
        }
    ).done(function( data ) {
            var c=data;
            var html= c[0] +" <?php echo JText::_('COM_CATALOG_CART_ARTICLES_FOR')." " ?>"+c[1];
            $( "#dialog" ).dialog( "open" );
            $( "#cart_detail_str" ).html( html);
            return true;
      }
    );
    return false;
}
</script>
<div id="dialog" title="El producto fue anadido correctamente">
    <div id="dialog-product-detail">
        
    </div>
    <a class="button" onclick="$( '#dialog' ).dialog( 'close' );">Seguir comprando</a>
    <a class="button" href="index.php?option=com_catalog&view=sales">Ir al carrito</a>
</div>
<h3 class="page-header_border"><i class="fa fa-heart fa-rotate-270"></i><span>
<?php echo JText::_('COM_CATALOG_NEW_PLANS_TITLE'); ?></span><i class="fa fa-heart fa-rotate-90"></i>
</h3>
<div class="products-holder">
    <?php foreach($products_date as $product): ?>
        <?php 
        $img = $product->getMainImage();
        $lval = $product->getLanguageValue($LangId);
        if(strlen($img->ImageUrl)> 4)
            $image = $img->ImageThumb;
        else
            $image='./components/com_catalog/images/no-image-listing.jpg';
        ?>
         <div class="product_holder span4">
	     <img src="<?php echo $image; ?>" />
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
	
