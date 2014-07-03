<?php

require_once BASE_DIR . LIBS . DB . DBOBJECT;

class twoCO {

  protected $sid = 202234853;
  protected $secret='tango';
  protected $products = array(); //$products[#]['name'], $products[#]['price'], $products[#]['descrip']
  protected $test_mode = false;
  protected $lang = "es_la";
  protected $card_holder_name = "";
  protected $city = "";
  protected $phone = "";
  protected $phone_extension="";
  protected $email = "";
  protected $country="";
  protected $zip="";
  protected $state="";
  protected $street_address="";
  protected $street_address2="";
  protected $ship_name = "";
  protected $ship_city = "";
  protected $ship_email = "";
  protected $ship_country="";
  protected $ship_zip="";
  protected $ship_street_address="";
  protected $ship_street_address2="";
  protected $ship_state="";
  protected $x_receipt_link_url="";
  protected $pay_method="";

  /**
   * GETTER AND SETTERS
   */
  public function setCardHolderName($cardHolderName) {

    $this->card_holder_name = $cardHolderName;
  }
  
  public function setx_receipt_link_url($url)
  {
      $this->x_receipt_link_url=$url;
  }
  
  public function setpay_method($pm)
  {
      $this->pay_method = $pm;
  }

  public function setship_State($state){
      $this->ship_state=$state;
  }
  
  public function setState($state){
      $this->state=$state;
  }
  
  public function setCity($city) {

    $this->city = $city;
  }

  public function setPhone($phone) {

    $this->phone = $phone;
  }
  
  public function setPhone_extension($phone) {

    $this->phone_extension = $phone;
  }

  public function setEmail($email) {

    $this->email = $email;
  }

  public function setLang($lang) {

    $this->lang = $lang;
  }
  
  public function setCountryCode($cc)
  {
      $this->country=$cc;
  }
  
  public function setZip($cc)
  {
      $this->zip=$cc;
  }
  
  public function setStreetAddress($sta)
  {
      $this->street_address=$sta;
  }
  
  public function setStreetAddress2($sta)
  {
      $this->street_address2=$sta;
  }
  
  public function setship_CardHolderName($cardHolderName) {

    $this->ship_name = $cardHolderName;
  }

  public function setship_City($city) {

    $this->ship_city = $city;
  }
  
  public function setship_CountryCode($cc)
  {
      $this->ship_country=$cc;
  }
  
  public function setship_Zip($cc)
  {
      $this->ship_zip=$cc;
  }
  
  public function setship_StreetAddress($sta)
  {
      $this->ship_street_address=$sta;
  }
  
  public function setship_StreetAddress2($sta)
  {
      $this->ship_street_address2=$sta;
  }
  /**
   * END OF GETTER AND SETTERS
   */

  /**
   * Class constructor for 2CO
   *
   */
  public function __construct() {

    
  }

  /**
   * Agrega un producto a la lista de venta
   *
   * @param string    $name       nombre del producto
   * @param float      $price      precio del producto
   * @param integer    $cant      cantidad del producto  
   * @param string     $descrip    descripción del producto
   * @param string     $type       string with the type of product if is shipping, tax or product
   * @param string     $tangible    string Y if product is tangible N if not
   */
  public function AddProduct($name, $price, $cant, $descrip = "", $type='product', $tangible='Y') {

    $product['name'] = urlencode($name);
    $product['price'] = $price;
    $product['quantity'] = $cant;
    $product['descrip'] = urlencode($descrip);
    $product['type'] = $type;
    $product['tangible'] = $tangible;
    $this->products[] = $product;
  }

  /**

   * Activates de test mode

   */
  public function ActivateTestMode() {

    $this->test_mode = true;
  }

  /**

   * Creates the products parameters URL

   *

   * @return string con el URL de parámetros para los productos

   */
  private function GenerateProductsForm() 
  {
    $counter = 0;
    $products_inputs="";
    foreach ($this->products as $product) {

      //NAME
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_name" value="'.$product['name'].'" />';
      //PRICE
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_price" value="'.$product['price'].'" />';
      //QUANTITY
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_quantity" value="'.$product['quantity'].'" />';
      //DESCRIPCIÓN
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_quantity" value="'.$product['descrip'].'" />';
      //TYPE
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_type" value="'.$product['type'].'" />';
      //TANGIBLE
      $products_inputs.= '<input type="hidden" name="li_'.$counter.'_tangible" value="'.$product['tangible'].'" />';
      $counter++;
    }
    return $products_inputs;
  }

