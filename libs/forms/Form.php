<?php

require_once BASE_DIR . LIBS . FORMS . FORMS_RECAPTCHA;
require_once BASE_DIR . LIBS . FORMS . FORMS_EDITOR;
require_once BASE_DIR . LIBS . FORMS . FORMS_RANGE_SLIDER;

/**
 * Form field generator class
 * @author Gabriel Elias González Disla
 */
class Form 
{
    /**
     * @var Form contiene la instancia de la clase
     */
    private static $instance = null;

    /**
     *
     * @var Form Array of instances
     */
    private static $instances = array();

    /**
     * Array of form fields
     * @var array
     */
    private $fields = array();

    /**
     * @var boolean true if the form generates the javascript 
     * for their fields.
     */
    private $hasScript=true;



    /**
     *
     * @var FormLayouts::FORMS_DEFAULT_LAYOUT|FormLayouts::FORMS_UL_LAYOUT|FormLayouts::FORMS_TABLE_LAYOUT|FormLayouts::FORMS_LINE_BREAK_LAYOUT  Form rendering layout
     */

    private $layout = FormLayouts::FORMS_DEFAULT_LAYOUT;

    private function __construct() 
    {
        return $this;
    }



    public function __destruct() 
    {
        self::$instance = null;
        self::$instances = null;
        unset($this);
    }



    /**
     * Obtiene la unica instancia de la clase
     *
     * @param string $name put a name to make the instance unique and permanent
     *
     * @return Form con la instancia de la clase
     */
    public static function getInstance($name = "") 
    {
        if ($name != "") 
        {
            if (isset(self::$instances[$name])) 
            {
                return self::$instances[$name];

            } 
            else 
            {
                self::$instances[$name] = new Form();
                return self::$instances[$name];
            }
        }
        if (!self::$instance) 
        {
            self::$instance = new Form();
        }
        return self::$instance;
    }



    /**
     * Gets the array of instances
     * @return Form array of form instances
     */
    public static function getInstances() 
    {
        return array_merge(self::$instances, array(self::$instance));
    }



    /**
     *
     * @param boolean true if the form generates the JS for its
     * fields.
     */
    public function setHasScript($v)
    {
        $this->hasScript=$v;
    }

    

    /**

     * @return boolean true if the form generates the JS for its

     * fields. 

     */

    public function getHasScript()
    {
        return $this->hasScript;
    }

    

    /**
     * Clear the field array
     */
    public function clear() 
    {
        $this->fields = array();
    }



    //====================================== FORM FORMATION FUNCTIONS ==========================================================//



    /**
     * Sets the field attributes of disabled, required and readonly
     *
     * @param type $required different from false if required
     * @param type $disabled different from false if disabled
     * @param type $readonly different from false if readonly
     *
     * @return string Field Status string ===> (3 params different from false)
     * 'required="required" disabled="disabled"  readonly="readonly" '
     */
    private function SetFieldStatus($required, $disabled, $readonly) {

        $html = "";
        if ($required !== false)
        {
            $html.=" required=\"required\"";
        }
        if ($disabled !== false)
        {
            $html.=" disabled=\"disabled\"";
        }
        if ($readonly !== false)
        {
            $html.=" readonly=\"readonly\"";
        }
        return $html;
    }



    /**
     * Creates a captcha field with a name 'recaptcha_response_field'
     * @param string $publickey key of the catpcha provide by
     * google reCaptcha API.
     * @param string $error error string of the captcha generated.
     *
     * @return string $html with the captcha field generated
     */
    public function GenerateCaptchaField($publickey, $error) 
    {
        $captcha = recaptcha_get_html($publickey, $error);
        $this->fields['captcha' . uniqid()] = $captcha;
    }



    /**
     * Generates a label html
     * @param type $text text of the label
     * @param type $for Specifies which form element a label is bound to
     * @param type $id Id of the label
     * @param type $class class of the label
     * @return string HTML of the label formed.
     */
    public function Label($text, $for, $id = "", $class = "", $return = false) 
    {
        $tfor = strip_tags($for);
        $tfor = str_replace('*', "", $tfor);
        $html = "<label for=\"$tfor\"";

        if ($id != "")
        {
            $html.="id=\"" . ($id) . "\"";
        }
        if ($class != "")
        {
            $html.="class=\"" . ($class) . "\" ";
        }

        $html.=" >";
        $html.=$text . "</label>
        ";
        if ($return === false)
        {
            $this->fields['label' . $for] = $html;
        }
        else
        {
            return $html;
        }
    }

    

