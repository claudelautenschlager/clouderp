<?php
/**
 * @package    Clouperp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Customer Table class
 *
 * @since  0.0.1
 */
class ClouderpTableGenfactura extends JTable
{

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  connector object
	 */
	public  function __construct(&$db)
	{
		parent::__construct('#__0001_cerp_facturarun', 'id', $db);
	}
	
	public function store($updateNulls = false){
		
//		JFactory::getApplication()->enqueueMessage('store of ClouderpTableGenfactura aufgerufen');
		//Datum von Deeutsch auf Englisch
		$this->zahlungsfrist = $this->date2german($this->zahlungsfrist);
		$this->verrechnetbis = $this->date2german($this->verrechnetbis);
		$this->fakturadatum = $this->date2german($this->fakturadatum);

		$this->updDate=Date('Y-m-d H:i');
		$this->updUser=JFactory::getUser()->name; // https://docs.joomla.org/Accessing_the_current_user_object
		if(empty($this->id)){
			$this->addDate=Date('Y-m-d H:i');
			$this->addUser=JFactory::getUser()->name;
		}
		return parent::store($updateNulls);
	}
	
	//kopiert auch nach facturas-model
	protected function date2german($d){
		$arr=explode('.', $d);
		if(count($arr)==3 && strlen($arr[2])==4){
			$engDate=$arr[2].'/'.$arr[1].'/'.$arr[0];
		}
		return $engDate;
	}
}
