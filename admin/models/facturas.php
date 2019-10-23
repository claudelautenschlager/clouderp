<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * FacturaList Model
 *
 * @since  0.0.1
 */
class ClouderpModelFacturas extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'title', 'status', 'searchByState','hasmailadr', 'facturarunid'
			);
		}

		parent::__construct($config);
	}




	public function rebook()
	{
		//JLoader::register('SendfacturaHelper', dirname(__FILE__) . '/helpers/sendfacturahelper.php');
	    
//	    ClouderpBookingHelper::bookFactura(3241);
	    
	    
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)->from('#__0001_cerp_factura f')->select("id, title, zahlungsbetrag, zahlungsdatum")->where('status=5');
		$db->setQuery($query);

		$lst=$db->loadObjectList();

		if(count($lst)>0){
			$conf=JComponentHelper::getParams('com_clouderp');
			foreach($lst as $factura){
			//	ClouderpBookingHelper::bookFactura($factura->id);
			
				$param=array(
					'date' =>$factura->zahlungsdatum,
					'beleg'=>'',
					'buchtext' =>'Einzahlung '.$factura->title,
					'kontoS' => $conf['ktDebiBank'],
					'kontoH' => $conf['ktDebiSammel'],
					'Betrag'=>$factura->zahlungsbetrag*100,
					'textS'=>'',
					'textH' =>''
					
				);
				ClouderpBookingHelper::writeSammelbuchung($param);
			}
		}
	}


	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$select="fa.`id`, fa.`status`, fa.`title`,fa.`betrag`,date_format(`zahlungsfrist`,'%d.%m.%Y') as zahlungsfrist, date_format(`addDate`,'%d.%m.%Y') as facturadate, ".
		        "date_format(`zahlungsdatum`,'%d.%m.%Y') as zahlungsdatum, fa.readyForSend, remindlevel, remindcost, fa.facturemedium, ".
		        "s.statustext, fa.email, length(email_attachment) as datalength, ".
				" case when fa.readyForSend=1 then 'Versandbereit' when fa.facturemedium=2 then 'Versand erwünscht' when fa.readyForSend=2 then date_format(`versanddatum`,'%d.%m.%Y') when fa.readyForSend=4 then 'Keine eMailadresse' when fa.readyForSend=9 then 'Fehler' else '' end as mailstatus";
		$query->select($select);

		// From the curlingevents table
		$query->from('#__0001_cerp_factura as fa')
		      ->leftJoin('#__0001_cerp_facturastate as s ON fa.status=s.id');

		$states=$this->getState('filter.searchByState');

		if(isset($states) && !empty($states) && count($states)>0){
			$query->where('status in (' . implode(",", $states) . ')');
//			JFactory::getApplication()->enqueueMessage('Test:'. $states[0]);
		}
		
		$hasmailadr=$this->getState('filter.hasmailadr');
		if(isset($hasmailadr) && !empty($hasmailadr) ){
			switch($hasmailadr){
				case '0':
				break;
				case '1':
					$query->where("email =  ''");
					break;
				case '2':
					$query->where("email <>  ''");
					break;
				case '3':
					$query->where("fa.facturemedium=2");
					break;
				case '4':
					$query->where("(fa.facturemedium=2 or email =  '')");
					break;
			}
		}
		$facturarunid=$this->getState('filter.facturarunid');
		if(isset($facturarunid) && !empty($facturarunid) ){
			$query->where("runid=".$db->escape($facturarunid));
		}
		

		$search = $this->getState('filter.search');
		if(!empty($search)){
			$search = "'%".$db->escape($search, true)."%'";
			$query->where(" fa.title like " . $search);
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));
//		JFactory::getApplication()->enqueueMessage('Query:' .  $query);