    /**
     * Insert pure html beetwen the form
     * @param string $html html to add to the form
     * 
     */
    public function HTML($html)
    {
        $this->fields[uniqid()]=$html;
    }



    /**
     * gets the HTML of the Joomla editor area.
     * ONly works when joomla framework is up.
     *
     * @param   string   $name     The control name.
     * @param   string   $html     The contents of the text area.
     * @param   string   $width    The width of the text area (px or %).
     * @param   string   $height   The height of the text area (px or %).
     * @param   integer  $col      The number of columns for the textarea.
     * @param   integer  $row      The number of rows for the textarea.
     *
     * @return html of the editor, null on error
     */
    public function JEditor($fname, $fvalue, $width, $height, $cols, $rows) 
    {
        if (defined('_JEXEC') == true) 
        {
            $ed = JFactory::getEditor();
            $this->RegisterField($fname, $ed->display($fname, $fvalue, $width, $height, $cols, $rows));
        }

    }



    /**
     * Sets a joomla media field to the form. only works when joomla
     * Framework is up.
     * 
     * @param type $name name of the field
     * @param type $value value of the field
     * @param type $dir directory of the media field
     * @param string $id id of the field
     * @param string $class class of the field
     */
    public function JMediaField($name, $value, $dir="stories", $id="", $class="")
    {
        if (defined('_JEXEC') == true) 
        {
            $xmlfile = new SimpleXMLElement('<field name="'.$name.'" type="media" directory="'.$dir.'" />');
            $f = new JForm('temp');
            $f->load($xmlfile);
            $f->setField($xmlfile);
            $f->setFieldAttribute($name, 'id', $id);
            $f->setFieldAttribute($name, 'class', $class);
            $f->setValue($name,null, $value);
            $this->RegisterField($name, $f->getInput($name));
        }
    }

    

    /**
     * Register the field in the field array
     * 
     * @param string $fieldname name of the field
     * @param string $fieldhtml html of the field
     */
    private function RegisterField($fieldname, $fieldhtml)
    {
        $lenght=strlen($fieldname);
        if($fieldname[$lenght-1] == ']' && $fieldname[$lenght-2] == '[')
        {
            $this->fields[$fieldname.uniqid()]=$fieldhtml;
        }
        else
        {
            $this->fields[$fieldname]=$fieldhtml;
        }
    }

    

    /**
     * Generates a JQuery range slider field. JQuery 
     * Libraries must be included manually if this field is used.
     *
     * @param string $id id of the field
     * @param string $name name of the field
     * @param string $value value of the field
     * @param integer $min minimun value
     * @param integer $max maximun value
     * @param numeric $step ratio of change of the slider
     * @param string  $type type of the range field, 2 available min(Min to maximun value) or minmax(double range field)
     * @param string  $currency value of the page
     *
     * @return string
     */
    public function RangeSlider($id, $name, $value, $min = 0, $max = 100, $step=1, $type = "min", $currency = "US$") 
    {
        $rfield = new FormRangeSlider($id, $name, $value, $min, $max, $type, $currency, $step);
        $html=$rfield->getHtml();
        if($this->hasScript === false)
        {
            $arr=explode("</script>", $html);
            $html=$arr[1];
        }
        $this->fields[$name] = $html;
    }

    

    /**
     * Creates a Date Field with Jquery DateTimePicker plugin v2.2.9. JQuery 
     * Libraries must be included manually if this field is used.
     * 
     * @param string  $name name of the field
     * @param string  $value value of the field
     * @param string  $id id of the field
     * @param string  $class class of the field
     * @param boolean $required true if the field is required
     * @param string  $format date Format datetime "Y-m-d" by default
     * @param array   $language_code language code 2 chars standard code en, es, de, fr, etc.
     * 
     * @preserve jQuery DateTimePicker plugin v2.2.9
     * @homepage http://xdsoft.net/jqplugins/datetimepicker/
     * 
     */
    public function Date($name, $value, $id, $class = "", $required = false, $format="Y-m-d", $language_code="en")
    {
        $html = "<input type=\"text\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, false, false);
        $html.="/>";
        $script='<script>
        $(function() {
            $( "#'.$id.'" ).datetimepicker({ format:"'.$format.'", lang:"'.$language_code.'", timepicker:false,});
        });
        </script>
        ';
        $this->fields[$name] = $script.$html;
    }
    
