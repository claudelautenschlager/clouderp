<?php
/**
  @package    Clouderp
  @author     Claude Lautenschlager
  @copyright  Copyright (C) 2017 - 2019 All rights reserved.
  @license    httpwww.gnu.orglicensesgpl.html GNUGPL

 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Jobs Component
 *
 * @since  0.0.1
 */
class ClouderpViewBuchung extends JViewLegacy
{
	/**
	 * Display job item
	 *
	 * @param   string  $tpl  template name
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Assign data to the view
		$this->item = $this->get('Item');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new RuntimeException(implode('<br />', $errors), 500);
		}

		// Display the view
		parent::display($tpl);
	}
}
