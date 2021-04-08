<?php
/**
* Joomlaquiz component for Joomla 3.0
* @package Joomlaquiz
* @author JoomPlace Team
* @Copyright Copyright (C) JoomPlace, www.joomplace.com
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');
JHtml::_('behavior.multiselect');
$extension  = 'com_surveyforce';
$rows = $this->items;

?>
<?php echo $this->loadTemplate('menu');?>
<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=templates'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<?php endif;?>
	<div id="j-main-container" class="span10">

	<table class="table table-striped" id="surveyforceList">
		<thead>
		<tr>
			<th width="1%" class="nowrap center">#</th>
			<th width="1%" class="hidden-phone">
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th class="nowrap center"><?php echo JText::_('COM_SURVEYFORCE_NAME'); ?></th>
			<th class="nowrap center"><?php echo JText::_('COM_SURVEYFORCE_TEMPLATE_SYSTEMNAME'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($rows); $i < $n; $i++) {
			$row = $rows[$i];
			$link 	= 'index.php?option=com_surveyforce&view=template&id='. $row->id;
			//JHTML::_('tooltip', $tooltip, $title, $image, $text, $href, $link);

			$thumbPath = '/components/com_surveyforce/templates/'.$row->sf_name.'/thumb.jpg';

			if ( file_exists(JPATH_ROOT.$thumbPath) )
			{
				$displayName = JHTML::_('tooltip', '<img src="'.JUri::root().$thumbPath.'" />"', '', '', $row->sf_display_name, $link, $row->sf_display_name);
			}
			else
			{
				$displayName = '<a href="'.$link.'">'.$row->sf_display_name.'</a>';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $row->id; ?></td>
				<td><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
				<td align="left">
						<?php echo $displayName;?>
				</td>
				<td align="center" class="center"><a href="<?php echo $link;?>">
						<?php echo $row->sf_name;?>
					</a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}?>
		</tbody>
		<tfoot>
			<tr><td colspan="4"><?php echo JText::_('COM_SURVEYFORCE_IF_YOU_HAVE_ZIP_FILE'); ?></td> </tr>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>

</form>