<?php
/**
 * Survey Force Deluxe component for Joomla 3
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('formbehavior.chosen', 'select');
$app = JFactory::getApplication();
$input = $app->input;
$surveys_list = $this->surveys;
$ordering_list = $this->ordering_list;
?>
<?php echo $this->loadTemplate('menu'); ?>
<style>
    .adminlist img{
        max-height: 250px;
    }
</style>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce?&id=' . (int) $this->report['start_data']->id); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
	<input type="hidden" name="boxchecked" value="1" />
	<input type="hidden" name="cid[]" value="<?php echo $this->report['start_data']->id; ?>" />
<table class="table table-striped">
    <tr>
        <th colspan="2" align="left"><?php echo JText::_('COM_SURVEYFORCE_SURVEY_INFORMATION'); ?></th>
    </tr>
    <tr>
        <td>
            <b><?php echo JText::_('COM_SURVEYFORCE_NAME'); ?>: </b><?php echo $this->report['survey_info']->sf_name ?><br>
            <b><?php echo JText::_('COM_SURVEYFORCE_DESCRIPTION'); ?></b><br><?php echo nl2br($this->report['survey_info']->sf_descr) ?><br>
            <b><?php echo JText::_('COM_SURVEYFORCE_START_AT'); ?>: </b><?php echo $this->report['start_data']->sf_time ?><br>
            <b><?php echo JText::_('COM_SURVEYFORCE_USER'); ?> :</b>
            <?php
            switch ($this->report['start_data']->usertype) {
                case '0': echo JText::_('COM_SURVEYFORCE_ANONYMOUS');
                    break;
                case '1': echo JText::_('COM_SURVEYFORCE_REGISTERED_USER') . ": " . $this->report['start_data']->reg_username . ", " . $this->report['start_data']->reg_name . " (" . $this->report['start_data']->reg_email . ")";
                    break;
                case '2': echo JText::_('COM_SURVEYFORCE_INVITED_USER') . ": " . $this->report['start_data']->inv_name . " " . $this->report['start_data']->inv_lastname . " (" . $this->report['start_data']->inv_email . ")";
                    break;
            }
            ?>

        </td>
    </tr>
</table>

<div class="clearfix"></div>

<div class="list-questions">
	<?php 

	foreach ($this->report['questions'] as $qrow) {
	$k = 1;?>
	<table class="table table-striped">
		<tr class="row0">
			<th colspan="2" align="left"><?php echo $qrow['question']->sf_qtext; ?></th>
		</tr>
		<?php

		switch ($qrow['question']->sf_qtype) {
		/*
			case 2:
				$a_printed = 0;
				foreach($qrow['answer_data'] as $ans){
					if($ans['alt_text']){ 
						echo "<tr class='row".$k."'><td colspan='2'>" . $ans['f_text'] . "</td></tr>";
						$a_printed = 1;
					}
				}
				if(!$a_printed){
						echo "<tr class='row".$k."'><td colspan='2'>-</td></tr>";
				}
				break;
			case 3:
				foreach($qrow['answer_data'] as $ans){
					if($ans) echo "<tr class='row".$k."'><td colspan='2'>" . $ans['f_text'] . "</td></tr>";
				}
				break;
				*/
			case 2:
				foreach ($qrow['answer_data']['answers'] as $arow) {
					$img_ans = $arow['alt_text'] ? "<img src='".JURI::root()."administrator/components/com_surveyforce/assets/images/tick.png'  border='0' />" : '';
					echo "<tr class='row".$k."'><td width='1%'>" . $img_ans . "</td><td width='30%'>" . $arow['f_text'] . "</td></tr>";
					$k = 1 - $k;
				}
				break;
			case 3:
				foreach ($qrow['answer_data']['answers'] as $answ) {
					foreach ($answ as $arow) {
						$img_ans = $arow['alt_text'] ? "<img src='".JURI::root()."administrator/components/com_surveyforce/assets/images/tick.png'  border='0' />" : '';
						echo "<tr class='row".$k."'><td width='30%'>" . $arow['f_text'] . "</td><td>" . $img_ans . "</td></tr>";
						$k = 1 - $k;
					}
				}
				break;
				
			case 1:	
				echo "<tr class='row".$k."'><td colspan=2><b>Scale: </b>" . $qrow['answer_data']['answers']['scale'] . "</td></tr>";
				if(count($qrow['answer_data']['answers']['answer'])){
					foreach ($qrow['answer_data']['answers']['answer'] as $arow) {
						echo "<tr class='row".$k."'><td colspan=2><b>".$arow['f_text'].": </b>" . $arow['alt_text'] . "</td></tr>";
					}
				}

				$k = 1 - $k;
				break;
			case 5:	
			case 6:			
			case 9:
				if(count($qrow['answer_data']['answers']['answer'])){
					foreach ($qrow['answer_data']['answers']['answer'] as $arow) {
						echo "<tr class='row".$k."'><td width='30%'>" . $arow['f_text'] . "</td><td>" . $arow['alt_text'] . "</td></tr>";
				}
				}
				$k = 1 - $k;
				break;
			case 4: //TODO: check work

				if (isset($qrow['answer_data']['answers']['answer_count'])){
					$tmp = JText::_('COM_SURVEYFORCE_1ST_ANSWER');
					for($ii = 1; $ii <= $qrow['answer_data']['answers']['answer_count']; $ii++) {
						if ($ii == 2) $tmp = JText::_('COM_SURVEYFORCE_SECOND_ANSWER');
						elseif($ii == 3)	$tmp = JText::_('COM_SURVEYFORCE_THIRD_ANSWER');
						elseif ($ii > 3) $tmp = $ii. JText::_('COM_SURVEYFORCE_X_ANSWER');
						foreach($qrow['answer_data']['answers']['answer'] as $answer) {
							if ($answer->ans_field == $ii) {
								echo "<tr class='row".$k."'><td width='30%'>".$tmp.nl2br(($answer->ans_txt == ''? JText::_('COM_SURVEYFORCE_NO_ANSWER'):$answer->ans_txt))."</td><td>&nbsp;</td></tr>";
								$k = 1 - $k;
								$tmp = -1;
							}
						}
						if ($tmp != -1)	{
							echo "<tr class='row".$k."'><td width='30%'>".$tmp.JText::_('COM_SURVEYFORCE_NO_ANSWER')."</td><td>&nbsp;</td></tr>";
							$k = 1 - $k;
						}
					}
				}
				else {
					echo "<tr class='row".$k."'><td width='30%'>".nl2br($qrow['answer_data']['answers']['answer'])."</td><td>&nbsp;</td></tr>";
				}
				break;
			default:
				echo "<tr class='row".$k."'><td width='30%'>".nl2br($qrow['answer_data']['answers']['answer'])."</td><td>&nbsp;</td></tr>";
				break;
		}
		?>
	</table>
	<?php if ($qrow['question']->sf_impscale) {?>
		<table class="adminlist">
			<tr>
				<td colspan="2" align="left"><b><?php echo $qrow['answer_data']['imp_answers']->iscale_name ?></b></td>
			</tr>
			<?php
			foreach ($qrow['answer_data']['imp_answers']->answer_imp as $arow) {
				$img_ans = $arow->alt_text ? "<img src='".JURI::root()."administrator/components/com_surveyforce/assets/images/tick.png'  border='0' />" : '';
				echo "<tr class='row".$k."'><td width='30%'>" . $arow->f_text . "</td><td>" . $img_ans . "</td></tr>";
				$k = 1 - $k;
			}
			?>
		</table>
	<?php } ?>
	<br>
	<?php } ?>

</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>

 <div class="clearfix"></div>