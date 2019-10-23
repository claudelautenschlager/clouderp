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
 * Balance Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerBalance extends JControllerAdmin
{
	public function showbalance(){
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=balance' , false));
	}
}
