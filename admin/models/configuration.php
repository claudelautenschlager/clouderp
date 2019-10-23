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
 * configuration Modelenq

 *
 * @since  0.0.1
 */
class ClouderpModelConfiguration extends JModelForm
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
	public function getTable($type = 'Configuration', $prefix = 'ClouderpTable', $config = array())
	{
		return null; //JTable::getInstance($type, $prefix, $config);
	}
/*
	public function apply($data){
		//JFactory::getApplication()->enqueueMessage('ClouderpModelConfiguration/save: ' .  $data->id);
		return false;
//		return true;
	}
	
*/	
	public function getItem($pk = null){
	    if(!isset($this->item)){
			//$this->item=$this->getItemFromDBO();
			$this->item=SendfacturaHelper::getConfig();
    		if($this->item==false){
    			$this->item=new stdClass();
    			$this->item->id=0;
    	        $this->item->smtp_host='';
    	        $this->item->smtp_port='500';
    	        $this->item->smtp_username='test';
    	        $this->item->smtp_password='';
    		}
	    }		
		return $this->item;
	}

	public function write($data){
		$user=JFactory::getUser();
		//$rec=$this->getItemFromDBO();
		$rec=SendfacturaHelper::getConfig();
		$isNew = ($rec==false);
		$userid=$user->id; // https://docs.joomla.org/Accessing_the_current_user_object
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		if($isNew){
			$columns = array('id', 'smtp_host',	'smtp_port','smtp_sendername','smtp_username','smtp_password','addDate','addUser');
			$values = array($user->id, 
			                $db->quote($data['smtp_host']),
			                $db->quote($data['smtp_port']),
			                $db->quote($data['smtp_sendername']),
			                $db->quote($data['smtp_username']), 
							$db->quote($data['smtp_password']),
							$db->quote(Date('Y-m-d H:i')),$db->quote($user->name)
							);
			$query->insert($db->quoteName('#__0001_cerp_configpersonal'))
				   ->columns($db->quoteName($columns))
                   ->values(implode(',', $values));
		}
		else{
			$fields = array(
			    $db->quoteName('smtp_host') . ' = ' . $db->quote($data['smtp_host']),
				$db->quoteName('smtp_port'). ' = ' . $db->quote($data['smtp_port']),
				$db->quoteName('smtp_sendername'). ' = ' . $db->quote($data['smtp_sendername']),
				$db->quoteName('smtp_username') . ' = ' . $db->quote($data['smtp_username']),
				$db->quoteName('smtp_password') . ' = ' . $db->quote($data['smtp_password']),
			
				$db->quoteName('updDate') . ' = ' . $db->quote(Date('Y-m-d H:i')),
				$db->quoteName('updUser') . ' = ' . $db->quote($user->name)
			);
			$conditions = array(
				$db->quoteName('id') . ' = ' . $user->id,
			);

			$query->update('#__0001_cerp_configpersonal')->set($fields)->where($conditions);
		}
		
		$db->setQuery($query);
		$db->execute();		
	    
        return true;	    
	}

/*
	protected function getItemFromDBO(){
		$res=false;
		$userid=JFactory::getUser()->id; // https://docs.joomla.org/Accessing_the_current_user_object
		$db = $this->getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select("*")
			  ->from('#__0001_cerp_configpersonal')
			  ->where("id=".$userid);
		$db->setQuery($query);
		
		$lst=$db->loadObjectlist();
		if(count($lst)>0){
			$res=$lst[0];
		}

		return $res;
	}
*/
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
		$form = $this->loadForm('com_clouderp.configuration', 'configuration', array('control' => 'jform', 'load_data' => $loadData));

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
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.configuration.data', array());
		if (empty($data))
		{
			$data = $this->getItem();
//			JFactory::getApplication()->enqueueMessage('+++ClouderpModelfactura/getForm: ' .  $form->facturaCalendarText);
		}

		$this->preprocessData('com_clouderp.configuration', $data);

		return $data;
	}
	
	public function sendMail($data){
		JFactory::getApplication()->setUserState('com_clouderp.edit.configuration.data', $data);
		try{
			$mailer = JFactory::getMailer();
			
			$mailer->useSmtp('true', $data['smtp_host'], $data['smtp_username'], $data['smtp_password'], null, $data['smtp_port']);
			$mailer->setSender(array($data['smtp_username'],$data['smtp_sendername']));
			$user = JFactory::getUser();
			$mailer->addRecipient($user->email);
	//		$mailer->addBcc("webmaster@curling-zuerich.ch");
			
			$mailer->setSubject('Testmail');

			$body   = "Alles OK";
			
			$mailer->isHtml(false);
		
			$mailer->setBody($body);
			
			$send = $mailer->Send();
			
		
			$mailer->ClearAllRecipients();
			JFactory::getApplication()->enqueueMessage('Testmail konnte fehlerfrei versendet werden');
		} catch (Exception $e) {
//			JFactory::getApplication()->enqueueMessage('Error beim versand:' .  $e->getMessage());
			$mailer->ClearAllRecipients();
			JFactory::getApplication()->enqueueMessage('Error beim Testmail:' .  $e->getMessage(), "error");
		}
		return true;
	}
}
