<?php
/**
 * @package    Clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_clouderp'))
{
	new RuntimeException(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

// Require helper file
JLoader::register('ClouderpHelper', dirname(__FILE__) . '/helpers/clouderp.php');

// Import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by seclink
$controller = JControllerLegacy::getInstance('Clouderp');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task', '', 'CMD'));

// Redirect if set by the controller
$controller->redirect();
