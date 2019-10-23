<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 *
 * @since  0.0.1
 */
class ClouderpModelCarddav extends JModelList
{
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	 
	public function getCarddavItems(){
	    try{
	        $this->mailserver="imap.hosting-ch.ch";
	        //	    $this->mailserver="pop.ccdz.ch";
	        //	    $this->mailserver="212.59.186.17";
	        $this->port="995/pop3";
	        $this->port="143/imap";
	        $this->user="verteiler@ccdz.ch";
	        $this->pass="Rink1234!";
	        
	        return $this->getEmailsImap();
    		
	    }catch(Exception $e){
	        return array(
	            "0"=>array("title"=>'1', "inhalt"=>$e->getMessage())
	            );
	    }
	}
	
	protected $mailserver;
	protected $port;
	protected $user;
	protected $pass;
	
	    
	    //open connection to mailbox, read all unread mails
		
	protected function getEmailsImap(){
	    $result=array();
	    $params = array("/ssl", "/novalidate-cert");
	    $target="{" . $this->mailserver . ":" . $this->port . "}INBOX";
	    $imap = imap_open( $target, $this->user, $this->pass,0,0); //, $params );
	    $totalrows = imap_num_msg($imap);
		
		JLoader::register('EmailMessage', ClouderpHelper::getBasePath().'/components/com_clouderp/helpers/emailmessage.php');
		
		$result=array();
		
		for ($index = 0; $index < $totalrows; $index++){
// set to true to get the message parts (or don't set to false, the default is true)
			$emailMessage=new EmailMessage($imap, 1);
			$emailMessage->getAttachments = true;
			$emailMessage->fetch();
			
			array_push($result,["subject" =>  $emailMessage->subject]);
			array_push($result,["sender" =>  $emailMessage->sender]);
			array_push($result,["Body" =>  $emailMessage->bodyHTML]);
			
			$emailMessage->reSend('claude.lautenschlager@abraxas.ch');
			
			imap_delete ($imap, $index+1);

//			print_r($emailMessage);
		}
		imap_close($imap);
		
		
		return $result;
		
	}
	protected function getEmailsImap2(){
	    $result=array();
	    $params = array("/ssl", "/novalidate-cert");
	    $target="{" . $this->mailserver . ":" . $this->port . "}INBOX";
	    $imap = imap_open( $target, $this->user, $this->pass,0,0); //, $params );
        if ($imap)
        {
            echo "Connected\n";
            $check = imap_mailboxmsginfo($imap);
            
			array_push($result,["Date" =>  $check->Date]);
            array_push($result,["Driver"   =>  $check->Driver]);
            array_push($result,["Unread"   =>   $check->Unread]);
            array_push($result,["Size" => $check->Size]);
            
            $totalrows = imap_num_msg($imap);
            //iterate through all unread mails
            for ($index = 0; $index < $totalrows; $index++)
            {
                $header = imap_header($imap, $index + 1);
                array_push($result,["Subject" =>$header->subject]);
                $prettydate = date(DateTime::ISO8601 , $header->udate);
                array_push($result,["Sended at" => $prettydate ]);
                //get email author
                $email = "{$header->from[0]->mailbox}@{$header->from[0]->host}";
                array_push($result,["Sender" => $email ]);;
                
                //get mail body
                $struct=imap_fetchstructure ( $imap, $index + 1);
                $obj_section = $struct;
                $section = "1";
                for ($i = 0 ; $i < 10 ; $i++) {
                    if ($obj_section->type == 0) {
                        break;
                    } else {
                        $obj_section = $obj_section->parts[0];
                        $section.= ($i > 0 ? ".1" : "");
                    }
                }
                
                array_push($result,["Section" => $section]);
                $section="1.1";
                
                $text = imap_fetchbody($imap, $index + 1, $section );
                // Décodage éventuel
                if ($obj_section->encoding == 3) {
                    $text = imap_base64($text);
                } else if ($obj_section->encoding == 4) {
                    $text = imap_qprint($text);
                }
                // Encodage éventuel
                foreach ($obj_section->parameters as $obj_param) {
                    if (($obj_param->attribute == "charset") && (mb_strtoupper($obj_param->value) != "UTF-8")) {
                        $text = utf8_encode($text);
                        break;
                    }
                }
                
                //array_push($result,["Body" => $text]);
                
//                $ar= $this->create_part_array($struct);
                
                //array_push($result,["Body1" => base64_decode(imap_fetchbody ($imap, $index + 1, "1.1"))]);
                 array_push($result,["Body1" => imap_fetchbody ($imap, $index + 1, "1.1.2")]);
                 array_push($result,["Body2" => base64_decode(imap_fetchbody ($imap, $index + 1, "3"))]);
                //array_push($result,["Body2" => imap_fetchbody ($imap, $index + 1, 2)]);
                
                //array_push($result,["Body" => imap_body($imap, $index + 1)]);
                
                 /*
                  * Versuch ein Mail mittels imap zu senden
                 $envelope["from"]= "verteiler@ccdz";
                 $envelope["to"]  = "claude.lautenschlager@abraxas.ch";
                 $body= imap_body($imap, $index + 1);
                 $mail=imap_mail_compose ($envelope , $body );
                 imap_mail ($envelope["to"], 'Veiterleitung' , $mail);
                 
//                 $envelope["cc"]  = "bar@example.com";
*/
                 
                 
            }
            //close connection to mailbox
            imap_close($imap);
            return $result ;
        }
        else
        {
            $this->dump("Can't connect: " . imap_last_error());
            return false;
        }
    }
    
    
    
    
    function create_part_array($structure, $prefix="") {
        //print_r($structure);
        if (sizeof($structure->parts) > 0) {    // There some sub parts
            foreach ($structure->parts as $count => $part) {
                $this->add_part_to_array($part, $prefix.($count+1), $part_array);
            }
        }else{    // Email does not have a seperate mime attachment for text
            $part_array[] = array('part_number' => $prefix.'1', 'part_object' => $obj);
        }
        return $part_array;
    }
    // Sub function for create_part_array(). Only called by create_part_array() and itself.
    function add_part_to_array($obj, $partno, & $part_array) {
        $part_array[] = array('part_number' => $partno, 'part_object' => $obj);
        if ($obj->type == 2) { // Check to see if the part is an attached email message, as in the RFC-822 type
            //print_r($obj);
            if (sizeof($obj->parts) > 0) {    // Check to see if the email has parts
                foreach ($obj->parts as $count => $part) {
                    // Iterate here again to compensate for the broken way that imap_fetchbody() handles attachments
                    if (sizeof($part->parts) > 0) {
                        foreach ($part->parts as $count2 => $part2) {
                            $this->add_part_to_array($part2, $partno.".".($count2+1), $part_array);
                        }
                    }else{    // Attached email does not have a seperate mime attachment for text
                        $part_array[] = array('part_number' => $partno.'.'.($count+1), 'part_object' => $obj);
                    }
                }
            }else{    // Not sure if this is possible
                $part_array[] = array('part_number' => $prefix.'.1', 'part_object' => $obj);
            }
        }else{    // If there are more sub-parts, expand them out.
            if (sizeof($obj->parts) > 0) {
                foreach ($obj->parts as $count => $p) {
                    $this->add_part_to_array($p, $partno.".".($count+1), $part_array);
                }
            }
        }
    }
    
    
    
    protected function dump($var){
        echo "<pre><div align='left'>";
        print_r($var);
        echo "</div></pre>";
    }
	
	
	
	
	protected function getListQuery()
	{
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select("*");
		$query->from('#__0001_cerp_template');

		JFactory::getApplication()->enqueueMessage('ClouderpModelCarddav(/getListQuery sollte nicht aufgerufen werden');
		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   name if column that should be used for order
	 * @param   string  $direction  ordering direction
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Load the filter state.
		$this->setState('filter.search', $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string'));
		// List state information.
		parent::populateState('title', 'asc');
	}
}
