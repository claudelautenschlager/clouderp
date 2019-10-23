<?php

/**
 * @package		Ph cloud
 * @copyright	KAINOTOMO PH LTD - All rights reserved.
 * @author		KAINOTOMO PH LTD
 * @link		https://www.kainotomo.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die;

class ClouderpControllerMaildistributing extends JControllerForm {

//Aufruf http://localhost/vdcb/index.php?option=com_clouderp&task=maildistributing.checkMails
//Aufruf https://www.curling-zuerich.ch/index.php?option=com_clouderp&task=maildistributing.checkMails



    public function checkMails($model = null) {
	//JFactory::getApplication()->enqueueMessage('ClouderpControllerFacturas/generatefactura');
		$res['error']=0;
		$res['errortext']='';
		try{
			ini_set('max_execution_time', 300);
			$comParams=JComponentHelper::getParams('com_clouderp');
			$config=array("smtp_host"=>$comParams['smtp_host'], "smtp_port"=>$comParams['smtp_port'], "smtp_username" =>$comParams['smtp_username'], "smtp_password"=>$comParams['smtp_password'], "displayname"=>$comParams['smtp_sendername']);
			$anz=$this->start($config);
			$res['errortext']='Anzahl Mails:'.$anz;
		}catch(Exception $e){
			$res['error']=1;
			$res['errortext']=$e->getMessage();
		}
		header('Content-Type: text/json; charset=utf-8');
		echo json_encode($res);
		\JFactory::getApplication()->close();
    }
	
	protected function start($config){
		$debug=true;
		//throw new Exception( $this->getBasePath());
		JLoader::register('ClouderpHelper', $this->getBasePath() . 'administrator/components/com_clouderp/helpers/clouderp.php');
		JLoader::register('SendfacturaHelper', $this->getBasePath() . 'administrator/components/com_clouderp/helpers/sendfacturahelper.php');
		JLoader::register('EmailMessage', $this->getBasePath() . 'administrator/components/com_clouderp/helpers/emailmessage.php');
		
	    $params = array("/ssl", "/novalidate-cert");
		$distributionList=$this->getDistributionlist();  //alle Verteilerliste als eMail-Postfach
		$counMails=0;
		$doBreakAfterFirstMail=false;
		foreach($distributionList as $distribution){
			$target="{" . $distribution->smtp_host . ":" . $distribution->smtp_port . "}INBOX";
			$imap = imap_open( $target, $distribution->smtp_username, $distribution->smtp_password,0,0); //, $params );
			if (!$imap){
				throw new Exception("Cannot open connection ;".$target. "with user:".$distribution->smtp_username);
			}
			$totalrows = imap_num_msg($imap);
		
			for ($index = 0; $index < $totalrows; $index++){
// set to true to get the message parts (or don't set to false, the default is true)
				$Answer="<html><body><h2>Dein Mail wurde an folgende Emailadressen versendet:</h2><br><table><thead><tr><td>Name</td><td>E-Mail</td></tr></thead><tbody>";
				$emailMessage=new EmailMessage($imap, $index+1);
				$emailMessage->getAttachments = true;
				$emailMessage->fetch();
				
				$senderWhitelist=$this->getSender($emailMessage->sender);
				if(empty($senderWhitelist)){
					throw new Exception('Sender ist nicht in der Whitelist:'. $emailMessage->sender);
				}
				else{
				    $emailMessage->sender=$senderWhitelist->forwardsender;
				    if(!empty($distribution->selUserId)){
				        $option=['userid'=>$senderWhitelist->myselfuserid];
					}else{
						$option=['verteilliste'=>$distribution->id];
					}
					$personen= $this->getRecipientlist($option);  //alle Customer, die diese Verteilliste aboniert haben
					if(!empty($emailMessage->bodyHTML)){
					   $body=$emailMessage->bodyHTML;
					}else{
					    $body=$emailMessage->bodyPlain;
					}
					foreach($personen as $person){
						if(!empty($person->email)){
							$emailMessage->bodyHTML= SendfacturaHelper::generateMailDetailParse($body, $person);
							$to=$person->email;
							if($debug){
							    $to= 'claude.lautenschlager@abraxas.ch';
							    $to= 'info@ccdz.ch';
							}
							$emailMessage->reSend($config, $to, $senderWhitelist->forwardsender, $senderWhitelist->forwardsendername);
							if(!$debug){
							    // Pro Druchlauf soll nur ein Mail abgearbeitet werden, danach warten wir auf den nächsten Job
							    //Im Debugmode werden die Mails nicht gelöscht, was dazu führen würde, dass immer das gleiche Mail verarbeitet werden würde
							     $doBreakAfterFirstMail=true;
							}
							$Answer.= ("<tr><td>".$person->lastname." ".$person->firstname."</td><td>".$person->email."</td></tr>");
							$counMails++;
							if($debug){
    							break;  //brauche nicht allen zu schicken
							}
						}
					}
				}
				$Answer .="</tbody></table></body></html>";
				$to=$emailMessage->sender;
				if($debug){
					$to='info@ccdz.ch';
				}
				$this->sendConfirmation($config, $to, $emailMessage->subject, $Answer);
				if(!$debug){
				    imap_delete ($imap, $index+1);
				}
				if(!$debug){
				    break;  // Ein Mail pro Aufruf
				}
			}
			imap_expunge($imap);  // LÃ¶schen der Mails
			imap_close($imap);
			if($doBreakAfterFirstMail)  
				break;  
		
		}
		return $counMails;
	}
	
	
	protected function sendConfirmation($mailConfig, $to, $subject, $body){
		try{
			$mailer = JFactory::getMailer();
			
			$mailer->useSmtp('true', $mailConfig['smtp_host'], $mailConfig['smtp_username'], $mailConfig['smtp_password'], null, $mailConfig['smtp_port']);
			$mailer->setSender(array($mailConfig['smtp_username'],$mailConfig['displayname']));
			
			$mailer->addRecipient($to);
	//		$mailer->addBcc("webmaster@curling-zuerich.ch");
	
			$currentCoding= mb_internal_encoding();
			mb_internal_encoding('UTF-8');
			$subject = str_replace("_"," ", mb_decode_mimeheader($subject));
			mb_internal_encoding($currentCoding);
			
			$mailer->setSubject(utf8_encode('Versandbestätigung zu: ').$subject);

			$body   = $body;
			
			$mailer->isHtml(true);
//			$mailer->Encoding = 'base64';
			
			$mailer->setBody($body);
			
			$send = $mailer->Send();
			
		
			$mailer->ClearAllRecipients();
//			JFactory::getApplication()->enqueueMessage('Result SendMail:' .  $send);
		} catch (Exception $e) {
//			JFactory::getApplication()->enqueueMessage('Error beim versand:' .  $e->getMessage());
			$mailer->ClearAllRecipients();
			throw $e;
		}
		return true;
	}
	
	protected function getDistributionlist(){
		$db = JFactory::getDBO();
		
		$query = $db->getQuery(true)
		         ->from('#__0001_cerp_maildistributer')
				 ->select("*");
		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	protected function getSender($sender){
	    $db = JFactory::getDBO();
	    
	    $query = $db->getQuery(true)
	    ->from('#__0001_cerp_mailwhitelist')
	    ->select("*")
	    ->where("`sender`='".$sender."'");
	    $db->setQuery($query);
	    
	    return $db->loadObject();
	}
	
	protected function getRecipientlist($config){
		$db = JFactory::getDBO();
		//select * from vdcb_0001_cerp_customers where FIND_IN_SET ('1',maildistribution )
		$query = $db->getQuery(true)
		         ->from('#__0001_cerp_customers')
				 ->select("*");
		if(isset($config['verteilliste'])){
			$query->where("FIND_IN_SET ('".$config['verteilliste']."',maildistribution )");
		}
		if(isset($config['userid'])){
			$query->where("`joomlauserid`='".$config['userid']."'");
		}

		$db->setQuery($query);
		
		return $db->loadObjectList();
	}
	
	
	protected  function getBasePath(){
	    $list=explode('components',__DIR__);
	    return $list[0];
	}	
	
}