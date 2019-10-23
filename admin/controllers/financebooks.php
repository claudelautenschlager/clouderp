<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Financebooks Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerFinancebooks extends JControllerAdmin
{
	public function getModel($name = 'Financebook', $prefix = 'ClouderpModel', $config = array())
	{
	    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	    
	    return $model;
	}
	
	public function delete(){
	    $res= $this->input->get('cid', array(), 'array');
	    return parent::delete();
	    //return true;
	}
	
	public function attachment(){
			//JFactory::getApplication()->enqueueMessage('attachment wird angezeigt');
		$input = JFactory::getApplication()->input;
		$id = $input->get->get('id');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		         ->from('#__0001_buchung')
				 ->select("`bu_belegfile`,`bu_belegfilename`,`bu_belegfilemime`")
				 ->where("`id`=". $db->quote($id));
		$db->setQuery($query);
//		print $query;
		$lst=$db->loadObject();
		
		
		$tmp_path=JFactory::getConfig()->get('tmp_path');
		$fName=$tmp_path . '/p1.pdf' ;
		file_put_contents($fName, base64_decode($lst->bu_belegfile));
		
		header('Content-Type: '.$lst->bu_belegfilemime.'; charset=utf-8');
		header('Content-disposition: inline; filename='.$lst->bu_belegfilename,';');
		
		
		
		echo base64_decode($lst->bu_belegfile);

		\JFactory::getApplication()->close();
		
	}
}
