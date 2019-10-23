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

<form action="<?php echo JRoute::_('index.php?option=com_clouderp&view=balance'); ?>" method="post" name="adminForm" id="adminForm">
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
			echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<hr/>
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'bilanz')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'bilanz', JText::_('COM_COMCLOUD_BALANCE_TAB_BILANZ', true)); ?>
			<table class="table table-striped" id="customerList">
				<tbody>
				    <?php echo $this->loadTemplate('bodybi'); ?>
				</tbody>
			</table>
			
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'er', JText::_('COM_COMCLOUD_BALANCE_TAB_ER', true)); ?>
			<table class="table table-striped" id="customerList">
				<tbody>
				    <?php echo $this->loadTemplate('bodyer'); ?>
				</tbody>
			</table>
		
		
			
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
			
		
		
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
