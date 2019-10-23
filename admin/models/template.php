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
 * Template Model
 *
 * @since  0.0.1
 */
class ClouderpModelTemplate extends JModelAdmin
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
	public function getTable($type = 'Template', $prefix = 'ClouderpTable', $config = array())
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
		$form = $this->loadForm('com_clouderp.template', 'template', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.template.data', array());
		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('template.id') == 0)
			{
			}
//			JFactory::getApplication()->enqueueMessage('+++ClouderpModeltemplate/getForm: ' .  $form->templateCalendarText);
		}

		$this->preprocessData('com_clouderp.template', $data);

		return $data;
	}
	
	public function showTemplate($data){
//		JLoader::register('SendfacturaHelper', '../../helpers/sendfacturahelper.php');
		
//		JFactory::getApplication()->enqueueMessage('helper:'.dirname(__FILE__) . '/helpers/sendfacturahelper.php');
//		JFactory::getApplication()->enqueueMessage('template:'.$data['template']);
		
		$factura=new stdClass();
		
		$factura->sayhello='Hallo Mitglied';
		$factura->firstname='Hans';
		$factura->lastname='Muster';
		$factura->fak_name="Firma Muster AG";
		$factura->fak_address="Flughafenstrasse 12";
		$factura->fak_zipcode="8900";
		$factura->fak_town="Wallisellen";
		$factura->address='Rosenbergstrasse 18';
		$factura->zipcode='8000';
		$factura->town='Zürich';
		$factura->fakturadatum=date("Y-m-d");
		$factura->fakturadatumformat='%d.%m.%Y';
		$factura->zahlungsfrist=date("Y-m-d");
		$factura->zahlungsfristformat='%d.%m.%Y';
		$factura->phone='044 123 44 55';
		$factura->mobil='079 123 44 55';
		$factura->param1='Parameter 1';
		$factura->param2='Parameter 2';
		$factura->param3='Parameter 3';
		$factura->param4='Parameter 4';
		$factura->rechnungstitel='Beitäge 2019';
		$factura->email='test2@swisccom.com';
		$factura->betrag='500.00';
		$factura->category='Aktivmitglied';
		$factura->prodtitle='Produkt';
		$factura->prod_desc='Beispiel einer Beschreibung';
		$factura->remindlevel=1;
		$factura->remindcost=25;

		$template = '<page>'.$data['template'].'</page>';
		$pdf= SendfacturaHelper::generateFacturaDetailsAttachment($template,$factura);
		header('Content-Type: application/pdf; charset=utf-8');
		header('Content-disposition: inline; filename=vorschau.pdf');
		echo $pdf;
		\JFactory::getApplication()->close();
	}
}
