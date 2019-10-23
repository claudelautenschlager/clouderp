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
 * FinancebookDetails Table class
 *
 * @since  0.0.1
 */
class ClouderpTableFinancebookdetail extends JTable
{

	/**
	 * Constructor
	 *
	 * @param   JDatabaseDriver  &$db  connector object
	 */
	public  function __construct(&$db)
	{
		parent::__construct('#__0001_buchungdetail', 'id', $db);
	}
/*	
	public function load($keys = NULL, $reset = true){
		
		$res = parent::load($keys, $reset);
//		JFactory::getApplication()->enqueueMessage('Table ClouderpTableFinancebookdetail/read:');
		return $res;
	}
*/	
	public function store($updateNulls = false){
		
		return parent::store($updateNulls);
	}
}
