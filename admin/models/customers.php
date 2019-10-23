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
 * Customers Model
 *
 * @since  0.0.1
 */
class ClouderpModelCustomers extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'status','birth', 'geburttag','cat_id', 'changes'
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
		
		$changes=$this->getState('filter.changes');

		$select="concat(cu.lastname,' ',cu.firstname) as title, cu.*, c.title as status, p.vp, cu.town, date_format(`birth`,'%d.%m.%Y') as geburt, birth, date_format(`birth`,'%m.%d') as geburttag";
		
		if(isset($changes) && !empty($changes) ){
			$select.=", ifnull((select title as futurestate from #__categories c2 where cu.catidfuture=c2.id),'Austritt') as futurestate";
		}

		$query->select($select);
		
		// From the curlingevents table
		$query->from('#__0001_cerp_customers as cu')
		      ->leftJoin('#__categories as c ON cu.catid=c.id')
			  ->join('LEFT OUTER','#__0001_cerp_product as p ON cu.productabo=p.id');

		// Filter by category.
/*		
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where('catid = ' . (int) $categoryId);
		}
*/
		$catids=$this->getState('filter.cat_id');
		if(isset($catids) && !empty($catids) && count($catids)>0){
			$query->where('catid in (' . implode(",", $catids) . ')');
		}
		
		
		if(isset($changes) && !empty($changes) ){
			$query->where('catidfuture <>0');
		}
		
		$search = $this->getState('filter.search');
//		JFactory::getApplication()->enqueueMessage('Suche:' .  $searchFrom);
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$search=" (firstname like ".$search." or lastname like ". $search ." or town like ". $search.")";
			$query->where($search);
		}

//		JFactory::getApplication()->enqueueMessage('Query' .  $query);
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

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
		parent::populateState('title', 'asc');
	}
}
