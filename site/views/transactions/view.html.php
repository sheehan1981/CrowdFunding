<?php
/**
 * @package      CrowdFunding
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CrowdFundingViewTransactions extends JViewLegacy {
    
	protected $state;
	protected $items;
	protected $params;
	protected $pagination;
	
    protected $option;
    
    public function __construct($config) {
        parent::__construct($config);
        $this->option = JFactory::getApplication()->input->get("option");
    }
    
    public function display($tpl = null) {
        
        // Initialise variables
        $this->items      = $this->get('Items');
		$this->state	  = $this->get('State');
		$this->params	  = $this->state->get('params');
		$this->pagination = $this->get('Pagination');
        
		if(!empty($this->items)) {
		    jimport("crowdfunding.currency");
            $currencyId        = $this->params->get("project_currency");
    		$this->currency    = CrowdFundingCurrency::getInstance(JFactory::getDbo(), $currencyId);
		}
		
		// Prepare filters
        $this->listOrder  = $this->escape($this->state->get('list.ordering'));
        $this->listDirn   = $this->escape($this->state->get('list.direction'));
        $this->saveOrder  = (strcmp($this->listOrder, 'a.ordering') != 0 ) ? false : true;
        
        $this->userId     = JFactory::getUser()->id;
        
        // Helpers
		JLoader::register('JHtmlString', JPATH_LIBRARIES.'/joomla/html/html/string.php');
		
		$this->version    = new CrowdFundingVersion();
		
        $this->prepareDocument();
                
        parent::display($tpl);
    }
    
    /**
     * Prepare document
     */
    protected function prepareDocument(){
        
        $app       = JFactory::getApplication();
        
        //Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
        
        // Prepare page heading
        $this->prepearePageHeading();
        
        // Prepare page heading
        $this->prepearePageTitle();
        
        // Meta Description
        if($this->params->get('menu-meta_description')){
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }
        
        // Meta keywords
        if($this->params->get('menu-meta_keywords')){
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }
        
        if ($this->params->get('robots')){
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
		
        // Styles
        $this->document->addStyleSheet('media/'.$this->option.'/css/site/style.css');
        
        // Scripts
        JHtml::_('bootstrap.tooltip');
    }
    
    private function prepearePageHeading() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
        // Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menus    = $app->getMenu();
		$menu     = $menus->getActive();
		
		// Prepare page heading
        if($menu){
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }else{
            $this->params->def('page_heading', JText::_('COM_CROWDFUNDING_TRANSACTIONS_DEFAULT_PAGE_TITLE'));
        }
		
    }
    
    private function prepearePageTitle() {
        
        $app      = JFactory::getApplication();
        /** @var $app JSite **/
        
		// Prepare page title
        $title    = $this->params->get('page_title', '');
        
        // Add title before or after Site Name
        if(!$title){
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		} elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		
        $this->document->setTitle($title);
		
    }
}