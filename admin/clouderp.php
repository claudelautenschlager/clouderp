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
JLoader::register('SendfacturaHelper', dirname(__FILE__) . '/helpers/sendfacturahelper.php');
JLoader::register('ClouderpBookingHelper', dirname(__FILE__) . '/helpers/clouderpbookinghelper.php');

const FACTURA_STATE_START='1';
const FACTURA_STATE_READY='2';
const FACTURA_STATE_SENT='3';
const FACTURA_STATE_REMINDED='4';
const FACTURA_STATE_PAID='5';
const FACTURA_STATE_FINISHED='9';


// Import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Clouderp
$controller = JControllerLegacy::getInstance('Clouderp');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task', '', 'CMD'));

// Redirect if set by the controller
$controller->redirect();