    /**
     * Creates a DateTime Field with Jquery DateTimePicker plugin v2.2.9. JQuery 
     * Libraries must be included manually if this field is used.
     * 
     * @param string  $name name of the field
     * @param string  $value value of the field
     * @param string  $id id of the field
     * @param string  $class class of the field
     * @param boolean $required true if the field is required
     * @param string  $format datetime format "Y-m-d H:i" by default.
     * @param array   $language_code language code 2 chars standard code en, es, de, fr, etc.
     * 
     * @preserve jQuery DateTimePicker plugin v2.2.9
     * @homepage http://xdsoft.net/jqplugins/datetimepicker/
     * 
     */
    public function DateTime($name, $value, $id, $class = "", $required = false, $format="Y-m-d H:i", $language_code="en")
    {
        $html = "<input type=\"text\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, false, false);
        $html.="/>";
        $script='<script>
        $(function() {
            $( "#'.$id.'" ).datetimepicker({ format:"'.$format.'", lang:"'.$language_code.'"});
        });
        </script>
        ';
        $this->fields[$name] = $script.$html;
    }



    /**
     * Generates a tinymce editor.
     * 
     * @param string  $elemname name of the field
     * @param string  $value value of the field
     * @param string  $elemid id of the field
     * @param string  $tinyclass class of the html
     * @param boolean $required true if the field is required
     * @param string  $tinyjsurl url of the js lib of tinymce look for
     * the file. the default value is the path for the administrator section
     * @param integer $cols number of columns
     * @param integer $rows number of rows
     *
     */
    public function Editor($elemname, $value, $elemid='default_tiny_id', $tinyclass='default_tiny_class', $required = false, $tinyjsurl = "libs/js/tinymce/tiny_mce.js", $cols = 75, $rows = 15) 
    {
        $editor = FormEditor::getInstance($elemname, $elemid, $tinyclass, $value, $required, $tinyjsurl, $cols, $rows);
        $jspath = AuxTools::getJSPathFromPHPDir(BASE_DIR);
        if (is_array($editor) != true) 
        {
            $load_script = '
                <script type="text/javascript" src="' . $jspath . LIBS . JS . TINYMCE . TINYMCE_RAW. '"></script>';
                $html=$editor->getHtml();
                if($this->hasScript === false)
                {
                    $arr=explode("</script>", $html);
                    $html=$arr[1];
                }
            $this->fields[$elemname] = $load_script . $editor->getHtml();
        } 
        else 
        {
            if (isset($editor['notnew'])) 
            {
                $html=$editor['notnew']->getHtml();
                if($this->hasScript === false)
                {
                    $arr=explode("</script>", $html);
                    $html=$arr[1];
                }
                $this->fields[$elemname] = $html;
            }
        }
    }

    

    /**
     * Generates a phone masked text field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field  ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     */
    public function Phone($name, $value, $id, $class = "", $required = false, $event_hash = array(), $disabled = false, $readonly = false)
    {
        $script='
        <script>    
        jQuery(function($){
            $("#'.$id.'").mask("(999) 999-9999");
        });
        </script>
        ';
        $html = "<input type=\"text\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);

        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields[$name] = $script.$html;
    }

    

    /**
     * Generates a phone masked text field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field  ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     */
    public function PhoneWithExt($name, $value, $id, $class = "", $required = false, $event_hash = array(), $disabled = false, $readonly = false)
    {
        $script='
        <script>    
        jQuery(function($){
            $("#'.$id.'").mask("(999) 999-9999? x99999");
        });
        </script>
        ';
        $html = "<input type=\"text\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);

        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields[$name] = $script.$html;
    }



    /**
     * Generates an input hidden field
     *
     * @param string $name name of the field
     * @param string $value value of it
     * @param string $id Id of the field
     * @param string $class Class of the field
     *
     * @return string of the field formed.
     */
    public function Hidden($name, $value, $id = "", $class = "") 
    {
        $html = "<input type=\"hidden\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" />";
        $this->fields[$name] = $html;

    }

