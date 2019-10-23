<?php
/**
 * @package    Clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * General Controller of Seclinks component
 *
 * @since  0.0.1
 */
class ClouderpController extends JControllerLegacy
{
	/**
	 * The generic display task
	 *
	 * @param   bool   $cachable   is this view a cachabel one
	 * @param   array  $urlparams  url parameters
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$input =&JFactory::getApplication()->input;

		// Set default view if not set
		$input->set('view', $input->get("view", "Clouderp", "CMD"));

		// Call parent behavior
		parent::display($cachable, $urlparams);
	}
}
