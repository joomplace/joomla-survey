<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

class SurveyforceModelSections extends JModelList {

    
    public function delete($sids) {
        
        $db = JFactory::getDbo();
        
        if(count($sids)){
            foreach ($sids as $sid) {

                $db->setQuery("SELECT `id` FROM `#__survey_force_quests` WHERE `sf_section_id` = '".$sid."'");
                $quests = $db->loadColumn();

                if(count($quests)){
                    foreach ($quests as $quest) {
                        $db->setQuery("UPDATE `#__survey_force_quests` SET `sf_section_id` = 0 WHERE `id` = '".$quest."'");
                        $db->execute();
                    }
                }         

                $db->setQuery("DELETE FROM `#__survey_force_qsections` WHERE `id` = '".$sid."'");
                $db->execute();
            }
        }

    }
    

}
