<?php
/**
 * @package    clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;

/**
 * Class SendfacturaHelper
 *
 * @since  0.0.1
 */
//throw new Exception (__FILE__);
require_once dirname(__FILE__).'/../3rdparty/twig/vendor/autoload.php';
//require_once './components/com_clouderp/3rdparty/twig/vendor/autoload.php';
//require_once dirname(__FILE__).'/../vendor/autoload.php';
//require_once './components/com_clouderp/3rdparty/html2pdf/vendor/autoload.php';
require_once dirname(__FILE__).'/../3rdparty/html2pdf/vendor/autoload.php';


use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;

abstract class SendfacturaHelper
{
	 public static function generateFacturaDetailsAttachment($template, $factura){
		
		$template= self::generateFacturaDetailParse($template,$factura);
		
		try {
			$html2pdf = new Html2Pdf('P', 'A4', 'fr');
			$html2pdf->setDefaultFont('Arial');
			$html2pdf->writeHTML($template);
			$titel=self::sanitize($factura->lastname.$factura->firstname) ;
			$filename= 'C:\\temp\\'.$titel.'.pdf';
			//$pdf= $html2pdf->output($filename, 'F');
			$pdf= $html2pdf->output($filename, 'S');
			return $pdf;
		} catch (Html2PdfException $e) {
			$html2pdf->clean();
			$formatter = new ExceptionFormatter($e);
			throw new Exception($formatter->getHtmlMessage());
		}

	}
	
	protected static function sanitize($string, $force_lowercase = true, $anal = false) {
		$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
					   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
					   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
		$clean = trim(str_replace($strip, "", strip_tags($string)));
		$clean = preg_replace('/\s+/', "-", $clean);
		$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
		return ($force_lowercase) ?
			(function_exists('mb_strtolower')) ?
				mb_strtolower($clean, 'UTF-8') :
				strtolower($clean) :
			$clean;
	}
	
	public static function generateFacturaDetailParse($template,$factura){
		$loader = new \Twig\Loader\ArrayLoader([
			'index' => $template,
		]);
		$twig = new \Twig\Environment($loader);
//			echo $template;
				
		$fak_name= empty($factura->fak_name)? $factura->firstname.' '.$factura->lastname: $factura->fak_name;
		$fak_address=empty($factura->fak_address)? $factura->address: $factura->fak_address;
		$fak_zipcode=empty($factura->fak_zipcode) ? $factura->zipcode: $factura->fak_zipcode;
		$fak_town = empty($factura->fak_town) ?$factura->town :$factura->fak_town;
		
		//JFactory::getApplication()->enqueueMessage('f1:'.$factura->fak_name);
		//JFactory::getApplication()->enqueueMessage('f2:'.$fak_name);
		
		
		$res= $twig->render('index', array('anrede'=>$factura->sayhello,
										 'vorname' => $factura->firstname, 
										 'nachname' =>$factura->lastname, 
										 'adrname' => $fak_name,
										 'strasse'=>$fak_address, 
										 'plz'=>$fak_zipcode, 
										 'ort'=>$fak_town, 
										 'fakturadatum'=>self::formatDate($factura->fakturadatum, $factura->fakturadatumformat), 
										 'zahlungsfrist'=>self::formatDate($factura->zahlungsfrist, $factura->zahlungsfristformat), 
										 'telefon'=>$factura->phone, 
										 'natel'=>$factura->mobil, 
										 'parameter1'=>$factura->param1,
										 'parameter2'=>$factura->param2,
										 'parameter3'=>$factura->param3,
										 'parameter4'=>$factura->param4,
										 'rechnungstitel' => $factura->rechnungstitel,
										 'email' => $factura->email,
										 'betrag' => number_format($factura->betrag,2),
										 'status' => $factura->category,
										 'produkt' => $factura->prodtitle, 
										 'produktbeschreibung' => $factura->prod_desc,
										 'mahnstufe' => $factura->remindlevel,
										 'mahngebuehr' =>number_format($factura->remindcost,2),
										 'totalbetrag' =>number_format($factura->remindcost + $factura->betrag,2)
										 )
		);
		return $res;
	}
	
	public static function generateMailDetailParse($template,$person){
		$loader = new \Twig\Loader\ArrayLoader([
			'index' => $template,
		]);
		$twig = new \Twig\Environment($loader);
//			echo $template;
//		JFactory::getApplication()->enqueueMessage('Error hier2?');
		$res= $twig->render('index', array('anrede'=>$person->sayhello,
										 'vorname' => $person->firstname, 
										 'nachname' =>$person->lastname, 
										 'strasse'=>$person->address, 
										 'plz'=>$person->zipcode, 
										 'ort'=>$person->town, 
										 'telefon'=>$person->phone, 
										 'natel'=>$person->mobil, 
										 'email' => $person->email
										 )
		);
		return $res;
	}

	protected static function formatDate($fdate, $format){
		$fd = new DateTime($fdate);
		setlocale(LC_ALL, "german");
		$fd_time=strtotime($fdate);
//https://www.php.net/manual/de/function.strftime.php
		$result = strftime($format, $fd_time);   //31. Oktober 2019
		setlocale(LC_ALL, "en-US");			
		return $result;
	}
	
	public static function getConfig(){
		$res=false;
		$userid=JFactory::getUser()->id; // https://docs.joomla.org/Accessing_the_current_user_object
		$db = JFactory::getDbo();
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
	
	public static function sendMail($factura){
		try{
			$config=SendfacturaHelper::getConfig();		
			$mailer = JFactory::getMailer();
			
			$mailer->useSmtp('true', $config->smtp_host, $config->smtp_username, $config->smtp_password, null, $config->smtp_port);
			
//			$config = JFactory::getConfig();
			$sender = array( 
				$config->smtp_username,
				$config->smtp_sendername 
			);
			$mailer->setSender($sender);
			
			$recipient = $factura->email;
			$mailer->addRecipient($recipient);
			$mailer->addBcc("webmaster@curling-zuerich.ch");
			
			$mailer->setSubject($factura->email_subject);

			$body   = $factura->email_body;
			
			$mailer->isHtml(true);
			$mailer->Encoding = 'base64';
			
			$mailer->setBody($body);
			
			$pdf=base64_decode($factura->email_attachment);		
			$filename=self::sanitize($factura->lastname.$factura->firstname).'.pdf' ;
			$tmp_path=JFactory::getConfig()->get('tmp_path');
			file_put_contents($tmp_path . '/' . $filename, $pdf);
			$mailer->addAttachment($tmp_path . '/' . $filename);
			
			$send = $mailer->Send();
			
			unlink($tmp_path . '/' . $filename);
			
		
			$mailer->ClearAllRecipients();
		} catch (Exception $e) {
//			JFactory::getApplication()->enqueueMessage('Error beim versand:' .  $e->getMessage());
			$mailer->ClearAllRecipients();
			throw $e;
		}
		return true;
	}
	
	public static function loadTemplate($id){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
					 ->from('#__0001_cerp_template t')
					 ->select("template")
					 ->where("`id`=".$id);
		$db->setQuery($query);
		
		$template=($db->loadObject())->template;
		$template="<page>".$template."</page>";
		
		return $template;
	}
}
