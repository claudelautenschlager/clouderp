<?php
/**
 * @package    clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * SeclinksList Model
 *
 * @since  0.0.1
 */
class ClouderpModelClouderp extends JModelAdmin
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   name if column that should be used for order
	 * @param   string  $direction  ordering direction
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// List state information.
//		parent::populateState('title', 'asc');
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		return false;
		//$form = $this->loadForm('com_clouderp.clouderp', 'seclink', array('control' => 'jform', 'load_data' => $loadData));
	}
}
