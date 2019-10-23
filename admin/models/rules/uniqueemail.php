<?php
/**
 * @package    CloudERP
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
class JFormRuleUniqueemail extends JFormRule
{
	/**
	 * The regular expression.
	 *
	 * @access	protected
	 * @var		string
	 * @since	1.6
	 */
	protected $regex = '[0-9A-Za-z ]+';  //'^[^0-9]+$';
	
}
