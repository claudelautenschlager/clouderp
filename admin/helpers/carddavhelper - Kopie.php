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
 * Class CarddavHelper
 * see: http://sabre.io/dav/building-a-carddav-client/
 * @since  0.0.1
 */
class CarddavHelper
{
	protected $item;
	protected $path="/vdcb/phcloud/carddav.php/addressbooks/vcard/Aktiven'";
	protected $auth=base64_encode("vcard:1234");
	                
	public function __construct($config = array())
		$item=array(
			"UID"=>'',
			"FN"=>'',
            "NICKNAME"=>'',
            "EMAIL"=>'',
            "CELL"=>'',
            "HOME" =>'',
            "ADR:"=>'',
            "NOTE"=>'',
            "BDAY" =>'',
            "ORG"='',
            "TITLE"=>'',
            "ANNIVERSARY"=>'',
            "IMPP"=>'',
            "N" => array("lastname"=>'', "firstname" =>'', "p1"=>'', "p2"=> '', "p3"=>'')
            "REV"=>'',
			"PHOTO"=>'';
		);
	}
	
	public function load($key){
		$data=$this->buildRequest($key);
	}
	
	public function getCardsBasic(){
		return array(
			"0"=>array("title"=>'1', "inhalt"=>''),
			"2"=>array("title"=>'2', "inhalt"=>'')
		);
	}
	
	public function getCardsFile(){
		$data=$this->buildRequest();
		
		
		$header="cache-control: no-cache\r\n".
             	"content-type: text/plain\r\n".
			    "Depth: 2\r\n".
			    "Content-length: ".strlen($data)."\r\n".
			    "Authorization: Basic " . base64_encode("vcard:1234")."\r\n".
			    "User-Agent: CloudERP/1.0\r\n".
			    "Accept: */*\r\n".
			    "Connection: keep-alive\r\n";
		
		
		$context = stream_context_create(array(
			'http' => array(
			  'method' => 'REPORT',
			  'header' => $header,
			  'content' => $data,
			)
		));

		$response = file_get_contents($url, false, $context);
		
		return array(
				"0"=>array("title"=>'1', "inhalt"=>$response),
				"2"=>array("title"=>'2', "inhalt"=>"cURL Error #:")
				);
	}
	
	public function getCards($payload){
		$curl = curl_init();
		$url = 'http://localhost'.$this->path;
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "REPORT",
		  CURLOPT_POSTFIELDS => $payload,
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: text/plain",
			"Depth: 2",
			"Content-length: ".strlen($payload),
			"Authorization: Basic " . $auth,
			"User-Agent: CloudERP/1.0",
			"Accept: */*",
			"Connection: keep-alive"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			return array(
				"0"=>array("title"=>'1', "inhalt"=>''),
				"2"=>array("title"=>'2', "inhalt"=>"cURL Error #:" . $err)
			);
		} else {
			return array(
				"0"=>array("title"=>'1', "inhalt"=>''),
			    "2"=>array("title"=>'2', "inhalt"=>$this->convertReportData($response))
			);
		}
	}

	protected function convertReportData($data){
	    $data=$this->removeNamespaces($data);
	    $iregndwas= new SimpleXMLElement($data);
	    $attr="address-data";
	    return $iregndwas->response->propstat->prop->$attr;
	}
	
	protected function removeNamespaces($data){
	    $teile=explode('<', $data);
	    unset($teile[0]);
	    $res='<'.$teile[1];
	    unset($teile[1]);
	    foreach($teile as $teil){
	        $part="<";
	        if(substr($teil,0,1)=="/"){
	            $part .="/";
	        }
	        
	        $t1=explode('>', $teil);  // $t1[0] müsste Elementname sein
	        $pos=strpos($t1[0], ':');
	        if($pos>0){
	            $t1[0]=substr($t1[0],$pos+1);
	        }
	        $t1[0] .= '>';
	        foreach($t1 as $p) $part.=$p;
//	        $part.= ">";
	        
	        $res .= $part;
	    }
	    return $res;
	}
	
	protected function removeNamespacesX($data){
	    $data=str_replace('<d:response>', '<response>', $data);
	    $data=str_replace('</d:response>', '</response>', $data);
	    $data=str_replace('<d:href>', '<href>', $data);
	    $data=str_replace('</d:href>', '</href>', $data);
	    $data=str_replace('<d:propstat>', '<propstat>', $data);
	    $data=str_replace('</d:propstat>', '</propstat>', $data);
	    $data=str_replace('<d:prop>', '<prop>', $data);
	    $data=str_replace('</d:prop>', '</prop>', $data);
	    $data=str_replace('<d:getetag>', '<getetag>', $data);
	    $data=str_replace('</d:getetag>', '</getetag>', $data);
	    $data=str_replace('<card:address-data>', '<addressdata>', $data);
	    $data=str_replace('</card:address-data>', '</addressdata>', $data);
	    $data=str_replace('<d:status>', '<status>', $data);
	    $data=str_replace('</d:status>', '</status>', $data);
	    
	    return $data;
	}
	
	protected function prittyPrint($text){
		return str_replace("\r","<br/>",str_replace("\n",'<br/>',$text));
	}

	protected function buildRequest($key){
		return '<card:addressbook-multiget xmlns:d="DAV:" xmlns:card="urn:ietf:params:xml:ns:carddav">
    <d:prop>
        <d:getetag />
        <card:address-data />
    </d:prop>
    <d:href>'.path.'/'.$key.'</d:href>
    </card:addressbook-multiget>';
	}
	
	public function getCardsXX(){
		//http://docs.php.net/manual/da/class.httprequest.php
		$r = new HttpRequest('http://localhost/vdcb/phcloud/carddav.php/addressbooks/vcard/Aktiven', HttpRequest::METH_REPORT);
		$r->setHeaders("Authorization: Basic " . base64_encode("vcard:1234"));
		$r->setRawPostData('<card:addressbook-multiget xmlns:d="DAV:" xmlns:card="urn:ietf:params:xml:ns:carddav">
    <d:prop>
        <d:getetag />
        <card:address-data />
    </d:prop>
    <d:href>/vdcb/phcloud/carddav.php/addressbooks/vcard/Aktiven/Lautenschlager_Claude_064.vcf</d:href>
</card:addressbook-multiget>');

		try {
			$r->send();
			if ($r->getResponseCode() == 200) {
				return array(
					"0"=>array("title"=>'1', "inhalt"=>$r->getResponseBody()),
					"1"=>array("title"=>'2', "inhalt"=>'')
				);
			}
		} catch (HttpException $ex) {
			echo $ex;
		}
		
		
		return array(
			"0"=>array("title"=>'1', "inhalt"=>''),
			"2"=>array("title"=>'2', "inhalt"=>'')
		);
	}
}
