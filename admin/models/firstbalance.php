<?php
/**
 * @package    CloudErp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Eröffnungsbilanz Model
 *
 * @since  0.0.1
 */
class ClouderpModelFirstbalance extends JModelAdmin
{
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database table object
	 */
	public function getTable($type = 'Account', $prefix = 'ClouderpTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function save($data){
		$data=JFactory::getApplication()->input->post->get('jform',array(),'RAW');
		$items=$this->getItem();
		
		forEach($data as $key => $f){
		    $konto=substr($key,16);   //key ist: jform[ko_firstbilance_nn]: 
		    if(is_numeric($konto)){
    		    $tbl = $this->getTable();
    		    if($tbl->load($konto)){
					$col=array_search($konto, array_column($items, 'id'));
					if($items[$col]->kr_typ==2){
					    $f=-1*$f;
					}
    		        $tbl->ko_firstbilance=$f*100;
    		        $tbl->store();
    		    }
		    }
		}
		$ii=count($data);
		return true;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed	A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_clouderp.firstbalance', 'firstbalance', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	public function getItem($pk = null)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		         ->from('#__0001_konto k')
				 ->join('INNER', $db->quoteName('#__0001_kontorahmen', 'r') . ' ON k.ko_kontorahmen=r.id and r.kr_typ in (1,2)')
				 ->select('k.*, r.kr_typ, 0 as fieldelement, case when kr_typ=1 then ko_firstbilance else -1*ko_firstbilance end as betrag');
		$db->setQuery($query);
		//JFactory::getApplication()->enqueueMessage($query);
		return $db->loadObjectList();
	}


	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		$data = $this->getItem();
		$this->preprocessData('com_clouderp.firstbalance', $data);

		return $data;
	}
}
