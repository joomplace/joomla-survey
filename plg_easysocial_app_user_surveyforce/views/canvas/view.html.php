<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

class SurveyForceViewCanvas extends SocialAppsView
{
    private $params;

	public function display($tpl = null, $docType = null)
	{
		$user= $my = JFactory::getUser();

		// Test if surveyforce exists
		if( !file_exists( JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_surveyforce'.DIRECTORY_SEPARATOR.'surveyforce.php' ) )
		{
			echo parent::display( 'canvas/not_exists' );
		}
		else{
			$mainframe = JFactory::getApplication();
            $action = $mainframe->input->get('action','default');

            if($action == 'default'){
                $limitstart = $mainframe->input->get('limitstart',0);
                $limit = $mainframe->input->get('limit',10);
                $userId = $user->id;
                $userName = $user->name;
                $isOwner	= ($my->id == $userId ) ? true : false;
                $model  = $this->getModel('surveyforce');
                $surveysCount = $model->getSurveysCount();
                $rows	= $model->_getEntries($isOwner, $limitstart, $limit);
                if($rows){
                    $data_exist = 1;
                }else{
                    $data_exist = 0;
                }

                $this->set('data_exist',$data_exist);
                $this->set('rows',$rows);
                $this->set('userId',$userId);
                $this->set('userName',$userName);
                $this->set('isOwner',$isOwner);
                $this->set('params',$this->params);
                $this->set('limit',$limit);
                $this->set('limitstart',$limitstart);
                $this->set('surveysCount',$surveysCount);
                echo parent::display( 'canvas/default' );
            }elseif($action == 'view'){
                $survey_id = JFactory::getApplication()->input->get('surv_id',0);
                $this->set('survey_id',$survey_id);
                echo parent::display( 'canvas/survey' );
            }elseif($action == 'manage'){
                echo parent::display( 'canvas/authoring' );
            }
        }
	}
}