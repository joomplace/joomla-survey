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

class SurveyforceControllerSections extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getModel($name = 'Section', $prefix = 'SurveyforceModel', $config = array('ignore_request' => true)) {
        return parent::getModel($name, $prefix, $config);
    }

    public function delete() {
        // Get items to remove from the request.

        $sids = JFactory::getApplication()->input->get('sid', array(), 'array');
        $surv_id = JFactory::getApplication()->input->get('surv_id');
        $model = $this->getModel();

        if ($model->delete($sids)) {
            $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($sids)));
        } else {
            $this->setMessage($model->getError());
        }
        
        $this->setRedirect(JRoute::_('index.php?option=com_surveyforce&view=questions&surv_id='.$surv_id, false));
    }

    public function apply()
    {
        echo 1;
        die;
    }

}
