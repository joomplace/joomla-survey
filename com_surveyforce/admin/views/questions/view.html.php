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

class SurveyforceViewQuestions extends JViewLegacy
{
    protected $items;
    protected $pagination;
    protected $state;

    function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $document->addScript('components/com_surveyforce/assets/js/js.js');
        $this->addTemplatePath(JPATH_BASE . '/components/com_surveyforce/helpers/html');
        $submenu = 'questions';
        SurveyforceHelper::showTitle($submenu);
        SurveyforceHelper::getCSSJS();        
        $this->addToolBar();

        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $state = $this->get('State');
        $sections = $this->get('Sections');
        $this->surv_id = JFactory::getApplication()->input->get('surv_id', 0);

        $rows = array();

        foreach ($items as $item) {
            if(!$item->sf_section_id){
                $rows[0][] = $item;
            }
        }

        if(!empty($sections)){
            foreach ($sections as $section) {
                foreach ($items as $item) {
                    if($item->sf_section_id && $item->sf_section_id == $section->id){
                        $rows[$section->id][] = $item;
                    }
                }
            }
        }

        if(empty($sections)) {
            $rows[0] = $items;
        }

        if (!empty($errors = $this->get('Errors'))) {
            JFactory::getApplication()->enqueueMessage(implode("\n", $errors), 'error');
            return false;
        }

		$this->survey_names = $this->get('Survey_names');
        $this->items = $rows;
        $this->sections = $sections;
        $this->pagination = $pagination;
        $this->state = $state;

        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $surv_id = JFactory::getApplication()->input->get('surv_id');

        $bar = JToolBar::getInstance('toolbar'); 
		$bar->appendButton( 'Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" onclick="javascript: tb_start(this);return false;" href="index.php?option=com_surveyforce&amp;tmpl=component&amp;task=question.new_question_type&amp;KeepThis=true&amp;surv_id='.JFactory::getApplication()->input->get('surv_id', 0).'&amp;TB_iframe=true&amp;height=350&amp;width=700" href="#"><i class="icon-new icon-white"></i>'.JText::_('COM_SURVEYFORCE_NEW').'</a></div>');
        
        JToolBarHelper::editList('question.edit');
        JToolBarHelper::divider();
        JToolBarHelper::custom('questions.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
        JToolBarHelper::custom('questions.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
        JToolBarHelper::custom('questions.move', 'checkbox-partial', 'checkbox-partial', 'COM_SURVEYFORCE_MOVE', true);
		JToolBarHelper::custom('questions.copy', 'copy.png', 'copy_f2.png', 'COM_SURVEYFORCE_COPY', true);
        JToolBarHelper::divider();
        JToolBarHelper::deleteList('', 'questions.delete');
        JToolBarHelper::divider();
        $bar->appendButton( 'Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small btn-success" href="index.php?option=com_surveyforce&amp;view=section&amp;surv_id='.$surv_id.'"><i class="icon-new icon-white"></i>'.JText::_('COM_SURVEYFORCE_ADD_NEW_SECTION').'</a></div>');
        $bar->appendButton( 'Custom', '<div id="toolbar-new" class="btn-group"><a class="btn btn-small" onclick="if (document.adminForm.boxcheckedsection.value==0){alert(\'Please first make a selection from the list\');}else{Joomla.submitbutton(\'sections.delete\');}"><i class="icon-delete"></i>'.JText::_('COM_SURVEYFORCE_REMOVE_SECTION').'</a></div>');
    }

    protected function getSortFields()
    {
        return array(
            'sf_qtext' => JText::_('COM_SURVEYFORCE_TEXT'),
            'ordering' => JText::_('COM_SURVEYFORCE_ORDER'),
            'sf_qtype' => JText::_('COM_SURVEYFORCE_TYPE'),
            'sf_survey' => JText::_('COM_SURVEYFORCE_SURVEY'),
            'sf_compulsory' => JText::_('COM_SURVEYFORCE_COMPULSORY'),
            'published' => JText::_('JPUBLISHED'),
            'id' => JText::_('COM_SURVEYFORCE_ID')
        );
    }

}