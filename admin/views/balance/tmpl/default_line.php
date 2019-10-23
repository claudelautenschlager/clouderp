<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
echo '<tr>';
echo '<td>';
echo $item->kontonr;
echo '</td>';
echo '<td>';
echo $item->bezeichnung;
echo '</td>';
echo '<td>';
echo $item->saldoper;
echo '</td>';
echo '</tr>';

?>