    /**
     * Generates an input file field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param string  $accept Accept atribute of the file field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field  ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     * @return string of the field formed.
     */
    public function File($name, $value, $id = "", $class = "", $accept = "", $required = false, $event_hash = array(), $disabled = false, $readonly = false) 
    {
        $html = "<input type=\"file\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);
        if ($accept != "")
        {
            $html.= "accept=\"$accept\" ";
        }
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields[$name] = $html;
    }



    /**
     * Generates an input text field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field  ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     */
    public function Text($name, $value, $id = "", $class = "", $required = false, $event_hash = array(), $disabled = false, $readonly = false) 
    {
        $html = "<input type=\"text\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);

        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields[$name] = $html;
    }



    /**
     * Generates an input text field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     * @return string of the field formed.
     */
    public function Password($name, $value, $id = "", $class = "", $required = false, $event_hash = array(), $disabled = false, $readonly = false) 
    {

        $html = "<input type=\"password\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);

        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields[$name] = $html;
    }



    /**
     * Generates a checkbox field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $checked different from false the value is checked
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly true if the field is readonly
     * @param boolean $disabled true if the field is disabled
     *
     * @return string of the field formed.
     */
    private function Checkbox($name, $value, $id = "", $class = "", $checked = false, $required = false, $event_hash = array(), $readonly = false, $disabled = false) 
    {
        $html = "<input type=\"checkbox\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);
        if ($checked !== false)
        {
            $html.="checked=\"checked\"";
        }
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        return $html;
    }



    /**
     * Generates a radiobutton field
     *
     * @param string  $name name of the field
     * @param string  $value value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $checked different from false the value is checked
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     * @return string of the field formed.
     */
    private function Radiobutton($name, $value, $id = "", $class = "", $checked = false, $required = false, $event_hash = array(), $readonly = false, $disabled = false) 
    {
        $html = "<input type=\"radio\" name=\"$name\" value=\"$value\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);
        if ($checked !== false)
        {
            $html.="checked=\"checked\"";
        }
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        return $html;
    }

    /**
     * Generates a checkboxes field
     *
     * @param string  $name name of the field
     * @param array   $values array of the options.
     * @example example of parm $value array(value => text), when
     * value has the '#__' prefix is a checked box.
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     * @return null|string returns null if the $name variables are not defined
     * else the string of the field formed.
     */
    public function Checkboxes($name, $values, $id = "", $class = "", $event_hash = array(), $readonly = false, $disabled = false) 
    {
        $html = "";
        $html.="<ul class=\"$name-checkboxes\">";
        if (count($values) > 1)
        {
            $name.="[]";
        }
        foreach ($values as $va => $te) 
        {
            $html.= "<li>";
            if (stripos($va, '#__') !== false) 
            {
                $var = str_replace("#__", " ", $va);
                $var = trim($var);
                $html.=$this->Checkbox($name, $var, $id, $class, true, false, $event_hash, $readonly, $disabled);
            } 
            else 
            {
                $html.=$this->Checkbox($name, $va, $id, $class, false, false, $event_hash, $readonly, $disabled);
            }
            $html.=$this->Label($te, $te, "$id-label", "form-generator-child-label-field", true);
            $html.= "</li>";
        }
        $html.="</ul>";
        $this->fields[$name] = $html;
    }



    /**
     * Generates a radiobuttons field
     *
     * @param string  $name name of the field
     * @param array   $values array of the options.
     * @example example of parm $value array(value => text), when
     * value has the '#__' prefix is a checked box.
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     * @return null|string returns null if the $name variables are not defined
     * else the string of the field formed.
     */

    public function Radiobuttons($name, $values, $id = "", $class = "", $event_hash = array(), $readonly = false, $disabled = false) 
    {
        $html = "";
        $html.="<ul class=\"$name-radiobuttons\">";
        foreach ($values as $va => $te) 
        {
            $html.= "<li>";
            if (stripos($va, '#__') !== false) 
            {
                $var = str_replace("#__", " ", $va);
                $var = trim($var);
                $html.=$this->Radiobutton($name, $var, $id, $class, true, false, $event_hash, $readonly, $disabled);
            } 
            else 
            {
                $html.=$this->Radiobutton($name, $va, $id, $class, false, false, $event_hash, $readonly, $disabled);
            }
            $html.=$this->Label($te, $te, "$id-label", "form-generator-child-label-field", true);
            $html.= "</li>";
        }
        $html.="</ul>";
        $this->fields[$name] = $html;
    }



