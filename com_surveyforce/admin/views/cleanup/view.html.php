<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewCleanup extends JViewLegacy {

    public function display($tpl = null) {

        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        SurveyforceHelper::showTitle('CLEANUP_TITLE');

        $this->survey_names = $this->get('Survey_names', 'Reports');

        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolBar() {
        $canDo = JHelperContent::getActions('com_surveyforce', 'component');
        JToolbar::getInstance('toolbar')->appendButton('Link', 'cancel', JText::_('COM_SURVEYFORCE_CLEANUP_CANCEL_BUTTON'), 'index.php?option=com_surveyforce&view=reports');
    }

}