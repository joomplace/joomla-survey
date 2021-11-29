<?php
/**
* Survey Force Deluxe component for Joomla 3 3.0
* @package Survey Force Deluxe
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');
 
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $ordering = $listOrder == 'ordering';
$user		= JFactory::getUser();
$userId		= $user->get('id');
$extension  = 'com_surveyforce';


$saveOrder	= $listOrder == 'ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_surveyforce&task=listusers.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<?php echo $this->loadTemplate('menu');?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	Joomla.submitbutton = function(task)
	{
		if (task == 'listusers.invite_users' || task == 'listusers.remind_users') {
			if (document.adminForm.boxchecked.value < 1) {
				alert('Please first make a selection from the list');
			}
			else {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
		else
			Joomla.submitform(task, document.getElementById('adminForm'));
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=listusers'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php endif;?>
    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                    <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG');?></label>
                    <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                    <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                    <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                    <label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
                    <?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                    <label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
                    <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
                            <option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
                            <option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
                    </select>
            </div>
            <div class="btn-group pull-right">
                    <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
                    <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
                            <?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
                    </select>
            </div>
        </div>
        <div class="clearfix"> </div>
        <table class="table table-striped" id="surveyforceList">
            		<thead>
				<tr>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 'id', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
					</th>
					<th  class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_LIST_OF_USERS', 'listname', $listDirn, $listOrder); ?> 
					</th>
					<th>
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_USERS_COUNT', 'sf_catdescr', $listDirn, $listOrder); ?>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_STARTS', 'published', $listDirn, $listOrder); ?>
					</th>
										
					<th  class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_SURVEY', 'sf_author_id', $listDirn, $listOrder); ?>
					</th>
                                        <th>
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_AUTHOR', 'date_created', $listDirn, $listOrder); ?>
					</th>
					<th width="120" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_CREATED', 'date_invited', $listDirn, $listOrder); ?>
					</th>
					<th  width="120" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_INVITED', 'date_invited', $listDirn, $listOrder); ?>
					</th>										
					<th  width="120" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_REMINDED', 'date_remind', $listDirn, $listOrder); ?>
					</th>
                                        
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="13">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering  = ($listOrder == 'ordering');
				$canEdit	= $user->authorise('core.edit',			$extension.'.listusers.'.$item->id);
                                $canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
                                $canChange	= $user->authorise('core.edit.state',	$extension.'.listusers.'.$item->id) && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">
                                    <td class="center">
						<?php echo $item->id; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="nowrap has-context">
                        <div class="pull-left">
                        <?php if ($canEdit) : ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=users&id='.$item->id);?>"><?php echo $this->escape($item->listname); ?></a>
                        <?php else : ?>
                                <?php echo $this->escape($item->listname); ?>
                        <?php endif; ?>
                        </div>
					</td>
					<td class="has-context">
                        <div class="pull-left">
                        <?php echo $item->users_count; ?>
                        </div>
					</td>
					<td class="center">
                       <div class="pull-left">
                        <?php echo $item->starts; ?>
                        </div>
					</td>
                    <td class="center">
                        <div class="pull-left">
                        <?php echo $item->survey_name; ?>
                        </div>
					</td>
	                <td class="center">
	                    <div class="pull-left">
	                    <?php echo $item->author; ?>
	                    </div>
					</td>
                    <td class="center">
                        <div class="pull-left">
                        <?php echo $item->date_created; ?>
                        </div>
					</td>
                    <td class="center">
                        <div class="pull-left">
                        <?php if($item->date_invited == '0000-00-00 00:00:00'){ echo '-'; } else { echo $item->date_invited;} ?>
                        </div>
					</td>
                    <td class="center">
                        <div class="pull-left">
                        <?php if($item->date_remind == '0000-00-00 00:00:00'){ echo '-'; } else { echo $item->date_remind; }?>
                        </div>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
        
    </div>
</form>