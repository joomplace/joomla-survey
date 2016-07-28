<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

class SurveyforceControllerSurvey extends JControllerForm
{

	protected $last_insert_id;

	public function __construct()
	{
		$this->_trackAssets = true;
		parent::__construct();
	}

	public function cancel()
	{
		$this->setRedirect('index.php?option=com_surveyforce&view=surveys');
	}

	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$this->last_insert_id = $model->getState($model->getName() . '.id');
	}

	public function save(){
		$task = JFactory::getApplication()->input->get('task');
		$data = JFactory::getApplication()->input->get('jform',array(),'Array');
		$save = parent::save();
		if($save && $task == 'save2copy'){
			$db = JFactory::getDbo();
		    $query = $db->getQuery(true);
			
			$query->select('*')
                ->from('#__survey_force_qsections')
                ->where('sf_survey_id = "'.$data['id'].'"');
            $db->setQuery($query);
			$sections = $db->loadObjectList('id');
            $query->clear();
			
			$oldSectId = 0;
			$newSectId = array();
						
			foreach($sections as $section){
				$query->clear();
				$oldSectId = $section->id;
				$section->id = '';
				$section->sf_survey_id = $this->last_insert_id;
				$db->insertObject('#__survey_force_qsections',$section);
				$section->id = $db->insertid();	
				$newSectId[$oldSectId] = $section->id; 				
			}
			
			$query->clear();
			
            $query->select('*')
                ->from('#__survey_force_quests')
                ->where('sf_survey = "'.$data['id'].'"');
            $db->setQuery($query);
            $questions = $db->loadObjectList('id');
            $query->clear();			          
			
			$oldQuests = array();
			$oldQuestId = 0;
			$newQuestId = array();
			$oldAnswerId = 0;
			$newAnswerId = array();
			
			foreach($questions as $qkey => $quest){
				$query->clear();
				$oldQuests[] = $quest->id;
				$oldQuestId = $quest->id;
				$quest->id = '';
				$quest->sf_survey = $this->last_insert_id;
				$quest->sf_section_id = $newSectId[$quest->sf_section_id];
				$db->insertObject('#__survey_force_quests',$quest);
				$quest->id = $db->insertid();			
				
				$newQuestId[$oldQuestId] = $quest->id; 
				
				$query->select('*')
					->from('#__survey_force_fields')
					->where('quest_id = "'.$qkey.'"');
				$db->setQuery($query);
				$answers = $db->loadObjectList('id');
				$query->clear();
					
				if($quest->sf_qtype= 9 && $quest->sf_qtype= 6){					
					foreach($answers as $ans){
							$oldAnswerId = $ans->id ;							
							$ans->id = '';
							$ans->quest_id = $quest->id;
							$db->insertObject('#__survey_force_fields',$ans);
							$newAnswerId[$oldAnswerId] = $db->insertid(); 
							
					}
					$db->setQuery("UPDATE #__survey_force_fields AS a , #__survey_force_fields AS b SET a.alt_field_id = b.id
							WHERE a.quest_id=b.quest_id AND a.ordering=b.ordering AND a.is_main=1 AND a.quest_id = ".$quest->id);
					$db->query();					
				}else{					
					foreach($answers as $ans){
						$oldAnswerId = $ans->id;						
						$ans->id = '';
						$ans->quest_id = $quest->id;
						$db->insertObject('#__survey_force_fields',$ans);						
						$newAnswerId[$oldAnswerId] = $db->insertid(); 
					}
				}
				
				$answers = array();
				$query->select('*')
					->from('#__survey_force_scales')
					->where('quest_id = "'.$qkey.'"');
				$db->setQuery($query);
				$answers = $db->loadObjectList('id');
				$query->clear();
				
				foreach($answers as $ans){
					$ans->id = '';
					$ans->quest_id = $quest->id;
					$db->insertObject('#__survey_force_scales',$ans);
				}
            }
			
			if(!empty($oldQuests)){
			$db->setQuery("SELECT *, '' AS id FROM #__survey_force_rules WHERE quest_id IN (".implode(",", $oldQuests).")");			
			$rules = $db->loadObjectList();					
			}		
			
			foreach($rules as $rule)
			{				
				$rule->quest_id = $newQuestId[$rule->quest_id];
				$rule->next_quest_id = $newQuestId[$rule->next_quest_id];
				$rule->answer_id = $newAnswerId[$rule->answer_id];
				
				$db->insertObject('#__survey_force_rules',$rule);
				
			}
		
			
        }
    }
}