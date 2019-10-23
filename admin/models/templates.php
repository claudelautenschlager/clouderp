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
 * CurlingeventsList Model
 *
 * @since  0.0.1
 */
class ClouderpModelTemplates extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select("*");

		// From the curlingevents table
		$query->from('#__0001_cerp_template');

		$search = $this->getState('filter.search');
//		JFactory::getApplication()->enqueueMessage('Suche:' .  $searchFrom);
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$search="title like ".$search ;
			$query->where($search);
//			JFactory::getApplication()->enqueueMessage('Query' .  $search);
		}

		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
//		JFactory::getApplication()->enqueueMessage('Query:' .  $query);
		return $query;
	}

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
		// Load the filter state.
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		// List state information.
		parent::populateState('title', 'asc');
	}
}
