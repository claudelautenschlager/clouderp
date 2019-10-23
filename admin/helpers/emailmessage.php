<?php

class EmailMessage {

	protected $connection;
	protected $messageNumber;
	
	public $bodyHTML = '';
	public $bodyPlain = '';
	public $attachments;
	
	public $getAttachments = true;
	
	public function __construct($connection, $messageNumber) {
	
		$this->connection = $connection;
		$this->messageNumber = $messageNumber;
		
	}

	public function fetch() {
		$structure = @imap_fetchstructure($this->connection, $this->messageNumber);
		if(!$structure) {
			return false;
		}
		else {
			$header = imap_header($this->connection, $this->messageNumber);
			$this->subject=$header->subject;
			$email = "{$header->from[0]->mailbox}@{$header->from[0]->host}";
            $this->sender= $email;
            if(isset($structure->parts)){
			     $this->recurse($structure->parts);
            }
            else{
                $body= @imap_fetchbody($this->connection, $this->messageNumber, "1");
                if($structure->subtype!='PLAIN'){
                    $body=base64_decode($body);
                }
                foreach($structure->parameters as $p){
                    if($p->attribute == 'charset'){
                        $charset=$p->value;
                    }
                }
                
                
                if($structure->subtype=="HTML"){
                    $this->bodyHTML=$body;
                }else{
                    $this->bodyPlain=$body;
                }
                $this->bodyHTMLEncoding= ($charset=='utf-8') ? 1:0;
            }
			return true;
		}
		
	}
	
	
	
	public function recurse($messageParts, $prefix = '', $index = 1, $fullPrefix = true) {

		foreach($messageParts as $part) {
			
			$partNumber = $prefix . $index;
			
			if($part->type == 0) {
				if($part->subtype == 'PLAIN') {
					$this->bodyPlain .= $this->getPart($partNumber, $part->encoding);
					$this->bodyPlainEncoding=$part->encoding;
				}
				else {
					$this->bodyHTML .= $this->getPart($partNumber, $part->encoding);
					$this->bodyHTMLEncoding=$part->encoding;
				}
			}
			elseif($part->type == 2) {
				$msg = new EmailMessage($this->connection, $this->messageNumber);
				$msg->getAttachments = $this->getAttachments;
				$msg->recurse($part->parts, $partNumber.'.', 0, false);
				$this->attachments[] = array(
					'type' => $part->type,
					'subtype' => $part->subtype,
					'filename' => '',
					'data' => $msg,
					'inline' => false,
				);
			}
			elseif(isset($part->parts)) {
				if($fullPrefix) {
					$this->recurse($part->parts, $prefix.$index.'.');
				}
				else {
					$this->recurse($part->parts, $prefix);
				}
			}
			elseif($part->type > 2) {
				if(isset($part->id)) {
					$id = str_replace(array('<', '>'), '', $part->id);
					$this->attachments[$id] = array(
						'type' => $part->type,
						'subtype' => $part->subtype,
						'filename' => $this->getFilenameFromPart($part),
						'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
						'inline' => true,
					);
				}
				else {
					$this->attachments[] = array(
						'type' => $part->type,
						'subtype' => $part->subtype,
						'filename' => $this->getFilenameFromPart($part),
						'data' => $this->getAttachments ? $this->getPart($partNumber, $part->encoding) : '',
						'inline' => false,
					);
				}
			}
			
			$index++;
			
		}
		
	}
	
	function getPart($partNumber, $encoding) {

		$data = imap_fetchbody($this->connection, $this->messageNumber, $partNumber);
		switch($encoding) {
			case 0: return $data; // 7BIT
			case 1: return $data; // 8BIT
			case 2: return $data; // BINARY
			case 3: return base64_decode($data); // BASE64
			case 4: return quoted_printable_decode($data); // QUOTED_PRINTABLE
			case 5: return $data; // OTHER
		}


	}
	
	function getFilenameFromPart($part) {

		$filename = '';

		if($part->ifdparameters) {
			foreach($part->dparameters as $object) {
				if(strtolower($object->attribute) == 'filename') {
					$filename = $object->value;
				}
			}
		}

		if(!$filename && $part->ifparameters) {
			foreach($part->parameters as $object) {
				if(strtolower($object->attribute) == 'name') {
					$filename = $object->value;
				}
			}
		}

		return $filename;

	}
	
	public function reSend($mailConfig, $to, $sender, $sendername){
		try{
			$mailer = JFactory::getMailer();
			
//			$mailer->useSmtp('true', 'smtp.curling-zuerich.ch', 'info@ccdz.ch', '#clCCdz!aktuar!2019', null, '587');
			$mailer->useSmtp('true', $mailConfig['smtp_host'], $mailConfig['smtp_username'], $mailConfig['smtp_password'], null, $mailConfig['smtp_port']);
			
//			$config = JFactory::getConfig();
			$sender = array( $sender, $sendername);
			$mailer->setSender($sender);
			
			$recipient = $to;
			$mailer->addRecipient($recipient);
	//		$mailer->addBcc("webmaster@curling-zuerich.ch");
			
			$mailer->setSubject($this->subject);
			if(isset($this->bodyHTML)){
			    if(isset($this->bodyHTMLEncoding)){
    			    switch($this->bodyHTMLEncoding){
    			        case 4: 
    			            $body   = utf8_encode($this->bodyHTML);
    			            break;
    			        case 1: //z.B. Vaszary
    			        default:
    			            $body   = $this->bodyHTML;
    			            break;
    			            
    			    }
			    }else{
			        $body   = $this->bodyHTML;
			    }
				$mailer->isHtml(true);
			}else{
			    $body   = $this->bodyPlain;
			}
//			$mailer->Encoding = 'base64';
			
			$mailer->setBody($body);
			
			$tmp_path=JFactory::getConfig()->get('tmp_path');
			
            			
			//$fName=$tmp_path . '/body.html' ;
			//file_put_contents($fName, $body);
			
			$toDelete=array();
			if($this->attachments){
    			foreach($this->attachments as $internername => $attachments){
    				$fName=$tmp_path . '/' . $attachments['filename'];
    				file_put_contents($fName, $attachments['data']);
    				/*
    				0	text	TYPETEXT
    				1	multipart	TYPEMULTIPART
    				2	message	TYPEMESSAGE
    				3	application	TYPEAPPLICATION
    				4	audio	TYPEAUDIO
    				5	image	TYPEIMAGE
    				6	video	TYPEVIDEO
    				7	model	TYPEMODEL
    				8	other	TYPEOTHER
    				*/
    				/*
    				application/octet-stream', string $disposition = 'attachment'
    				*/
    				switch($attachments['type']){
    					case 3:
    					case 5:
    					case 6:
    					case 4:
    					default:
    						$attType='application/'.$attachments['subtype'];
    						break;
    				}
    				$inline=$attachments['inline'] ? 'inline':'attachement';
//    				$inline='inline';
    				$fname2= $attachments['inline'] ? $internername: $attachments['filename'];
    				$fname2= $attachments['filename'];
   				    $mailer->addAttachment($fName, $fname2); //, 'base64', $attType, $inline);
    				$toDelete[]=$fName	;
    			}
			}
			$send = $mailer->Send();
			foreach($toDelete as $filename){
			     unlink($filename);
			}
			
			$mailer->ClearAllRecipients();
//			JFactory::getApplication()->enqueueMessage('Result SendMail:' .  $send);
		} catch (Exception $e) {
//			JFactory::getApplication()->enqueueMessage('Error beim versand:' .  $e->getMessage());
			$mailer->ClearAllRecipients();
			throw $e;
		}
		return true;
	}

}