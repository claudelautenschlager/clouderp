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
class JFormFieldFinanceaccount extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'financeaccount';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions()
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select("k.id, concat(ko_kontonr,' ', ko_bezeichnung) as title")
		      ->from('#__0001_konto k')
			  ->leftjoin('#__0001_kontorahmen r on r.id=ko_kontorahmen')
//			  ->where('kr_typ in (3,4)');
	;	
		$db->setQuery((string) $query);

		$lst = $db->loadObjectList();

		$options = array();

		if ($lst)
		{
		    foreach ($lst as $item)
			{
			    $options[] = JHtml::_('select.option', $item->id, $item->title);
			}
		}

		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
