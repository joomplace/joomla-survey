<?php

class plgSurveyRankingInstallerScript
{
	public function update()
	{
		$db = JFactory::getDbo();
		
		$query = "SELECT b.* FROM #__survey_force_quests as a, #__survey_force_fields as b WHERE b.quest_id = a.id AND a.sf_qtype = 9 AND  b.is_main = '1'";
		$db->setQuery($query);
		$is_main_rows = $db->loadObjectList();
		
		$query = "SELECT b.* FROM #__survey_force_quests as a, #__survey_force_fields as b WHERE b.quest_id = a.id AND a.sf_qtype = 9 AND  b.is_main = '0'";
		$db->setQuery($query);
		$is_alt_rows = $db->loadObjectList();
						
		foreach( $is_main_rows AS $i => $row){
			foreach( $is_alt_rows AS $ii => $alt_row){
				if($row->quest_id == $alt_row->quest_id)
				{	
					$query = $db->getQuery(true);
					$query->update('#__survey_force_fields')
						->set('`alt_field_id` = "' . $alt_row->id . '"')
						->where('`id` = ' .$row->id);
					$db->setQuery($query);
					$db->execute();					
					unset($is_main_rows[$i]);
					unset($is_alt_rows[$i]);
					break;
				}				
			}
		}
		
		
		$clons = array_merge($is_main_rows, $is_alt_rows);
		
		foreach($clons AS $clon){
			$columns = get_object_vars($clon);
			
			$values = array_values($columns);
			$columns = array_keys($columns);			
						
			unset($columns[0]);//removes id field
			unset($values[0]);//removes id field
			
			$values = array_map(array($db, 'quote'), $values);
		
			$query = $db->getQuery(true);
			$query->insert('#__survey_force_fields');
			$query->columns(implode(', ', $columns));
			$query->values(implode(', ', $values));	
			
			$db->setQuery($query);
			$db->execute();
			$new_field_id = $db->insertid();				
			
			$query = $db->getQuery(true);
			$query->update('#__survey_force_fields')
				->set('`alt_field_id` = "' . $new_field_id . '"')
				->set('`is_main` = "' . !$clon->is_main . '"')
				->where('`id` = ' .$clon->id);
			
			$db->setQuery($query);
			$db->execute();
		}		
	}
}