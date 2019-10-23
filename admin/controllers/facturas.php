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
 * Factura Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerFacturas extends JControllerAdmin
{
	
	public function doPay(){
		$data = JFactory::getApplication()->input->post->get('jform',array(),'array');
		$this->getModel('facturas')->buchePayment($data);
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=facturas' , false));
	}
	
	
	public function processOpenIssues(){
		//JFactory::getApplication()->enqueueMessage('ClouderpControllerFacturas/generatefactura');
		$res['error']=0;
		$res['errortext']='';
		try{
			$this->getModel('facturas')->processOpenIssues();
		}catch(Exception $e){
			$res['error']=1;
			$res['errortext']=$e->getMessage();
		}
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($res);
		\JFactory::getApplication()->close();
	}
	
	
	public function attachment(){
			//JFactory::getApplication()->enqueueMessage('attachment wird angezeigt');
		$input = JFactory::getApplication()->input;
		$id = $input->get->get('id');
//		var_dump($id);
		
//		$pks = $input->post->get('cid', array(), 'array'
	//	$id=1022;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		         ->from('#__0001_cerp_factura')
				 ->select("email_attachment, title")
				 ->where("`id`=". $db->quote($id));
		$db->setQuery($query);
//		print $query;
		$lst=$db->loadObject();
		
		header('Content-Type: application/pdf; charset=utf-8');
		header('Content-disposition: inline; filename='.$lst->title. '.pdf');
		echo base64_decode($lst->email_attachment);

		\JFactory::getApplication()->close();
		
	}
	
	public function sendmails(){
		$input = JFactory::getApplication()->input;
		$cid = $input->post->get('cid');
		$this->getModel('facturas')->marcFacturaReadyForSend($cid);
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=facturas' , false));
		//JFactory::getApplication()->enqueueMessage('sendmails: '. implode($cid, ','));
	}
	
	public function getModel($name = 'Factura', $prefix = 'ClouderpModel', $config = array())
	{
	    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
	    
	    return $model;
	}
}
