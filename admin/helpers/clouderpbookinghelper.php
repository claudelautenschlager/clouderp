<?php
/**
 * @package    clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;

/**
 * Class ClouderpBookingHelper
 *
 * @since  0.0.1
 */
abstract class ClouderpBookingHelper
{

	public static function deleteAccount($item){
		if($item->type==11){
			$conf=JComponentHelper::getParams('com_clouderp');
			if($item->id == $conf['ktDebiSammel'] || $item->id == $conf['ktDebiBank']){
				throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR1'));
			}
			$table = self::getAccountTable();
			if($table->load($item->id)){
				if($table->ko_countbook>0){
					throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR3'));
				}
				//Salodo muss 0 sein
				if($table->ko_firstbilance!=0){
					throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR2'));
				}
				$table->delete($item->id);
			}
		}else{
			//prüfen, ob dem Frame keine daten angehängt sind
			$option=['parent'=>$item->id];
			$lst=self::getKontoRahmen($option);
			if(count($lst)>0){
				throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR7'));
			}
			$lst=self::getKontoliste($option);
			if(count($lst)>0){
				throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR7'));
			}
			$table = self::getAccountframeTable();
			$table->delete($item->id);
		}
	}

	public static function saveAccount($data){
		//Prüfen
		$id=$data->id;
		if($data->type=='11'){
			$option=['kontonr'=>$data->kontonr, 'notid'=>$data->id];
			$lst=self::getKontoliste($option);
			
			if(count($lst)>0){
				throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR4'));
			}
			$table = self::getAccountTable();
			if($id >0){
				if(!$table->load($data->id)){
					throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR5'));
				}
			}
			$table->ko_kontorahmen=$data->kr_parentid;
			$table->ko_kontonr=$data->kontonr;
			$table->ko_bezeichnung=$data->bezeichnung;
			$table->ko_waehrung=$data->ko_waehrung;

			$table->ko_bebuchbar=$data->ko_bebuchbar;
			$table->ko_bilancewhenzero=$data->ko_bilancewhenzero;
			$table->ko_bilancewhenempty=$data->bilancewhenempty;
			$table->store();
			$id=$table->id;
			
			$option= ['id'=>$id];
			$lst=self::getKontoliste($option);
			return $lst[0];
		}else{
			//ref:{bezeichnung:'', kontonr:node.ref.kontonr+"?", ko_waehrung:'CHF', bilancewhenempty:0, kr_parentid:node.ref.id, type:node.ref.type, parent: node}
			$option=['kontonr'=>$data->kontonr, 'notid'=>$data->id];
			$lst=self::getKontoRahmen($option);
			if(count($lst)>0){
				throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR6'));
			}
			$table = self::getAccountframeTable();
			if($id >0){
				if(!$table->load($data->id)){
					throw new Exception(JText::_('COM_CLOUERP_KONTOPLAN_ERR5'));
				}
			}
			$table->kr_parentid=$data->kr_parentid;
			$table->kr_kontonr=$data->kontonr;
			$table->kr_bezeichnung=$data->bezeichnung;
			$table->kr_typ=$data->type;
			$table->kr_bilancewhenzero=$data->ko_bilancewhenzero;
			$table->store();
			$id=$table->id;
			
			$option=['id'=>$id];
			return self::getKontoRahmen($option)[0];
		}
	}
	
	public static function cummulateSaldoPer($ktrahmen, $field){
		$thisSaldo=0;
		if($ktrahmen->children!=null){
			foreach($ktrahmen->children as $child){
				if($child->type==11){
					//suboptimal hier
					if($child->ko_sumlastperiod==null){
						$child->ko_sumlastperiod=$child->ko_firstbilance;
					}
					$thisSaldo+=$child->$field;
				}
				else{
					$thisSaldo += self::cummulateSaldoPer($child, $field);
				}
			}
		}
		$ktrahmen->$field=$thisSaldo;
		return $thisSaldo;
	}

	public static function getKontoplanMitRahmen($option=[])
	{
		$rm= self::getKontoRahmen($option);
		$ktlist = self::getKontoliste($option);

		$ktPlan = self::buildKontoplan($rm, $ktlist);
		
		return $ktPlan;
	}


