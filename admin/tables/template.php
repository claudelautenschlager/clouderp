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
 * Template Table class
 *
 * @since  0.0.1
 */
class ClouderpTableTemplate extends JTable
{

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  connector object
	 */
	public  function __construct(&$db)
	{
		parent::__construct('#__0001_cerp_template', 'id', $db);
	}
	
	public function store($updateNulls = false){
		$this->updDate=Date('Y-m-d H:i');
		$this->updUser=JFactory::getUser()->name; // https://docs.joomla.org/Accessing_the_current_user_object
		if(empty($this->id)){
			$this->addDate=Date('Y-m-d H:i');
			$this->addUser=JFactory::getUser()->name;
		}
		return parent::store($updateNulls);
	}
}
