<?php
/**
 * @package    clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * SeclinksList Model
 *
 * @since  0.0.1
 */
class ClouderpModelClouderp extends JModelAdmin
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	
	public function getItem($pk = NULL){
		$data=new stdClass();
		
		//Anzeige Stan der Mitglieder
		$data->members=$this->getMembersCounts();
		$total=0;
		foreach($data->members as $m) $total+=$m->anzahl;
		$rec=new stdClass();
		$rec->status='Total';
		$rec->anzahl=$total;
		$data->members[]=$rec;
		
		/*Anzeige Stand der Rechnungen
		Anzahl Rechnungen
		Gesamtbetrag
		Anzahl nicht bezahlt 
		Betrag offen
		Anzahl Rechnungen ohne Mailversand
		Anzahl gemahnt
		Betrag Mahnungen
		*/
		$data->facturas=$this->getFactutaState();
		return $data;
	}
	
	
	protected function getFactutaState(){
		$db = JFactory::getDBO();
		$query="select 1 as typ, 1 as dtype, IFNULL(count(*),0) as wert from #__0001_cerp_factura where status<>9 union ";
		$query.="select 2 as typ, 2 as dtype, IFNULL(sum(betrag),0) as wert from #__0001_cerp_factura where status<>9 union ";
		$query.="select 11 as typ, 1 as dtype, IFNULL(count(*),0) as wert from #__0001_cerp_factura where status<>9 and zahlungsdatum is null union ";
		$query.="select 12 as typ, 2 as dtype, IFNULL(sum(betrag),0) as wert from #__0001_cerp_factura where status<>9 and  zahlungsdatum is null union " ;

		$query.="select 31 as typ, 1 as dtype, IFNULL(count(*),0) as wert from #__0001_cerp_factura where status<>9 and  remindlevel>0 union ";
		$query.="select 32 as typ, 2 as dtype, IFNULL(sum(remindcost),0) as wert from #__0001_cerp_factura where status<>9 and  remindlevel>0";
		$db->setQuery($query);

//			select 21 as typ, 1 as dtype, IFNULL(count(*),0) as wert from #__0001_cerp_factura where status<>9 and (email='' or  facturemedium=2) union 
		return $db->loadObjectList();
	}
	
	protected function getMembersCounts(){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);		
		$query->select("c.title as status, count(*) as anzahl")->from('#__0001_cerp_customers as cu')->leftJoin('#__categories as c ON cu.catid=c.id')->where('1')->group('c.title')->order('title');
		$db->setQuery($query);

		return $db->loadObjectList();
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
		// List state information.
//		parent::populateState('title', 'asc');
	}
	
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		return false;
		//$form = $this->loadForm('com_clouderp.clouderp', 'seclink', array('control' => 'jform', 'load_data' => $loadData));
	}
}
