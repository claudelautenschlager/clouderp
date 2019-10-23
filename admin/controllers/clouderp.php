<?php
/**
 * @package    clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Seclinks Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerPhoto extends JControllerLegacy
{
	public function upload(){
/*		
		JLog::addLogger(
		   array(
				// Sets file name
				'text_file' => 'com_clouderp.log.php'
		   ),
			   // Sets messages of all log levels to be sent to the file
		   JLog::ALL,
			   // The log category/categories which should be recorded in this file
			   // In this case, it's just the one category from our extension, still
			   // we need to put it inside an array
		   array('com_clouderp')
	    );
	    JLog::add('Upload (start)', JLog::DEBUG, 'com_clouderp');
		*/
		$data=json_decode(file_get_contents('php://input'));
		
		$image=$this->getImage($data->data);
		$folder = empty($data->path)? "": "\\".$data->path;
		
		$fileName = JPATH_ROOT. "\\images\\".$data->path.$data->filename;
		
		$result="{\"result\":\"OK\", \"text\":\"Datei ".str_replace("\\",'/', $fileName)." gespeichert!\"}";
		
		try{
			$filename_w = str_replace("/","\\",$fileName);
			$file_w = fopen($filename_w, 'w+');
			fwrite($file_w, $image);
			fclose($file_w);
		} catch (Exception $e) {
			$result="{\"result\":\"ERROR\", \"text\":\"Ein Fehler ist beim Speichern entstanden:".$e->getMessage()."\"}";
		}
		
		header('Content-Type: application/text; charset=utf-8');
		echo $result;
		
		\JFactory::getApplication()->close();
	}
	
	protected function getImage($imgAsBinCode){
		if(substr($imgAsBinCode,0,22)=='data:image/png;base64,'){
			return base64_decode(substr($imgAsBinCode,21));
		}
		if(substr($imgAsBinCode,0,23)=='data:image/jpeg;base64,'){
			return base64_decode(substr($imgAsBinCode,22));
		}
		return "";
	}
}
