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
 * Customer Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerKontoplan extends JControllerAdmin
{

	function __construct(){
	    parent::__construct();
		$list=explode('com_clouderp',__DIR__);
	    $path=$list[0].'com_clouderp/';
	    JLoader::register('ClouderpBookingHelper', $path . 'helpers/clouderpbookinghelper.php');
		JLoader::register('ClouderpTableAccount', $path . 'tables/account.php');
//	    $b = class_exists("ClouderpTableAccount");
	}
	
	
	//http://localhost/vdcb/?option=com_clouderp&task=kontoplan.getkontoplan
	public function getkontoplan(){
		$kontoplan=ClouderpBookingHelper::getKontoplanMitRahmen();
		header('Content-Type: text/json; charset=utf-8');

		echo json_encode($kontoplan);
		\JFactory::getApplication()->close();
	}
	
	//http://localhost/vdcb/?option=com_clouderp&task=kontoplan.deleteitem
	public function deleteitem(){
		$res['error']=0;
		$res['errortext']='alles ok';
		try{
			$data = json_decode(file_get_contents('php://input'));
			ClouderpBookingHelper::deleteAccount($data);
		}catch(Exception $e){
			$res['error']=1;
			$res['errortext']=$e->getMessage();
		}
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($res);
		\JFactory::getApplication()->close();
	}
	
	public function deleteitem2(){
		$res['error']=0;
		$res['errortext']='alles ok';
		try{
			ClouderpBookingHelper::deleteAccount(null);
		}catch(Exception $e){
			$res['error']=1;
			$res['errortext']=$e->getMessage();
		}
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($res);
		\JFactory::getApplication()->close();
	}
	
	
}