    /**
     * Generates a option field for the form
     *
     * @param string  $value value of the option tag
     * @param string  $text  string with text for the option tag
     * @param boolean $selected true if the option tag is selected.
     *
     * @return string returns a string the option formed.
     */
    private function Option($index, $text, $selected = false) 
    {
        $html = "<option ";
        if ($selected == true)
        {
            return $html . "value=\"$index\" selected > $text </option>";
        }
        return $html . "value=\"$index\" > $text </option>";
    }

    private function Options($options, $isgroup = false, $groupname = "") 
    {
        $ohtml = "";
        if ($isgroup !== false)
        {
            $ohtml.="<optgroup label=\"$groupname\">";
        }
        foreach ($options as $option => $text) 
        {
            if (is_array($text) == false) 
            {
                if (stripos($option, "#__") !== false) 
                {
                    $ohtml.="
                        ";
                    $var = str_replace("#__", "", $option);
                    $var = trim($var);
                    $ohtml.=$this->Option($var, $text, true);
                    $ohtml.="
                        ";

                } 
                else 
                {

                    $ohtml.="
                        ";
                    $ohtml.=$this->Option($option, $text);
                    $ohtml.="
                        ";
                }

            }
            else
            {
                $ohtml.=$this->Options($text, true, $option);
            }
        }
        if ($isgroup !== false)
        {
            $ohtml.="</optgroup>";
        }
        return $ohtml;
    }

    /**
     * Generates a select field for the form
     *
     * @param string  $name name of the field
     * @param array   $values array of the options.
     * @example example of parm $value array(value => text), when
     * value has the '#__' prefix is a checked box.
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required different from false the field is required
     * @param array   $event_hash array of events of the field ex: array('event'=>'JS-code')
     * @param boolean $readonly different from false is readonly
     * @param boolean $disabled different from false is disabled
     *
     * @return string with the select formed.
     */
    public function SelectBox($name, $values, $id = "", $class = "", $required = false, $event_hash = array(), $readonly = false, $disabled = false) 
    {
        $html = "<select name=\"$name\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $ohtml = $this->Options($values);
        $this->fields[$name] = $html . "> $ohtml</select>";
    }



