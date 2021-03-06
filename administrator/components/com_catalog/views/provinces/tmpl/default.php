<?php 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$cid=0;
if(isset($_POST['cid']) && $_POST['cid'] > 0)
{
    $cid=$_POST['cid'];
    $ob = new province(0);
    $nelementsbypage =NUMBER_ELEMENTS_BY_PAGE;
    $total = count($ob->findAll('CountryId',$cid,true, $ob->getPrimaryKeyField()));
    $lower_limit=0;

    if(isset($_REQUEST['limitstart']))
        $lower_limit=$_REQUEST['limitstart'];

    $objs= $ob->findAll('CountryId',$cid,true, $ob->getPrimaryKeyField(), $lower_limit, $nelementsbypage);
    $LangId=AuxTools::GetCurrentLanguageIDJoomla();
    $lang = new languages($LangId);
}
else
{
    $ob = new province(0);
    $nelementsbypage =NUMBER_ELEMENTS_BY_PAGE;
    $total = count($ob->findAll(null,null,true, $ob->getPrimaryKeyField()));
    $lower_limit=0;

    if(isset($_REQUEST['limitstart']))
        $lower_limit=$_REQUEST['limitstart'];

    $objs= $ob->findAll(null,null,true, $ob->getPrimaryKeyField(), $lower_limit, $nelementsbypage);
    $LangId=AuxTools::GetCurrentLanguageIDJoomla();
    $lang = new languages($LangId);
}
?>

<div id="j-main-container" class="span10"> 
    <div class="btn-toolbar" id="toolbar">
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small btn-success" href="./index.php?option=com_catalog&view=provinces&layout=edit&cid=<?php echo $cid ?>">
                        <?php echo JText::_('COM_CATALOG_NEW')?>
                    <span class="icon-new icon-white"></span>
                </a>
        </div>
        <div class="btn-wrapper" id="toolbar-new">
                <a class="btn btn-small" href="./index.php?option=com_catalog&view=countries">
                <span class="icon-cancel"></span>
                <?php echo JText::_('COM_CATALOG_CANCEL')?>
                </a>
        </div>
    </div>
    <h3>
        <?php  echo JText::_('COM_CATALOG_PROVINCES')." ". JText::_('COM_CATALOG_MANAGER'); ?>
    </h3>
    <table class="table table-striped">
        <tr>
              <th><?php echo JText::_('COM_CATALOG_ID')?></th>
              <th><?php echo JText::_('COM_CATALOG_NAME')?></th>
              <th><?php echo JText::_('COM_CATALOG_COUNTRY')?></th>
              <th><?php echo JText::_('COM_CATALOG_ACTIONS')?></th>
        </tr>
<?php
foreach($objs as $obj)
{
    $country = new country($obj->CountryId);
?>

    <tr>
        <td> <?php echo $obj->Id; ?></td>
        <td> <?php echo $obj->Name; ?></td>
        <td> <?php echo $country->Name; ?></td>
         <td>

            <form method="POST" action="./index.php?option=com_catalog&view=provinces&layout=edit">
                 <input type="hidden"  name="id" value="<?php echo $obj->Id; ?>"/>
                 <input type="hidden"  name="cid" value="<?php echo $obj->CountryId; ?>"/>                 
                 <input type="hidden" name="limitstart" value="<?php echo $lower_limit; ?>" />
                 <button><?php echo JText::_('COM_CATALOG_EDIT')?></button>
            </form>
            <a class="button" href="./index.php?option=com_catalog&view=provinces&action=delete&id=<?php echo $obj->Id; ?>">
            <?php echo JText::_('COM_CATALOG_DELETE')?>
            </a>
        </td>    
    </tr>
<?php
}
?>
      </table>
    <?php
    echo HtmlGenerator::GeneratePagination($ob->getObjectName(), './index.php?option=com_catalog&view=provinces', $total, $lower_limit, $nelementsbypage);
    ?>
</div>