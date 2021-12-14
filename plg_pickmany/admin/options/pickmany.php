<?php
/**
* Survey Force Deluxe component for Joomla 3
* @package Component.Surveyforce
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php
JToolBarHelper::title( ($row->id ? JText::_('COM_SURVEYFORCE_EDIT_QUESTION') : JText::_('COM_SURVEYFORCE_NEW_QUESTION')).' ('.(($q_om_type == 1)?JText::_('COM_SURVEYFORCE_LIKERT_SCALE'):(JText::_('COM_SURVEYFORCE_PICK').(($q_om_type == 2)?JText::_('COM_SURVEYFORCE_ONE'):JText::_('COM_SURVEYFORCE_MANY'))) ) .')', 'static.png' );
if ( strpos(JUri::base(), 'administrator/') )
	$setDefaultLink = "index.php?option=com_surveyforce&view=set_default&id=".$row->id;
else
	$setDefaultLink = JRoute::_('index.php?option=com_surveyforce').'?task=set_default&id='. $row->id;
?>
<table class="table table-striped" width="100%">
	<tr>
		<td colspan="2">
			<script type="text/javascript" language="javascript" >
				jQuery.noConflict();
				var sf_is_loading = false;
			</script>
			<table class="table table-striped" id="show_quest">
				<tr>
					<th class="title" colspan="4"><?php echo JText::_('COM_SURVEYFORCE_DONT_SHOW_QUESTION'); ?></th>
				</tr>
				<?php if (is_array($lists['quest_show']) && count($lists['quest_show']))
					foreach($lists['quest_show'] as $rule) {
						?>
						<tr>
							<td width="375px;"> <?php echo JText::_('COM_SURVEYFORCE_FOR_QUESTION'); ?> "<?php echo $rule->sf_qtext;?>" <input type="hidden" name="sf_hid_rule2_id[]" value="<?php echo $rule->bid;?>" /><input type="hidden" name="sf_hid_rule2_alt_id[]" value="<?php echo $rule->fdid;?>" /><input type="hidden" name="sf_hid_rule2_quest_id[]" value="<?php echo $rule->qid;?>" /></td>
							<td colspan="2"> <?php echo JText::_('COM_SURVEYFORCE_ANSWER_IS'); ?> "<?php echo $rule->qoption;?>"</td>
							<td><a href="javascript: void(0);" onclick="javascript:Delete_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a></td>
						</tr>
					<?php }?>
			</table>
			<table width="100%"  id="show_quest2" class="table table-striped">
				<tr>
					<td style="width:70px;"><?php echo JText::_('COM_SURVEYFORCE_FOR_QUESTION'); ?> </td><td style="width:15px;"><?php echo $lists['quests3'];?></td>
					<td width="auto" colspan="2" ><div id="quest_show_div"></div>
					</td>
				</tr>
				<tr>
					<td colspan="4" style="text-align:left;"><input class="btn" id="add_button" type="button" name="add" value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onclick="javascript: if(!sf_is_loading) addRow();"  />
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<br />

<small><?php echo JText::_('COM_SURVEYFORCE_DOUBLE_CLICK_TO_EDIT_OPTION'); ?></small>
<table class="table table-striped" id="qfld_tbl">
	<tr>
		<th width="20px" align="center">#</th>
		<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_QUESTION_OPTIONS'); ?></th>
		<th width="20px" align="center" class="title"></th>
		<th width="20px" align="center" class="title"></th>
		<th width="20px" align="center" class="title"></th>
		<th width="auto"></th>
	</tr>
	<?php

	$k = 0; $ii = 1; $ind_last = count($lists['sf_fields']);
	$other_option = null;

	foreach ($lists['sf_fields'] as $frow) {
		if (isset($frow->is_main) && $frow->is_main == 0) {
			$other_option = $frow;
			continue;
		}
		?>
		<input type="hidden" name="old_sf_hid_field_ids[]" value="<?php echo $frow->id?>"/>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $ii?></td>
			<td align="left" onDblClick="edit_name(event, 'sf_hid_fields[]', 'sf_hid_field_ids[]');"><input type="hidden" name="sf_hid_fields[]" value="<?php echo $frow->ftext?>"/><input type="hidden" name="sf_hid_field_ids[]" value="<?php echo $frow->id?>"/>
				<?php echo $frow->ftext?>

			</td>
			<td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a></td>
			<td><?php if ($ii > 1) { ?><a href="javascript: void(0);" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a><?php } ?></td>
			<td><?php if ($ii < $ind_last) { ?><a href="javascript: void(0);" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"></a><?php } ?></td>
			<td></td>
		</tr>
		<?php
		$k = 1 - $k; $ii ++;
	} ?>
</table>

<table width="100%" class="table table-striped" >
	<tr class="<?php echo "row$k"; ?>">
		<td width="20px" align="center"><input type="checkbox" onchange="javascript:Add_fields_to_select();" name="other_option_cb" id="other_option_cb" value="2"  <?php echo (($other_option != null && !isset($lists['other_option'])) || (isset($lists['other_option']) && $lists['other_option'] == 1) ?'checked="checked"':'')?> /></td>
		<td align="left" colspan="5"><?php echo JText::_('COM_SURVEYFORCE_OTHERS_OPTION'); ?> <input onkeyup="javascipt: Add_fields_to_select();"  class="text_area" style="width:120px " type="text" name="other_option" id="other_option" value="<?php echo ($other_option == null?'Other':$other_option->ftext)?>">
			<input type="hidden" name="other_op_id" value="<?php echo ($other_option == null?'0':$other_option->id)?>"/>
		</td>
	</tr>
</table>

<div style="text-align:left; padding-left:30px ">
	<input id="new_field" class="text_area" style="width:205px " type="text" name="new_field">
	<input class="btn" type="button" name="add_new_field" style="width:70px " value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'sf_hid_fields[]', 'sf_hid_field_ids[]');">
</div>

<br />
<table width="100%" class="table table-striped" >
	<tr class="<?php echo "row$k"; ?>">
		<td width="100" align="left"><input class="btn" type="button" name="set_default" value="<?php echo JText::_('COM_SURVEYFORCE_SET_DEFAULT'); ?>" onClick="<?php echo ($row->id > 0?"javascript: location.href = '".$setDefaultLink."';":"javascript: alert('".JText::_('COM_SURVEYFORCE_YOU_CAN_SET_DEFAULT_ANSWERS')."');")?>"></td>
		<td align="left" colspan="5">
			<?php echo JText::_('COM_SURVEYFORCE_MAX_NUMBER_CHECKED_OPTIONS'); ?>
			( <small><?php echo JText::_('COM_SURVEYFORCE_SET_ZERO_IF_THERE_NO_MAXIMUM'); ?></small> )
			<input type="number" value="<?php echo (!empty($sf_num_options) ? $sf_num_options : 0); ?>"
                   id="jform_sf_num_options" name="jform[sf_num_options]" style="width:120px;" class="text_area" aria-invalid="false"
                   min="0" oninput="this.value=(this.value<Number(this.min) ? this.min : this.value);" >
		</td>
	</tr>
</table>
<br />
<table class="table table-striped">
	<tr>
		<th width="20px" align="center">#</th>
		<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_QUESTION_RULES'); ?></th>
		<th width="20px" align="center" class="title"></th>
		<th width="20px" align="center" class="title"></th>
		<th width="20px" align="center" class="title"></th>
		<th width="auto"></th>
	</tr></table>

<table class="table table-striped" id="qfld_tbl_rule">
	<tr>
		<th width="2%" align="center">#</th>
		<th class="title" width="14%"><?php echo JText::_('COM_SURVEYFORCE_ANSWER'); ?></th>
		<th class="title" width="22%"><?php echo JText::_('COM_SURVEYFORCE_QUESTION'); ?></th>
		<th class="title" width="22%"><?php echo JText::_('COM_SURVEYFORCE_PRIORITY'); ?></th>
		<th width="2%" align="left" class="title"></th>
		<th width="2%" align="left" class="title"></th>
		<th width="auto"></th>

		<th width="auto"></th>

	</tr>

	<?php
	$k = 0; $ii = 1; $ind_last = count($lists['sf_fields_rule']);
	if($ind_last)
		foreach ($lists['sf_fields_rule'] as $rrow) { ?>
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $ii?></td>
				<td align="left">
					<?php echo $rrow->ftext;?>
					<input type="hidden" name="sf_hid_rule[]" value="<?php echo $rrow->ftext?>">
				</td>
				<td align="left">
					<?php echo $rrow->next_quest_id . ' - ' . (strlen(strip_tags($rrow->sf_qtext)) > 55? mb_substr(strip_tags($rrow->sf_qtext), 0, 55).'...': strip_tags($rrow->sf_qtext))?>
					<input type="hidden" name="sf_hid_rule_quest[]" value="<?php echo $rrow->next_quest_id?>">
				</td>
				<td>
					<input type="text" style="text-align:center" class="text_area" name="priority[]" size="3" value="<?php echo $rrow->priority?>" />
				</td>
				<td><a href="javascript: void(0);" onClick="javascript:Delete_tbl_row2(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>

			</tr>
			<?php
			$k = 1 - $k; $ii ++;
		} ?>
</table>
<br/>
<div style="text-align:left; padding-left:30px ">
	<input type="checkbox" name="super_rule" value="1" <?php echo $lists['checked']; ?> />&nbsp;<?php echo JText::_('COM_SURVEYFORCE_GO_TO_QUESTION'); ?> <?php echo $lists['quests2']; ?> <?php echo JText::_('COM_SURVEYFORCE_NEXT_REGARDLESS_WHAT_ANSWER'); ?><br />
	<small><?php echo JText::_('COM_SURVEYFORCE_TO_OVERRIDE_THIS_RULE'); ?></small>
</div><br />
<div style="text-align:left; padding-left:30px ">

	<?php echo JText::_('COM_SURVEYFORCE_IF'); ?><?php echo JText::_('COM_SURVEYFORCE_ANSWER_IS'); ?> <?php echo $lists['sf_list_fields']; ?>,

	<?php echo JText::_('COM_SURVEYFORCE_GO_TO_QUESTION'); ?><?php echo $lists['quests']; ?>, <?php echo JText::_('COM_SURVEYFORCE_PRIORITY'); ?> <input type="text" style="text-align:center" class="text_area" name="new_priority" id="new_priority" size="3" value="0" />

	<input class="btn" type="button" name="add_new_rule"  value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field2('sf_field_list', 'qfld_tbl_rule', 'sf_hid_rule[]', 'sf_quest_list', 'sf_hid_rule_quest[]');">

</div>
<br />