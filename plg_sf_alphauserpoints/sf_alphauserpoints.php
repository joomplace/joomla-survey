<?php
/**
* JoomlaSurveyForce system plugin for Joomla
* @version $Id: sf_alphauserpoints.php 2009-11-16 17:30:15
* @package JoomlaSurveyForce
* @subpackage sf_alphauserpoints.php
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/


defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if(!class_exists('plgSystemSF_alphauserpoints')){
	class plgSystemSF_alphauserpoints extends JPlugin{ 
		/*
		 * Constructor
		 */
		function __construct(&$subject, $config)
		{
			parent::__construct($subject, $config);
		}

		/*
		 * This method calls when user finishes survey
		 * start_id - unique start ID in Joomla SurveyForce component
		 * survey_id - SurveyForce id
		 * survey_title - SurveyForce title
		 * user_points - user points
		 * passing_points - passing points
		 * total_points - max points
		 * passed - passed or failed (1 or 0)
		 * started - start date
		 * spent_time - spent time
		*/
		function onSForceFinished($params){			
			if (!file_exists(JPATH_SITE.'/components/com_alphauserpoints/helper.php')) 
				return;		
			require_once(JPATH_SITE.'/components/com_alphauserpoints/helper.php');
			
			$user = & JFactory::getUser(); 
			
			$comment = $this->params->get('comment', '');
			$points_rule = $this->params->get('points_rule', 'always');
			$fixed_points = (int)$this->params->get('fixed_points', 0);
			$points = $fixed_points ? (int)$this->params->get('points', 0): $params['user_points'];
			$add_points_once = (int)$this->params->get('add_points_once', 0);

			$pps = $this->params->get('pps');
			if($pps->$params['survey_id']){
				$points = $pps->$params['survey_id'];
			}
			
			if ($points == 0 || ($points_rule == 'onsuccess' && !$params['passed'])) {
				return;
			}
			
			$params['user_id'] = $user->get('id');
			$params['user_name'] = $user->get('name');
			$params['ended'] = date('Y-m-d H:i:s');
			foreach($params as $key=>$value){	
				$comment = str_replace('{'.$key.'}', $value, $comment);
			}
			
			AlphaUserPointsHelper::newpoints('plgup_surveyforcepoints', '', ($add_points_once ? $params['survey_id'] : ''), $comment, $points);
		}
	}
}