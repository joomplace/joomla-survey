<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import the model file from the core
Foundry::import( 'admin:/includes/model' );


class SurveyForceModel extends EasySocialModel
{
	
	function __construct(){
		$this->db = Foundry::db();
        $this->params = JFactory::getApplication()->input;
	}

	public function _getEntries($isOwner, $limitstart, $limit, $userPage = null){

		$my		=  JFactory::getUser();
		$db		= Foundry::db();

		$order_by = $this->params->get('order_by', 'ordering');
		$order = $this->params->get('order', 'DESC');

		$date	= new JDate();
		$now	= $date->__toString();


		if (!$isOwner) {
			$my = Foundry::user();
            $isFriend = $my->isFriends($userPage->id);

			if ($isFriend) {
				$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$userPage->id."' AND ( sf_public = 1 OR sf_reg = 1 OR sf_friend = 1)";
			} elseif ($my->id) {
				$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$userPage->id."' AND ( sf_public = 1 OR sf_reg = 1 )";
			} else {
				$query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$userPage->id."' AND ( sf_public = 1 )";
			}

		} else {
            $query = "SELECT * FROM `#__survey_force_survs` WHERE `sf_author` = '".$my->id."' AND sf_public = 1 LIMIT $limitstart , $limit";
		}

		$db->SetQuery( $query );

		$rows = $db->loadObjectList();

		if($db->getErrorNum()) {
			JError::raiseError( 500, $db->stderr());
		}

		return $rows;
	}

    public function getSurveysCount(){
        $my		=  JFactory::getUser();
        $db		= Foundry::db();

        $db->SetQuery("SELECT count(`id`) as count FROM `#__survey_force_survs` WHERE `sf_author`='".$my->id."'");
        return $db->loadResult();

    }

}