<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerSet_default extends JControllerForm
{

	public function cancel($key = NULL){
		
		$qid = $_SESSION['qid'];
		unset($_SESSION['qid']);

		$this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$qid);
	}

    public function save(){
        
        if(isset($_SESSION['qid'])) {
            unset($_SESSION['qid']);
        }

        $jinput = \JFactory::getApplication()->input;
    	$data = $jinput->post;
 		
    	$quest_id = $jinput->getInt('id', 0);
    	$sf_qtype = $jinput->get('sf_qtype');

    	$database = JFactory::getDBO();

		$query = "SELECT sf_survey FROM `#__survey_force_quests` WHERE `id` = $quest_id ";
		$database->SetQuery($query);
		$survey_id = $database->LoadResult(); 
		$data['survey_id'] = $survey_id;

		if ( $quest_id > 0 && $survey_id > 0) {
		
			$query = "DELETE FROM `#__survey_force_def_answers` WHERE `survey_id` = $survey_id AND quest_id = $quest_id ";
			$database->SetQuery($query);
			$database->execute();

			$type = SurveyforceHelper::getQuestionType($sf_qtype);
			JPluginHelper::importPlugin('survey', $type);
        	$className = 'plgSurvey' . ucfirst($type);

        	if (method_exists($className, 'onSaveDefault'))
                $className::onSaveDefault($data); 

            $this->setRedirect('index.php?option=com_surveyforce&view=question&layout=edit&id='.$quest_id);
	    }
	}
}
