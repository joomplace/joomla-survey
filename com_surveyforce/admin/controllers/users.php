<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

class SurveyforceControllerUsers extends JControllerForm
{
	public function getModel($name = 'Users', $prefix = 'SurveyforceModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

	public function cancel($key = null)
    {
		$this->setRedirect('index.php?option=com_surveyforce&view=listusers');
	}

    public function add()
    {
        parent::add();
    }

     public function delete()
     {
     	$listid = JFactory::getApplication()->input->get('listid');
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        
        if (!is_array($cid) || count($cid) < 1) {
            JFactory::getApplication()->enqueueMessage(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
        } else {
            // Get the model.
            $model = $this->getModel();

            $cid = ArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=users&id='.$listid, false));
    }
}
