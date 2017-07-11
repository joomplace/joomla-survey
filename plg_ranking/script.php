<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSurveyRankingInstallerScript
{
	public function update()
	{
		$db = JFactory::getDbo();
		
		$query = "SELECT b.* FROM #__survey_force_quests as a, #__survey_force_fields as b WHERE b.quest_id = a.id AND a.sf_qtype = 9 AND  b.is_main = '1'";
		$query = $db->getQuery(true);
		$query->select($db->qn('b').'.*')
			->from($db->qn('#__survey_force_fields','b'))
			->join('left',$db->qn('#__survey_force_quests','a').' ON '.$db->qn('b.quest_id').' = '.$db->qn('a.id'))
			->where($db->qn('a.sf_qtype').' = 9');
		$fields = $db->setQuery($query)->loadObjectList();
		
		$questions = array();
		foreach($fields as $field){
			if(!isset($questions[$field->quest_id])){
				$questions[$field->quest_id] = array('left_side'=>array(), 'right_side'=>array());
			}
			$questions[$field->quest_id][($field->is_main)?'right_side':'left_side'][] = $field;
		}
		
		foreach($questions as $key => $que){
			$ls = 0;
			$ls = count($que['left_side']);
			$rs = 0;
			$rs = count($que['right_side']);
			if($ls >= $rs){
				$recs_missed = $ls-$rs;
				$added_fields = array();
				while ($recs_missed):
					foreach($que['right_side'] as $rsf){
						if($recs_missed){
							$nfobj = clone $rsf;
							$nfobj->id = '';
							$db->insertObject('#__survey_force_fields',$nfobj);
							$nfobj->id = $db->insertid();
							$added_fields[] = $nfobj;
							$recs_missed--;
						}
					}
				endwhile;
				$que['right_side'] = array_merge_recursive($que['right_side'],$added_fields);
				foreach($que['left_side'] as $i => $lsf){
					$query = $db->getQuery(true);
					$query->update('#__survey_force_fields')
						->set('`alt_field_id` = "' . $que['right_side'][$i]->id . '"')
						->where('`id` = ' .$lsf->id);
					$db->setQuery($query);
					$db->execute();					
				}
				echo "<pre>";
				print_r($que);
				echo "</pre>";
			}else{
				$recs_missed = $rs-$ls;
				$added_fields = array();
				while ($recs_missed):
					foreach($que['left_side'] as $lsf){
						if($recs_missed){
							$nfobj = clone $lsf;
							$nfobj->id = '';
							$added_fields[] = $nfobj;
							$recs_missed--;
						}
					}
				endwhile;
				$que['left_side'] = array_merge_recursive($que['left_side'],$added_fields);
				foreach($que['right_side'] as $i => $rsf){
					$que['left_side'][$i]->alt_field_id = $rsf->id;
					if($que['left_side'][$i]->id){
						$query = $db->getQuery(true);
						$query->updateObject('#__survey_force_fields',$que['left_side'][$i],$que['left_side'][$i]->id);
					}else{
						$db->insertObject('#__survey_force_fields',$que['left_side'][$i]);
					}
				}
			}
		}
		
	}
}