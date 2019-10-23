<?php
/**
 * @package    Clouderp
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2010 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Accountpage View
 *
 * @since  1.0.0
 */
class ClouderpViewAccountpage extends JViewLegacy
{
	protected $items;
	protected $saldolist;

	protected $pagination;

	protected $state;
	
	public $activeFilters;

	/**
	 * Customer view display method
	 *
	 * @param   string  $tpl  templae name
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Get data from the model
		$my = JFactory::getUser();
		$this->items = $this->get('Items');
		if(count($this->items)>0){
			$this->saldolist=$this->get('Saldolist');
		}

		
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new RuntimeException(implode('<br />', $errors), 500);
		}

		// Set the toolbar
		ClouderpHelper::addSubmenu('clouderp');

		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();
		
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
// Damit bleiben die Filter sichtbar. Warum aber 		$this->get('ActiveFilters') == false;
//		$this->activeFilters = true;
/*
		JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function($){
				$('.limit').hide();
				});"
		);
*/
		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		$state = $this->get('State');
		$canDo = JHelperContent::getActions('com_clouderp', 'category', $state->get('filter.category_id'));
		$user  = JFactory::getUser();

		JToolBarHelper::title(JText::_('COM_CLOUDERP_ACCOUNTPAGE_TITLE'), 'clouderp');
	}
}