	public static function getKontoRahmen($option)
	{
		$db = JFactory::getDbo();
//kr_showonbalance

		$select="`id`, `kr_parentid`,	`kr_kontonr` as kontonr, `kr_bezeichnung` as bezeichnung,`kr_typ` as type,`kr_bilancewhenzero` as ko_bilancewhenzero, date_format(`addDate`,'%d.%m.%Y') as adddate, date_format(`updDate`,'%d.%m.%Y') as upddate, 1 as doshow, 0 as saldoper";

		if(isset($option['lastperiod']) && $option['lastperiod']){
			$select=$select.",null as ko_sumlastperiod, 0 as ko_budget, 0 as ko_budgetlastperiod, 0 as ko_firstbilance";
		}

		$query = $db->getQuery(true)->from('#__0001_kontorahmen')->select($select);
		
		if(isset($option['kontonr'])){
			$query->where('kr_kontonr='.$db->quote($option['kontonr']));
		}
		if(isset($option['notid'])){
			$query->where('id <> '.$db->quote($option['notid']));
		}
		if(isset($option['id'])){
			$query->where('id ='.$db->quote($option['id']));
		}
		if(isset($option['parent'])){
			$query->where('kr_parentid ='.$db->quote($option['parent']));
		}
		
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}

	public static function getKontoliste($option)
	{
		$db = JFactory::getDbo();
		
		$select="`id`,`ko_kontorahmen` as kr_parentid,`ko_kontonr` as kontonr,`ko_bezeichnung` as bezeichnung,11 as type, `ko_waehrung`,`ko_bebuchbar`, `ko_bilancewhenzero`,`ko_bilancewhenempty` as bilancewhenempty, ko_firstbilance, ko_firstbilance+ko_sum as saldo, ko_countbook, date_format(`ko_lastbook`,'%d.%m.%Y') as ko_lastbook, date_format(`addDate`,'%d.%m.%Y') as adddate, date_format(`updDate`,'%d.%m.%Y') as upddate,`addUser`,`updUser`,1 as doshow";
		if(isset($option['lastperiod']) && $option['lastperiod']){
			$select=$select.",`ko_sumlastperiod`, `ko_budget`, `ko_budgetlastperiod`";
		}
		
		if(isset($option['boolingTillDate'])){
			$select=$select.",ko_firstbilance+IFNULL((select sum(`bd_betrag`) from `#__0001_buchungdetail` d join #__0001_buchung b on (b.id=d.bd_parentid) where k.id = d.`bd_konto` and bu_datum<='".$option['boolingTillDate']."'),0) as saldoper";
		}
	
		$query = $db->getQuery(true)->from('#__0001_konto k')
		           ->select($select);
		if(isset($option['kontonr'])){
			$query->where('ko_kontonr='.$db->quote($option['kontonr']));
		}
		if(isset($option['notid'])){
			$query->where('id <> '.$db->quote($option['notid']));
		}
		if(isset($option['id'])){
			$query->where('id ='.$db->quote($option['id']));
		}
		if(isset($option['parent'])){
			$query->where('ko_kontorahmen ='.$db->quote($option['parent']));
		}
		$db->setQuery($query);
		$records= $db->loadObjectList();
		
		return $records;
	}

	protected static function addKontoToRahmen($ktlist, $Parent){
		$item = array();
		foreach ($ktlist as $ko){
			if($ko->kr_parentid == $Parent->id){
				$Parent->children[]=$ko;
			}
		}
		return $item;
	}
	
	protected static function buildKtRahmenTree($ktrahmen, $ktlist, $Parent){
		$item = array();
		foreach ($ktrahmen as $kr){
			if($kr->kr_parentid == $Parent->id){
				$item[]=$kr;
				$kr->children=self::buildKtRahmenTree($ktrahmen, $ktlist, $kr);
				if(empty($kr->children)){
					self::addKontoToRahmen($ktlist, $kr);
				}
			}
		}
		return $item;
	}
	
	
	protected static function buildKontoplan($ktrahmen, $ktlist){
		$item = array();
		foreach ($ktrahmen as $kr){
			if(empty($kr->kr_parentid)){
				$item[]=$kr;
				$kr->children=self::buildKtRahmenTree($ktrahmen, $ktlist, $kr);
				if(empty($kr->children)){
					self::addKontoToRahmen($ktlist, $kr);
				}
			}
		}
		return $item;
	}
	
