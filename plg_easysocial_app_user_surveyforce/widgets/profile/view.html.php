<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2012 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );


class SurveyForceWidgetsProfile extends SocialAppsWidgets{

	public function sidebarBottom( $user ){
        $out_st = '';
        $mainframe = JFactory::getApplication();
        $limitstart = $mainframe->input->get('limitstart',0);
        $limit = $mainframe->input->get('limit',10);
        $userId = $user->id;
        $userName = $user->name;
        $isOwner	= ($my->id == $userId ) ? true : false;
        $model  = $this->getModel('surveyforce');
        $surveysCount = $model->getSurveysCount();
        $rows	= $model->_getEntries($isOwner, $limitstart, $limit, $user);

        if($rows){
            $data_exist = 1;
        }else{
            $data_exist = 0;
        }

        if ($data_exist) {
    ?>
    <style>
        .surveysList{
            margin:10px 0 10px 0;
            border-bottom: 1px solid #D7D7D7;
        }

        .surveysHeader{
            border-bottom: 1px solid #D7D7D7;
            color: #666666;
            font-size: 12px;
            font-weight: bold;
        }

        .surveysList .surveyItem{
            margin:10px;
        }
    </style>
    <?php
        echo '<div class="surveysList">';
            echo '<div class="surveysHeader">User\'s surveys list:</div>';
            foreach( $rows as $row ){
                $viewSurveyLink = JRoute::_("index.php?option=com_easysocial&view=apps&id=".$this->getApp()->id.":surveyforce-easysocial&layout=canvas&action=view&surv_id=".$row->id);
                ?>

                   <div class="surveyItem">
                        <div class="row-fluid">
                            <div class="span7 survey_title">
                                <a href="<?php echo $viewSurveyLink;?>" ><?php echo $row->sf_name;?></a>
                            </div>
                            <div class="span7 offset1 survey_description">
                                <?php echo $row->surv_short_descr;?>
                            </div>
                        </div>
                    </div>

            <?php
            }
        echo '</div>';
        }else{
            echo 'There are no surveys for showing.';
        }


	}


	public function getPhotos( $user , $params ){

	}
}