<?php

/**
 * Description of view
 *
 * @author Gabriel
 */
class CatalogViewCoupons  extends JViewLegacy 
{
        /**
         * HelloWorlds view display method
         * @return void
         */
        function display($tpl = null) 
        {
                // Check for errors.
                if (count($errors = $this->get('Errors'))) 
                {
                        JError::raiseError(500, implode('<br />', $errors));
                        return false;
                }
                $this->actions();
                // Display the template
                parent::display($tpl);

        }

        function actions()
        {
            if(isset($_POST['action']) || isset($_GET['action']))
            {
                if(isset($_GET['action']))
                    $action=$_GET['action'];
                if(isset($_POST['action']))
                    $action=$_POST['action'];
                
                $type_msg='message';
                switch ($action)
                {
                    case 'edit':
                        $id=0;
                        $obj=new catalogcoupon(0);
                    
                        if(isset($_POST['Id']))
                        {
                            $id=$_POST['Id'];
                            $obj=new catalogcoupon($id);
                            $obj->setAttributes($_POST);
                            $obj=$obj->update();
                            if($obj !== false)
                                $action_msg =JText::_('COM_CATALOG_EDITED');
                            else 
                            {
                                $type_msg='error';
                                $action_msg =JText::_('COM_CATALOG_ERROR_EDITING');
                            }
                        }
                        else
                        { 
                            $obj->setAttributes($_POST);
                            $obj=$obj->insert();
                            if($obj !== false)
                                $action_msg =JText::_('COM_CATALOG_CREATED');
                            else
                            {

                                $action_msg =JText::_('COM_CATALOG_ERROR_CREATING');
                                $type_msg='error';
                            }

                            $what_msg  =" ". JText::_('COM_CATALOG_COUPON'); 
                            JFactory::getApplication()->enqueueMessage(
                                    $action_msg.$what_msg,
                                    $type_msg

                            );

                        }

                    break;



                    case 'delete':
                        if(isset($_GET['id']))
                        {
                            $id=$_GET['id'];
                            $obj=new catalogcoupon($id);

                            if($obj->delete() == true)
                                $action_msg =JText::_('COM_CATALOG_DELETED');

                            else
                            {
                                $type_msg='error';
                                $action_msg =JText::_('COM_CATALOG_ERROR_DELETING');
                            }

                            $what_msg  =" ". JText::_('COM_CATALOG_COUPON'); 
                            JFactory::getApplication()->enqueueMessage(
                                    $action_msg.$what_msg,
                                    $type_msg

                            );
                        }
                    break;
                 
                }
            }
        }
}

?>
