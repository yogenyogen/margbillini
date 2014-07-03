<?php
$jspath = AuxTools::getJSPathFromPHPDir(BASE_DIR); 
/**
 * @version		$Id: default.php 15 2009-11-02 18:37:15Z chdemko $
 * @package		Joomla16.Tutorials
 * @subpackage	Components
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @author		Christophe Demko
 * @link		http://joomlacode.org/gf/project/helloworld_1_6/
 * @license		License GNU General Public License version 2 or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$ro=JFactory::getApplication()->getPathway();
$path = "";
foreach($ro->getPathwayNames() as $p)
{
    $path.=DS.AuxTools::SEFReady($p);
}
$LangId = AuxTools::GetCurrentLanguageIDJoomla();
$category = new bll_category(0);
$root_categories = bll_category::getRootCategories();
?>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY19; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_UI_CORE; ?>"></script>
<link rel="stylesheet" href="<?php echo $jspath . LIBS . JS . JQUERY_UI . JQUERY_CSS . JQUERY_UI_CSS; ?>" /> 
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . MASKED_INPUTS_JQUERY; ?>"></script>
<script type="text/javascript" src="<?php echo $jspath . LIBS . JS . TINYMCE . TINYMCE_JQUERY; ?>"></script>

<?php 
if(count($root_categories) > 0):    
?>
<div id="category_block">
	<div id="categories">
		<ul id="cat-list">
	        <?php
	        $index=1;
	        foreach($root_categories as $rcat):
	            
                    $lval=$rcat->getLanguageValue($LangId);
                    $url=$path.DS.AuxTools::SEFReady(bll_category::generateSEFUrl($rcat->Id, $LangId)).".html";
	               ?>
	                <li id="category-<?php echo $rcat->Id ?>" class="category">
	                    <a href="<?php echo $url; ?>">
	                    <?php 
	                        echo $lval->Name;
	                    ?>
	                    </a>
	                </li>
	                <?php
	            $index++;
	        endforeach;
	        ?>
	    </ul>
	</div>
</div>
    <?php

    else:

        echo "<p>".JText::_('COM_CATALOG_NO_CATEGORY_AVAILABLE')."</p>";

    endif;

?>