    /**
     * Generates an input submit field
     *
     * @param string  $text value of it
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param array   $event_hash array of events of the field
     * @param boolean $disabled true if the field is disabled
     *
     * @return null|string returns null if the $name variables are not defined
     * else the string of the field formed.
     */
    public function Submit($text, $id = "", $class = "", $event_hash = array(), $disabled = false)
    {

        $html = "<input type=\"submit\" value=\"$text\" id=\"$id\" class=\"$class\" ";
        $html.=$this->SetFieldStatus(false, $disabled, false);
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.=" $event=\"$jscode\" ";
            }
        }
        $html.="/>";
        $this->fields['submit' . uniqid()] = $html;
    }

    

    /**
     * Generates an form button
     * 
     * @param string $text Text of the button
     * @param string $name button name
     * @param string $id button id
     * @param string $class button class
     * @param string $url new action for the form
     * @param string $type type of the button. button, submit, reset
     */
    public function Button($text, $name='', $id='btn', $class='class', $url=null, $type='button')
    {
        if($url === null)
        {
            $html='<button name="'.$name.'" id="'.$id.'" class="'.$class.'" type="'.$type.'">'.$text.'</button>';
        }
        else
        {
            $html='<button formaction="'.$url.'" name="'.$name.'" id="'.$id.'" class="'.$class.'" type="'.$type.'">'.$text.'</button>';
        }
        $this->fields[$name . uniqid()] = $html;
    }
    
    /**
     * 
     * Generates a form link button
     * 
     * @param string $text Text of the button
     * @param string $url new action for the form
     * @param string $name button name
     * @param string $id button id
     * @param string $class button class
     * @param string $ico_html icon html if the button has one
     * 
     */
    public function LinkButton($text, $url, $name='', $id='btn', $class='class', $ico_html="")
    {
        $html='<button name="'.$name.'" id="'.$id.'" class="'.$class.'" type="button"><a href="'.$url.'" id="'.$id.'_link" class="'.$class.'_link">'.$ico_html.$text.'</a></button>';
        $this->fields[$name . uniqid()] = $html;
    }



    /**
     * Generates a textarea field for the form
     *
     * @param string  $name name of the field
     * @param string  $text string with the textarea text.
     * @param integer $cols number of columns of the textarea
     * @param integer $rows number of rows of the textarea
     * @param string  $id Id of the field
     * @param string  $class Class of the field
     * @param boolean $required true if the field is required
     * @param array   $event_hash array of events of the field
     * @param boolean $readonly true if the field is readonly
     * @param boolean $disabled true if the field is disabled
     * @return null|string returns null if hash is null or return an string with
     * the textarea formed.
     */
    public function TextArea($name, $text, $id = "", $class = "", $cols = 20, $rows = 5, $required = false, $event_hash = array(), $readonly = false, $disabled = false) 
    {

        if (is_nan($cols) === true)
        {
            $cols = 20;
        }
        if (is_nan($rows) === true)
        {
            $rows = 5;
        }
        $html = "";
        $html = "<textarea name=\"$name\" id=\"$id\" class=\"$class\"  ";
        $html.=$this->SetFieldStatus($required, $disabled, $readonly);
        if (is_array($event_hash) == true)
        {
            foreach ($event_hash as $event => $jscode) 
            {
                $html.="$event=\"$jscode\"";
            }
        }
        $html.=">";
        $html.=$text;
        $html.="</textarea>";
        $this->fields[$name] = $html;
    }



    //===================================== END FORM FORMATION FUNCTIONS ==========================================================//
    //===================================== FORM RENDERING FUNCTIONS ==========================================================//
    /**
     * @param string  $action uri of the action of the form.
     * @param array   $event_hash array of events of the field  ex: array('event'=>'JS-code')
     * @param boolean $upload true if the form uploads files
     * @param string  $name   form name
     * @param string  $method form data transfer method(POST, GET, REQUEST)
     * @param string  $id     form id
     * @param string  $class  form_class
     *
     * @return string|null string with the form html, null on error.
     */
    public function Render($action = '', $event_array = array(), $upload = false, $name = 'form_', $method = 'POST', $id='form_id', $class='form_class') 
    {
        $fname = '';
        if ($name == 'form_')
        {
            $fname = $name . uniqid();
        }
        else
        {
            $fname = $name;
        }
        $html = $this->OpenStructure($fname, $action, $method, $upload, $id, $class, $event_array);
        if ($html === null)
        {
            return $html;
        }
        switch ($this->layout) 
        {
            case FormLayouts::FORMS_DEFAULT_LAYOUT:
                $html .= "<div id=\"$fname\">";
                foreach ($this->fields as $f_name => $field_html) 
                {
                    if (strpos($f_name, 'label') !== false)
                    {
                        $html .= "<div>";
                    }
                    $html.=$field_html;
                    if (strpos($f_name, 'label') === false)
                    {
                        $html .= "</div>";
                    }
                }
                $html .= "</div>";
                break;
            case FormLayouts::FORMS_UL_LAYOUT:
                $html .= "<ul id=\"$fname\" style=\"list-style:none;\">";
                foreach ($this->fields as $f_name => $field_html) 
                {
                    $html .= "<li>";
                    $html.=$field_html;
                    $html .= "</li>";
                }
                $html .= "</ul>";
                break;

            case FormLayouts::FORMS_TABLE_LAYOUT:
                $html .= "<table id=\"$fname\">";
                foreach ($this->fields as $f_name => $field_html) 
                {
                    if (strpos($f_name, 'label') !== false)
                    {
                        $html .= "<tr>";
                    }
                    $html .= "<td>";
                    $html.=$field_html;
                    $html .= "</td>";
                    if (strpos($f_name, 'label') === false)
                    {
                        $html .= "</tr>";
                    }
                }
                $html .= "</table>";
                break;

            case FormLayouts::FORMS_LINE_BREAK_LAYOUT:
                $index = 1;
                foreach ($this->fields as $f_name => $field_html) 
                {

                    $html.=$field_html;
                    if (strpos($f_name, 'label') === false)
                    {
                        $html.="<br/>";
                    }
                    $index++;

                }
                break;

            case FormLayouts::FORMS_BUTTON_ROW_LAYOUT:
                echo "<style>div#$fname { display: inline-block; }</style>";
                $html .= "<table id=\"$fname\">";
                $html .= "<tr>";
                foreach ($this->fields as $f_name => $field_html) 
                {
                    $html .= "<td>";
                    $html .= $field_html;
                    $html .= "</td>";
                }
                $html .= "</tr>";
                $html .= "</table>";
                break;

            default:

                break;
        }
        $html .= $this->CloseStructure();
        $this->clear();
        return $html;
    }
    
    /**
     *
     * @return string|null string with the form html, null on error.
     */
    public function renderFields() 
    {
        $html="";
        foreach ($this->fields as $f_name => $field_html) 
        {
            $html.=$field_html;
        }
        $this->clear();
        return $html;
    }

    /**
     * Creates the close of the group of elements tag structure in html
     * with a form tag within
     *
     * @return string html text with the close form structure
     */
    private static function CloseStructure() 
    {
        $html = "
                    </form>
               </div>
                        ";
        return $html;

    }

    /**
     * Creates the close of the group of elements tag structure in html
     * with a form tag within
     *
     * @param string  $name string with the name of the object.
     * @param string  $action uri of the action of the form.
     * @param string  $method form data transfer method(POST, GET)
     * @param boolean $upload true if the form will have uploads
     * fields.
     * @param string  $id     form id
     * @param string  $class  form_class
     * @param array $eventhash array of events
     *
     * @return string html text with the open form structure
     */
    private function OpenStructure($name, $action, $method, $upload, $id, $class, $eventhash = array()) 
    {

        $html = "<div id=\"$name-holder\">";
        $method = strtoupper($method);
        if ($method != 'POST' && $method != 'GET')
        {
            return null;
        }
        if ($upload != true) 
        {
            $enctype = "application/x-www-form-urlencoded";
        } 
        else 
        {
            $enctype = "multipart/form-data";
        }
        $formopentag = "<form name=\"$name\" id=\"$id\" class=\"$class\" action=\"$action\" enctype=\"$enctype\" method=\"$method\" accept-charset=\"UTF-8\"";

        if (is_array($eventhash) == true) 
        {
            foreach ($eventhash as $e => $js) 
            {
                $formopentag.=" $e=\"$js\"";
            }
            $formopentag.=">";
        }
        else
        {
            $formopentag.=">";
        }
        return $html . $formopentag;
    }



    //===================================== END OF FORM RENDERING FUNCTIONS =======================================================//
    /**
     *
     * @param int $layout Constant from FormLayouts class.
     * @return void
     */
    public function setLayout($layout) 
    {
        $layout = (int) $layout;
        switch ($layout) 
        {
            case FormLayouts::FORMS_DEFAULT_LAYOUT:
                $this->layout = FormLayouts::FORMS_DEFAULT_LAYOUT;
                break;

            case FormLayouts::FORMS_UL_LAYOUT:
                $this->layout = FormLayouts::FORMS_UL_LAYOUT;
                break;

            case FormLayouts::FORMS_TABLE_LAYOUT:
                $this->layout = FormLayouts::FORMS_TABLE_LAYOUT;
                break;

            case FormLayouts::FORMS_LINE_BREAK_LAYOUT:
                $this->layout = FormLayouts::FORMS_LINE_BREAK_LAYOUT;
                break;

            case FormLayouts::FORMS_BUTTON_ROW_LAYOUT:
                $this->layout = FormLayouts::FORMS_BUTTON_ROW_LAYOUT;
                break;

            default:
                $this->layout = FormLayouts::FORMS_DEFAULT_LAYOUT;
                break;
        }
    }

}

class FormLayouts 
{
    const FORMS_DEFAULT_LAYOUT = 0;
    const FORMS_UL_LAYOUT = 1;
    const FORMS_TABLE_LAYOUT = 2;
    const FORMS_LINE_BREAK_LAYOUT = 3;
    const FORMS_BUTTON_ROW_LAYOUT = 4;
}

