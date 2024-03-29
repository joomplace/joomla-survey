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
$listOrder	= $this->state->get('list.ordering', 'us.id');
$listDirn	= $this->state->get('list.direction', 'asc');

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

</script>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=authors&layout=users'); ?>" method="post" id="adminForm" name="adminForm" >
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
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 'us.id', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)"/>
				</th>
				<th class="nowrap left">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_NAME', 'us.name', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_USERNAME', 'us.username', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_EMAIL', 'us.email', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_LAST_VISIT', 'us.lastvisitDate', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo $item->id; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php echo $item->name; ?>
					</td>
					<td class="center">
						<?php echo $item->username; ?>
					</td>
					<td class="center">
						<?php echo $item->email; ?>
					</td>
					<td class="center">
						<?php echo $item->lastvisitDate; ?>
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