<?php


/**
 * Description of bll_product
 *
 * @author Gabriel
 */
class bll_product extends catalogproduct {

    public function __construct($id) {

        return parent::__construct($id);

    }

    /**
     * Selects one object from the table depending on which
     * attribute you are looking for.
     *
     * @param string|array $field name of the field to search for delete.
     * when $field is an array. field array(array(fieldname => OP)) when value is
     * the statement field[i] of the value value[i] and OP are 
     * the following operators:
     * Op(=, !=, <>).
     * @param string|array $value value of the field to search for delete.
     * when $value is an array. value array(array(val1 => Glue)) when value is
     * the value[i] of the statement field[i] and GLue are logic operators:
     * Logic(AND, OR).
     * @param  boolean $DESC ascendent
     * @param  string  $order_field Field for the order by
     * @param  integer $lower_limit  lower limit on the query, it must be
     * an integer otherwise is going to be ignored
     * @param  integer $higher_limit higher limit on the query, it must be
     * an integer otherwise is going to be ignored
     * 
     * @return bll_product dbobject or false on failure.
     */
    public function find($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {

        return parent::find($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);

    }

    

    /**

     * Selects multiples object from the table depending on which

     * attribute you are looking for.

     *

     * @param string|array $field name of the field to search for delete.

     * when $field is an array. field array(array(fieldname => OP)) when value is

     * the statement field[i] of the value value[i] and OP are 

     * the following operators:

     * Op(=, !=, <>).

     * @param string|array $value value of the field to search for delete.

     * when $value is an array. value array(array(val1 => Glue)) when value is

     * the value[i] of the statement field[i] and GLue are logic operators:

     * Logic(AND, OR).

     * @param  boolean $DESC ascendent

     * @param  string  $order_field Field for the order by

     * @param  integer $lower_limit  lower limit on the query, it must be

     * an integer otherwise is going to be ignored

     * @param  integer $higher_limit higher limit on the query, it must be

     * an integer otherwise is going to be ignored

     * 

     * @return bll_product dbobject or false on failure.

     */

    public function findAll($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {

        return parent::findAll($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);

    }
    
    /**
     * Find products by category
     * @param integer $cid
     * 
     * @return bll_product array of products
     */
    public static function find_products($cid = null, $lower_limit=null, $upper_limit=null)
    {
        $db = new dbprovider(true);
        $salep = new catalogproductsale(-1); 
        $valid_delim=false;
        $query = "select Id from #__catalogproduct where ";
        if($cid != null)
        {
            $query.= "CategoryId = ".$db->escape_string($cid)." AND ";
        }
        $query2 = $query;
        $query.="Id Not IN "
                . "(select ProductId from `#__".$salep->getTableName()."` where `SaleId` IN "
                . "(select Id from `#__catalogsale` where `SaleStateId`=1))";
        $query2.="Id IN "
                . "(select ProductId from `#__".$salep->getTableName()."` where `SaleId` IN "
                . "(select Id from `#__catalogsale` where `SaleStateId`=1))"; 
        if ($lower_limit !== null && $upper_limit !== null):
            if ((is_int($lower_limit) && is_int($upper_limit)) || ( is_numeric($lower_limit) && is_numeric($upper_limit)))
            {
                $valid_delim=true;
            }
        endif;
        $db->Query($query);
        $products = array();
        $products =$db->getNextObjectList();
        $db->Query($query2);
        $products=array_merge($products, $db->getNextObjectList());
        if($valid_delim != true)
        {
            $lower_limit = 0;
            $upper_limit = count($products);
        }
        $final_arr = array();
        $index =0;
        foreach($products as $pobj)
        {
            if($index >= $lower_limit && $index < ($lower_limit+$upper_limit))
            {
                $final_arr[]= new bll_product($pobj->Id);
            }
            $index++;
        }
        return $final_arr;
    }

    

    /**

     * Delete the object instance in the database

     *

     * @param string|array $field name of the field to search for delete.

     * when $field is an array. field array(array(fieldname => OP)) when value is

     * the statement field[i] of the value value[i] and OP are 

     * the following operators:

     * Op(=, !=, <>).

     * @param string|array $value value of the field to search for delete.

     * when $value is an array. value array(array(val1 => Glue)) when value is

     * the value[i] of the statement field[i] and GLue are logic operators:

     * Logic(AND, OR).

     *

     * @warning if the funtion is used without parameters

     * there`s only a successful delete if the object

     * Id is found in the database.

     *

     * @return boolean|bll_product Not false on success.

     */

    public function delete($field = "", $value = "") {

        return parent::delete($field, $value);

    }

    

    /**

     * Insert the object to the database

     *

     * @return bll_product not false on success.

     */

    public function insert() {

        

        $dttz = new DateTimeZone(LIB_TIMEZONE);

        $cur_dt = new DateTime("now", $dttz);

        $prop = AuxTools::getPropertiesFromObj($cur_dt);

        $_POST['CreatedAt']=$prop['date'];

        $_POST['UpdatedAt']=$prop['date'];

        $this->setAttributes($_POST);

        $obj= parent::insert();

        if($obj !== false)
        {
            $oid = $obj->Id;
            //adding lang values
            $this->addLangValue($oid);
            if(isset($_POST['Images']))
            {
                $index=1;
                $images=array();
                for($i=0; $i < count($_POST['Images']); $i++)
                    $images[$_POST['Images'][$i]]=$_POST['ImagesThumb'][$i];
                
                if(isset($_POST['mainimg']))
                    $index=$_POST['mainimg'];
                $this->setImages($images, $index);
            }
            $this->setFields();
            return $this;

        }

    }

    

    /**

     * Update the object to the database

     *

     * @return bll_product not false on success.

     */
    public function update()
    {

        $dttz = new DateTimeZone(LIB_TIMEZONE);
        $cur_dt = new DateTime("now", $dttz);
        $prop = AuxTools::getPropertiesFromObj($cur_dt);
        $_POST['UpdatedAt']=$prop['date'];
        $this->setAttributes($_POST);
        $obj= parent::update();
        if($obj !== false)
        {
            $oid = $obj->Id;
            //adding lang values
            $this->addLangValue($oid);
            if(isset($_POST['Images']))
            {
                $index=1;
                $images=array();
                for($i=0; $i < count($_POST['Images']); $i++)
                    $images[$_POST['Images'][$i]]=$_POST['ImagesThumb'][$i];
                
                if(isset($_POST['mainimg']))
                    $index=$_POST['mainimg'];
                $this->setImages($images, $index);
            }
            $this->setFields();
            return $this;
        }
    }

    

    /**

     * Add the language values

     * @param int $id id of the main object 

     */

    private function addLangValue($id)

    {

        //deleting lang values

        $cfl=new catalogproductlang(0);

        $cfl->delete('ProductId',$id);

        //adding lang values

        $langs = languages::GetLanguages();

        foreach($langs as $lang)

        {

            $lang_suffix = "_".$lang->lang_id;

            $lv = new catalogproductlang(0);

            $lv->ProductId = $id;

            $lv->Name = $_POST['Name'.$lang_suffix];
            $lv->Description = $_POST['Description'.$lang_suffix];
            $lv->Alias = $_POST['Alias'.$lang_suffix];
            $lv->Note = $_POST['Note'.$lang_suffix];
            $lv->LangId=$lang->lang_id;

            $lv = $lv->insert();

        }

    }

    

    

    /**

     * Get the language value

     * @param type $LangId id of the language

     * @return catalogproductlang catalogproductlang value object.

     */

    public function getLanguageValue($LangId)

    {

        $language = new languages($LangId);

        if($language->lang_id <= 0)

            return new catalogproductlang(-1);

        $langval  = new catalogproductlang(-1);

        

        return $langval->find(

                              array(

                                  array('ProductId','='),

                                  array('LangId','=')

                                   ), 

                              array(

                                  array($this->Id,null),

                                  array($LangId,'AND')

                                  )

                             );

        

    }

    

    /**

     * Get the language value

     * @param type $LangId id of the language

     * @return catalogproductlang array of catalogproductlang value object.

     */

    public function getLanguageValues()

    {

        $langval  = new catalogproductlang(-1);

        

        return $langval->findAll(

                              array(

                                  array('ProductId','=')

                                   ), 

                              array(

                                  array($this->Id,null)

                                  )

                             );

        

    }

    

    /**

     * Gets the images of the product

     * @return catalogproductimage array of images

     */

    public function getImages()

    {

        $ima = new catalogproductimage(0);

        return $ima->findAll('ProductId', $this->Id);

    }

    

    /**
     * Set the images of the product
     * @param array $ImgArray hash of images=>thumbnail strings
     */
    private function setImages($ImgArray, $index=1)
    {
        if(is_array($ImgArray) && $this->Id > 0)
            {
                $images=$ImgArray;
                $limage = new catalogproductimage(0);
                $limage->delete('ProductId', $this->Id);
                $i=1;
                foreach($images as $img => $thumb)
                {
                    $limage = new catalogproductimage(0);
                    $limage->ImageUrl=$img;
                    $limage->ImageThumb=$thumb;
                    $limage->ProductId=$this->Id;
                    if($i == $index)
                        $limage->Main = 1;

                    else
                        $limage->Main=0;
                    $limage->insert();

                    $i++;

                }

            }

    }

    

    /**

     * Get the category

     * 

     * @return bll_category

     */

    public function getCategory()

    {

        $category = new bll_category($this->CategoryId);

        return $category;

    }

    

    /**

     * Gets all the fields for the product.

     * 

     * @return bll_field

     */

    public function getFields()

    {

        $cat = $this->getCategory();

        return $cat->getAllFields();

    }
    
    /**
     * Get the field value of a product
     * 
     * @param int $fieldid id of the field
     * @param int $productid id of the product
     * 
     * @return bll_productfieldvalue returns the product field value.
     */
    public static function getProductFieldValue($fieldid, $productid)
    {

        $lfv = new bll_productfieldvalue(0);

        $field = new bll_field($fieldid);

        $product = new bll_product($productid);

        //non multilanguage

        $lfv = $lfv->find(

                    array(

                          array('ProductId','='),

                          array('FieldId','=')

                           ), 

                    array(

                          array($product->Id,null),

                          array($field->Id,'AND')

                          )

                );

       

        return $lfv;

    }
    
    /**
     * Get the products from a category
     * @param integer $cid category id
     * @return bll_product array of products
     */
    public static function getProductsByCategory($cid)
    {
        $p = new bll_product(-1);
        return $p->findAll('CategoryId', $cid);
    }

    

    /**

     * Generate the form fields

     */

    public function GenerateFormFields()

    {

        $fields = $this->getFields();

        $LangId = AuxTools::GetCurrentLanguageIDJoomla();

        $html="";

        foreach($fields as $field)

        {

            $multilang = $field->MultiLanguage;

            $required = $field->Required;

            $max = $field->Max;

            $min = $field->Min;

            $name = "";

            $value= bll_product::getProductFieldValue($field->Id, $this->Id)->Value;

            $event_hash=array();

            $type = $field->Datatype;

            $symbol = $field->Symbol;

            $fieldlangvalue = $field->getLanguageValue($LangId);

            $label = $fieldlangvalue->Label;

            if(bll_field::isMultipleFieldFromDatatype($field->Datatype))

            {

                $multiple = new bll_multiplefieldvalue(0);

                $multiples = $multiple->findAll(array(

                          array('FieldId','=')

                           ), 

                    array(

                          array($field->Id, null)

                          )

                        );

                

                

                   $values = json_decode($value);

                   if(!is_array($values))

                       $values=array();

                   $sval = array();

                   foreach($multiples as $mul)

                   {

                       $selected=false;

                       $v = $mul->Value;

                       if($multilang == true)

                       {

                           $v=$mul->getLanguageValue($LangId)->Value;

                       }

                       foreach($values as $val)

                       {

                           if($mul->Id == $val)

                           {

                               $selected=true;

                               break;

                           }

                       }

                       if($selected == true)

                           $sval['#__'.$mul->Id]=$v;

                       else

                           $sval[$mul->Id]=$v;

                   }

                   $name ="field_".$field->Id; 

                   $html.=$this->renderField($name, $label, $sval, $type, $required, $min, $max, $symbol, $event_hash);

            }

            else

            {

                if($multilang == true)

                {

                    foreach(languages::GetLanguages() as $lang)

                    {

                        $fieldlangvalue = $field->getLanguageValue($lang->lang_id);

                        $label = $fieldlangvalue->Label."($lang->title_native)";

                        $name ="field_$field->Id"."_$lang->lang_id";

                        $val = bll_product::getProductFieldValue($field->Id, $this->Id)->getLanguageValue($lang->lang_id)->Value;

                        $html.=$this->renderField($name, $label, $val, $type, $required, $min, $max, $symbol, $event_hash);

                    }

                }

                else

                {

                    $name ="field_".$field->Id;

                    $html.=$this->renderField($name, $label, $value, $type, $required, $min, $max, $symbol, $event_hash);

                }

            }

            

        }

        return $html;

    }

    

    /**

     * 

     * @return string html with the fields info from the product

     */

    public function displayProductFields()

    {

        $fields=array();

        $multiple_fields=array();

        $LangId=AuxTools::GetCurrentLanguageIDJoomla();

        foreach($this->getFields() as $field)

        {

            if(bll_field::isMultipleFieldFromDatatype($field->Datatype) == true)

                $multiple_fields[]=$field;

            else

                $fields[]=$field;

        }

        $html="<div class=\"product-fields-holder\">";

        foreach($fields as $fil)

        {

            $lv = $fil->getLanguageValue($LangId);   

            $fv = bll_product::getProductFieldValue($fil->Id, $this->Id);

            $value=$fv->Value;

            if($fil->MultiLanguage!=0)

                $value  = $fv->getLanguageValue($LangId)->Value;

            $html.="<div id=\"field_$fil->Id\" class=\"field_info\">

                        <p class=\"title\">$lv->Label:</p>

                        <div class=\"container\">

                        $value

                        </div>

                    </div>";

        }

        foreach($multiple_fields as $fil)

        {

            $lv = $fil->getLanguageValue($LangId);   

            $fv = bll_product::getProductFieldValue($fil->Id, $this->Id);

            $value=  json_decode($fv->Value);

            if(is_array($value))

            {

                $values="<ol id=\multiple_$fil->Id\">";

                foreach($value as $v)

                {

                    $mfv = new bll_multiplefieldvalue($v);

                    if($fil->MultiLanguage!=0)

                    {

                        $values.="<li>".$mfv->getLanguageValue($LangId)->Value."</li>";

                    }

                    else

                        $values.="<li>$mfv->Value</li>";

                }

                $values.="</ol>";

            }

            else

                $values="";

            

            $html.="<div id=\"field_$fil->Id\" class=\"field_info\">

                        <p class=\"title\">$lv->Label:</p>

                        <div class=\"container\">

                        $values

                        </div>

                    </div>";

        }

        $html.="</div>";

        return $html;

    }

    

    /**

     * Gets the product main image

     * 

     * @return catalogproductimage gets the product main image

     */

    public function getMainImage()
    {
           $images  = $this->getImages();
           foreach($images as $img)
           {
               if($img->Main == '1')
               {
                   return $img;
               }
           }
           return new catalogproductimage(0);

    }

    

    /**

     * Save the fields from the product form,

     * Generated by the fucntion GenerateFormFields

     * 

     * @see GenerateFormFields

     */

    public function setFields()

    {

        $field_values=array();

        $field_lang_values=array();

        foreach($_POST as $k => $val)

        {

            if(strrpos($k, 'field_') !== false)

            {

                $arr = explode('_', $k);

                if(count($arr) > 2)

                {

                    $flag=true;

                    foreach($field_lang_values as $k => $lv)

                    {

                        if(strrpos($k, 'field_'.$arr[1]) !== false)

                        {

                            $flag=false;

                            break;

                        }

                    }

                    if($flag==true)

                        $field_lang_values[$k]=$val;

                }

                else 

                    $field_values[$k]=$val;

            }

        }

        $lfv = new catalogproductfieldvalue(0);

        $lfv->delete('ProductId', $this->Id);

        foreach($field_values as $k=>$fv)

        {

            $arr = explode('_', $k);

            $fid = $arr[1];

            $lfv = new catalogproductfieldvalue(0);

            $lfv->ProductId=$this->Id;

            $lfv->FieldId = $fid;

            if(is_array($fv))

                $lfv->Value=  json_encode($fv);

            else

                $lfv->Value = $fv;



            $lfv->insert();

            

        }

        foreach($field_lang_values as $k => $fv)

        {

            $arr = explode('_', $k);

            $fid = $arr[1];

            $lfv = new catalogproductfieldvalue(0);

            $lfv->ProductId=$this->Id;

            $lfv->FieldId = $fid;

            $lfv=$lfv->insert();

            $languages = languages::GetLanguages();

            foreach($languages as $lang)

            {

                $lfvl = new catalogproductfieldvaluelang(0);

                $lfvl->LangId =$lang->lang_id;

                $lfvl->ProductFieldValueId=$lfv->Id;

                $value = $_POST['field_'.$fid.'_'.$lang->lang_id];

                $lfvl->Value=$value;

                $lfvl->insert();

            }

        }

    }

    

    private function renderField($name, $label, $value, $type, $required, $min, $max, $symbol, $event_hash=array(), $disabled=false, $readonly=false)

    {

        $form = form::getInstance();

        $id = $name.'_id';

        $class= $name.'_class';

        $form->Label($label, $name);

        switch($type)

        {

            case "numeric":

              $form->Text($name, $value, $id, $class, $required, $event_hash,$disabled, $readonly);

              break;

            case "text":

              $form->Text($name, $value, $id, $class, $required, $event_hash,$disabled, $readonly);

              break;

            case "textarea":

              $form->TextArea($name, $value, $id, $class, 20, 6, $required, $event_hash, $readonly, $disabled);

              break;

            case "editor":

              $form->Editor($name, $value, $name.  uniqid(), $name."_tiny", $required);

              break;

            case "numericrange":

              $form->RangeSlider($id, $name, $value, $min, $max, $max/$min, 'min', $symbol);

              break;

            case "numericrange2":

              $form->RangeSlider($id, $name, $value, $min, $max, $max/$min, 'minmax', $symbol);

              break;

            case "date":

              $form->Date($name, $value, $id, $class, $readonly,$event_hash,$disabled,$readonly);

                break;

            case "selectbox":

              $form->SelectBox($name, $value, $id,$class, $required,$event_hash,$readonly,$disabled);

              break;

            case "radiobutton":

              $form->Radiobuttons($name, $value, $id,$class,$event_hash,$readonly,$disabled);

              break;

            case "checkbox":

              $form->Checkboxes($name, $value, $id,$class,$event_hash,$readonly,$disabled);

              break;

            default :

                return null;

              break;

          }

          $html  = $form->Render();

          $allowable_tags="<br><li><script><style><select><option><p><textarea><input><label><div><ul><button><a><h1><h2><h3><h4><h5><fieldset><ol>

                           <br/></li></script></style></select></option></p></textarea><input/></label></div></ul></button></a></h1></h2></h3></h4></h5></fieldset></ol>";

          return strip_tags($html, $allowable_tags);

    }

    /**
     * Check product availability
     * 
     * @param integer $pid product id
     * @return catalogproductsale array of sales
     */
    static function check_product_sales($pid)
    {
        $db = new dbprovider(true);
        $salep = new catalogproductsale(-1); 
        
        $query = "select * from `#__".$salep->getTableName()."` where `ProductId`=".$db->escape_string($pid)." AND `SaleId` IN "
                . "(select Id from `#__catalogsale` where `SaleStateId`=1)";
        $db->Query($query);
        $psales = $db->getNextObjectList();
        return $psales;
    }
    
}

