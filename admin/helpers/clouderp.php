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
 * Class SeclinksHelper
 *
 * @since  0.0.1
 */
abstract class ClouderpHelper
{
	public static function addSubmenu($vName = 'clouderp')
	{

		$my = JFactory::getUser();
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_FINANCEBOOK'),
			'index.php?option=com_clouderp&view=financebooks',
			$vName == 'financebooks'
		);
			
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_FACTURAS'),
			'index.php?option=com_clouderp&view=facturas',
			$vName == 'facturas'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_CUSTOMER'),
			'index.php?option=com_clouderp&view=customers',
			$vName == 'customers'
		);
		JHtmlSidebar::addEntry(
				JText::_('COM_CLOUDERP_SUBMENU_KONTOPLAN'),
				'index.php?option=com_clouderp&view=kontoplan',
				$vName == 'kontoplan'
			);
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_TEMPLATES'),
			'index.php?option=com_clouderp&view=templates',
			$vName == 'templates'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_PRODUCTS'),
			'index.php?option=com_clouderp&view=products',
			$vName == 'templates'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_clouderp',
			$vName == 'categories'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_CONFIG'),
			'index.php?option=com_clouderp&view=configuration&layout=edit',
			$vName == 'configuration'
		);
		//Eröffnungsbilanz
		//http://localhost/vdcb/administrator/index.php?option=com_clouderp&view=firstbalance&layout=edit
/*		
		JHtmlSidebar::addEntry(
			'Test Carddav',
			'index.php?option=com_clouderp&view=carddav',
			$vName == 'carddav'
		);
*/		
/*		
		JHtmlSidebar::addEntry(
			JText::_('COM_CLOUDERP_SUBMENU_RULES'),
			'index.php?option=com_curlingevents&view=curlingrules',
			$vName == 'curlingrules'
		);
*/		
	}
	
	public static function getBasePath(){
	    $list=explode('administrator',__DIR__);
	    return $list[0].'administrator';
	}
	
	public static function germanDatetoEnglish($datum){
		$arr=explode('.', $datum);
		if(count($arr)==3 && strlen($arr[2])==4){
			$engDate=$arr[2].'/'.$arr[1].'/'.$arr[0];
//			JFactory::getApplication()->enqueueMessage('birth2:[' .  $this->birth . ']');
			return $engDate;
		}
		return $datum;
	}
	
	public static function dateGerman2English($d){
		$arr=explode('.', $d);
		if(count($arr)==3 && strlen($arr[2])==4){
			$engDate=$arr[2].'/'.$arr[1].'/'.$arr[0];
			return $engDate;
		}
		return $d;
	}
}
