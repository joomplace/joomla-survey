<?php
/**
 * Survey Force Deluxe component for Joomla 3 3.0
 * @package Survey Force Deluxe
 * @author JoomPlace Team
 * @Copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted access');

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
	$saveOrderingUrl = 'index.php?option=com_surveyforce&task=users.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

?>
<?php echo $this->loadTemplate('menu'); ?>
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
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=users&id='.$this->listid); ?>" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm" class="form-validate">
     <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search" class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG');?></label>
                <input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
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
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_NAME', 'name', $listDirn, $listOrder); ?> 
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_LASTNAME', 'lastname', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_EMAIL', 'email', $listDirn, $listOrder); ?>
				</th>                                   
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering  = ($listOrder == 'ordering');
			$canEdit	= $user->authorise('core.edit',			$extension.'.users.'.$item->id);
            $canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
            $canChange	= $user->authorise('core.edit.state',	$extension.'.users.'.$item->id) && $canCheckin;
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
                    	<?php echo $this->escape($item->name); ?>
                    </div>
				</td>
				<td class="has-context">
                    <div class="pull-left">
                    <?php echo $item->lastname; ?>
                    </div>
				</td>
				<td class="center">
                   <div class="pull-left">
                    <?php echo $item->email; ?>
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
	<input type="hidden" name="listid" value="<?php echo $this->listid; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>