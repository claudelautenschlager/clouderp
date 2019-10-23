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
 * Genremind Model
 *
 * @since  0.0.1
 */
class ClouderpModelGenremind extends JModelAdmin
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
	public function getTable($type = 'Genfactura', $prefix = 'ClouderpTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$form = $this->loadForm('com_clouderp.genremind', 'genremind', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.genremind.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
			$cids=JFactory::getApplication()->getUserState('com_clouderp.edit.genremind.id','');
			if(is_array($cids)){
//			JFactory::getApplication()->enqueueMessage('loadFormData/aufgerufen:'.$cids);
				$data->cids = implode(',',$cids);
			}

			// Prime some default values.
			if ($this->getState('genremind.id') == 0)
			{
			}
		}
		JFactory::getApplication()->setUserState('com_clouderp.edit.genremind.id','');  // immer wieder leer
		$this->preprocessData('com_clouderp.genremind', $data);

		return $data;
	}
	
	public function save($data){
		$res=parent::save($data);
//		$res=true;
		//$data = JFactory::getApplication()->setUserState('com_clouderp.rundata', $this->getState($this->getName() . '.id'));
		if($res){
			$this->generateRemindRecord($data);
		}
		return $res;
		
	}
	/*
	delete from  `vdcb_0001_cerp_factura` WHERE 1;
delete from  `vdcb_0001_cerp_facturarun` WHERE 1;
	*/
	protected function generateRemindRecord($data){
		$id =$this->getState($this->getName() . '.id');
		$db = JFactory::getDbo();
		
		$fields = array(
			    $db->quoteName('status') . ' = ' . $db->quote(FACTURA_STATE_START),
				$db->quoteName('fakturadatum') . ' = ' . $db->quote(ClouderpHelper::dateGerman2English($data['fakturadatum'])),
				$db->quoteName('zahlungsfrist') . ' = ' . $db->quote(ClouderpHelper::dateGerman2English($data['zahlungsfrist'])),
				$db->quoteName('runid') . ' = ' . $id,
				$db->quoteName('remindlevel') . ' = ' . $db->escape($data['remindlevel']),
				$db->quoteName('remindcost') . ' = ' . $db->quoteName('remindcost') .'+ '. $db->escape($data['remindcost']),
				$db->quoteName('updDate') . ' = ' . $db->quote(Date('Y-m-d H:i')),
				$db->quoteName('updUser') . ' = ' . $db->quote(JFactory::getUser()->name)
			);
//		$cids=substr($db->quote($data['cids']), 1, strlen($data['cids']));
		$cids=$db->escape($data['cids']);
		
		$query = $db->getQuery(true)
		         ->update('#__0001_cerp_factura')
				 ->set($fields)
				 ->where('id in ('. $cids .')')
				 ->where('status ='.FACTURA_STATE_SENT)
		         ->where('remindlevel='. $db->escape($data['remindlevel']-1))
				 ->where('zahlungsfrist <'. $db->quote(ClouderpHelper::dateGerman2English($data['fakturadatum'])));
				 
		$db->setQuery($query);
		JFactory::getApplication()->enqueueMessage('query:'. $query);
		
		$res=$db->execute();
		print_r($res);
	}
}
