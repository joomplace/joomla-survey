<?php
JToolBarHelper::title( ($row->id ? JText::_('COM_SURVEYFORCE_EDIT_QUESTION') : JText::_('COM_SURVEYFORCE_NEW_QUESTION')).' ('.(($q_om_type == 4)?JText::_('COM_SURVEYFORCE_SHORTANSWER'):'' ) .')', 'static.png' );
if ( strpos(JUri::base(), 'administrator/') )
	$setDefaultLink = "index.php?option=com_surveyforce&view=set_default&id=".$row->id;
else
	$setDefaultLink = JRoute::_('index.php?option=com_surveyforce').'?task=set_default&id='. $row->id;
?>
<br/>
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

