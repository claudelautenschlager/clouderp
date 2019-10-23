<?php
/**
 * @package    Clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
JHtml::_('formbehavior.chosen', 'select');
?>

<form action="<?php echo JRoute::_('index.php?option=com_clouderp&view=accountpage'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
		<?php if (empty($this->items)): ?>
			<div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php endif; ?>

		<?php
			//JFactory::getApplication()->enqueueMessage($this);
			echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
<!--		<div class="clearfix"></div>  -->
		<?php echo $this->loadTemplate('foot'); ?>
		<hr/>
		<table class="table table-striped" id="customerList">
			<thead>
				<?php echo $this->loadTemplate('head'); ?>
			</thead>
			<tbody>
				<?php echo $this->loadTemplate('body'); ?>
			</tbody>
		</table>
		
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
<?php
/*		
		<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>" />
*/
?>		
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