	public static function deleteBookingItem($option){
		$id = $option['id'];
		$oldItems = self::readBookingDetails($id);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->delete($db->quoteName('#__0001_buchungdetail'));
		$query->where("bd_parentid = ".$id);

		$db->setQuery($query);

		$result = $db->execute();
		
		if(isset($option['recalc']) && $option['recalc']){
			$lstOfAccounts=array_column($oldItems, 'bd_konto');
			self::updateSaldo($listOfAccounts);
		}
	}
	
	
	protected static function updateSaldo($listOfAccounts){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__0001_konto k set ko_sum=(select sum(bd_betrag) from `#__0001_buchungdetail` where bd_konto=k.id), ko_countbook=(select count(*) from `#__0001_buchungdetail` where bd_konto=k.id)');
		
		if(count($lstOfAccounts)>0){
			$query->where("id in (".implode(',',$lstOfAccounts).")");
		}

		$db->setQuery($query);
//				JFactory::getApplication()->enqueueMessage($query);
		$result = $db->execute();
	}

	public static function saveBookingItems($parentId, $items){
	    //Update all Items
	    $oldItems = self::readBookingDetails($parentId);
		$listOfAccounts=[];
	    
	    $tables=array();
    	foreach ($items as $item){
    	    $table = self::getBookingDetailsTable();
    	    $tables[]=$table ;
    	    if($item->id>0){
    	        if(!$table->load($item->id)){
    	            return false;
    	        }
    	    }else{
    	        $table->bd_parentid=$parentId;
    	    }
            $table->bd_konto=$item->bd_konto;
            $table->bd_sollhaben=$item->sollhaben;
            $table->bd_waehrung=$item->bd_waehrung;
            $table->bd_betrag=$item->bd_betrag*100;
            $table->bd_kurs=$item->bd_kurs*10000;
            $table->bd_text=$item->bd_text;
			
			$listOfAccounts[]=$item->bd_konto;
    	}
    	
    	//Zuerst gelöschte eliminieren
    	//JFactory::getApplication()->enqueueMessage(implode(',',array_column($tables, 'id')));
    	
    	$listOfIds=array_column($tables, 'id');
    	foreach($oldItems as $item){
    	    if(array_search($item->id, $listOfIds)===false){
    	        //JFactory::getApplication()->enqueueMessage($item->id ." würde gelöscht werden");
    	        $table = self::getBookingDetailsTable();
    	        $table->delete($item->id);
				$listOfAccounts[]=$item->bd_konto;
    	    }
    	}
    	
    	foreach($tables as $table){
    	    $table->store();
    	}
    	
		self::updateSaldo($listOfAccounts);
    	
    	return true;
	}

	
	protected static function getBookingDetailsTable($type = 'Financebookdetail', $prefix = 'ClouderpTable', $config = array())
	{
	    return JTable::getInstance($type, $prefix, $config);
	}

	protected static function getAccountTable($type = 'Account', $prefix = 'ClouderpTable', $config = array())
	{
	    return JTable::getInstance($type, $prefix, $config);
	}

	protected static function getAccountframeTable($type = 'Accountframe', $prefix = 'ClouderpTable', $config = array())
	{
	    return JTable::getInstance($type, $prefix, $config);
	}











	public static function generateBookingDetails($parentKeyList, $sollhaben){
	    //Achtung: Falls hier was geändert wird, muss dies auch in booking.js unter addBookingItem gemacht
		$nrec=new stdClass();
	    $nrec->id=0;
		$nrec->bd_parentid=$parentKeyList;
		$nrec->bd_konto='';
		$nrec->sollhaben=$sollhaben;
		$nrec->bd_waehrung='CHF';
		$nrec->bd_betrag=0;
		$nrec->bd_kurs=10000;
		$nrec->bd_text='';
		$nrec->ktonr='';
		$nrec->ktobez='';
		$nrec->ktoid=0;
		return $nrec;
	}




