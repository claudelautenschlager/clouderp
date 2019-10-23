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
 * financebook Model
 *
 * @since  0.0.1
 */
class ClouderpModelFinancebook extends JModelAdmin
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
	public function getTable($type = 'Financebook', $prefix = 'ClouderpTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}


	public function save($data){
	    
	    $input = JFactory::getApplication()->input;
	    $file  = $input->files->get('file_upload');
	    if(isset($file )){
    	    // Cleans the name of teh file by removing weird characters
    	    $filename = JFile::makeSafe($file['name']);
    	    
    	    $src  = $file['tmp_name'];
    	    $fn=file_get_contents($src);
    	    
    	    if($fn!==false){
    	        
    	        
    	        $tmp_path=JFactory::getConfig()->get('tmp_path');
    	        $fName=$tmp_path . '/upload.pdf' ;
    	        file_put_contents($fName, base64_encode($fn));
    	        
    	        $n=strlen(base64_encode($fn));
    	        
    	        $data['bu_belegfile']=base64_encode($fn);
    	        $data['bu_belegfilename']=$filename;
    	        $data['bu_belegfilemime']=$file['type'];
    	    }
	    }
	    
	    
		$res=parent::save($data);
		if($res){
		    //Get die ID
		    $id= $this->getState('financebook.id');
//		    JFactory::getApplication()->enqueueMessage($data['bookitemjson']);
//		    JFactory::getApplication()->enqueueMessage($id);
		    
		    $bookingItems=json_decode($data['bookitemjson']);
		    ClouderpBookingHelper::saveBookingItems($id, $bookingItems);
		}
		return $res;
	}

	public function delete(&$pks){
		if(count($pks)==1){
			$option=['id'=>$pks[0], 'recalc'=>true];
			ClouderpBookingHelper::deleteBookingItem($option);
		}
		return parent::delete($pks);
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
		$form = $this->loadForm('com_clouderp.financebook', 'financebook', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}
	
	
	public function getItem($pk = null)
	{
		$item=parent::getItem($pk);
//		JFactory::getApplication()->enqueueMessage('pk ist null');	    
//		print_r($item);
		if(!empty($item->id)){
			ClouderpBookingHelper::addBookingDetails(array($item), $item->id);
		}else{
			$r1=ClouderpBookingHelper::generateBookingDetails(0, 2);
			$r2=ClouderpBookingHelper::generateBookingDetails(0, 0);
			$item->details=[$r1,$r2];
		}
		return $item;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.financebook.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
			

			// Prime some default values.
			if ($this->getState('financebook.id') == 0)
			{
			}
//			JFactory::getApplication()->enqueueMessage('+++ClouderpModelProduct/getForm: ' .  $form->facturaCalendarText);
		}

		$this->preprocessData('com_clouderp.financebook', $data);

		return $data;
	}
	
	public function getKontoliste(){
		$option=[];
		return ClouderpBookingHelper::getKontoliste($option);
	}
}
