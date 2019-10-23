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
 * Genfactura Model
 *
 * @since  0.0.1
 */
class ClouderpModelGenfactura extends JModelAdmin
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
		$form = $this->loadForm('com_clouderp.genfactura', 'genfactura', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.genfactura.data', array());
		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('genfactura.id') == 0)
			{
			}
		}

		$this->preprocessData('com_clouderp.genfactura', $data);

		return $data;
	}
	
	public function save($data){
//		JFactory::getApplication()->enqueueMessage('save of ClouderpModelGenfactura aufgerufen');
		$res=parent::save($data);
		//$data = JFactory::getApplication()->setUserState('com_clouderp.rundata', $this->getState($this->getName() . '.id'));
		if($res){
			$this->generateFacturaRecord();
		}
		return $res;
		
	}
	/*
	delete from  `vdcb_0001_cerp_factura` WHERE 1;
delete from  `vdcb_0001_cerp_facturarun` WHERE 1;
	*/
	protected function generateFacturaRecord(){
		$db = JFactory::getDbo();
/*
		$db->setQuery("delete from `#__0001_cerp_factura`");
		$db->execute();
		$db->setQuery("delete from `#__0001_cerp_facturarun`");
		$db->execute();
		$db->setQuery("update `#__0001_cerp_customers` set facturatedtill=null");
		$db->execute();
		
		$query = $db->getQuery(true);
*/		
		$db = JFactory::getDbo();
		$id =$this->getState($this->getName() . '.id');
		$db = JFactory::getDbo();
		$sql="INSERT INTO `#__0001_cerp_factura`(`status`, `title`, `customerid`, `productid`, `betrag`, `fakturadatum`, `zahlungsfrist`, `email`, `runid`, `email_subject`, `addDate`, `addUser`, `facturemedium`) ".
		     "select ".FACTURA_STATE_START.", concat(`lastname`,' ',`firstname`), c.`id`, p.`id`, p.vp, `fakturadatum`, `zahlungsfrist`, `email`, r.id, `email_subject`, CURRENT_TIMESTAMP(), r.addUser, c.facturemedium " .
             "FROM `#__0001_cerp_customers` c ".
			 "join `#__0001_cerp_facturarun` r on (r.id=" . $id ." and (facturatedtill is null or facturatedtill<zahlungsfrist))".
			 "left outer join `#__0001_cerp_product` p on (p.id=c.productabo)".
			 "where (r.nullerrechnung=0 or case when isnull(p.vp) then price1 else p.vp end>0)";
		$db->setQuery($sql);
		
		$db->execute();
	}
}
