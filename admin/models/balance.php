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
 * Balance Model
 *
 * @since  0.0.1
 *  http://localhost/vdcb/administrator/index.php?option=com_clouderp&view=accountpage&layout=default&account=1
 */
class ClouderpModelBalance extends JModelList
{
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'dateper'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	protected function getListQuery()
	{
		// Ist nur Pseudo, für das Framework 
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("*")->from('#__0001_konto')->where('1');

		return $query;
	}
	
	protected function normalizeItems(&$itemsflach, $item, $level){
		$item->level=$level;
		if($item->children!=null){
			//Zuerst Konten entfernen, die nicht angezeigt werden sollen, wenn sie 0 haben
			foreach($item->children as $i =>$child){
				if($child->type==11 && $child->ko_bilancewhenzero=0 && empty($child->saldoper)){
					//löschen
					unset($item->children[$i]);
				}
			}
			
		}
		if($item->children!=null){
			$itemsflach[]=$item;     //Bäume ohne Childs werden gar nicht erst aufgenommen
			foreach($item->children as $child){
				if($child->type==11){
					$child->level=$level+1;
					$itemsflach[]=$child;
				}
				else{
					$this->normalizeItems($itemsflach, $child, $level+1);
				}
			}
		}
	}
	
	public function getItems(){
		$itemsflach[0]=[];
		$itemsflach[1]=[];
		$dateper=$this->getState('filter.dateper', '', "STRING");
		if(!empty($dateper)){
			$dateper=ClouderpHelper::germanDatetoEnglish($dateper);
			$option=['boolingTillDate' =>$dateper, 'lastperiod'=>true];
			$items=ClouderpBookingHelper::getKontoplanMitRahmen($option);
			
			//noch ein wenig rechnen
			foreach($items as $rec){
				$rec->saldoper +=$rec->ko_firstbilance;
				//wenn es keine Vorjahrezahlen gibt, dann übernehmen wir $rec->ko_firstbilance
				if($rec->ko_sumlastperiod==null){
					$rec->ko_sumlastperiod=$rec->ko_firstbilance;
				}
			}
			
			foreach($items as $rec){
				ClouderpBookingHelper::cummulateSaldoPer($rec, 'saldoper');
				ClouderpBookingHelper::cummulateSaldoPer($rec, 'ko_sumlastperiod');
				ClouderpBookingHelper::cummulateSaldoPer($rec, 'ko_budget');
				ClouderpBookingHelper::cummulateSaldoPer($rec, 'ko_budgetlastperiod');
			}
			
			//Jetzt müssen wir das ganze flachsetzen
			
			//Bilanz
			$this->normalizeItems($itemsflach[0], $items[0], 0);
			$this->normalizeItems($itemsflach[0], $items[1], 0);
			//Gewinn/Verlustrecord einsetzen
			if(isset($items[0]) && isset($items[1])){
				$nrec=new stdClass();
				$nrec->kontonr='';
				$nrec->bezeichnung='Gewinn/Verlust';
				$nrec->type=12;
				$nrec->saldoper=$items[0]->saldoper + $items[1]->saldoper;
				$nrec->doshow=1;
				$nrec->level=0;
				$nrec->ko_sumlastperiod=$items[0]->ko_sumlastperiod + $items[1]->ko_sumlastperiod;
				$itemsflach[0][]=$nrec;
			}
			
			//ER
			$this->normalizeItems($itemsflach[1], $items[2], 0);
			$this->normalizeItems($itemsflach[1], $items[3], 0);
			if(isset($items[2]) && isset($items[3])){
				$nrec=new stdClass();
				$nrec->kontonr='';
				$nrec->bezeichnung='Gewinn/Verlust';
				$nrec->type=12;
				$nrec->saldoper=$items[2]->saldoper + $items[3]->saldoper;
				$nrec->doshow=1;
				$nrec->level=0;
				$nrec->ko_sumlastperiod=$items[2]->ko_sumlastperiod + $items[3]->ko_sumlastperiod;
				$itemsflach[1][]=$nrec;
				
			}
			
		}
		return $itemsflach;
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
		parent::populateState('datumper', 'desc');
	}
}
