<?php
if (!defined('BASE_DIR'))
    define('BASE_DIR', dirname(dirname(__FILE__)));

//if (!defined('DS'))
    //define('DS', DIRECTORY_SEPARATOR);


define('INCLUDES', 'includes.php');

define('FACTORY', 'factory.php');
define('DEFAULT_CONVERTION_RATE', 1.0);
define('DEFAULT_CURRENCY', 'US$');
define('NUMBER_ELEMENTS_BY_PAGE', 10);

define('_EXEC', 1);
define('JS_PREFOLDER', './');
define('CONSTANTS', 'Constants.php');
define('PHP_EXT', '.php');
define('FORM_TOKEN', 100);
define('SESSION_LENGTH', 1800);
define('LIB_TIMEZONE', 'America/Puerto_Rico');

/* DAL/BLL LAYERS and views for components */

define('MODELS', '/models');
define('CONTROLLERS', '/controllers/');
define('DATA', '/data');
define('LOGIC', '/logic');
define('VIEWS', '/views/');
define('TEMPT', 'templates/');
define('TMPL', 'tmpl/');

/* ROOT folders */
define('COMPONENTS', '/components/');
define('LANGS', '/languages/');
define('LIBS', '/libs/');
///javascript files

define('JS', 'js/');
define('JQUERY_CSS', 'css/smoothness/');
define('JQUERY_UI_CSS', 'jquery-ui-1.10.4.css');
define('JQUERY_UI_CSS_MIN', 'jquery-ui-1.10.4.min.css');
define('JQUERY_UI', 'ui/');
define('JQUERY_UI_CORE', 'jquery-ui-1.10.4.js');
define('JQUERY_UI_CORE_MIN', 'jquery-ui-1.10.4.min.js');
define('TINYMCE', 'tinymce/');
define('TINYMCE_RAW', 'tiny_mce_src.js');
define('TINYMCE_JQUERY', 'jquery.tinymce.js');
define('CAROUSEL', 'Carousel/');
define('CAROUSEL_JQUERY_MOUSEWHEEL', 'helper-plugins/jquery.mousewheel.min.js');
define('CAROUSEL_JQUERY_TOUCHSWIPE', 'helper-plugins/jquery.touchSwipe.min.js');
define('CAROUSEL_JQUERY_DEBOUNCE', 'helper-plugins/jquery.ba-throttle-debounce.min.js');
define('CAROUSEL_JQUERY_LIB', 'jquery.carouFredSel-6.1.0-packed.js');
define('SCROLLER', 'scroller/');
define('SCROLLER_CSS', 'smoothDivScroll.css');
define('SCROLLER_JQUERY', 'jquery.smoothDivScroll-1.3.js');
define('SCROLLER_JQUERY_KINETIC', 'jquery.kinetic.js');
define('SCROLLER_JQUERY_MOUSEWHEEL', 'jquery.mousewheel.min.js');
define('MASKED_INPUTS_JQUERY', 'jquery.maskedinput.min.js');
define('pagination', 'pagination/');
define('DOMHELP', 'DOMhelp.js');
define('PAGINATION', 'pagination.js');
define('SLIDESHOW', 'slideshow/');
define('SLIDES_CAPTION', 'caption/');
define('SLIDES_LOADING_IMG', 'loading.gif');
define('SLIDES_CSS', 'global.css');
define('SLIDES_JQUERY', 'slides.jquery.js');
define('JQUERY', 'jquery-1.10.2.js');
define('DATE_TIME_JS', 'datetime/jquery.datetimepicker.js');
define('DATE_TIME_CSS', 'datetime/jquery.datetimepicker.css');
define('TINY_INPUT_JS', 'tokeninput/jquery.tokeninput.js');
define('TINY_INPUT_CSS', 'tokeninput/token-input-mac.css');

//CONFIG FILE NAME HERE
define('CONFIG_FILE', 'config.php');

