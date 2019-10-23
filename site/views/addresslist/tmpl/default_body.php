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
?>
<?php foreach ($this->items as $i => $item):?>
	<?php if($this->viewmodus=="card"): ?>
		<div class="w3-container paddingnextcard">
		  <div class="w3-card-4" style="width:100%;">
			<header class="w3-container headercard row-fluid" style="background-color:Cornsilk ;">
				<h5>&nbsp;
					<div class='span6'><b><?php echo $item->title;?></b></div>
					<div class='span5 hidden-phone' style="text-align:right;"><i><?php echo $item->status;?></i></div>
					<div class='span5 hidden-desktop'"><i><?php echo $item->status;?></i></div>
				</h5>
			</header>

			<div class="w3-container container-fluid">
				<div class="row-fluid" style="margin-left:5px !important;">
					<div class='span6'>
						<div class="row-fluid" style="margin-left:5px !important!">
							<div class='span3 hidden-phone'>Email:</div>
							<div class='span6'><a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a></div>
						</div>
						<?php if(!empty($item->mobil) || !empty($item->phone)): ?>
							<div class="row-fluid ">
								<div class='span3 hidden-phone'>Telefon:</div>
								<?php if($this->full || $item->publicationrestriction!=2): ?>
									<div class='span6'>
										<?php if(!empty($item->mobil)):?>
											<a href="tel:<?php echo $item->mobil;?>"><?php echo $item->mobil; ?></a>
										<?php endif;?>
										&nbsp;
										<?php if(!empty($item->phone)):?>
											<a href="tel:<?php echo $item->phone;?>"><?php echo $item->phone; ?></a>
										<?php endif;?>
									</div>
								<?php endif;?>
							</div>
						<?php endif; ?>
						<?php if($this->full): ?>
							<div class="row-fluid ">
								<div class='span3'>Geburtstag:</div>
								<?php if($item->birthday!='00.00.0000'): ?>
									<div class='span6'><?php echo $item->birthday;?></div>
								<?php endif; ?>
							</div>
							<div class="row-fluid">
								<div class='span3'>Eintritt:</div>
								<div class='span6'><?php echo $item->entrydate;?></div>
							</div>
						<?php endif; ?>
					</div>
					<?php if($this->full || $item->publicationrestriction!=3): ?>
						<div class='span5'>
							<div class="row-fluid">
								<div class='span3'>Adresse:</div>
							</div>
							<div class="row-fluid">
								<div class='span8'><?php echo $item->firstname.' '.$item->lastname;?></div>
							</div>
							<div class="row-fluid">
								<div class='span8'><?php echo $item->address;?></div>
							</div>
							<div class="row-fluid">
								<div class='span8'><?php echo $item->zipcode.' '.$item->town;?></div>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php else:?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="nowrap has-context" style="width:260px;">
				<?php echo $this->escape($item->title); ?>
			</td>
			<td class="nowrap has-context">
				<?php echo $item->status; ?>
			</td>
			<td class="nowrap has-context">
				<a href="mailto:<?php echo $item->email; ?>"><?php echo $item->email; ?></a>
			</td>
			<td class="nowrap has-context">
				<?php if(!empty($item->mobil)):?>
					<a href="tel:<?php echo $item->mobil;?>"><?php echo $item->mobil; ?></a>
				<?php endif;?>
				&nbsp;
				<?php if(!empty($item->phone)):?>
					<a href="tel:<?php echo $item->phone;?>"><?php echo $item->phone; ?></a>
				<?php endif;?>
			</td>
		</tr>
	 <?php endif; ?>
<?php endforeach; ?>