	protected static function readBookingDetails($parentKeyList){
	    //Achtung: Falls hier was geändert wird, muss dies auch in booking.js unter addBookingItem gemacht
	    $db = JFactory::getDbo();
	    $query = $db->getQuery(true)->from('#__0001_buchungdetail bd')
	    ->select("bd.`id`,`bd_parentid`,`bd_konto`,`bd_sollhaben` as sollhaben,	`bd_waehrung`,`bd_betrag`,`bd_kurs`,`bd_text`, s.`ko_kontonr` as ktonr, s.`ko_bezeichnung` as ktobez,s.id as ktoid")
	    ->join('LEFT', $db->quoteName('#__0001_konto', 's') . ' ON (' . $db->quoteName('bd_konto') . ' = ' . $db->quoteName('s.id') . ')')
	    ->order('bd.id asc');
	    
	    
	    $query->where($db->quoteName('bd_parentid') .' IN ('. $parentKeyList. ')');
	    
	    $db->setQuery($query);
//		JFactory::getApplication()->enqueueMessage($query);		
	    return $db->loadObjectList();
/*		
$arr=[];
return $arr;
*/
	}

	
	public static function addBookingDetails($buchungen, $parentKeyList){
	    
	    $bdetails = self::readBookingDetails($parentKeyList);

		self::buildBuchungen($buchungen, $bdetails);

		return $buchungen;
	}
	
	
	protected static function buildBuchungDetails($bdetails, $buchung){
		foreach ($bdetails as $ko){
			if($ko->bd_parentid == $buchung->id){
				$buchung->details[]=$ko;
				$ko->bd_betragDisplay=number_format(($ko->sollhaben-1)*$ko->bd_betrag/100, 2, '.', "'");
				$ko->bd_kursDisplay=number_format($ko->bd_kurs/1000, 3, '.', "'");
			}
		}
	}

	public static function getSaldolist($account){
		$db = JFactory::getDbo();
	    $query = $db->getQuery(true)
		        ->from('`#__0001_buchungdetail` d')
	            ->select("sum(bd_betrag) as betrag, count(*) as anzahl, bd_konto,  `bu_datum`, max(date_format(`bu_datum`,'%d.%m.%Y')) as dd, min(ko_firstbilance) as ko_firstbilance")
	            ->join('inner','#__0001_buchung b on d.bd_parentid = b.id')
				->join('inner','#__0001_konto k on d.bd_konto = k.id')
				->group('bd_konto, bu_datum')
				->where('bd_konto ='. $account)
				->order('bu_datum asc');
	    
	    
	    $db->setQuery($query);
	    
	    $rows= $db->loadObjectList();
		
		$res=[];
		$total=0;
		foreach($rows as $row){
			$res[$row->dd]=$total+$row->ko_firstbilance+$row->betrag;
			$total+=$row->betrag;
		}
		
		/*
		$imax=count($rows);
		if($imax>0){
			$total=$rows[0]->ko_firstbilance;
		
			for($i=$imax-1; $i>=0; $i--){
				$row=$rows[$i];
				$total+=$row->betrag;
				$res[$row->dd]=$total;
			}
		}
		*/
		return $res;
	}
	

	
	public static function buildBuchungen($buchungen, $bdetails){
		$item = array();
		foreach ($buchungen as $bu){
			$item[]=$bu;
			self::buildBuchungDetails($bdetails, $bu);
		}
		return $item;
	}
	
	public static function writeSammelbuchung($param){
	    $Financebook=JTable::getInstance('Financebook', 'ClouderpTable', null);
	    $Financebook->bu_datum=$param['date'];
	    $Financebook->bu_belegnr=$param['beleg'];
	    $Financebook->bu_text=$param['buchtext'];
	    $Financebook->store();
	    
	    $parentId=$Financebook->id;
	    $param['paramkey']=$parentId;
	    
	    
	    $tblbdetail=JTable::getInstance('Financebookdetail', 'ClouderpTable', null);
	    $tblbdetail->bd_parentid=$parentId;
	    $tblbdetail->bd_konto=$param['kontoS'];
	    $tblbdetail->bd_sollhaben=2;
	    $tblbdetail->bd_waehrung='CHF';
	    $tblbdetail->bd_betrag=$param['Betrag'];
	    $tblbdetail->bd_kurs=10000;
	    $tblbdetail->bd_text=$param['textS'];
	    $tblbdetail->store();
	    $param['fibukeyS']= $tblbdetail->id;
	    
	    
	    $tblbdetail=JTable::getInstance('Financebookdetail', 'ClouderpTable', null);
	    $tblbdetail->bd_parentid=$parentId;
	    $tblbdetail->bd_konto=$param['kontoH'];
	    $tblbdetail->bd_sollhaben=0;
	    $tblbdetail->bd_waehrung='CHF';
	    $tblbdetail->bd_betrag=-1* $param['Betrag'];
	    $tblbdetail->bd_kurs=10000;
	    $tblbdetail->bd_text=$param['textH'];
	    $tblbdetail->store();
	    $param['fibukeyH']= $tblbdetail->id;
	    return $param;
	}
	
