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

class SurveyforceControllerSurveys extends JControllerAdmin {

    public function __construct($config = array()) {
        parent::__construct($config);
    }

    public function getModel($name = 'Surveys', $prefix = 'SurveyforceModel') {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        return $model;
    }

    public function add() {
        $this->setRedirect('index.php?option=com_surveyforce&task=survey.add');
    }

    public function delete() {
        // Get items to remove from the request.
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $tmpl = JFactory::getApplication()->input->get('tmpl');
        if ($tmpl == 'component')
            $tmpl = '&tmpl=component';
        else
            $tmpl = '';

        if (!is_array($cid) || count($cid) < 1) {
            JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
        } else {
            // Get the model.
            $model = $this->getModel();

            // Make sure the item ids are integers
            jimport('joomla.utilities.arrayhelper');
            JArrayHelper::toInteger($cid);

            // Remove the items.
            if ($model->delete($cid)) {
                $this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit() {
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $item_id = $cid['0'];
        $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&task=survey.edit&id=' . $item_id, false));
    }

	public function preview()
	{
		$database = JFactory::getDbo();
		$cid = (int) end( JFactory::getApplication()->input->get('cid', array(), 'array') );

		$unique_id = md5(uniqid(rand(), true));
		$query = "INSERT INTO `#__survey_force_previews` SET `preview_id` = '".$unique_id."', `time` = '".strtotime(JFactory::getDate())."'";
		$database->setQuery( $query );
		$database->query( );

		$this->setRedirect( JRoute::_(JUri::root()."index.php?option=com_surveyforce&view=survey&id={$cid}&preview=".$unique_id) );
	}
	
	public function copy(){
	
		$cids = implode(',',JFactory::getApplication()->input->get('cid',array(),'array'));
		
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('s.*');
        $query->from('`#__survey_force_cats` as s');
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		if($categories && $cids){
			?>
			<form action="index.php" method="POST">
				<label>
					<?php echo JText::_('COM_SURVEYFORCE_COPY_TO'); ?>
				</label>
				<div>
					<select name="cat_id">
					<?php
					foreach($categories as $category){
						echo '<option value="'.$category->id.'">'.$category->sf_catname.'</option>';
					}
					?>
					</select>
				</div>
				<button class="btn btn-default"><?php echo JText::_('COM_SURVEYFORCE_MOVE_SUBMIT'); ?></button>
				<input type="hidden" name="surveys" value="<?php echo $cids; ?>" />
				<input type="hidden" name="task" value="surveys.copyto" />
				<input type="hidden" name="option" value="com_surveyforce" />
			</form>
			<?php
		}
	}
	public function copyto(){
		
		$input = JFactory::getApplication()->input;
		$sf = $input->get('cat_id',0);
		
		$ids = $input->get('surveys','','string');	
		
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__survey_force_survs')
				->where('`id` IN ('.$ids.')');
			$db->setQuery($query);
			$surveys = $db->loadObjectList('id');
			
			$query->clear();

		foreach($surveys as $survey){
			
			$query->clear();
			$old_id = $survey->id;
			$survey->id = '';
			$survey->sf_cat = $sf;
			$db->insertObject('#__survey_force_survs',$survey);
			$survey->id = $db->insertid();
				
			$db = JFactory::getDbo();
		    $query = $db->getQuery(true);
			
			$query->select('*')
                ->from('#__survey_force_qsections')
                ->where('sf_survey_id = "'.$old_id.'"');
            $db->setQuery($query);
			$sections = $db->loadObjectList('id');
            $query->clear();
			
			$oldSectId = 0;
			$newSectId = array();
						
			foreach($sections as $section){
				$query->clear();
				$oldSectId = $section->id;
				$section->id = '';
				$section->sf_survey_id = $survey->id;
				$db->insertObject('#__survey_force_qsections',$section);
				$section->id = $db->insertid();	
				$newSectId[$oldSectId] = $section->id; 				
			}
			
			$query->clear();
			
            $query->select('*')
                ->from('#__survey_force_quests')
                ->where('sf_survey = "'.$old_id.'"');
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
				$quest->sf_survey = $survey->id;
				
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
		
			
		JFactory::getApplication()->redirect('index.php?option=com_surveyforce&view=surveys');
	
	}

}
