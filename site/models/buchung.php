<?php
/**
  @package    Clouderp
  @author     Claude Lautenschlager
  @copyright  Copyright (C) 2017 - 2019 All rights reserved.
  @license    httpwww.gnu.orglicensesgpl.html GNUGPL

 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class ClouderpModelBuchung extends JModelItem
{
	protected $item;

	/**
	 * populate internal state
	 *
	 * @return void
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$id = $app->input->get('mandant', '', 'INT');
		$this->setState('clouderp.mandant', $id);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		parent::populateState();
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for table. Optional.
	 *
	 * @return  JTable A database object
	 */
	public function getTable($type = 'XXXX', $prefix = 'XXXXX', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the job
	 *
	 * @return object The job to be displayed to the user
	 */
	public function getItem()
	{
		$db = JFactory::getDbo();
		$mandant = $this->getState('clouderp.mandant');

		//$query = $db->getQuery(true)->from('#__0001_kontorahmen')->select('*');
		$query = $db->getQuery(true)->
		         from('#__0001_buchung')
				 ->select("`id`, date_format(`bu_datum`,'%d.%m.%Y') as bu_datum, `bu_belegnr`, `bu_sammelparent`, `bu_text`, date_format(`addDate`,'%d.%m.%Y') as addDate, date_format(`updDate`,'%d.%m.%Y') as `updDate`, `addUser`, updUser")
				 ->order('bu_datum desc');
				 
//$query->where('id=1');
		$db->setQuery($query);
	
		$buchungen = $db->loadObjectList();
		
		$query = $db->getQuery(true)->from('#__0001_buchungdetail bd')
			->select("bd.`id`,`bd_parentid`,`bd_konto`,`bd_sollhaben` as sollhaben,	`bd_waehrung`,`bd_betrag`,`bd_kurs`,`bd_text`, s.`ko_kontonr` as ktonr, s.`ko_bezeichnung` as ktobez,s.id as ktoid")
			->join('LEFT', $db->quoteName('#__0001_konto', 's') . ' ON (' . $db->quoteName('bd_konto') . ' = ' . $db->quoteName('s.id') . ')')
            ->order('bd.id asc');
		
		$parentKey='-1';
		foreach($buchungen as $buchung){
			$parentKey .= (','. strval($buchung->id));
		}
		$query->where($db->quoteName('bd_parentid') .' IN ('. $parentKey. ')');
		
	
		$db->setQuery($query);
	
		$bdetails = $db->loadObjectList();

		$this->item=$this->buildBuchungen($buchungen, $bdetails);

		return $this->item;
	}
	
	protected function buildBuchungDetails($bdetails, $buchung){
		foreach ($bdetails as $ko){
			if($ko->bd_parentid == $buchung->id){
				$buchung->details[]=$ko;
				$ko->bd_betragDisplay=number_format(($ko->sollhaben-1)*$ko->bd_betrag/100, 2);
				$ko->bd_kursDisplay=number_format($ko->bd_kurs/1000, 3);
			}
		}
	}
	
	protected function buildBuchungen($buchungen, $bdetails){
		$item = array();
		foreach ($buchungen as $bu){
			$item[]=$bu;
			$this->buildBuchungDetails($bdetails, $bu);
		}
		return $item;
	}
}
