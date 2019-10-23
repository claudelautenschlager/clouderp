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
 * Financebooks Model
 *
 * @since  0.0.1
 */
class ClouderpModelFinancebooks extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'bu_datum','bu_belegnr', 'bu_text'
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
		$query->select("bu.`id`, date_format(bu.`bu_datum`,'%d.%m.%Y') as bu_datumformatiert, bu.`bu_belegnr`, bu.`bu_sammelparent`, bu.`bu_text`, date_format(`addDate`,'%d.%m.%Y') as addDate, date_format(`updDate`,'%d.%m.%Y') as `updDate`, `addUser`, updUser, bu_belegfilename")
			  ->from('#__0001_buchung as bu');
/*		
		      ->leftJoin('#__categories as c ON cu.catid=c.id')
			  ->join('LEFT OUTER','#__0001_cerp_product as p ON cu.productabo=p.id');
*/
/*		
		$catids=$this->getState('filter.cat_id');
		if(isset($catids) && !empty($catids) && count($catids)>0){
			$query->where('catid in (' . implode(",", $catids) . ')');
		}
*/		
		$search = $this->getState('filter.search');
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$search=" (bu_text like " . $search. ")";
			$query->where($search);
		}


		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

//		JFactory::getApplication()->enqueueMessage('Sortierung' .  $orderCol);
		
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
		parent::populateState('bu_datum', 'desc');
	}
	
	
	public function getItems(){
		$buchungen=parent::getItems();
		
		$parentKey='-1';
		foreach($buchungen as $buchung){
			$parentKey .= (','. strval($buchung->id));
		}
		return ClouderpBookingHelper::addBookingDetails($buchungen, $parentKey);
	}
	
	
}
