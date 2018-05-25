<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

class SurveyforceViewSet_default extends JViewLegacy {

    protected $lists;
    protected $form;

    public function display($tpl = null) {

        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        
        $submenu = "set_default";
        SurveyforceHelper::showTitle($submenu);

        $this->lists = $this->get('Options');
        $type = SurveyforceHelper::getQuestionType($this->lists['sf_qtype']);
        JPluginHelper::importPlugin('survey', $type);
        $className = 'plgSurvey' . ucfirst($type);

        if (method_exists($className, 'onGetDefaultForm'))
                $form = $className::onGetDefaultForm($this->lists);

        $this->form = $form;
        $this->id = $this->lists['row']->id;
        $this->sf_qtype = $this->lists['sf_qtype'];

		if (method_exists($className, 'onGetAdminOptions'))
			$this->options = $className::onGetDefaultForm($this->lists);

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage($this->get('Errors'), 'error');
            return false;
        }

        $this->addToolbar($this->id);
        parent::display($tpl);
    }

    protected function addToolbar($id) {
        
    	$_SESSION['qid'] = $id;

        JToolBarHelper::save('set_default.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('set_default.cancel', 'JTOOLBAR_CANCEL');
        
    }

}