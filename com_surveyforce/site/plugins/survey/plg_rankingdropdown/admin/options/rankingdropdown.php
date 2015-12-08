<?php 
JToolBarHelper::title( ($row->id ? JText::_('COM_SURVEYFORCE_EDIT_QUESTION') : JText::_('COM_SURVEYFORCE_NEW_QUESTION')).' ('.JText::_('COM_SURVEYFORCE_RANKING').(($q_om_type == 5)?JText::_('COM_SURVEYFORCE_DROPDOWN'):JText::_('COM_SURVEYFORCE_DRAG_N_DROP')).')', 'static.png' );
if ( strpos(JUri::base(), 'administrator/') )
	$setDefaultLink = "index.php?option=com_surveyforce&view=set_default&id=".$row->id;
else
	$setDefaultLink = JRoute::_('index.php?option=com_surveyforce').'?task=set_default&id='. $row->id;
?>
<table width="100%">
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



<small><?php echo JText::_('COM_SURVEYFORCE_DOUBLE_CLICK_TO_NAME'); ?></small>
<table class="table table-striped" id="qfld_tbl">
		<tr>
			<th width="20px" align="center">#</th>
			<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_NAME'); ?></th>
			<th class="title" width="200px"><?php echo JText::_('COM_SURVEYFORCE_ALT_NAME'); ?></th>
			<th width="20px" align="center" class="title"></th>
			<th width="20px" align="center" class="title"></th>
			<th width="20px" align="center" class="title"></th>		
			<th></th>
		</tr>
		<?php
		$k = 0; $ii = 1; $ind_last = count($lists['sf_fields']);
		foreach ($lists['sf_fields'] as $frow) { ?>
			<input type="hidden" name="old_sf_field_ids[]" value="<?php echo $frow->id?>">
			<input type="hidden" name="old_sf_alt_field_ids[]" value="<?php echo $frow->alt_field_id?>">
			<tr class="<?php echo "row$k"; ?>">
				<td align="center"><?php echo $ii?></td>
				<td align="left" ondblclick="edit_name(event, 'sf_fields[]', 'sf_field_ids[]');"><input type="hidden" name="sf_fields[]" value="<?php echo $frow->ftext?>"><input type="hidden" name="sf_field_ids[]" value="<?php echo $frow->id?>">
					<?php echo $frow->ftext?>

				</td>
				<td align="left" ondblclick="edit_name(event, 'sf_alt_fields[]', 'sf_alt_field_ids[]');"><input type="hidden" name="sf_alt_fields[]" value="<?php echo $frow->alt_field_full?>"><input type="hidden" name="sf_alt_field_ids[]" value="<?php echo $frow->alt_field_id?>">
					<?php echo $frow->alt_field_full?>

				</td>
				<td><a href="" onClick="javascript:Delete_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/publish_x.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_DELETE'); ?>"></a></td>
				<td><?php if ($ii > 1) { ?><a href="" onClick="javascript:Up_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/uparrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_UP'); ?>"></a><?php } ?></td>
				<td><?php if ($ii < $ind_last) { ?><a href="" onClick="javascript:Down_tbl_row(this); return false;" title="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"><img src="<?php echo JURI::root()?>administrator/components/com_surveyforce/assets/images/downarrow.png"  border="0" alt="<?php echo JText::_('COM_SURVEYFORCE_MOVE_DOWN'); ?>"></a><?php } ?></td>
				<td></td>
			</tr>
		<?php
		$k = 1 - $k; $ii ++;
		 } ?>
		</table><br>
	<div style="text-align:left; padding-left:30px ">
		<input id="new_field" class="text_area" style="width:205px " type="text" name="new_field">
		<input id="new_alt_field" class="text_area" style="width:205px " type="text" name="new_alt_field">
		<input class="btn" type="button" name="add_new_field" style="width:70px " value="<?php echo JText::_('COM_SURVEYFORCE_ADD'); ?>" onClick="javascript:Add_new_tbl_field('new_field', 'qfld_tbl', 'sf_fields[]', 'sf_field_ids[]', 'new_alt_field', 'sf_alt_fields[]', 'sf_alt_field_ids[]');">
	</div>



	<br />
	<table width="100%" class="table table-striped">
		<tr class="<?php echo "row$k"; ?>">
			<td width="100" align="left"><input class="btn" type="button" name="set_default" value="<?php echo JText::_('COM_SURVEYFORCE_SET_DEFAULT'); ?>" onClick="<?php echo ($row->id > 0?"javascript: location.href = '".$setDefaultLink."';":"javascript: alert('".JText::_('COM_SURVEYFORCE_YOU_CAN_SET_DEFAULT_ANSWERS')."');")?>"></td>
			<td align="left" colspan="5">
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