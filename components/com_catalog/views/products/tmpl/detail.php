<?php
$id =0;
if(isset($_REQUEST['pid']))
{
    $id=$_REQUEST['pid'];
}
$product = new bll_product($id);
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$img = $product->getMainImage();
$lval = $product->getLanguageValue($LangId);

if(strlen($img->ImageUrl)> 4)
    $image = $img->ImageUrl;
else
    $image='./components/com_catalog/images/no-image-listing-detail.jpg';

?>
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
    
    var cant=$("#quantity").val();
    $.ajax({
        url:"../index.php?option=com_catalog&task=setproduct&format=json", 
        data:{ pid: pid, cant: $("#quantity").val() },
        dataType:'json'
        }
    ).done(function( data, textStatus, jqXHR) {
            var c=data;
            var price = "<?php echo $product->SalePrice; ?>";
            var curr = "<?php echo DEFAULT_CURRENCY; ?>";
            var str=cant+" x "+curr+(cant*price);
            $("#cantelem").html("");
            $("#cantelem").html(str);
            var html= c[0] +" <?php echo JText::_('COM_CATALOG_CART_ARTICLES_FOR')." " ?>"+c[1];
            $( "#dialog" ).dialog( "open" );
            $( "#cart_detail_str" ).html(html);
            $('#dialog-product-detail').html($('#list-product-detail-'+pid).html()); 
            return true;
      }
    ).fail(function( jqXHR, textStatus, errorThrown ) {
        alert(textStatus+", Error:"+errorThrown);
    });
    return false;
}
</script>
<div id="dialog" title="El producto fue anadido correctamente">
    <div id="dialog-product-detail">
        
    </div>
    <a class="button" onclick="$( '#dialog' ).dialog( 'close' );">Seguir comprando</a>
    <a class="button" href="index.php?option=com_catalog&view=sales">Ir al carrito</a>
</div>
 <div style="display:none;" id="list-product-detail-<?php echo $product->Id ?>">
     <img id="image-popup" src="<?php echo $image; ?>" />
     <p><?php echo $product->getLanguageValue($LangId)->Name; ?></p>
     <p id="cantelem"></p>
 </div>
<div class="product_detail">
	<h4><i class="fa fa-heart fa-rotate-270"></i><span>
    <?php 

    echo $lval->Name;

    ?></span><i class="fa fa-heart fa-rotate-90"></i>
    </h4>
    <div class="top-holder">
        <div class="img-holder">
            <img class="lis-image" src="<?php echo $image; ?>" />
        </div>
        <div class="top-info">

            
            
            <div class="description">
                <?php 
                echo $lval->Description;
                ?>
            </div>
			<div class="line"></div>
			<div class="sep_corchete">
			<div class="all_right_content">
					<input id="quantity" class="number_qty" value="1" type="number" name="quantity" min="1" max="15">
					<div class="line"></div>
					<p class="price">
	                	<?php 
	                	echo AuxTools::MoneyFormat($product->SalePrice);
	                	?>
	            	</p>
					<div class="line"></div>
                        <button type="button" id="order" href="#" onclick="return addproduct(<?php echo $product->Id; ?>);">
	                        <?php echo JText::_('COM_CATALOG_ORDER') ?><i class="fa fa-shopping-cart fa-3x"></i>
	                </button>
            	<div class="note">
                	<?php 
                	echo $lval->Note;
                	?>
            	</div>
            </div>
                   
          </div>
          
            
        </div>
    </div>
</div>