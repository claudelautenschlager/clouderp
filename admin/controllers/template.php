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
 * Templates Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerTemplate extends JControllerForm
{
	public function preview(){
		$data = JFactory::getApplication()->input->post->get('jform',array(),'array');
		$this->getModel('template')->showTemplate($data);
	}
}
