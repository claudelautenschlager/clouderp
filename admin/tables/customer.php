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
class ClouderpTableCustomer extends JTable
{

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  connector object
	 */
	public  function __construct(&$db)
	{
		parent::__construct('#__0001_cerp_customers', 'id', $db);
	}
/*	
	public function load($keys = NULL, $reset = true){
		
		$res = parent::load($keys, $reset);
		if($res && !empty($this->maildistribution)){
			$this->maildistribution=explode(',',$this->maildistribution);
		}
//		JFactory::getApplication()->enqueueMessage('Table Customer/read:'.count($this->maildistribution));
		return $res;
	}
*/	
	public function store($updateNulls = false){
		//Datum von Deeutsch auf Englisch
		$arr=explode('.', $this->birth);
		if(count($arr)==3 && strlen($arr[2])==4){
			$engDate=$arr[2].'/'.$arr[1].'/'.$arr[0];
			$this->birth = $engDate;
//			JFactory::getApplication()->enqueueMessage('birth2:[' .  $this->birth . ']');
		}
		
//		JFactory::getApplication()->enqueueMessage('Table Customer:'.$this->maildistribution[0]);
		$tmp=$this->maildistribution;
		if(!empty($this->maildistribution) && is_array($this->maildistribution)){
			$this->maildistribution=implode(',',$this->maildistribution);
		}
		
		$this->updDate=Date('Y-m-d H:i');
		$this->updUser=JFactory::getUser()->name; // https://docs.joomla.org/Accessing_the_current_user_object
		if(empty($this->id)){
			$this->addDate=Date('Y-m-d H:i');
			$this->addUser=JFactory::getUser()->name;
		}
		$res= parent::store($updateNulls);
		$this->maildistribution=$tmp;
		return $res;
	}
}
