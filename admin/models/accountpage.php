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
 * Accountpage Model
 *
 * @since  0.0.1
 *  http://localhost/vdcb/administrator/index.php?option=com_clouderp&view=accountpage&layout=default&account=1
 */
class ClouderpModelAccountpage extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'bu_datum', 'bu_text', 'bd_konto'
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

		$acc=$this->setState('com_clouderp.filter.bd_konto');
		$account=$this->getState('filter.bd_konto', 0);
		if(!is_numeric($account)){
			$account=0;
		}
//		JFactory::getApplication()->enqueueMessage('Account:' .  $account);
		// Select some fields
		$query->select("bu.`id`, d.bd_konto, bd_sollhaben, date_format(bu.`bu_datum`,'%d.%m.%Y') as bu_datumformatiert, bu.`bu_belegnr`, bu.`bu_text`, bu_belegfilename, bd_betrag, concat(k.ko_kontonr,' ',k.ko_bezeichnung) as ko_kontonr, ko_waehrung, 0 as journalsaldo")
			  ->from('#__0001_buchung as bu')
		      ->leftJoin('#__0001_buchungdetail d on d.bd_parentid=bu.id')
			  ->leftJoin('#__0001_konto k on d.bd_konto=k.id')
			  ->where('exists(select * from #__0001_buchungdetail d2 where d2.bd_parentid=bu.id and d2.bd_konto = '.$account.')')
			  ->where('d.bd_konto != '.$account);

		$search = $this->getState('filter.search');
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$search=" (bu_text like " . $search. ")";
			$query->where($search);
		}

		$query->order('bu_datum desc');

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
	    $input = JFactory::getApplication()->input;		
		$account  = $input->get->get('account', 0, 'INT');
//		JFactory::getApplication()->enqueueMessage('Account pS:' .  $account);
		if($account>0){ // && !property_exists($this->state, 'filter.bd_konto')){
			$this->setState('filter.bd_konto',$account);
			$this->setState($this->context . '.filter.bd_konto',$account);
		}	
		
	}

	protected function loadFormData(){
		//Das anzuzeigende Konto kann über die URL angegeben werden. Dieses übersteuert die Filtereinstellung
		$data=parent::loadFormData();
		$account=$this->getState($this->context.'.filter.bd_konto', 0);
		if($account>0){
			$data->filter['bd_konto']=$account;
		}

		return $data;
	}
	
	

	public function getSaldolist(){
		$saldolist=null;
		$account=$this->getState('filter.bd_konto', 0);
		if($account>0){
			$saldolist=ClouderpBookingHelper::getSaldolist($account);
		}
		return $saldolist;
	}
}
