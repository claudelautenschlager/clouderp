<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Curlingevent Form Field class for the Curlingevents component
 *
 * @since  0.0.1
 */
class JFormFieldDateprintformat extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'Dateprintformat';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
		$options = array();

		$options[] = JHtml::_('select.option', '%d.%m.%Y', '23.09.2018');
		$options[] = JHtml::_('select.option', '%d. %b. %Y', '23. Sept. 2018');
		$options[] = JHtml::_('select.option', '%d. %B %Y', '23. September 2018');
		$options[] = JHtml::_('select.option', 'Ende %B %Y', 'Ende September 2018');

		

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
