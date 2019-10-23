<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//require_once('')

/**
 * Customer Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerCustomers extends JControllerAdmin
{
    /*
	public function getModel($name = 'Customer', $prefix = 'ClouderpModel', $config = array())
	{
	    $model = parent::getModel($name, $prefix, array('ignore_request' => false));
	    
	    return $model;
	}
	
	*/
	public function csv(){
	    $input=&JFactory::getApplication()->input;
	    $input->set('view', $input->get("view", "Clouderp", "CMD"));
	    $input->set('layout', 'csv');
	    $this->display();
	}

}
