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
<form action="<?php echo JRoute::_('index.php?option=com_clouderp&view=addresslist'); ?>" method="post" name="adminForm" id="adminForm">
<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
<?php if (empty($this->items)): ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php endif; ?>

<?php
	//JFactory::getApplication()->enqueueMessage($this);

?>
<!--		<div class="clearfix"></div>  -->

<?php if (!empty($this->items)): ?>
	<?php if($this->viewmodus=="card"): ?>
		<?php echo $this->loadTemplate('foot'); ?>
		<?php echo $this->loadTemplate('body'); ?>
	<?php else: ?>
		<table class="table table-striped" id="customerList">
			<thead>
				<?php echo $this->loadTemplate('head'); ?>
			</thead>
			<tbody>
				<?php echo $this->loadTemplate('body'); ?>
			</tbody>
			<tfoot>
				<?php echo $this->loadTemplate('foot'); ?>
			</tfoot>
		</table>
		
	<?php endif; ?>
<?php endif; ?>


<input type="hidden" name="task" value="" />
<?php
/* falls Fullordering eingesetzt wird, dürfen diese beiden Felder nicht ausgegeben werden
<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->state->get('list.direction')); ?>" />
*/
?>

</form>