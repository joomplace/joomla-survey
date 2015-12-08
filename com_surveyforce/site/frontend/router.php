<?php

/**
 * Surveyforce Component for Joomla 3
 * @package Surveyforce
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;

function SurveyforceBuildRoute(&$query) {
    $segments = array();

    if (isset($query['view']) && isset($query['tmpl']) && isset($query['id'])) {
        $segments[] = $query['id'] . '-edit-survey';
        unset($query['view']);
        unset($query['tmpl']);
        unset($query['id']);
    }
    if (isset($query['tmpl']) && isset($query['id']) && isset($query['task'])) {
        if ($query['task'] == 'survey.state') {
            $segments[] = $query['id'] . '-survey-state';
        }
        unset($query['task']);
        unset($query['tmpl']);
        unset($query['id']);
    }
    if (isset($query['view']) && isset($query['tmpl'])) {
        $segments[] = 'add-testimonial';
        unset($query['view']);
        unset($query['tmpl']);
    }
    if (isset($query['tmpl']) && isset($query['id'])) {
        $segments[] = $query['id'] . '-read-survey';
        unset($query['tmpl']);
        unset($query['id']);
    }
    if (isset($query['task']) && isset($query['id'])) {
        if ($query['task'] == 'survey.state') {
            $segments[] = $query['id'] . '-survey-state';
        } elseif ($query['task'] == 'survey.delete') {
            $segments[] = $query['id'] . '-survey-delete';
        }
		else
			$segments[] = $query['id'] . '-authoring.'.'task-'.$query['task'];

        unset($query['task']);
        unset($query['id']);
    }
    if (isset($query['id'])) {
        $segments[] = $query['id'] . '-survey-page';
        unset($query['id']);
    }

    return $segments;
}

function SurveyforceParseRoute($segments) {
    $segment = explode(':', $segments[0]);
    $vars = array();

    switch ($segments[0]) {
        case 'add':
            $vars['view'] = 'form';
            $vars['tmpl'] = 'component';
            break;
    }
    switch ($segment[1]) {
        case 'edit-survey':
            $vars['view'] = 'form';
            $vars['tmpl'] = 'component';
            $vars['id'] = $segment[0];
            break;
        case 'survey-page':
            $vars['id'] = $segment[0];
            break;
        case 'read-survey':
            $vars['tmpl'] = 'component';
            $vars['id'] = $segment[0];
            break;
        case 'survey-state':
            $vars['task'] = 'survey.state';
            $vars['id'] = $segment[0];
            break;
        case 'survey-delete':
            $vars['task'] = 'survey.delete';
            $vars['id'] = $segment[0];
            break;
		default:
			$segment2 = explode('.', $segment[1]);
			if ( count($segment2) > 1 )
			{
				$vars['view'] = $segment2[0];
				$vars['task'] = @end( @explode('-', $segment2[1]));
				$vars['id'] = $segment[0];
			}
		break;
    }

	if ( empty($vars['view']) )	$vars['view'] = 'survey';

    return $vars;
}
