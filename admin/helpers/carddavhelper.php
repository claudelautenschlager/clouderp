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
	public $card;
	
//	protected $path="/vdcb/phcloud/carddav.php/addressbooks/vcard/Aktiven";
	protected $auth;
	protected $inUse;
	protected $isdirty=true;
	public $currentKey;
	protected $urlList;
	                
	function __construct($config = array()){
	
	    $this->auth=base64_encode($config["user"].":". $config["password"]);
		
	    $this->clean();
		
		$this->inUse=array("XXX999","UID","FN","NICKNAME","EMAIL","CELL", "HOME", "ADR", "NOTE","BDAY","ORG","TITLE","ANNIVERSARY","IMPP","N","REV","PHOTO");
		
		$this->loadURLList();
	}
	
	protected function buldVardItem($id){
	    $res='';
	    switch($id){
	        case "ADR":
	            $res="ADR;TYPE=HOME:;;" .$this->card["ADR"]["STREET"]. ';'. $this->card["ADR"]["TOWN"]. ';;'.$this->card["ADR"]["ZIPCODE"];
	            break;
	        case "N":
	            $res="N:" .$this->card["N"]["LASTNAME"]. ';'. $this->card["N"]["FIRSTNAME"]. ';;;'; 
	            break;
	        case "HOME":
	            $res="TEL;TYPE=HOME:" . $this->card["HOME"];
	            break;
	        case "PHONE":
	            $res="TEL;TYPE=CELL:" . $this->card["CELL"];
	            break;
	        default:
	            if(!empty($this->card[$id])){
	               $res = $id. ":". $this->card[$id];
	            }
	    }
	    return $res ."\r\n";
	}
	
	public function store(){
//	    $this->card["UID"]=$this->currentKey;
		JFactory::getApplication()->enqueueMessage('Carddavspeichern: '. $this->currentKey);
//		return;
	    if($this->isdirty){
    	    $payload="BEGIN:VCARD\r\nVERSION:3.0\r\n";
    	    $payload .= $this->buldVardItem("UID");
    	    $payload .= $this->buldVardItem("FN");
    	    $payload .= $this->buldVardItem("NICKNAME");
    	    $payload .= $this->buldVardItem("EMAIL");
    	    
    	    $payload .= $this->buldVardItem("HOME");
    	    $payload .= $this->buldVardItem("PHONE");
    	    $payload .= $this->buldVardItem("ANNIVERSARY");
    	    $payload .= $this->buldVardItem("ADR");
    	    $payload .= $this->buldVardItem("BDAY");
    	    $payload .= $this->buldVardItem("ORG");
    	    $payload .= $this->buldVardItem("TITLE");
    	    $payload .= $this->buldVardItem("N");
    	    
    	    $payload.= "END:VCARD";
    	    
    	    $curl = $this->setCurlOpt($this->currentKey, "PUT", $payload);
    	    
    	    $response = curl_exec($curl);
    	    
    	    $err = curl_error($curl);
    	    $info=curl_getinfo($curl);
    	    
    	    curl_close($curl);
    	    
    	    if ($err) {
    	        throw new Exception($err);
    	    }
    	    $this->isdirty=false;
	    }
	}
	
	
	public function delete(){
	    $url = $this->currentKey; 
	    
	    $curl = $this->setCurlOpt($url, "DELETE");
	    
	    $response = curl_exec($curl);
	    
	    $err = curl_error($curl);
	    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    
	    curl_close($curl);
	    
	    if ($err) {
	        throw new Exception($err);
	    }
	    
	    $this->clean();
	}
	
	
	public function clean(){
	    $this->card=array("UID"=>'',"FN"=>'',"NICKNAME"=>'',"EMAIL"=>'',"CELL"=>'',"HOME" =>'',
	        "ADR"=>array("STREET"=>'', "TOWN"=>'', "ZIPCODE"=>''),
	        "NOTE"=>'',"BDAY" =>'',"ORG"=>'',"TITLE"=>'',"ANNIVERSARY"=>'',"IMPP"=>'',
	        "N" => array("LASTNAME"=>'', "FIRSTNAME" =>''),
	        "REV"=>'',"PHOTO"=>'');
	    $this->isdirty=true;
	}
	
	
	public function load($key){
	    $url = $key; 
		JFactory::getApplication()->enqueueMessage('Carddav lesen: '. $key);
		$this->isdirty=false;  //alle Werte sid noch Original
		$this->currentKey=$key;
//		return;
		$adr = $this->getCards($url);
		$this->bind($adr);

		$this->isdirty=false;  //alle Werte sid noch Original
		$this->currentKey=$key;
	}
	

	public function set($field, $value){
	    if(array_search($field, $this->inUse)){
	       if(is_array($value)){
	           $res=array_diff($this->card[$field], $value);
	           $isEqual=count($res)==0;
	       }else{
	           $isEqual=($this->card[$field] == $value);	           
	       }
	       if(!$isEqual){
	           $this->card[$field]=$value;
	           $this->isdirty = true;
	       }
	    }
	}
	
	
	protected function bind($adr){
		$Elements=explode("\r\n",$adr);
		
		foreach($Elements as $Element){
		    $split=explode(':',$Element);
		    
		    switch($split[0]){
		      case "TEL;TYPE=CELL":
		        $split[0]="CELL";
		        break;
		      case "TEL;TYPE=HOME":
		          $split[0]="HOME";
		          break;
			case "ADR;TYPE=HOME":
		          $split[0]="ADR";
		          break;
		    }
		    if(array_search($split[0], $this->inUse)){
		        $this->card[$split[0]]=$split[1];
		    }
		}
		//Einige Elemente müssen noch gesplittet werden
		//ADR: Altberg 11;8836 Bennau;;;;;
		$Elements=explode(";",$this->card["ADR"]);
		if(!empty($Elements[0])){
			$this->card["ADR"]=array("STREET"=>$Elements[0], "TOWN"=>$Elements[1], "ZIPCODE"=>'');
		}else{
			$this->card["ADR"]=array("STREET"=>$Elements[2], "TOWN"=>$Elements[3], "ZIPCODE"=>$Elements[5]);
		}
		
		//Benz;Monica;;;
		$Elements=explode(";",$this->card["N"]);
		$this->card["N"]=array("FIRSTNAME"=>$Elements[1], "LASTNAME"=>$Elements[0]);
	}
	
	public function getCards($url){
	    $curl =$this->setCurlOpt($url, "GET");
    
	    $response = curl_exec($curl);
	    $err = curl_error($curl);
	    $info=curl_getinfo($curl);
	    
	    $httpCode = curl_getinfo($curl , CURLINFO_HTTP_CODE);
	    
	    curl_close($curl);
	    
	    if ($err) {
	        throw new Exception($err);
	    }
	    else{
	        return $response;
	    }
	}
	
	protected function setCurlOpt($url, $method, $payload=''){
	    $engine= curl_init();
	    
	    curl_setopt_array($engine, array(
	        CURLOPT_URL => $url,
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_ENCODING => "",
	        CURLOPT_MAXREDIRS => 10,
	        CURLOPT_TIMEOUT => 30,
	        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	        CURLOPT_CUSTOMREQUEST => $method,
	        CURLOPT_FAILONERROR=>true,
	        CURLOPT_POSTFIELDS =>$payload,
	        CURLOPT_HTTPHEADER => array(
	            "cache-control: no-cache",
	            "content-type: text/plain",
	            "Content-length: ".strlen($payload),
	            "Authorization: Basic " . $this->auth,
	            "User-Agent: CloudERP/1.0",
	            "Accept: */*",
	            "Connection: keep-alive"
	        ),
	    ));
	    if(!empty($payload)){
//	        curl_setopt(CURLOPT_POSTFIELDS, $payload);
	    }
	    
	    return $engine;
	}
	
	public function buildUID(){
		$uid=$this->card["N"]["LASTNAME"]. $this->card["N"]["FIRSTNAME"].'_'.time();
		$uid=str_replace(array(' ', '/'), array('',''), $uid);
		
		JFactory::getApplication()->enqueueMessage('buildUID: '. $uid);
		$this->card["UID"]=$uid;
		return $uid;
	}
	
	public function buildURI($addressbookid){
		$key=array_search($addressbookid, array_column($this->urlList, 'id'));
		$this->currentKey= $this->urlList[$key]->url.'/'.$this->card["UID"].'.vcf';
		return $this->currentKey;
	}
	
	public function checkSameAddressbook($idnew, $idold){
		$key1=array_search($idnew, array_column($this->urlList, 'id'));
		$key2=array_search($idold, array_column($this->urlList, 'id'));
		
		return $this->urlList[$key1]->url == $this->urlList[$key2]->url;
	}
	
	public function getTextOfAddressbook($id){
		$key=array_search($id, array_column($this->urlList, 'id'));
		
		return $this->urlList[$key]->usercat;
	}
	
	protected function loadURLList(){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
		        ->from('#__0001_davUserCat d')
		       ->leftjoin('#__categories c on c.id=d.id')
               ->select('d.*, c.title as usercat');
		$db->setQuery($query);
        $this->urlList =$db->loadObjectList();
	}	
	
/*	
	public function getCardsXX($payload){
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
		  CURLOPT_FAILONERROR=>true,
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache",
			"content-type: text/plain",
			"Depth: 2",
			"Content-length: ".strlen($payload),
			"Authorization: Basic " . $this->auth,
			"User-Agent: CloudERP/1.0",
			"Connection: keep-alive"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		$info=curl_getinfo($curl);

		curl_close($curl);

		if ($err) {
			throw new Exception($err);
		}
		else{
			
			$data=$this->removeNamespaces($response);
			$xml= new SimpleXMLElement($data);
			
			//$xml->response->propstat->status müsste 'HTTP/1.1 200 OK' sein
			$attr="address-data";
			return $xml->response->propstat->prop->$attr;	
		}
	}
*/	
	
	
		
	

/*		
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
	
	protected function buildRequest($key){
		return '<card:addressbook-multiget xmlns:d="DAV:" xmlns:card="urn:ietf:params:xml:ns:carddav">
    <d:prop>
        <d:getetag />
        <card:address-data />
    </d:prop>
    <d:href>'.$this->path . '/' .$key .'</d:href>
    </card:addressbook-multiget>';
	}
*/	
	
}
