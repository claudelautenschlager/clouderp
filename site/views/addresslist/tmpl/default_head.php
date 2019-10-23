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
<?php if($this->viewmodus=="list"): ?>
<tr>
	<th class="title" style="width:260px;">
		Title
	</th>
	<th class="title">
		Status
	</th>
	<th class="title">
		eMail
	</th>
	<th class="title">
		Telefon
	</th>
</tr>

<?php endif; ?>

