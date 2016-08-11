<?php
/**
 * Surveyforce Deluxe Component for Joomla 3
 * @package Surveyforce Deluxe
 * @author JoomPlace Team
 * @copyright Copyright (C) JoomPlace, www.joomplace.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 */
defined('_JEXEC') or die('Restricted Access');

?>
<div class="category-list">
	<div class="content-category">
		<h2><?php echo $this->item->sf_catname; ?></h2>
		<div class="category-desc"><?php echo $this->item->sf_catdescr; ?>
			<div class="clr"></div>
		</div>

		<div class="cat-children"><h3><?php echo JText::_('COM_SURVEYFORCE_SURVEYS'); ?></h3>
			<?php if ($this->item->surveys): ?>
				<?php foreach ( $this->item->surveys as $survey ) : ?>
					<h3 class="page-header item-title"><a href="<?php echo JRoute::_('index.php?option='.COMPONENT_OPTION.'&id='.$survey->id.'&Itemid='.COMPONENT_ITEM_ID, false, -1); ?>"><?php echo $survey->sf_name; ?></a></h3>
					<div class="category-desc"><?php echo $survey->surv_short_descr; ?></div>
				<?php endforeach; ?>
			<?php  else: ?>
				<h4 class="page-header item-title"><?php echo JText::_('COM_SF_NO_SURVEYS') ?></h4>
			<?php endif; ?>
		</div>

	</div>
</div>
