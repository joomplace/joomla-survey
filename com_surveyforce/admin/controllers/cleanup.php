<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerCleanup extends JControllerForm {

    public function display($cachable = false, $urlparams = false)
    {
        $vName = $this->input->getCmd('view', 'cleanup');
        $this->input->set('view', $vName);

        if ($view = $this->getView('cleanup', 'html'))
        {
            $modelCleanup  = $this->getModel('Cleanup', 'SurveyforceModel');
            $modelReports = $this->getModel('Reports', 'SurveyforceModel');

            // Push the model into the view
            $view->setModel($modelCleanup, true);	//as default
            $view->setModel($modelReports);

            $view->display();
        }

        return $this;
    }

    public function results_cleanup()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $app   = JFactory::getApplication();
        $user = JFactory::getUser();
        $context = "$this->option.$this->context";

        if(!$user->authorise('core.delete')){
            $app->enqueueMessage(JText::_('COM_SURVEYFORCE_CLEANUP_ERROR_NOT_PERMITTED'), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_surveyforce&view=reports', false));
            return false;
        }

        $model = $this->getModel('Cleanup', 'SurveyforceModel');
        $data  = $this->input->post->get('jform', array(), 'array');

        if(isset($data['date_start']) && $data['date_start'] && isset($data['date_end']) && $data['date_end']){
            if(strtotime($data['date_start']) > strtotime($data['date_end'])){
                $app->setUserState($context . '.data', $data);
                $app->enqueueMessage(JText::_('COM_SURVEYFORCE_CLEANUP_ERROR_NOT_VALID_DATES'), 'error');
                $this->setRedirect(JRoute::_('index.php?option=com_surveyforce&task=cleanup.display', false));
                return false;
            }
        }

        if(!$model->results_cleanup($data)){
            $app->setUserState($context . '.data', $data);
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_surveyforce&task=cleanup.display', false));
            return false;
        }

        $app->setUserState($context . '.data', null);
        $app->enqueueMessage(JText::_('COM_SURVEYFORCE_CLEANUP_SUCCESS'), 'message');
        $this->setRedirect(JRoute::_('index.php?option=com_surveyforce&view=reports', false));

        return true;
    }
}