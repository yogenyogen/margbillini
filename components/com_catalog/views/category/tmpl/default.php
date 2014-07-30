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

<?php 
if(count($root_categories) > 0):    
?>
<h1><?php echo JText::_('COM_CATALOG_CATALOG'); ?></h1>
<div class="span12">
    <h4><?php echo JText::_('COM_CATALOG_CATEGORIES'); ?></h4>
	<div class=" row-fluid span12">
	        <?php
	        $index=0;
	        foreach($root_categories as $rcat):
	            if($index%4 == 0 && $index > 0):    
                        ?>
                     </div>
                    <div class="clearfix"><hr></div>
    
                     <div class=" span12 row-fluid">
                    <?php 
                    endif;
                    $lval=$rcat->getLanguageValue($LangId);
                    $url=DS.JText::_('COM_CATALOG_CATALOG_NEEDLE').DS.
                         AuxTools::SEFReady(bll_category::generateSEFUrl($rcat->Id, $LangId)).".html";
	            if(is_file(JPATH_ROOT.DS.$rcat->ThumbUrl))
                    {
                        $imguri = $rcat->ThumbUrl;
                    }
                    else
                    {
                        $imguri="./components/com_catalog/images/no-image-category.jpg";
                    }   
                    ?>   
                        <div class="span3">
	                    <a href="./index.php/<?php echo $url; ?>">
                                <img src="<?php echo $imguri; ?>" />
                            </a>
                            <div>
                            <a href="./index.php/<?php echo $url; ?>">
	                    <?php 
	                        echo $lval->Name;
	                    ?>
	                    </a>
                            </div>    
	                </div>
	                <?php
	            $index++;
	        endforeach;
	        ?>
	    </div>
	</div>
</div>
    <?php

    else:

        echo "<p>".JText::_('COM_CATALOG_NO_CATEGORY_AVAILABLE')."</p>";

    endif;

?>
