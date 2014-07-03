<?php



/**
 * Description of bll_category
 *
 * @author Gabriel
 */
class bll_category extends catalogcategory {

    

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

     * @return bll_category dbobject or false on failure.

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

     * @return bll_category dbobject or false on failure.

     */

    public function findAll($field = "", $value = "", $DESC = true, $order_field = "", $lower_limit = null, $upper_limit = null) {

        return parent::findAll($field, $value, $DESC, $order_field, $lower_limit, $upper_limit);

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

     * @return boolean|bll_category Not false on success.

     */

    public function delete($field = "", $value = "") {

        return parent::delete($field, $value);

    }

    

    /**

     * Insert the object to the database

     *

     * @return bll_category not false on success.

     */

    public function insert() {

        $this->setAttributes($_POST);
        $obj= parent::insert();
        if($obj !== false)
        {
            $oid = $obj->Id;
            //adding lang values
            $this->addLangValue($oid);
            return $this;
        }
        return false;

    }

    /**

     * Update the object to the database

     *

     * @return bll_category not false on success.

     */

    public function update() {

        $this->setAttributes($_POST);

        $obj= parent::update();

        if($obj !== false)

        {

            $oid = $obj->Id;

            //adding lang values

            $this->addLangValue($oid);

            return $this;

        }

        return false;

    }

    

    /**
     * Add the language values
     * @param int $id id of the main object 
     */
    private function addLangValue($id)
    {
        $fid = $id;
        $langs = languages::GetLanguages();
        //deleting old values
        $cfl=new catalogcategorylang(0);
        $cfl->delete('CategoryId',$id);
        //adding lang values
        foreach($langs as $lang)
        {
            $lang_suffix = "_".$lang->lang_id;
            $lv = new catalogcategorylang(0);
            $lv->CategoryId = $fid;
            $lv->Name = $_POST['Name'.$lang_suffix];
            $lv->Alias = $_POST['Alias'.$lang_suffix];
            $lv->Description = $_POST['Description'.$lang_suffix];
            $lv->LangId=$lang->lang_id;
            $lv = $lv->insert();
        }
    }
   
    
    /**

     *

     * @param array $fieldarray int array of field id

     */

    public function setFields($fieldarray)

    {   

        if($this->Id > 0)

        {

            $idcat = $this->Id;

            $catfield = new catalogcategoryfield(-1);

            $catfield->delete('CategoryId', $idcat);

            foreach($fieldarray as $fid)

            {

                if($fid > 0)

                {

                  $cf = new catalogcategoryfield(-1);  

                  $cf->FieldId = $fid;

                  $cf->CategoryId = $idcat;

                  $cf->insert();

                }

            }

        }

    }

    

    /**

     * Gets all the fields from a category

     * @return bll_field array of fields

     */

    public function getFields()

    {

        $idcat = $this->Id;

        $catfield = new catalogcategoryfield(-1);

        $rel= $catfield->findAll('CategoryId', $idcat);

        $fields=array();

        foreach($rel as $cf)

        {

            $fields[]=new bll_field($cf->FieldId);

        }

        return $fields;

    }

    

    /**

     * Gets all the fields from a category including the parent ones

     * @return bll_field array of fields

     */

    public function getAllFields()

    {

        return array_merge($this->getFields(), $this->getAntecedentsFields());

    }

    

    /**

     * Gets all the available fields from a category

     * @return bll_field array of fields

     */

    public function getAvailableFields()

    {

        $field = new bll_field(0);

        $fields =$field->findAll();

        $parents_category_fields = $this->getAntecedentsFields();

        $available_fields=array();

        foreach($fields as $f)

        {

            $flag=true;

            foreach($parents_category_fields as $pf)

            {

                if($f->Id == $pf->Id)

                {

                    $flag=false;

                    break;

                }

            }

            if($flag===true)

                $available_fields[]=$f;

        }

        return $available_fields;

    }

    

    /**

     * Return the line of direct antecedents of a category

     * 

     * @return bll_category array of parent, grandparent, grandgrandparent

     */

    public function getAntecedentsCategories()

    {

        $ant = array();

        $cat = new bll_category($this->CategoryId);

        while($cat->Id > 0)

        {

            $ant[]=$cat;

            if($cat->CategoryId <= 0 || $cat->CategoryId === null)

                break;

            $cat =  new bll_category($cat->CategoryId);

        }

        return $ant;

    }

    

    /**

     * Return the fields from the line of direct antecedents of a category

     * @return bll_field Collections of Id of Fields that belongs to the  

     */

    public function getAntecedentsFields()

    {

        $query="SELECT DISTINCT FieldId FROM `lsn02_catalogcategoryfield` WHERE ";

        $ants = $this->getAntecedentsCategories();

        $index=1;

        if(count($ants) <= 0)

            return array();

        

        foreach($ants as $c)

        {

            if($index == count($ants))

                $query.="CategoryId = $c->Id";

            else

                $query.="CategoryId = $c->Id OR";

            $index++;

        }

        $db = $this->getProvider(true);

        $db->Query($query);

        $objl= $db->getNextObjectList();

        $fa = array();

        foreach($objl as $ob)

        {

            $f = new bll_field($ob->FieldId);

            $fa[]=$f;

        }

        return $fa;

    }

    

    /**

     * Gets all the children category from the category.

     * 

     * @return bll_category array of categories

     */

    public function getChildrenCategories()

    {

        return $this->findAll('CategoryId', $this->Id);

    }

    

    

    /**

     * Gets the parent category

     * @return bll_category

     */

    public function getParent()

    {

        return $this->find('Id', $this->CategoryId);

    }

    

    /**

     * Gets the root parent category from this

     * @return bll_category

     */

    public function getRootCategory()

    {

        $rootcat=$this->getParent();

        while($rootcat->CategoryId > 0)

        {

            $rootcat=$this->getParent();

        }

        return $rootcat;

    }

    

    /**

     * Get the language value

     * @param type $LangId id of the language

     * @return catalogcategorylang catalogcategorylang value object.

     */

    public function getLanguageValue($LangId)
    {
        $language = new languages($LangId);
        $langval  = new catalogcategorylang(-1);
        $langval =$langval->find(
                              array(
                                  array('CategoryId','='),
                                  array('LangId','=')
                                   ), 
                              array(
                                  array($this->Id,null),
                                  array($LangId,'AND')
                                  )
                             );
        return $langval;
    }

    

    /**

     * Get the language value

     * @param type $LangId id of the language

     * @return catalogcategorylang array of catalogcategorylang value object.

     */

    public function getLanguageValues()

    {

        $langval  = new catalogcategorylang(-1);

        

        return $langval->findAll(

                              array(

                                  array('CategoryId','=')

                                   ), 

                              array(

                                  array($this->Id,null)

                                  )

                             );

        

    }

    
    /**
     * 
     * @param bll_category $root_cat 
     */
    public static function getCategoriesSelectorHTML($root_cat, $lower_limit=0)
    {
        $html="";
        $LangId = AuxTools::GetCurrentLanguageIDJoomla();
        $children_categories = $root_cat->getChildrenCategories();
        $html="
        <script>
        $(function() {
            $( \".accordion_$root_cat->Id\" ).accordion();
        });
        </script>    
        <div class=\"accordion_$root_cat->Id\">";
        foreach($children_categories as $cc)
        {
            if(count($cc->getChildrenCategories())>0)
            {
                $html.='<h3>
                     '.$cc->getLanguageValue($LangId)->Name.'
                </h3> 
                <div>
                    '.self::getCategoriesSelectorHTML($cc).'
                </div>';
            }
            else
            {
                
                $url=AuxTools::SEFReady(JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.bll_category::generateSEFUrl($cc->Id, $LangId)).".html";
                $html.='
                </div>    
                <div>
                    <a href="'.$url.'">
                    <form action="'.$url.'" method="POST">
                        <input name="cid" type="hidden" value="'.$cc->Id.'"/>
                        <input type="submit" value="'.$cc->getLanguageValue($LangId)->Name.'"/>
                    </form>
                    </a>
                </div>
                <div class="accordion_'.$root_cat->Id.'">
                ';
            }
        }
        $html.="</div>";
        return $html;
    }


    /**
     * Gets all the root categories
     * @return bll_category 
     */
    public static function getRootCategories()
    {
        $val  = new bll_category(-1);
        
        return $val->findAll(
                              array(
                                  array('CategoryId','IS')
                                   ), 
                              array(
                                  array(null,null)
                                  )
                             );
    }
    
    /**
     * Gets the sef ready link to the category
     * @param type $idc
     * @param type $LangId
     * @return string sef ready link
     */
    public static function generateSEFUrl($idc, $LangId)
    {
        $str="";
        $cat = new bll_category($idc);
        $array=array();
        $array[]=$cat;
        while($cat->CategoryId > 0)
        {
            $cat = new bll_category($cat->CategoryId);
            $array[]=$cat;
            
        }
        $array = array_reverse($array);
        $i=1;
        foreach($array as $c)
        {
            $str.=$c->getLanguageValue($LangId)->Name;
            if($i < count($array))
            $str.=DS;
            $i++;
        }
        return $str;
    }



    /**
     * Get the list of category as a tree
     * @param bll_category $cats_level array of categories
     * @return string
     */
    public static function getCategoriesTreeHtmlAdmin($cats_level, $exe=0)
    {
        $LangId=  AuxTools::GetCurrentLanguageIDJoomla();
        $str='';
        $dialog='';
        if($exe<=0)
        {
            $str='
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
            <style>
            ul.li-left
            {
                list-style:none;
            }
            ul.li-left li
            {
                float:left;
            }
            </style>
            <script type="text/javascript">
                $(function() {

                    // run the effect
                    $( ".toggled" ).toggle( "blind", {}, 500 );
                    var link="";
                    $( "#dialog-confirm" ).dialog({
                                autoOpen: false,
                                resizable: false,
                                show: {
                                    effect: "blind",
                                    duration: 500
                                  },
                                  hide: {
                                    effect: "blind",
                                    duration: 300
                                  },
                                buttons: {
                                  "'.JText::_('COM_CATALOG_DELETE').'": function() {
                                    $( this ).dialog( "close" );
                                    window.location.href = link;
                                  },
                                  "'.JText::_('COM_CATALOG_CANCEL').'": function() {
                                    $( this ).dialog( "close" );
                                    return false;
                                  }
                                }
                              });
                   $( ".dialog-button" ).click(function() {
                              $( "#dialog-confirm" ).dialog( "open" );
                              link=$(this).attr("href");
                              return false;
                   });

                });
            </script>';
            $dialog='<div id="dialog-confirm" title="'.JText::_('COM_CATALOG_ASK_CATEGORY_DELETE').'">
                <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>
                '.JText::_('COM_CATALOG_WARNING_CATEGORY_DELETE').'
                </p>
              </div>';
            }

            $str.='
                <div class="row-fluid">';
            foreach($cats_level as $obj)
            {
                $childrens=$obj->getChildrenCategories();
                if(count($childrens)>0)
                {
                    $str.='
                     <script type="text/javascript">
                        $(function() {
                          // set effect from select menu value
                          $( "#button'.$obj->Id.'" ).click(function() {
                            var options = {};
                             if($(this).html()=="[+]")
                             {
                                $(this).html("[-]");
                             }
                             else
                                $(this).html("[+]");

                            // run the effect
                            $( "#effect'.$obj->Id.'" ).toggle( "blind", options, 500 );
                            return false;
                          });
                        });
                        </script>

                            ';
                }
                $str.='
                <div class="row-fluid"><ul class="li-left">';
                if(count($childrens)>0)
                $str.='<li><a id="button'.$obj->Id.'">[+]</a></li>';
                
                $str.='
                <li><form method="POST" action="./index.php?option=com_catalog&view=category&layout=edit">
                     <input type="hidden"  name="id" value="'.$obj->Id.'"/>
                     <button>'.$obj->getLanguageValue($LangId)->Name.'</button>
                </form></li>';
                $str.='
                <li><a class="dialog-button" href="./index.php?option=com_catalog&view=category&action=delete&id='.$obj->Id.'">
                [X]
                </a>
                </li></ul></div>';
                if(count($childrens)>0)
                {
                    $str.='<div id="effect'.$obj->Id.'" class="row-fluid toggled">
                            '.self::getCategoriesTreeHtmlAdmin($childrens, $exe+count($cats_level)).'
                          </div>';
                    $exe++;
                }
        }
        $str.=$dialog;
        $str.='</div>';
        return $str;
    }
    
    /**
     * Get the list of category as a tree
     * @param bll_category $cats_level array of categories
     * @return string
     */
    public static function getCategoriesTreeHtmlProductSelection($cats_level, $exe=0)
    {
        $LangId=  AuxTools::GetCurrentLanguageIDJoomla();
        $str='';
        if($exe<=0)
        {
            $str='
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
            <style>
            ul.li-left
            {
                list-style:none;
            }
            ul.li-left li
            {
                float:left;
            }
            </style>
            <script type="text/javascript">
                $(function() {

                    // run the effect
                    $( ".toggled" ).toggle( "blind", {}, 500 );
                   

                });
            </script>';
            
            }

            $str.='
                <div class="row-fluid">';
            foreach($cats_level as $obj)
            {
                $childrens=$obj->getChildrenCategories();
                if(count($childrens)>0)
                {
                    $str.='
                     <script type="text/javascript">
                        $(function() {
                          // set effect from select menu value
                          $( "#button'.$obj->Id.'" ).click(function() {
                            var options = {};
                             if($(this).html()=="[+]")
                             {
                                $(this).html("[-]");
                             }
                             else
                                $(this).html("[+]");

                            // run the effect
                            $( "#effect'.$obj->Id.'" ).toggle( "blind", options, 500 );
                            return false;
                          });
                        });
                        </script>

                            ';
                }
                $str.='
                <div class="row-fluid"><ul class="li-left">';
                if(count($childrens)>0)
                $str.='<li><a id="button'.$obj->Id.'">[+]</a></li>';

                $str.='
                <li><form method="POST" action="./index.php?option=com_catalog&view=products&layout=edit">
                     <input type="hidden"  name="cid" value="'.$obj->Id.'"/>
                     <button>'.$obj->getLanguageValue($LangId)->Name.'</button>
                </form></li>';
                $str.='
                </ul></div>';
                if(count($childrens)>0)
                {
                    $str.='<div id="effect'.$obj->Id.'" class="row-fluid toggled">
                            '.self::getCategoriesTreeHtmlProductSelection($childrens, $exe+count($cats_level)).'
                          </div>';
                    $exe++;
                }
        }
        $str.='</div>';
        return $str;
    }
    
     /**
     * Get the list of category as a tree
     * @param bll_category $cats_level array of categories
     * 
     * @return string
     */
    public static function getCategoriesTreeHtmlChangeProductCategory($cats_level, $exe=0, $lid=0)
    {
        $LangId=  AuxTools::GetCurrentLanguageIDJoomla();
        $str='';
        if($exe<=0)
        {
            $str='
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
            <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
            <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
            <style>
            ul.li-left
            {
                list-style:none;
            }
            ul.li-left li
            {
                float:left;
            }
            </style>
            <script type="text/javascript">
                $(function() {

                    // run the effect
                    $( ".toggled" ).toggle( "blind", {}, 500 );
                   

                });
            </script>';
            
            }

            $str.='
                <div class="row-fluid">';
            foreach($cats_level as $obj)
            {
                $childrens=$obj->getChildrenCategories();
                if(count($childrens)>0)
                {
                    $str.='
                     <script type="text/javascript">
                        $(function() {
                          // set effect from select menu value
                          $( "#button'.$obj->Id.'" ).click(function() {
                            var options = {};
                             if($(this).html()=="[+]")
                             {
                                $(this).html("[-]");
                             }
                             else
                                $(this).html("[+]");

                            // run the effect
                            $( "#effect'.$obj->Id.'" ).toggle( "blind", options, 500 );
                            return false;
                          });
                        });
                        </script>

                            ';
                }
                $str.='
                <div class="row-fluid"><ul class="li-left">';
                if(count($childrens)>0)
                $str.='<li><a id="button'.$obj->Id.'">[+]</a></li>';

                $str.='
                <li><form method="GET" action="./index.php">
                     <input type="hidden" name="option" value="com_catalog" />
                     <input type="hidden" name="view" value="products" />
                     <input type="hidden" name="action" value="changecategory" />
                     <input type="hidden"  name="cid" value="'.$obj->Id.'"/>
                     <input type="hidden"  name="id" value="'.$lid.'"/>
                     <button>'.$obj->getLanguageValue($LangId)->Name.'</button>
                </form></li>';
                $str.='
                </ul></div>';
                if(count($childrens)>0)
                {
                    $str.='<div id="effect'.$obj->Id.'" class="row-fluid toggled">
                            '.self::getCategoriesTreeHtmlProductSelection($childrens, $exe+count($cats_level), $lid).'
                          </div>';
                    $exe++;
                }
        }
        $str.='</div>';
        return $str;
    }

}

?>

