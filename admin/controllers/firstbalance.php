<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Eröffnunsgbilanz Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerFirstbalance extends JControllerForm
{
	public function save(string $key = null, string $urlVar = null){
//		JFactory::getApplication()->enqueueMessage('save of ClouderpControllerGenfactura aufgerufen');
		if(parent::save($key, $urlVar)){
			$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=kontoplan' , false));
		}
	}
	public function cancel(){
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=kontoplan' , false));
	}
}
