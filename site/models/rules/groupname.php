<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Form Rule class for the Joomla Framework.
 *
 * @since  0.0.1
 */
class JFormRuleGroupname extends JFormRule{
	public function test(\SimpleXMLElement $element, $value, $group = null, $input = null, $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] == 'true' || (string) $element['required'] == 'required');

		if (!$required && empty($value))
		{
			return true;
		}
		
		$lst= explode(";", $value);
		if(count($lst) != $input->get('maxGroups')) {
			return new Exception('Die Anzahl Gruppenbezeichnungen stimmt nicht mit der angegebenen Gruppenzahl überein' . $input->get('maxGroups') .'/'. count($lst));
		}
		return true;
	}
	
}