/*
		$query = $db->getQuery(true);
		$query->select($select);

		// From the curlingevents table
		$query->from('#__0001_cerp_factura as fa')
		      ->leftJoin('#__0001_cerp_facturastate as s ON fa.status=s.id');
*/			  
		return $query;
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
		parent::populateState('title', 'asc');
	}
	
	
	public function getPaymentForm($data = array(), $loadData = false)
	{
		// Get the form.
		$form = $this->loadForm('com_clouderp.payment', 'payment', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	public function marcFacturaReadyForSend($cid){
		$anz=0;
		if(count($cid)>0){
			$error=false;
			foreach($cid as $id){
				if(!is_numeric($id)){
					$error=true;
					break;
				}
			}
			if(!$error){
//				$anz=count($cid);
				$db = JFactory::getDbo();

				$query = $db->getQuery(true);
				$query->update('#__0001_cerp_factura')->set('readyForSend=1');
				$query->where('id in ('.implode($cid, ',') .')' );
				$query->where('status in('.FACTURA_STATE_READY.')');
			
				$db->setQuery($query);
				$db->execute();
				$anz=$db->getAffectedRows();
			}
		}
		JFactory::getApplication()->enqueueMessage($anz. ' Rechnungen für den Versand markiert:');
	}
	
	
	public function countOpenIssues()
	{
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true)
		         ->from('#__0001_cerp_factura')
				 ->select("count(*) as anz")
				 ->where("`status`=1 or readyForSend=1");
		$db->setQuery($query);
		
		$item=$db->loadObject();
		return $item->anz;
	}

	public function processOpenIssues()
	{
		//JLoader::register('SendfacturaHelper', dirname(__FILE__) . '/helpers/sendfacturahelper.php');
	    
//	    ClouderpBookingHelper::bookFactura(3241);
	    
	    
		$db = JFactory::getDbo();

		$select="f.*, ".
		        "cu.`firstname`, cu.`lastname`, cu.`address`, cu.`zipcode`, cu.`town`, cu.`phone`, cu.`mobil`, cu.`birth`, cu.`entrydate`, cu.`sayhello`, cu.`fak_name`,cu.`fak_address`,cu.`fak_zipcode`,cu.`fak_town`, ".
				"c.title as category, ".
				"ru.param1, ru.param2, ru.param3, ru.param4, ru.title as rechnungstitel, ru.email_body as email_template, ru.`fakturadatumformat`, ru.`zahlungsfristformat`, ru.`verrechnetbis`, ".
				"templateid, f.betrag, prod.title as prodtitle, prod.description as prod_desc";
		
		$query = $db->getQuery(true)
		         ->from('#__0001_cerp_factura f')
				 ->join('LEFT', $db->quoteName('#__0001_cerp_facturarun', 'ru') . ' ON (' . $db->quoteName('runid') . ' = ' . $db->quoteName('ru.id') . ')')
				 ->join('LEFT', $db->quoteName('#__0001_cerp_customers', 'cu') . ' ON (' . $db->quoteName('customerid') . ' = ' . $db->quoteName('cu.id') . ')')
				 ->join('LEFT', $db->quoteName('#__0001_cerp_product', 'prod') . ' ON (' . $db->quoteName('productabo') . ' = ' . $db->quoteName('prod.id') . ')')
				 ->leftJoin('#__categories as c ON cu.catid=c.id')
				 ->select($select)
				 ->where("f.status< 2 or f.readyForSend=1");
		$db->setQuery($query);

		$lst=$db->loadObjectList();
		if(count($lst)>0){
			$lauf=0;

			$template= SendfacturaHelper::loadTemplate($lst[0]->templateid);
			ini_set('max_execution_time', 300);
			
			foreach($lst as $factura){
				$lauf++;
				if($factura->status==1){
					$this->generateFacturaDetailsMail($factura);
					$this->generateFacturaDetailsAttachment($template, $factura);
					$this->updateCustomer($factura);
					$this->generateFacturaDetailSetStatus($factura,FACTURA_STATE_READY);
				}
				if($factura->readyForSend==1){
					$this->generateFacturaDetailSetMailStatus($factura,'9'); // kann nur einmal versendet werden
					if(!empty($factura->email) && $factura->facturemedium==1){
						SendfacturaHelper::sendMail($factura);
						$this->generateFacturaDetailSetMailStatus($factura,'2');
					} else {
						$this->generateFacturaDetailSetMailStatus($factura,'4');  // kein Email
					}
					$newstate=FACTURA_STATE_SENT;
					if(!empty($factura->zahlungsdatum)){
						$newstate=FACTURA_STATE_PAID;
					} else if($factura->remindlevel>0){
						$newstate=FACTURA_STATE_REMINDED;
					}
					$this->generateFacturaDetailSetStatus($factura,$newstate);
					
					$lauf=$lauf+6;        // braucht länger
				}

				if($lauf>25){
					//nur 25 Records verarbeiten
					break;
				}
			}
		}
		return count($lst);
	}
	
	protected function updateCustomer($factura){
		$tb = JTable::getInstance("Customer", "ClouderpTable" , array());
		$tb->load($factura->customerid);
		$tb->facturatedtill= $factura->verrechnetbis;
		$tb->store();
	}
	
	protected function generateFacturaDetailsAttachment($template, $factura){
		$pdf= SendfacturaHelper::generateFacturaDetailsAttachment($template,$factura);
		
		$base64=base64_encode($pdf);
			
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);		
		$query->update('#__0001_cerp_factura')->set("email_attachment=".$db->quote($base64));
		$query->where('id='.$factura->id);
		$db->setQuery($query);
		$db->execute();
	}
	
	public function buchePayment($data){
		$betrag=$data['betrag'];
		if(is_numeric($betrag)){
			$zd=ClouderpHelper::germanDatetoEnglish($data['zahlungsdatum']);
			
			$ids=explode(',',$data['id']);
			foreach($ids as $id){
				if(is_numeric($id)){
					$tbl = JTable::getInstance("Factura", "ClouderpTable" , array());
					if($tbl->load($id)){
						$tbl->zahlungsdatum=$zd;
						if($betrag==-1){
							$tl->zahlungsbetrag=$betrag;
						}else{
							$tl->zahlungsbetrag=$tl->zahlungsbetrag+$betrag;
						}
					
						if($tbl->store()){
							//Fibubuchung
							$conf=JComponentHelper::getParams('com_clouderp');
							$param=array(
								'date' =>$zd,
								'beleg'=>'',
								'buchtext' =>'Einzahlung '.$tbl->title,
								'kontoS' => $conf['ktDebiBank'],
								'kontoH' => $conf['ktDebiSammel'],
								'Betrag'=>$betrag*100,
								'textS'=>'',
								'textH' =>''
								
							);
							ClouderpBookingHelper::writeSammelbuchung($param);
						}
					}
				}
			}
		}
	}


	protected function generateFacturaDetailsMail($factura){
		$mail= SendfacturaHelper::generateFacturaDetailParse($factura->email_template,$factura);
		$tb = JTable::getInstance("Factura", "ClouderpTable" , array());

		$tb->load($factura->id);
		$tb->email_body=$mail;
		$tb->store();
	}

	protected function generateFacturaDetailSetStatus($factura, $status){
		$db = JFactory::getDbo();
		
	
		$query = $db->getQuery(true);
		$query->update('#__0001_cerp_factura')
		  ->set('status='.$status)
		  ->where('id='.$factura->id);
/*		
		if($status==FACTURA_STATE_READY){
		    $query->set('readyForSend=1');
		}
*/
		$db->setQuery($query);
		$db->execute();
		
		
		if($status==FACTURA_STATE_SENT){
		    ClouderpBookingHelper::bookFactura($factura->id);
		}
	}
	
	protected function generateFacturaDetailSetMailStatus($factura, $status){
		$versanddatum=($status==2)? "'".date("Y-m-d")."'": 'null';
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->update('#__0001_cerp_factura')->set('readyForSend='.$status.', versanddatum='.$versanddatum)->where('id='.$factura->id);
//		echo($query);		
		
		$db->setQuery($query);
		$db->execute();
	}
	
}
