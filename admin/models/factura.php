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
 * factura Model
 *
 * @since  0.0.1
 */
class ClouderpModelFactura extends JModelAdmin
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
	public function getTable($type = 'Factura', $prefix = 'ClouderpTable', $config = array())
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
		$form = $this->loadForm('com_clouderp.factura', 'factura', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.factura.data', array());
		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('factura.id') == 0)
			{
			}
//			JFactory::getApplication()->enqueueMessage('+++ClouderpModelfactura/getForm: ' .  $form->facturaCalendarText);
		}

		$this->preprocessData('com_clouderp.factura', $data);

		return $data;
	}

	public function save($data){
		if($data['status']==FACTURA_STATE_START){
			//Im fall von Änderung des Status, müssen ev. Daten neu gelesen werden
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
					->from('#__0001_cerp_factura f')
					->leftjoin('#__0001_cerp_customers c on f.customerid = c.id')
				    ->leftjoin('#__0001_cerp_product p on p.id=c.productabo')
                    ->select('p.id as prodnr, p.vp, c.email')
					->where('f.id = '.$data['id']);
//			JFactory::getApplication()->enqueueMessage($query);
			$db->setQuery($query);
			$cc =$db->loadObject();
			
			$data['betrag']=$cc->vp;
			$data['email']=$cc->email;
			$data['productid']=$cc->prodnr;
		}
		$res=parent::save($data);
		
		
		
		return $res;
	}
	
}
