<?php

    defined( '_JEXEC' ) or die( 'Unauthorized Access' );

    $_SESSION['view'] = 'authoring';
    $dir_ry = JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_surveyforce'.DIRECTORY_SEPARATOR;
    //include_once( $dir_ry ."helpers/surveyforce.php") ;

    $lang = JFactory::getLanguage();
    $extension = 'com_surveyforce';
    $base_dir = JPATH_SITE;
    $language_tag = 'en-GB';
    $reload = true;
    $lang->load($extension, $base_dir, $language_tag, $reload);


    $extension = 'com_surveyforce';
    $base_dir = JPATH_ADMINISTRATOR;
    $language_tag = 'en-GB';
    $reload = true;
    $lang->load($extension, $base_dir, $language_tag, $reload);

    require_once( $dir_ry ."helpers".DIRECTORY_SEPARATOR."edit.surveyforce.html.php") ;
    require_once( $dir_ry ."helpers".DIRECTORY_SEPARATOR."surveyforce.php") ;
    require_once( $dir_ry ."models".DIRECTORY_SEPARATOR."authoring.php") ;
    $model = new SurveyforceModelAuthoring();


?>
<style>
    #limit { width: 55px; }

    #survey-edit.tabs{
        top: 2px;
        position: relative;
    }

    div#fd dl{
        margin-bottom: 0px !important;
    }

    dt.tabs h3{
        margin: 4px 0px 4px 0px !important;
        font-size: 16px !important;
    }
    .inputbox{
        height: 25px !important;
    }

</style>
<script>
jQuery(document).ready(function(){

    jQuery('a.btn[title=Cancel]').bind('click', function(){
        window.history.go(-1);
        return false;
    });

    jQuery('a.btn[title=Back]').click( function(){
        window.history.go(-1);
        return false;
    });

});
</script>

<?php
    echo $model->getPage();
?>