	/*
	 * bookFactura bucht auf die korrekte Buchung einer Verrechnung
	 */
	public static function bookFactura($facturaId){
	    $db = JFactory::getDbo();
	    //Faktura holen
	    $query = $db->getQuery(true)
        	    ->from('#__0001_cerp_factura fa')
        	    ->select("fa.*, p.accountsell, fr.title as buchungstitel")
        	    ->join('LEFT', $db->quoteName('#__0001_cerp_facturarun', 'fr') . ' ON (fr.id=fa.runid)')
        	    ->join('LEFT', $db->quoteName('#__0001_cerp_product', 'p') . ' ON (fa.productid=p.id)')
        	    ->where("fa.id=".$db->escape($facturaId));
	    $db->setQuery($query);
	    $factura =$db->loadObject();

	    if($factura->fibuid==0){
	        //Noch nicht gebucht! Mal sehen, ob die Sammelbuchung schon geschrieben wurde
	        $query = $db->getQuery(true)
        	        ->from('#__0001_buchungdetail b')
        	        ->select("b.bd_parentid")
        	        ->where("id in (select fibuid from #__0001_cerp_factura f where f.runid = ".$factura->runid.")");
	        $db->setQuery($query);
	        $sammelParent =$db->loadObjectList();
	        
	        if(count($sammelParent)>0){
	            $parentId=$sammelParent[0]->bd_parentid;
	            //Neuen Buchungsatz an die Sammelbuchung hinzufügen
	            $tblbdetail=JTable::getInstance('Financebookdetail', 'ClouderpTable', null);
	            $tblbdetail->bd_parentid=$parentId;
	            $tblbdetail->bd_konto=$factura->accountsell;
	            $tblbdetail->bd_sollhaben=0;
	            $tblbdetail->bd_waehrung='CHF';
	            $tblbdetail->bd_betrag=-100* $factura->betrag;
	            $tblbdetail->bd_kurs=10000;
	            $tblbdetail->bd_text=$factura->title;
	            $tblbdetail->store();
	            $fibuId= $tblbdetail->id;
				
				self::reCalculateSammelBuchung($parentId);
	        }else{
	            //Sammelbuchung erzeugen
	            //Gegenbuchung anlegen
	            $conf=JComponentHelper::getParams('com_clouderp');
	            $param=array(
	                'date' =>$factura->fakturadatum,
	                'beleg'=>'',
	                'buchtext' =>$factura->buchungstitel,
	                'kontoS' => $conf['ktDebiSammel'],
	                'kontoH' => $factura->accountsell,
	                'Betrag'=>100*$factura->betrag,
	                'textS'=>'',
	                'textH' =>$factura->title
	                
	            );
	            $param=self::writeSammelbuchung($param);
	            $fibuId=$param['fibukeyH'];
	        }
	        //Key zur Buchung speichern
	        $tblbFactura=JTable::getInstance('Factura', 'ClouderpTable', null);
	        if($tblbFactura->load($facturaId)){
	            $tblbFactura->fibuid=$fibuId;
	            $tblbFactura->store();
	        }
	    }else{
	        $tblbdetail=JTable::getInstance('Financebookdetail', 'ClouderpTable', null);
	        $tblbdetail->load($factura->fibuid);
	        if($tblbdetail->bd_betrag!=-100* $factura->betrag){
	           $tblbdetail->bd_betrag=-100* $factura->betrag;
	           $tblbdetail->store();
	           self::reCalculateSammelBuchung($tblbdetail->bd_parentid);
	        }
	    }
	}
	
	protected static function reCalculateSammelBuchung($parentId){
	    $db = JFactory::getDbo();
	    
	    $query = $db->getQuery(true)
	       ->from('#__0001_buchungdetail')
	       ->select("min(id) as firstid, sum(bd_betrag) as betrag")
	       ->where("bd_parentid=". $parentId);

	    $db->setQuery($query);
	    
	    $result = $db->loadObject();
	    if($result){
	        $tbl=self::getBookingDetailsTable();
	        if($tbl->load($result->firstid)){
	            $tbl->bd_betrag=$tbl->bd_betrag+(-1*$result->betrag);
	            $tbl->store();
	        }
	        
	    }
	}
}