  /**
   * Genera la base y parámetros esenciales de 2CO
   *
   * @param int $sale_id  the ID of the sales
   *
   * @return string con el URL básico
   */
  private function GenerateBasicForm($sell_id) {

    //BASE URL

    $url = "<form name=\"TwoCOprocess\" action=\"https://www.2checkout.com/checkout/purchase\" method=\"POST\">";

    //SID

    $url .= "<input type=\"hidden\" name=\"sid\" value=\"$this->sid\" />";
    $url .= "<input type=\"hidden\" name=\"mode\" value=\"2CO\" />";
    //NÚMERO DE ORDEN DE COMPRA
    $url .= "<input type=\"hidden\" name=\"cart_order_id\" value=\"$sell_id\" />";
    $url .= "<input type=\"hidden\" name=\"merchant_order_id\" value=\"$sell_id\" />";
    //TOTAL
    $url .= "<input type=\"hidden\" name=\"total\" value=\"".$this->CalculateTotal()."\" />";

    //TEST MODE

    if ($this->test_mode === true) 
      $url .= "<input type=\"hidden\" name=\"demo\" value=\"Y\" />";
    else
      $url .= "<input type=\"hidden\" name=\"demo\" value=\"N\" />";
    
    if($this->x_receipt_link_url != "")
        $url .= "<input type=\"hidden\" name=\"x_receipt_link_url\" value=\"$this->x_receipt_link_url\" />";
    if($this->pay_method != "")
        $url .= "<input type=\"hidden\" name=\"pay_method\" value=\"$this->pay_method\" />";
    return $url;
  }

  /**
   * Genera los parámetros del URL paral comprador
   *
   * @return string   con los parametros de URL para la info del comprador
   */
  private function GenerateBuyerInfo() {

    $url = "<input type=\"hidden\" name=\"card_holder_name\" value=\"$this->card_holder_name\" />";
    $url.= "<input type=\"hidden\" name=\"city\" value=\"$this->city\" />";
    $url.= "<input type=\"hidden\" name=\"country\" value=\"$this->country\" />";
    $url.= "<input type=\"hidden\" name=\"zip\" value=\"$this->zip\" />";
    $url.= "<input type=\"hidden\" name=\"state\" value=\"$this->state\" />";
    $url.= "<input type=\"hidden\" name=\"ship_state\" value=\"$this->ship_state\" />";
    $url.= "<input type=\"hidden\" name=\"street_address\" value=\"$this->street_address\" />";
    $url.= "<input type=\"hidden\" name=\"street_address2\" value=\"$this->street_address2\" />";
    $url.= "<input type=\"hidden\" name=\"ship_name\" value=\"$this->ship_name\" />";
    $url.= "<input type=\"hidden\" name=\"ship_city\" value=\"$this->ship_city\" />";
    $url.= "<input type=\"hidden\" name=\"ship_country\" value=\"$this->ship_country\" />";
    $url.= "<input type=\"hidden\" name=\"ship_zip\" value=\"$this->ship_zip\" />";
    $url.= "<input type=\"hidden\" name=\"ship_street_address\" value=\"$this->ship_street_address\" />";
    $url.= "<input type=\"hidden\" name=\"ship_street_address2\" value=\"$this->ship_street_address2\" />";
    $url.= "<input type=\"hidden\" name=\"email\" value=\"$this->email\" />";
    $url.= "<input type=\"hidden\" name=\"phone\" value=\"$this->phone\" />";
    $url.= "<input type=\"hidden\" name=\"phone_extension\" value=\"$this->phone_extension\" />";
    $url.= "<input type=\"hidden\" name=\"lang\" value=\"$this->lang\" />";

    return $url;
  }

  /**
   * Calcula el total de la venta
   *
   * @warning VALOR TOTAL REDONDEADO A SEGUNDA CIFRA DECIMAL!
   *
   * @return float con el total
   */
  private function CalculateTotal() {
    $total = 0.00;

    foreach ($this->products as $product) {

      $total += $product['price']*$product['quantity'];
    }

    return number_format(round($total, 2), 2, '.', '');
  }

  /**
   * Genera el URL de redirección a 2CO para hacer la venta
   *
   * @param int $sell_id  ID de la venta
   *
   * @warning VALOR TOTAL REDONDEADO A SEGUNDA CIFRA DECIMAL!
   *
   * @return string con el URL de redirección a 2CO
   */
  public function GenerateSell($sell_id) {

    return $this->GenerateBasicForm($sell_id) . 
           $this->GenerateProductsForm() . $this->GenerateBuyerInfo()
           ."</form>
    <script type=\"text/javascript\">
    window.onload = function(){
        document.TwoCOprocess.submit();
    };
    </script>";
  }

  /**
   * Verificar si el hash es válido dado las informaciones
   *
   * @param int       $sale_id    número de orden de compra
   * @param float     $total      total generado por la orden
   * @param string    $hash       hash retornado por 2CO para verificar
   *
   * @return boolean TRUE si la venta fue aprovada correctamente por 2CO, FALSE caso contrario
   *
   * @warning el parámetro $total debe de ser la suma de todos los item y el total generado es redondeado a 2 cifra decimal
   */
  public function CheckSale($sale_id, $total, $hash) {

    // md5 hash as 2co formula: md5(secret_word + vendor_number + order_number + total)
    // Referencia: https://www.2checkout.com/blog/knowledge-base/merchants/tech-support/3rd-party-carts/md5-hash-checking/how-do-i-use-the-md5-hash/
    if($this->test_mode == false)
    {
        if (strtoupper(md5($this->secret . $this->sid . $sale_id . $total)) == $hash) 
        {       
          return true;
        }
        return false;
    }
    else
    {
        if (strtoupper(md5($this->secret . $this->sid . 1 . $total)) == $hash) 
        {
          return true;
        }
        return false;
    }
  }

}

?>