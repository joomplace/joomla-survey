<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Survey Force Deluxe
* @author JoomPlace Team
* @copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$option = 'com_surveyforce';
$app = JFactory::getApplication();
$new_qtype_id = $app->getUserStateFromRequest( "question.new_qtype_id", 'new_qtype_id', 0 );
$surv_id = $app->getUserStateFromRequest( "question.sf_survey", 'sf_survey', 0 );

if (class_exists('JToolBar')) {
	$bar = JToolBar::getInstance('toolbar');
	// Add a cancel button
	$bar->appendButton( 'Standard', 'next', JText::_('COM_SURVEYFORCE_NEXT'), 'question.add', false, true );
	$bar->appendButton( 'Standard', 'cancel', JText::_('COM_SURVEYFORCE_CANCEL'), 'cancel', false, false ); 
}

$i = 0;
$countOfQuest = !empty($this->questions) ? count($this->questions) : 0;
?>
<script language="javascript" type="text/javascript" src="<?php echo JURI::root();?>administrator/components/com_surveyforce/assets/js/thickbox/thickbox.js" ></script>
<style type="text/css" >
	label { cursor:pointer;width: auto !important;}
	.btn-toolbar {float:right;}
</style>
<script>
    Joomla.submitbutton = function (pressbutton) {
        var form = document.adminForm;
        var elem = document.getElementsByName('new_qtype_id');

        var flag = false;
        for(var i=0;i < elem.length;i++){
            if(elem[i].checked == true){
                flag = true;
            }
        }

        if(!flag && pressbutton != 'cancel'){
            alert('<?php echo $this->escape(JText::_('COM_SURVEYFORCE_CHOOSE_TYPE'));?>');
            return false;
        } else {

            if (pressbutton == 'cancel') {
                parent.tb_remove();
                return;
            }

            form.submit();
        }

    }
</script>
<style>
	#question_type_description{
		margin:20px;
		font-size: 16px;
		padding: 10px;
	}
</style>
<?php 

$original_titles = array();
foreach ($this->questions as $p => $question) {
	
	switch(JText::_($question->sf_qtype)){
		case JText::_('COM_SURVEYFORCE_LIKERTSCALE'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_LIKERTSCALE').'</strong> - allows users to measure their attitude towards something.';
		break;
		case JText::_('COM_SURVEYFORCE_PICKONE'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_PICKONE').'</strong> - allows users to choose a single item from several choices.';
		break;
		case JText::_('COM_SURVEYFORCE_PICKMANY'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_PICKMANY').'</strong> - is a multiple response question.';
		break;
		case JText::_('COM_SURVEYFORCE_SHORT_ANSWER'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_SHORT_ANSWER').'</strong> - requires a short answer (it can be a single word or a phrase).';
		break;
		case JText::_('COM_SURVEYFORCE_RANKING_DROPDOWN'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_RANKING_DROPDOWN').'</strong> - allows users to rank items in a preferred order by selecting from a series of drop-down lists.';
		break;
		case JText::_('COM_SURVEYFORCE_RANKING_DRAGNDROP'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_RANKING_DRAGNDROP').'</strong> - allows users to rank a list of items using a simple drag-and-drop interface.';
		break;
		case JText::_('COM_SURVEYFORCE_BOILERPLATE'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_BOILERPLATE').'</strong> - does not require a response (the user may be asked to read some text, watch video,etc.).';
		break;
		case JText::_('COM_SURVEYFORCE_PAGE_BREAK'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_PAGE_BREAK').'</strong> - allows to separate questions and create new pages in a survey.';
		break;
		case JText::_('COM_SURVEYFORCE_S_RANKING'):
			$original_title[$p] = '<strong>'.JText::_('COM_SURVEYFORCE_S_RANKING').'</strong> - allows you to present your site users with a list of possible answers/options, which they may then rank in order of preference.';
		break;
		default:
			$original_title[$p] = 'None';
		break;
	}
}

?>
<form onsubmit="return false" action="index.php?option=com_surveyforce&view=question&layout=edit&surv_id=<?php echo $surv_id; ?>" method="post" id="adminForm" name="adminForm" target="_parent" enctype="multipart/form-data">
	<fieldset class="adminform">
	<legend><?php echo JText::_('COM_SURVEYFORCE_SELECT_NEW_QUESTION_TYPE');?></legend>
	<?php if (class_exists('JToolBar')) { echo $bar->render(); } ?>
		<table width="100%" cellpadding="2" cellspacing="2" class="admintable">
        <?php if(!empty($this->questions)): ?>
		<?php while($i < count($this->questions)):?>
			
			<tr>
				<td width="50%">
					<label for="new_qtype_id_<?php echo $this->questions[$i]->id?>"><input type="radio" onclick="Joomla.isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->questions[$i]->id?>" value="<?php echo $this->questions[$i]->id?>" <?php echo ($new_qtype_id == $this->questions[$i]->id? ' checked="checked" ': '')?> /> <span class="hasTooltip" data-original-title="<?php echo $original_title[$i]; ?>"><?php echo JText::_($this->questions[$i]->sf_qtype);?></span></label>
				</td>
				<td width="50%">
					<?php if(isset($this->questions[$i+1])):?>
					<label for="new_qtype_id_<?php echo $this->questions[$i+1]->id?>"><input type="radio" onclick="Joomla.isChecked(this.checked);" name="new_qtype_id" id="new_qtype_id_<?php echo $this->questions[$i+1]->id?>" value="<?php echo $this->questions[$i+1]->id?>" <?php echo ($new_qtype_id == $this->questions[$i+1]->id? ' checked="checked" ': '')?> /><span class="hasTooltip" data-original-title="<?php echo $original_title[$i+1]; ?>"><?php echo JText::_($this->questions[$i+1]->sf_qtype);?></span></label>
					<?php endif;?>
				</td>
			</tr>
		<?php $i = $i + 2;?>
		<?php endwhile; ?>
        <?php endif; ?>
		</table>
		<div id="question_type_description"></div>
	</fieldset>
			
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="com_surveyforce" />
	<input type="hidden" name="c_id" value="0" />
	<input type="hidden" name="surv_id" value="<?php echo $surv_id; ?>" />
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
	
	jQuery('.hasTooltip').mouseenter(function(){
    	var title = jQuery(this).attr('data-original-title');
    	jQuery('#question_type_description').html("");
    	jQuery('#question_type_description').html(title);
    	
    });

    jQuery('.hasTooltip').mouseleave(function(){
    	jQuery('#question_type_description').html("");
    });
</script>