<?php
/**
 * @package    Clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
?>
<tr>
	<th class="title" style="width:60px;">
		Datum
	</th>
	<th class="title" style="width:260px;">
		Text
	</th>
	<th class="title" style="width:60px; text-align:right;">
		Soll
	</th>
	<th class="title" style="width:60px; text-align:right;">
		Haben
	</th>
	<th class="title" style="width:20px;">
		Währung
	</th>
	<th class="nowrap" style="width:160px;"> 
		Gegenkonto
	</th>
	<th class="nowrap" style="width:60px; text-align:right;">
		Saldo
	</th>
</tr>
