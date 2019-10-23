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
 * configuration Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerConfiguration extends JControllerForm
{
	public function write(){
		$data = JFactory::getApplication()->input->post->get('jform',array(),'array');
		$this->getModel()->write($data);
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=facturas' , false));
	}
	
	public function cancel($key=null){
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=facturas' , false));
	}
	
	public function testmail(){
		$data = JFactory::getApplication()->input->get('jform',array(),'RAW');
		$this->getModel()->sendMail($data);
		$this->display();
	}
}
