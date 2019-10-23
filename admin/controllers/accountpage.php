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
 * Accountpage Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerAccountpage extends JControllerAdmin
{
	public function showaccountpage(){
		$this->setRedirect(JRoute::_('index.php?option=com_clouderp&view=accountpage' , false));
	}
}
