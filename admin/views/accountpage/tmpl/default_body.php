<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$user = JFactory::getUser();
$tmpl='';
?>
<?php foreach ($this->items as $i => $item):
	$canEdit = true;
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="nowrap has-context" style="width:60px;">
			<?php echo $item->bu_datumformatiert; ?>
		</td>
		
		<td class="nowrap has-context" style="width:260px;">
			
			<?php if ($canEdit): ?>
				<a href="<?php echo JRoute::_('index.php?option=com_clouderp&task=financebook.edit&id='.(int) $item->id); ?>">
					<?php echo $this->escape($item->bu_text);?>
				</a>
			<?php else: ?>
				<?php echo $this->escape($item->title); ?>
			<?php endif; ?>
			<?php if(!empty($item->bu_belegfilename)):?>
				<a href="index.php?option=com_clouderp&task=financebooks.attachment&id=<?php echo $item->id;?>" target="_blank"><span class="icon-file"></span></a>
			<?php endif; ?>		
		</td>
		<td class="nowrap has-context" style="width:60px; text-align:right;">
			<?php echo ($item->bd_sollhaben==0)?number_format($item->bd_betrag/-100, 2, '.', "'"):'&nbsp;'; ?>
		</td>
		<td class="nowrap has-context" style="width:60px; text-align:right;">
			<?php echo ($item->bd_sollhaben==0)?'&nbsp;': number_format($item->bd_betrag/100, 2, '.', "'"); ?>
		</td>
		<td class="nowrap has-context" style="width:20px;">
			<?php echo $item->ko_waehrung; ?>
		</td>
		<td class="nowrap has-context" style="width:160px;">
			<a href="<?php echo JRoute::_('index.php?option=com_clouderp&view=accountpage&layout=default&account='.(int) $item->bd_konto); ?>">
				<?php echo $item->ko_kontonr; ?>
			</a>
		</td>
		<td class="nowrap has-context" style="width:60px; text-align:right;">
			<?php 
				if($tmpl!=$item->bu_datumformatiert){
					echo number_format($this->saldolist[$item->bu_datumformatiert]/100, 2, '.', "'"); 
					$tmpl=$item->bu_datumformatiert;
				}else{
					echo '&nbsp;';
				}
			?>
		</td>
		<td>
		
		</td>
	</tr>
<?php endforeach; ?>