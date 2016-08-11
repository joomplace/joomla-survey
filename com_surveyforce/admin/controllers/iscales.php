<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class SurveyforceControllerIscales extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getModel($name = 'Iscales', $prefix = 'SurveyforceModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function add() {
       
    }

    public function delete() {
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        if ($tmpl == 'component')
            $tmpl = '&tmpl=component';
        else
            $tmpl = '';

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $item_id = $cid['0'];
      
    }

}
