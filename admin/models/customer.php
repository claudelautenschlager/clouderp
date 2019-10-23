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
 * customer Model
 *
 * @since  0.0.1
 */
class ClouderpModelCustomer extends JModelAdmin
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
	public function getTable($type = 'Customer', $prefix = 'ClouderpTable', $config = array())
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
		$form = $this->loadForm('com_clouderp.customer', 'customer', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}
		
		return $form;
	}

	public function save($data){
	    $comParams=JComponentHelper::getParams('com_clouderp');
	    $my = JFactory::getUser();
	    
	    if($my->authorise('core.admin') && $comParams->get('propagateuser')){
			$res = $data['propagateUser']==1 && $this->checkEmail($data['id'], $data['email']);
	        if($res){
	            JFactory::getApplication()->enqueueMessage(JText::_('COM_CLOUDERP_CUSTOMER_MSGDOUBLEEMAIL')  . ':' . $p,'error');
	            return false;
	        }
			//Zuerst noch die alten Daten lesen
			$tableOldData = $this->getTable();
			$tableOldData->load($data['id']);
    		if(parent::save($data)){
				if($data['propagateUser']==1){
					$this->propagateUser($data);
				}
				if($data['propagateDav']==1){
					$this->propagateDav($data, $tableOldData);
				}
    			return true;
    		}
	    }
		return false;
	}
	
	
	protected function propagateDav($data, $tableOldData){
		JLoader::register('CarddavHelper', ClouderpHelper::getBasePath().'/components/com_clouderp/helpers/carddavhelper.php');
		$comParams=JComponentHelper::getParams('com_clouderp');
		$config = array("user"=>$comParams->get("dav_username"), "password"=>$comParams->get("dav_password"));
		$engine= new CarddavHelper($config);
		
		$davId=$tableOldData->david;
    	
		if(!empty($davId)){
			try{
				$engine->load($tableOldData->david);
				if($data[catid]!=$tableOldData->catid){
					//Eventuell Wechsle des Adressbuches
					$res=$engine->checkSameAddressbook($data[catid], $tableOldData->catid);
					if(!$res){
						$engine->delete();
						$davId='';
					}
				}
			}catch(Exception $e){
				//Testen ob einfach die Ressource nicht mehr vorhanden ist
				print_r($e);
			}
		}
		$this->setDAVAttributs($engine, $data);
		$doSaveURI=false;
		if(empty($davId)){
			$uid = $engine->buildUID();
			$davId=$engine->buildURI($data[catid]);
			$doSaveURI=true;
		}
		try{
			$engine->store();
			if($doSaveURI){
				$tab=$this->getTable();
				$id=$this->getState($this->getName() . '.id', $tab->$key);
				$tab->load($id);
				$tab->david= $davId;
				$tab->store();
			}
		}catch(Exception $e){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CLOUDERP_CUSTOMER_MSGERRORSAVE')  . ' url:' .$engine->currentKey.' / Fehlermeldung'. $e->getMessage(),'error');
		}
	}
	
	protected function setDAVAttributs($engine, $data){
		$engine->set("EMAIL",$data['email']);
        $engine->set("FN",$data['lastname'].' '. $data['firstname']);
		$engine->set("NICKNAME",$data['sayhello']);
		if(!empty($data['mobil'])){
		  $engine->set("CELL",$data['mobil']);
		}
		if(!empty($data['phone'])){
		  $engine->set("HOME",$data['phone']);
		}
        $engine->set("ADR", array("STREET"=>$data['address'], "ZIPCODE"=>$data['zipcode'], "TOWN"=>$data['town']));
//		$engine->set("NOTE","Die erste Notiz");
        if(empty($data['birth'])){
            $engine->set("BDAY",ClouderpHelper::germanDatetoEnglish($data['birth']));
	    }
		$engine->set("ORG","Dolder Club Zürich");
        $engine->set("ANNIVERSARY",$data['entrydate']);
        
        
        $engine->set("N", array("LASTNAME"=>$data['lastname'], "FIRSTNAME"=>$data['firstname']));
//        $engine->set("LASTNAME",$data['lastname']);
//        $engine->set("FIRSTNAME",$data['firstname']);
		
        
        $engine->set("TITLE",$engine->getTextOfAddressbook($data['catid']));  //Mitgliederstatus
	}
	
	
	protected function propagateUser($data){
		//JLoader::register('UsersModelUser', dirname(__FILE__) . '../../com_users/models/user.php');
		JLoader::register('UsersModelUser', ClouderpHelper::getBasePath().'/components/com_users/models/user.php');
		
		if (class_exists('UsersModelUser')) {
			$userModel=new UsersModelUser();
			
			$userTable=$userModel->getTable();
			$ff=$userTable->getFields();
			$userData=array();
			foreach($ff as $k=>$v){
				$userData +=[$k=>""];
			}
			
			$customer=$this->getTable();
			$id=$this->getState($this->getName() . '.id', $customer->$key);
			$customer->load($id);
			$joomlauserid = $customer->joomlauserid;

			if($joomlauserid==0){
				$comParams=JComponentHelper::getParams('com_clouderp');
				$grp = $comParams->get('propagategroup');
//update `vdcb_0001_cerp_customers` join vdcb_users u on  name=concat(lastname,' ', firstname) set joomlauserid= u.id
				$username=$this->generateUserName($data['firstname'],$data['lastname']);
			
				$userData["name"]=$data['lastname'].' '.$data['firstname'];
				$userData["username"]=$username;
				$userData["password"] = 'juPfli2b79!';
				$userData["registerDate"]=Date('Y-m-d H:i');
				
				$userData["id"]=0 ;
				$userData["sendEmail"] ="0";
				$userData["resetCount"] ="0";
				$userData["block"]='0';
				$userData["groups"] = Array($grp);
				$mail=strtolower($data['email']);
				$gmail='';
				if(strpos('@gmail.com',$mail)){
					$gmail=$mail;
				}
				$userData["email"] =$mail;
				$userData["com_fields"] = array("login-google" =>$gmail, "login-fb"=>'');
				$userData["param"] = array("admin_style"=>'', "admin_language"=>'de-CH',  "language" => 'de-CH' , "editor"=>'', "helpsite" =>'', "timezone"=>'');
				
				$userData["password2"]=$userData["password"];
				if($userRes=$userModel->save($userData)){
					//  Neue ID vom User speichern
					$joomlauserid=$userModel->getState($userModel->getName() . '.id');
					$customer->joomlauserid=$joomlauserid;
					$customer->store();
					JFactory::getApplication()->enqueueMessage(JText::_('COM_CLOUDERP_CUSTOMER_MSGNEWUSER').$username,'info');
				}else{
					$errors=$userModel->getError();
					JFactory::getApplication()->enqueueMessage(JText::_('COM_CLOUDERP_CUSTOMER_MSGERROR') . $errors,'error');
				}
			}else{
				$user=$userModel->getItem($joomlauserid);
				foreach($userData as $k=>$v){
					$userData[$k]=$user->$k;
				}
				$userData["name"]=$data['lastname'].' '.$data['firstname'];
				$userData["email"]=$data['email'];
				$groups=array();
				foreach($user->groups as $k=>$v){
					if(is_numeric($v))
					 $groups[]=$v;
				}
				$userData["groups"]=$groups;
				$userData["password2"]=$userData["password"];
				$userRes=$userModel->save($userData);
			}
		}
	}
	
	
	
	public function delete(&$ids){
	    $my = JFactory::getUser();
		
		JLoader::register('CarddavHelper', ClouderpHelper::getBasePath().'/components/com_clouderp/helpers/carddavhelper.php');
		$comParams=JComponentHelper::getParams('com_clouderp');
		$config = array("user"=>$comParams->get("dav_username"), "password"=>$comParams->get("dav_password"));

    	$engine= new CarddavHelper($config);
	    
	    if($my->authorise('core.admin') ){
	        if($comParams->get('propagateuser')){
	            JLoader::register('UsersModelUser', ClouderpHelper::getBasePath().'/components/com_users/models/user.php');
	            if (class_exists('UsersModelUser')) {
    	            $userModel=new UsersModelUser();
    	            foreach($ids as $id){
    	                $customer=$this->getTable();
    	                $customer->load($id);
    	                $joomlauserid = $customer->joomlauserid;
    	                $newIdList=array($id);
    	                if(parent::delete($newIdList) && $joomlauserid>0){
    	                    $newIdList=array($joomlauserid);
    	                    $userModel->delete($newIdList);
    	                }
						if(!empty($customer->david)){
							try{
								$engine->load($customer->david);
								$engine->delete();
							}catch(Exception $e){
								//Fehler wird ignoriert
							}
							
						}
					}
	            }
	        }else{
	            parent::delete($ids);
	        }
	    }
	}
	
	protected function generateUserName($vname,$nname){
		//Name nur bis zum ersten Blank oder Strich
		$nname=rtrim($nname);
		$nname=explode(' ', $nname)[0];
		$nname=explode('-', $nname)[0];
		
	    $username=rtrim(substr($vname,0,1).$nname);
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
               ->from('#__users')
               ->select("count(*) as anz")
               ->where("username like ".$db->quote($username.'%'));
        $db->setQuery($query);
       
        $obj=$db->loadObject();
        if($obj->anz>0){
            $username= $username.trim(strval($anz));  //clautenschager1
        }
	    return strtolower($username);
	}
	
	
	protected function checkEmail($id, $email){
		
	    $email=strtolower($email);
	    
	    $db = JFactory::getDbo();
	    
	    $query = $db->getQuery(true)
    	    ->from('#__0001_cerp_customers')
    	    ->select("count(*) as anz")
    	    ->where("email = ".$db->quote($email));
	    if($id!=0){
	        $query->where("id <> ".$id); 
	    }
//		JFactory::getApplication()->enqueueMessage($query);

		$db->setQuery($query);
	    
	    $obj=$db->loadObject();

	    if($obj->anz>0){
	        return true;
	    }
	    return false;
	}
	
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_clouderp.edit.customer.data', array());
		if (empty($data))
		{
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('customer.id') == 0)
			{
			}
		}

		$this->preprocessData('com_clouderp.customer', $data);

		return $data;
	}
}
