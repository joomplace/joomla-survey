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

class SurveyforceViewSet_iscale extends JViewLegacy
{
    protected $lists;
    protected $form;

    public function display($tpl = null)
    {
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        
        $submenu = "set_default";
        SurveyforceHelper::showTitle($submenu);
        $this->setSession();

        $this->lists = $this->get('Lists');

        // Check for errors.
        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

        $this->addToolbar($this->id);
        parent::display($tpl);
    }

    protected function setSession()
    {
        $app = JFactory::getApplication();
        $session = JFactory::getSession();

        $session->set('is_return_sf', 1);
        $session->set('sf_qtext_sf', $app->input->get('sf_qtext');
        $session->set('sf_survey_sf', $app->input->get('sf_survey', ''));
        $session->set('sf_impscale_sf', $app->input->get('sf_impscale', ''));
        $session->set('ordering_sf', $app->input->get('ordering', ''));
        $session->set('sf_compulsory_sf', $app->input->get( 'sf_compulsory', ''));
        $session->set('insert_pb_sf', $app->input->get( 'insert_pb', ''));
        $session->set('published', $app->input->get( 'published', ''));
        $session->set('is_likert_predefined_sf', $app->input->get('is_likert_predefined', ''));
        $session->set('sf_hid_scale_sf', $app->input->get('sf_hid_scale', '', 'array', array()));
        $session->set('sf_hid_scale_id_sf', $app->input->get('sf_hid_scale_id', '', 'array', array()));
        $session->set('sf_hid_rule_sf', $app->input->get('sf_hid_rule', '', 'array', array()));
        $session->set('sf_hid_rule_quest_sf', $app->input->get('sf_hid_rule_quest', '', 'array', array()));      
        $session->set('sf_hid_rule_alt_sf', $app->input->get('sf_hid_rule_alt', '', 'array', array()));
        $session->set('priority_sf', $app->input->get('priority', '', 'array', array()));
        $session->set('sf_hid_fields_sf', $app->input->get('sf_hid_fields', '', 'array', array()));
        $session->set('sf_hid_field_ids_sf', $app->input->get('sf_hid_field_ids', '', 'array', array()));
        $session->set('sf_fields_sf', $app->input->get('sf_fields', '', 'array', array()));
        $session->set('sf_field_ids_sf', $app->input->get('sf_field_ids', '', 'array', array()));
        $session->set('sf_alt_fields_sf', $app->input->get('sf_alt_fields', '', 'array', array()));
        $session->set('sf_alt_field_ids_sf', $app->input->get('sf_alt_field_ids', '', 'array', array()));
        $session->set('other_option_cb_sf', $app->input->get('other_option_cb', 0));
        $session->set('other_option_sf', $app->input->get('other_option', '', 'STRING'));
        $session->set('other_op_id_sf', $app->input->get('other_op_id', 0));
        $session->set('sf_hid_rank_sf', $app->input->get('sf_hid_rank', '', 'array', array()));
        $session->set('sf_hid_rank_id_sf', $app->input->get('sf_hid_rank_id', '', 'array', array()));

    }

    protected function addToolbar($id)
    {
    	$_SESSION['qid'] = $id;
        JToolBarHelper::save('set_default.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::cancel('set_default.cancel', 'JTOOLBAR_CANCEL');
    }

}