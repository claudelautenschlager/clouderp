<?php
/**
 * @package    Joomlaphoto
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Joomlaphoto View
 *
 * @since  1.0.0
 */
class ClouderpViewClouderp extends JViewLegacy
{
	protected $items;
	protected $state;
	
	public function display($tpl = null)
	{
		$this->state = $this->get('State');
		
		ClouderpHelper::addSubmenu('clouderp');
		
		$this->addToolBar();
		$this->sidebar = JHtmlSidebar::render();


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

		JToolBarHelper::title(JText::_('COM_CLOUDERP'));

		$user	= JFactory::getUser();
//		$canDo = JHelperContent::getActions('com_clouderp', 'category', $this->item->catid);
/*
		// If not checked out, can save the item.
		if ($canDo->get('core.edit')||(count($user->getAuthorisedCategories('com_clouderp', 'core.edit'))))
		{
			JToolbarHelper::apply('joomlaphoto.apply');
			JToolbarHelper::save('joomlaphoto.save');
		}
*/
//      JHtmlSidebar::setAction('index.php?option=com_clouderp&view=joomlaphoto');
		
	}
}
