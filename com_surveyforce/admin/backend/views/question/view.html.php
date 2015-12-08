<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewQuestion extends JViewLegacy {

    protected $state;
    protected $item;
    protected $form;
    protected $surveys;
    protected $ordering_list;

    public function display($tpl = null) {

        $app = JFactory::getApplication();
        SurveyforceHelper::showTitle('QUESTION_ADMIN');
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');

        $this->option = 'com_surveyforce';
        $this->state = $this->get('State');
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        $new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
        $sf_survey = $app->getUserStateFromRequest( "question.sf_survey", 'sf_survey', 0 );

		if ( !$sf_survey )
			$sf_survey = $app->getUserStateFromRequest( "question.surv_id", 'surv_id', 0 );

        if ($this->item->id) {
            $new_qtype_id = $this->item->sf_qtype;
        } else {
            $this->item->sf_qtype = $new_qtype_id;
			$this->item->sf_survey = $sf_survey;
        }

        $type = SurveyforceHelper::getQuestionType($new_qtype_id);

        $this->surveys = $this->get('SurveysList');
        $this->ordering_list = $this->get('Ordering');

        JPluginHelper::importPlugin('survey', $type);
        $className = 'plgSurvey' . ucfirst($type);

        $data = array();
        $data['id'] = $this->item->id;
        $data['quest_type'] = $type;
        $data['item'] = $this->item;

        if($data['quest_type'] == 'pagebreak'){
			
            if (method_exists($className, 'onSaveQuestion'))
                $return = $className::onSaveQuestion($data);
            $app->redirect(JRoute::_(JURI::base().'index.php?option=com_surveyforce&view=questions&surv_id='.$this->item->sf_survey));
        }
        
        $model = JModelLegacy::getInstance("Question", "SurveyforceModel");
        $lists = $model->getLists($this->item->id);

        if (method_exists($className, 'onGetAdminOptions'))
                $this->options = $className::onGetAdminOptions($data, $lists);
        
        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }
      
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar() {

        JFactory::getApplication()->input->set('hidemainmenu', true);
        
        JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('question.saveandnew', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::custom('question.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
        JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
        JToolBarHelper::divider();
        JToolBarHelper::help('JHELP_COMPONENTS_WEBLINKS_LINKS_EDIT');

    }

}
