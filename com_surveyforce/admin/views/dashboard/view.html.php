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

class SurveyforceViewDashboard extends JViewLegacy
{
    protected $messageTrigger = false;
	protected $dashboardItems;
	protected $version;

    function display($tpl = null)
    {
        $submenu = 'about';
        SurveyforceHelper::showTitle($submenu);

        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $this->dashboardItems = $this->get('Items');

        $document = JFactory::getDocument();
	    $document->addScript(JURI::root() . 'administrator/components/com_surveyforce/assets/js/MethodsForXml.js');
	    $document->addScript(JURI::root() . 'administrator/components/com_surveyforce/assets/js/MyAjax.js');

        $this->version = SurveyforceHelper::getVersion();

	    $this->addToolbar();
        $this->setDocument();

	    $this->messageTrigger = $this->get('CurrDate');

        parent::display($tpl);
    }

    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_MANAGER_DASHBOARD'), 'dashboard');
    }

    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_SURVEYFORCE') . ': ' . JText::_('COM_SURVEYFORCE_MANAGER_DASHBOARD'));
    }

}