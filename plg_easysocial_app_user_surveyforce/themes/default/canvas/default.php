<?php

defined( '_JEXEC' ) or die( 'Unauthorized Access' );

?>

<?php
$manageSurveyLink = JRoute::_("index.php?option=com_easysocial&view=apps&id=".$this->vars['app']->id.":surveyforce-easysocial&layout=canvas&action=manage");

$writeNewLink = '<div style="font-size: 18px; color: rgba(0, 139, 204, 1);" class="surveymanage"><a class="sfbutton" href="'.$manageSurveyLink.
    '"><span>'.JText::_("PLG_SURVEYFORCE_MANAGEMENT").'</span></a></div><br/>';

if($isOwner) {
    echo $writeNewLink;
}
if ($data_exist) {

    foreach( $rows as $row ){
        //$surveyLink = JRoute::_("index.php?option=com_surveyforce&view=survey&id=".$row->id)."&tmpl=component&keepThis=true&TB_iframe=true";
        $viewSurveyLink = JRoute::_("index.php?option=com_easysocial&view=apps&id=".$this->vars['app']->id.":surveyforce-easysocial&layout=canvas&action=view&surv_id=".$row->id);
    ?>
        <div class=row-fluid>
            <div class="span12">
            <div class="row-fluid">
                <div class="span7 survey_title" style="margin:10px 0 20px;">
                    <a href="<?php echo $viewSurveyLink;?>" ><?php echo $row->sf_name;?></a>
                </div>
                <div class="span7 offset1 survey_description">
                    <?php echo $row->surv_short_descr;?>
                </div>
            </div>
            </div>
        </div>
    <?php
    }

    jimport('joomla.html.pagination');
    $pagination	= new JPagination( $surveysCount , $limitstart , $limit );
    echo '
        <div class="row-fluid">
          <div class="span6 offset4">
            <div class="pagination">
                '.$pagination->getPagesLinks().'
            </div>
           </div>
        </div>';
}



?>