//Lib Classes
define('LANGUAGES', 'languages.php');
if (!defined('MENU'))
{
    define('MENU', 'menu.php');
}
define('STYLES', 'styles/');
define('STYLE', 'styles.css');
define('MAIL', 'mail/');
define('PHPMAILER', 'phpmailer/');
define('PHP_MAILER', 'phpmailer.php');
define('MAILER', 'Mailer.php');
define('MAIL_HELPER', 'MailHelper.php');
define('DEVELOPERS', 'developers/');
define('ECHO_TOOLS', 'EchoTools.php');
define('IMAGES', 'images/');
define('IMAGEGENERATOR', 'ImageGenerator.php');
define('IMGS', 'imgs/');
define('CALIMG', 'cal.gif');
define('FILES', 'files/');
define('ODIRECTORY', 'oDirectory.php');
define('FILEUPLOADER', 'FileUploader.php');
define('HTML', 'html/');
define('HTML_GENERATOR', 'HtmlGenerator.php');
define('HTML_PURIFIER', 'htmlpurifier/HTMLPurifier.safe-includes.php');

/* PHP Libraries folders */
define('DB', 'db/');

/* database files */
define('DBPROVIDER', 'dbprovider.php');
define('DBOBJECT', 'dbobject.php');
define('DBINSTANCIATOR', 'dbinstanciator.php');
define('DBTABLE', 'dbtable.php');
define('DBQUERY', 'dbquery.php');



define('FORMS', 'forms/');
/* Forms files */
define('FORM', 'Form.php');
define('FORMS_EDITOR', 'FormEditor.php');
define('FORMS_RANGE_SLIDER', 'FormRangeSlider.php');
define('FORMS_OBJECT_LINKER', 'FormObjectLinker.php');
define('FORMS_RECAPTCHA', 'recaptchalib.php');
define('AJAX_LIBS', 'ajax/');
define('XMLRESPONSE', 'class.xmlresponse.php');
define('AJAX_JS_LIB', 'js/ajaxrequest.js');
define('AJAX_VALIDATOR', 'AjaxValidator.php');
define('TOOLS', 'tools/');

/* Tools files */
define('VALIDATOR', 'Validator.php');
define('PHP_OS_DETECTOR', 'PHPOSdetector.php');
define('AUXT', 'AuxTools.php');
define('SYS_CONFIG', 'systemconfig.php');
define('TEMPLATES', '/tmpl/');
define('CSS', 'css/');

//define

//Languages tags

define('LANG_TAG_EN_US', '_EN_AU');
define('LANG_TAG_EN_AU', '_EN_AU');
define('LANG_TAG_EN_GB', '_EN_GB');
define('LANG_TAG_ES_ES', '_ES_ES');
define('LANG_TAG_ES_MX', '_ES_MX');
define('LANG_TAG_DE_DE', '_DE_DE');
define('LANG_TAG_DE_CH', '_DE_CH');
define('LANG_TAG_IT_IT', '_IT_IT');
define('LANG_TAG_FR_FR', '_FR_FR');
define('LANG_TAG_FR_CA', '_FR_CA');
define('LANG_TAG_FR_BE', '_FR_BE');
define('LANG_TAG_FR_CH', '_FR_CH');

//Languages Names
define('LANG_NAME_EN_US', 'English');
define('LANG_NAME_EN_AU', 'English');
define('LANG_NAME_EN_GB', 'English');
define('LANG_NAME_ES_ES', 'Español');
define('LANG_NAME_ES_MX', 'Español');
define('LANG_NAME_IT_IT', 'Italiano');
define('LANG_NAME_FR_FR', 'Français');
define('LANG_NAME_FR_CA', 'Français');
define('LANG_NAME_FR_BE', 'Français');
define('LANG_NAME_FR_CH', 'Français');
define('LANG_NAME_DE_DE', 'Deustch');
define('LANG_NAME_DE_CH', 'Deustch');