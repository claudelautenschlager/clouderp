<?php
/**
 * @package    CloudERP
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2019 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */

defined('_JEXEC') or die;

/**
 * Installation class to perform additional changes during install/uninstall/update
 *
 * @since  0.0.1
 */
class com_clouderpInstallerScript
{
	/**
	 * Function to perform changes during install
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return  void
	 */
	public function install($parent)
	{
		// Initialize a new category
	    $this->addCategorie('Aktivmitglied');
	    $this->addCategorie('Passivmitglied');
	    $this->addCategorie('Veteranenmitglied');
		$this->addCategorie('Aktivmitglied / Ehrenmitglied');
		$this->addCategorie('Passivmitglied / Ehrenmitglied');
		$this->addCategorie('Junior/Juniorin');
		$this->addCategorie('Jungmitglied');
		this->addCategorie('Kandidat/Kandidatin');
		$parent->getParent()->setRedirectURL('index.php?option=com_clouderp');
	}
	
	private function addCategorie($text){
	    $category = JTable::getInstance('Category');
	    if (!$category->load(array('extension' => 'com_clouderp', 'title' => $text)))
	    {
	        $category->extension = 'com_clouderp';
	        $category->title = $text;
	        $category->description = '';
	        $category->published = 1;
	        $category->access = 1;
	        $category->params = '{"category_layout":"","image":""}';
	        $category->metadata = '{"author":"","robots":""}';
	        $category->metadesc = '';
	        $category->metakey = '';
	        $category->language = '*';
	        $category->checked_out_time = JFactory::getDbo()->getNullDate();
	        $category->version = 1;
	        $category->hits = 0;
	        $category->modified_user_id = 0;
	        $category->checked_out = 0;
	        
	        // Set the location in the tree
	        $category->setLocation(1, 'last-child');
	        
	        // Check to make sure our data is valid
	        if (!$category->check())
	        {
	            JFactory::getApplication()->enqueueMessage($category->getError());
	            
	            return;
	        }
	        
	        // Now store the category
	        if (!$category->store(true))
	        {
	            JFactory::getApplication()->enqueueMessage($category->getError());
	            
	            return;
	        }
	        
	        // Build the path for our category
	        $category->rebuildPath($category->id);
			JFactory::getApplication()->enqueueMessage($category->id . ' wurde angelegt!');
	    } else {
			JFactory::getApplication()->enqueueMessage('Load failed!');
		}
	    
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CLOUDERP_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return void
	 */
	public function update($parent)
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('COM_CLOUDERP_UPDATE_TEXT') . '</p>';
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @param   string                      $type    process type
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return void
	 */
	public function preflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_CLOUDERP_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @param   string                      $type    process type
	 * @param   JInstallerAdapterComponent  $parent  The class calling this method
	 *
	 * @return void
	 */
	public function postflight($type, $parent)
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		echo '<p>' . JText::_('COM_CLOUDERP_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
	}
}
