<?php
/**
 * JoomlaSurveyForce system plugin for Joomla
 * @version $Id: sf_alphauserpoints.php 2018-03-15 12:03:15
 * @package JoomlaSurveyForce
 * @subpackage sf_alphauserpoints.php
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

class plgSystemSF_alphauserpoints extends JPlugin
{
    /**
     * Constructor
     *
     * @access      protected
     * @param       object $subject The object to observe
     * @param       array $config An array that holds the plugin configuration
     * @since       1.6
     */
    public function __construct(&$subject, $config)
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
    public function onSForceFinished($params)
    {
        if (!file_exists(JPATH_SITE . '/components/com_alphauserpoints/helper.php')) {
            return;
        }
        require_once(JPATH_SITE . '/components/com_alphauserpoints/helper.php');

        $user = JFactory::getUser();

        $comment = htmlspecialchars($this->params->get('comment', ''), ENT_COMPAT,'UTF-8');
        $points_rule = htmlspecialchars($this->params->get('points_rule', 'always'), ENT_COMPAT,'UTF-8');
        $fixed_points = (int)$this->params->get('fixed_points', 0);
        $points = $fixed_points ? (int)$this->params->get('points', 0) : (int)$params['user_points'];
        $add_points_once = (int)$this->params->get('add_points_once', 0);

        $pps = $this->params->get('pps');
        $sid = $params['survey_id'];
        if(isset($pps->$sid) && $pps->$sid){
            $points = $pps->$sid;
        }

        if ($points == 0 || ($points_rule == 'onsuccess' && !$params['passed'])) {
            return;
        }

        $params['user_id'] = $user->id;
        $params['user_name'] = $user->name;
        $params['ended'] = date('Y-m-d H:i:s');
        foreach($params as $key=>$value){
            if($key != 'params') {
                $comment = str_replace('{' . $key . '}', $value, $comment);
            }
        }

        $referredid = $this->getReferredId($user->id);

        AlphaUserPointsHelper::newpoints('sysplgaup_surveyforcepoints', $referredid, ($add_points_once ? $params['survey_id'] : ''),
            $comment, $points);
    }

    private  function getReferredId($user_id=0)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('referreid'))
            ->from($db->qn('#__alpha_userpoints'))
            ->where($db->qn('userid') .'='. $db->q((int)$user_id));
        $db->setQuery( $query );
        $referredid = $db->loadResult();
        return $referredid;
    }
}