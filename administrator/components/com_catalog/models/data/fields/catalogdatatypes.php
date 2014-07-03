<?php

if(class_exists('form')!= true)
{
    if(!defined('BASE_DIR'))
    {
        require_once dirname(dirname(dirname(dirname(__FILE__)))).'/libs/defines.php';
    }
    require_once BASE_DIR.LIBS.INCLUDES;
}

/**
 * Description of products_datatypes
 *
 * @author Gabriel
 */
class catalogdatatypes {

  /**
   * Types of form fields available
   * @var array "NameofDatatype"=>"1". 1 => multiple, 0 => non-multiple
   */
  protected static $fieldtypes = array("numeric" => 0, "text" => 0, "date" => 0, "textarea" => 0, "editor" => 0,
      "selectbox" => 1, "checkbox" => 1, "radiobutton" => 1, "mediafield"=>0, "numericrange" => 0, "numericrange2" => 0);

  public static $hasScript=true;
  
  /**
   * returns the datatype array
   * @return array
   */
  public static function getFieldTypes() {
    return self::$fieldtypes;
  }

  /**
   * Obtiene si un DATATYPE es multiple field.
   *
   * @param string $name  con el NOMBRE del datatype
   * @return boolean TRUE si es multiplefield, FALSE caso contrario.
   */
  public static function IsMultipleField($name) {
    if (array_key_exists($name, self::$fieldtypes) != true)
      return false;
    if (self::$fieldtypes[$name] == 1) {
      return true;
    }
    return false;
  }
  
  /**
   * Obtiene si un DATATYPE es multiple field.
   *
   * @param string $name  con el NOMBRE del datatype
   * @return boolean TRUE si es un field, FALSE caso contrario.
   */
  public static function IsField($name) {
    if (array_key_exists($name, self::$fieldtypes) != true)
      return false;
    else
        return true;
  }

  /**
   * returns the datatype array
   * @param string $selected name of the selected element
   * @return array
   */
  public static function getFieldTypesForSelect($selected = null) {
    $arr = array();
    foreach (self::$fieldtypes as $key => $val) {
      if ($selected == $key)
        $arr["#__" . $key] = $key;
      else
        $arr[$key] = $key;
    }
    return $arr;
  }

  public static function PreviewField($fieldname, $labelname) {
    $html = "<div class=\"FieldPreview\">";
    $temp = "";
    if (array_key_exists($fieldname, self::$fieldtypes) == true) {
      $temp.=self::previewFormField($fieldname);
      if ($temp == "")
        return false;
      $html.="<table><tr><td valign=\"top\"><label>$labelname :</label></td><td valign=\"top\">";
      $html.=$temp . "</td></tr></table>";
    }
    else
      return false;
    
    $html.="</div>";
    return $html;
  }

  private static function previewFormField($fieldname) {
      $form = form::getInstance();
      $form->setHasScript(self::$hasScript);
    switch ($fieldname) {
      case "numeric":
        $form->Text("A", 250);
        break;
      case "text":
        $form->Text("A", "Example");
        break;
      case "textarea":
        $form->TextArea("A", "&lt;p&gt;
					This is some example text that you can edit inside the &lt;strong&gt;TinyMCE editor&lt;/strong&gt;.
				&lt;/p&gt;
				&lt;p&gt;
				Nam nisi elit, cursus in rhoncus sit amet, pulvinar laoreet leo. Nam sed lectus quam, ut sagittis tellus. Quisque dignissim mauris a augue rutrum tempor. Donec vitae purus nec massa vestibulum ornare sit amet id tellus. Nunc quam mauris, fermentum nec lacinia eget, sollicitudin nec ante. Aliquam molestie volutpat dapibus. Nunc interdum viverra sodales. Morbi laoreet pulvinar gravida. Quisque ut turpis sagittis nunc accumsan vehicula. Duis elementum congue ultrices. Cras faucibus feugiat arcu quis lacinia. In hac habitasse platea dictumst. Pellentesque fermentum magna sit amet tellus varius ullamcorper. Vestibulum at urna augue, eget varius neque. Fusce facilisis venenatis dapibus. Integer non sem at arcu euismod tempor nec sed nisl. Morbi ultricies, mauris ut ultricies adipiscing, felis odio condimentum massa, et luctus est nunc nec eros.
				&lt;/p&gt;"
                        );
        break;
      case "editor":
        $form->Editor("A", "&lt;p&gt;
					This is some example text that you can edit inside the &lt;strong&gt;TinyMCE editor&lt;/strong&gt;.
				&lt;/p&gt;
				&lt;p&gt;
				Nam nisi elit, cursus in rhoncus sit amet, pulvinar laoreet leo. Nam sed lectus quam, ut sagittis tellus. Quisque dignissim mauris a augue rutrum tempor. Donec vitae purus nec massa vestibulum ornare sit amet id tellus. Nunc quam mauris, fermentum nec lacinia eget, sollicitudin nec ante. Aliquam molestie volutpat dapibus. Nunc interdum viverra sodales. Morbi laoreet pulvinar gravida. Quisque ut turpis sagittis nunc accumsan vehicula. Duis elementum congue ultrices. Cras faucibus feugiat arcu quis lacinia. In hac habitasse platea dictumst. Pellentesque fermentum magna sit amet tellus varius ullamcorper. Vestibulum at urna augue, eget varius neque. Fusce facilisis venenatis dapibus. Integer non sem at arcu euismod tempor nec sed nisl. Morbi ultricies, mauris ut ultricies adipiscing, felis odio condimentum massa, et luctus est nunc nec eros.
				&lt;/p&gt;", 'editorid', 'editorpreview');
        break;
      case "numericrange":
        $form->RangeSlider("T", "A", 10);
        break;
      case "numericrange2":
        $form->RangeSlider("T", "", 10, 0, 100, 5, "minmax");
        break;
      case "date":
        $form->Date("A", "", "Date");
          break;
      case "selectbox":
        $html = "";
        $html.="<select name=\"select[]\">";
        for ($i = 1; $i <= 10; $i++) {
          $html.="<option value=\"$i\">Test$i</option>";
        }
        $html.="</select>";
        return $html;
        break;
      case "radiobutton":
        $html = "";
        $html.="<ul style=\"list-style:none; margin:0px; padding:0px;\">";
        for ($i = 1; $i <= 5; $i++) {
          $html.="<li><input type=\"radio\" name=\"radio\" value=\"$i\" /><label>Test$i</label></li>";
        }
        $html.="</ul>";
        return $html;
        break;
      case "checkbox":
        $html = "";
        $html.="<ul style=\"list-style:none; margin:0px; padding:0px;\">";
        for ($i = 1; $i <= 5; $i++) {
          $html.="<li><input type=\"checkbox\" name=\"check[]\" value=\"$i\" /><label>Test$i</label></li>";
        }
        $html.="</ul>";
        return $html;
        break;
      default :
          return null;
        break;
    }
    return $form->Render();
  }

  public static function TypeofFieldToText($name) {
    switch ($name) {
      case"numeric":
        return array("text", array("onchange" => " validate" . $name . "field();"));
        break;

      case"money":
        return array("text", array("onchange" => " validate" . $name . "field();"));
        break;

      default :
        return null;
        break;
    }
  }

}

?>