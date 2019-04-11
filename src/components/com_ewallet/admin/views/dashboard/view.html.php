<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');


class eWalletViewDashboard extends JViewLegacy
{

	function display($tpl = null)
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$this->_setToolBar();
		
		$model = $this->getModel('Dashboard');		
		
	   	$this->OrdersArray=$model->getOrdersArray();
	    $com_params=JComponentHelper::getParams('com_ewallet');
		$this->currency = $com_params->get('addcurrency');
        $this->ticketSalesLastweek=$model->getTicketSalesLastweek();
        $allincome =  $this->get( 'AllOrderIncome');
        $this->allincome=$allincome;
        $MonthIncome = $this->get( 'MonthIncome');
		$AllMonthName = $this->get( 'Allmonths');
		$this->MonthIncome=$MonthIncome;
		$this->AllMonthName=$AllMonthName;
		$xml=JFactory::getXML(JPATH_COMPONENT.DS.'ewallet.xml');
        $this->currentVersion=(string)$xml->version;
$tot_periodicorderscount = $this->get( 'periodicorderscount');
		$this->tot_periodicorderscount=$tot_periodicorderscount;
		//calling line-graph function
	    $statsforpie= $model->statsforpie();
	    $this->statsforpie = $statsforpie;
	    $this->notShippedDetails=$model->notShippedDetails();
	    
		//print_r($this->statsforpie);
	// j3.0
	
            if(version_compare(JVERSION, '3.0', 'ge'))
		{
			$this->sidebar = JHtmlSidebar::render();
		}
		parent::display($tpl);
		
	}//function display ends here
	
	function _setToolBar()
	{	// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::title( JText::_( 'COM_EWALLET_DASHBOARD' ), 'icon-48-ewallet.png' );
		
		// adding option btn
		JToolBarHelper::preferences( 'com_ewallet' );
	}
	
}// class
