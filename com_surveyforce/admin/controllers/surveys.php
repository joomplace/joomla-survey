<?php

/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Router\Route;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Uri\Uri;

class SurveyforceControllerSurveys extends JControllerAdmin
{
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    public function getModel($name='Surveys', $prefix='SurveyforceModel', $config=array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function add()
    {
        $this->setRedirect('index.php?option=com_surveyforce&task=survey.add');
    }

    public function delete()
    {
        $this->checkToken();
        $cid = $this->input->get('cid', array(), 'array');
        $tmpl = $this->input->get('tmpl', '');

        if($tmpl == 'component') {
            $tmpl = '&tmpl=component';
        } else {
            $tmpl = '';
        }

        if (!is_array($cid) || count($cid) < 1) {
            Factory::getApplication()->enqueueMessage(Text::_($this->text_prefix . '_NO_ITEM_SELECTED'), 'error');
        } else {
            $model = $this->getModel();
            $cid = ArrayHelper::toInteger($cid);

            if ($model->delete($cid)) {
                $this->setMessage(Text::plural($this->text_prefix . '_N_ITEMS_DELETED', count($cid)));
            } else {
                $this->setMessage($model->getError());
            }
        }

        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $tmpl, false));
    }

    public function edit()
    {
        $cid = $this->input->get('cid', array(), 'array');
        $item_id = !empty($cid['0']) ? (int)$cid['0'] : 0;
        $this->setRedirect(Route::_('index.php?option=' . $this->option . '&task=survey.edit&id=' . $item_id, false));
    }

	public function preview()
	{
		$database = Factory::getDbo();
        $cids = $this->input->get('cid', array(), 'array');
		$cid = (int) end($cids);

		$unique_id = md5(uniqid(rand(), true));
		$query = "INSERT INTO `#__survey_force_previews` SET `preview_id` = '".$unique_id."', `time` = '".strtotime(Factory::getDate())."'";
		$database->setQuery($query);
		$database->execute();

		$this->setRedirect(Route::_(Uri::root()."index.php?option=com_surveyforce&view=survey&id={$cid}&preview=".$unique_id));
	}
	
	public function copy()
    {
        $cid = $this->input->get('cid', array(), 'array');
		$cids = implode(',', $cid);
		
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('s.*');
        $query->from('`#__survey_force_cats` as s');
		$db->setQuery($query);
		$categories = $db->loadObjectList();
		
		if(!empty($categories) && !empty($cids)) {
			?>
			<form action="index.php" method="POST">
				<label>
					<?php echo Text::_('COM_SURVEYFORCE_COPY_TO'); ?>
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
                <a class="btn btn-danger" style="margin-right: 10px;"
                   href="<?php echo Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false)?>">
                    <?php echo Text::_('COM_SURVEYFORCE_MOVE_CANCEL'); ?>
                </a>
				<button class="btn btn-default"><?php echo Text::_('COM_SURVEYFORCE_MOVE_SUBMIT'); ?></button>
				<input type="hidden" name="surveys" value="<?php echo $cids; ?>" />
				<input type="hidden" name="task" value="surveys.copyto" />
				<input type="hidden" name="option" value="com_surveyforce" />
			</form>
			<?php
		}
	}

	public function copyto()
    {
		$input = Factory::getApplication()->input;
		$sf = $input->getInt('cat_id',0);
		$ids = $input->get('surveys','','string');
		
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select('*')
            ->from('#__survey_force_survs')
            ->where('`id` IN ('.$ids.')');
        $db->setQuery($query);
        $surveys = $db->loadObjectList('id');

		foreach($surveys as $survey){
			
			$query->clear();
			$old_id = $survey->id;
			$survey->id = '';
			$survey->sf_cat = $sf;
			$db->insertObject('#__survey_force_survs',$survey);
			$survey->id = $db->insertid();

            $query->clear();
			$query->select('*')
                ->from('#__survey_force_qsections')
                ->where('sf_survey_id = "'.$old_id.'"');
            $db->setQuery($query);
			$sections = $db->loadObjectList('id');
			
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

                $query->clear();
				$query->select('*')
					->from('#__survey_force_fields')
					->where('quest_id = "'.$qkey.'"');
				$db->setQuery($query);
				$answers = $db->loadObjectList('id');

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
					$db->execute();
				} else {
					foreach($answers as $ans){
						$oldAnswerId = $ans->id;						
						$ans->id = '';
						$ans->quest_id = $quest->id;
						$db->insertObject('#__survey_force_fields',$ans);						
						$newAnswerId[$oldAnswerId] = $db->insertid(); 
					}
				}
				
				$answers = array();
                $query->clear();
				$query->select('*')
					->from('#__survey_force_scales')
					->where('quest_id = "'.$qkey.'"');
				$db->setQuery($query);
				$answers = $db->loadObjectList('id');
				
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
			
			foreach($rules as $rule) {
				$rule->quest_id = $newQuestId[$rule->quest_id];
				$rule->next_quest_id = $newQuestId[$rule->next_quest_id];
				$rule->answer_id = $newAnswerId[$rule->answer_id];
				
				$db->insertObject('#__survey_force_rules',$rule);
			}
        }

		Factory::getApplication()->redirect('index.php?option=com_surveyforce&view=surveys');
	}

}
