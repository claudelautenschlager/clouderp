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
 * Financebook Table class
 *
 * @since  0.0.1
 */
class ClouderpTableFinancebook extends JTable
{

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  connector object
	 */
	public  function __construct(&$db)
	{
		parent::__construct('#__0001_buchung', 'id', $db);
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
		$this->bu_datum=ClouderpHelper::dateGerman2English($this->bu_datum);
		
		$this->updDate=Date('Y-m-d H:i');
		$this->updUser=JFactory::getUser()->name; // https://docs.joomla.org/Accessing_the_current_user_object
		if(empty($this->id)){
			$this->addDate=Date('Y-m-d H:i');
			$this->addUser=JFactory::getUser()->name;
		}
		//https://docs.joomla.org/File_form_field_type
		if(empty($this->bu_belegfile)){
		    unset($this->bu_belegfile);
		    unset($this->bu_belegfilename);
		    unset($this->bu_belegfilemime);
		}else{
		    $this->bu_belegfile=$this->bu_belegfile;
		}
		return parent::store($updateNulls);
	}
}
