<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 * http://localhost/vdcb/index.php/anzeige-resultate?layout=pdf
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>

<?php 
header('Content-Type: text/json; charset=utf-8');

echo json_encode($this->item);
\JFactory::getApplication()->close();

?>