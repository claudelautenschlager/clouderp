<?php
/**
 * @package    CurlingEvent
 * @author     Claude Lautenschlager
 * @copyright  Copyright (C) 2017 - 2018 All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the Curlingevents Component
 *
 * @since  0.0.1
 */
class ClouderpViewAddresslist extends JViewLegacy
{
	/**
	 * Display curlingevent item
	 *
	 * @param   string  $tpl  template name
	 *
	 * @return void
	 */
	protected $items;
	protected $full;
	protected $viewmodus;
	 
	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		$app=\JFactory::getApplication();
		$this->viewmodus=	$app->getUserStateFromRequest('com_clouderp.addresslist.list.showmodus','list_showmodus','card');
		
		$canDo = JHelperContent::getActions('com_clouderp');
		$this->full= $canDo->get('customer.view.full');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new RuntimeException(implode('<br />', $errors), 500);
		}
		JFactory::getDocument()->addStyleSheet('components/com_clouderp/views/addresslist/tmpl/card.css');

		$this->filterForm = $this->get('FilterForm');  // Suche nach Text und Status

		$this->activeFilters = $this->get('ActiveFilters');

//				$('.js-stools-container-filters').removeClass('hidden-phone')  wird nicht so toll umgesetzt		
		JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function($){
				$('.limit').hide();
				$('.js-stools-btn-clear').addClass('hidden-phone');
			});"
		);
		
		
/*		
		//Button um von Card auf List oder umgekehrt umzustellen
		if(count($this->items)<0){
			$nextModus=$this->items[0]->showmodus=="card"?'Anzeige als Liste':'Anzeige als Karte';
			JFactory::getDocument()->addScriptDeclaration("jQuery(document).ready(function($){
					var r= $('<button class=\"btn\" id=\"btshowmodus\">".$nextModus."</button>');
					$(\".js-stools-container-bar\").append(r);
					
					$('#btshowmodus').click(function(){
						var olValue=$('#showmodus').val();
						console.log(olValue);
						$('#showmodus').val(olValue=='card'?'list':'card');
						return false;
					});
					
				});"
			);
		}
*/		
		$this->activeFilters =true;
		parent::display($tpl);
	}
}
