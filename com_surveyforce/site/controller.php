<?php

/**
 * SurveyForce Delux Component for Joomla 3
 * @package   Surveyforce
 * @author    JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license   GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

/**
 * Surveyforce Component Controller
 */
class SurveyforceController extends JControllerLegacy
{
    /**
     * @param bool $cachable
     * @param array $urlparams
     * @internal in my opinion this method may contains only call parent display method. All the rest is superfluous
     * @return JControllerLegacy|void
     */
    public function display($cachable = false, $urlparams = array())
    {
        $view   = $this->input->get('view');
        $task   = $this->input->get('task');
        $itemid = $this->input->getInt('Itemid', 0);
        $this->input->set('Itemid', $itemid);

        $session = JFactory::getSession();
        $session_view = $session->get('view');

        if ($view == 'authoring' && empty($session_view)) {
            $session->set('view', 'authoring');
        } elseif ($view != 'authoring' && !empty($session_view) && $session_view != 'authoring') {
            $session->clear('view');
        }

        if (!empty($session_view) && $session_view == 'authoring' && $view != 'survey' && $view != 'passed_survey' && $view != 'category') {
            $this->input->set('view', 'authoring');
        } else {
            if ($view != 'category' && $view != 'insert_survey' && $view != 'passed_survey') {
                $this->input->set('view', 'survey');
            }
        }

        if ($task == 'start_invited') {
            $this->input->set('view', 'survey');
        }

        if ($task == 'view_users') {
            $this->input->set('view', 'authoring');
        }

        parent::display($cachable);
    }
}