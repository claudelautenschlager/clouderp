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
class ClouderpModelAddresslist extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'status', 'list', 'card'
			);
		}

		parent::__construct($config);
	}
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
		$query->select("concat(cu.lastname,' ',cu.firstname) as title, cu.*, c.title as status, date_format(`birth`,'%d.%m.%Y') as birthday");

		// From the curlingevents table
		$query->from('#__0001_cerp_customers as cu')
		      ->leftJoin('#__categories as c ON cu.catid=c.id');

		// Filter by category.
		$catids=$this->getState('filter.cat_ids');
		if(isset($catids) && !empty($catids) && count($catids)>0){
			$query->where('catid in (' . implode(",", $catids) . ')');
		}
		
		$search = $this->getState('filter.search');
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$search=" (firstname like ".$search." or lastname like ". $search ." or town like ". $search.")";
			$query->where($search);
		}
		
		$canDo = JHelperContent::getActions('com_clouderp');
		if(!$canDo->get('customer.view.full') && !$canDo->get('customer.view.partly')){
			$query->where("1=2");
		}
		if(!$canDo->get('customer.view.full')){
			$query->where("publicationrestriction<> 1");  //Nicht auf der Liste wenn Restriktion = All
		}
//		JFactory::getApplication()->enqueueMessage('Query:' .  $query);
		
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($orderCol . ' ' . $orderDirn));
//		JFactory::getApplication()->enqueueMessage('Order/getquery:' .  $orderCol.'/'. $orderDirn);		

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
		$app=\JFactory::getApplication();
	/*	
		// Load the filter state.
	    $app->setUserState($this->context . '.filter.cat_ids', null); // den alten wert löschen. Wieso dies nötig ist, keine Ahnung
		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.cat_ids', 'filter_cat_ids', array());
		$this->setState('filter.cat_ids', $categoryId);
		
		$app->setUserState($this->context . '.filter.showmodus', null); // den alten wert löschen. Wieso dies nötig ist, keine Ahnung
		$showmodus = $this->getUserStateFromRequest($this->context . '.filter.showmodus', 'filter_showmodus', 'card');
		$this->setState('filter.showmodus', $showmodus);
		
		$this->setState('list.start', 20);
*/		
		
//		print_r($this->state);
		parent::populateState('title', 'ASC');
	}
}
