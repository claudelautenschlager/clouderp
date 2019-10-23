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
 * products Controller
 *
 * @since  0.0.1
 */
class ClouderpControllerProducts extends JControllerAdmin
{
    public function getModel($name = 'Product', $prefix = 'ClouderpModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));
        
        return $model;
    }
}
