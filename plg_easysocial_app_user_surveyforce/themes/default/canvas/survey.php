<?php

    defined( '_JEXEC' ) or die( 'Unauthorized Access' );

    if(empty($survey_id)){
        echo JText::_('PLG_APP_USER_SURVEYFORCE_WRORNG_URL');
    }else{
        $dir_ry = JPATH_SITE.'/components/com_surveyforce/';
        include_once( $dir_ry ."helpers/surveyforce.php") ;
        include_once( $dir_ry ."helpers/templates.php") ;
        $helper = new SurveyforceHelper();
        $init_array = $helper->SF_ShowSurvey($survey_id);

        $this->survey = $init_array['survey'];
        $this->sf_config = $init_array['sf_config'];
        $this->is_invited = $init_array['is_invited'];
        $this->invite_num = $init_array['invite_num'];
        $this->rules = $init_array['rules'];
        $this->preview = $init_array['preview'];
        include_once( $dir_ry ."views/survey/tmpl/default.php") ;
    }

?>