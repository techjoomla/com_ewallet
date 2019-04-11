<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');


class eWalletViewOrders extends JViewLegacy
{

	function display($tpl = null)
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=$mainframe->input;
		$option = $input->get('option');

	/*load language file for plugin frontend*/
$lang =  JFactory::getLanguage();
$lang->load('com_ewallet', JPATH_SITE);

		$model = $this->getModel('Orders');
		$pstatus=array();
		$pstatus[]=JHTML::_('select.option','P', JText::_('COM_EWALLET_PENDIN'));
		$pstatus[]=JHTML::_('select.option','C', JText::_('COM_EWALLET_CONFR'));
		$pstatus[]=JHTML::_('select.option','RF', JText::_('COM_EWALLET_REFUN'));
		$pstatus[]=JHTML::_('select.option','E', JText::_('COM_EWALLET_ERR'));
		$this->pstatus=$pstatus;

		$sstatus = array();
		if(!version_compare(JVERSION, '3.0', 'lt'))
		{ // LESS THAN J3.0
			//$sstatus[] = JHTML::_('select.option','-1',  JText::_('COM_EWALLET_SELONE'));
		}
		$sstatus[] = JHTML::_('select.option','P',  JText::_('COM_EWALLET_PENDIN'));
		$sstatus[] = JHTML::_('select.option','C',  JText::_('COM_EWALLET_CONFR'));
		$sstatus[] = JHTML::_('select.option','RF',  JText::_('COM_EWALLET_REFUN'));
		$sstatus[]=JHTML::_('select.option','E', JText::_('COM_EWALLET_ERR'));
		$this->sstatus=$sstatus;
		$layout		= $input->get( 'layout','' );

		//Added by Sagar For Gateway Filter
			$search_gateway = $mainframe->getUserStateFromRequest( $option.'search_gateway', 'search_gateway','', 'string' );
			$search_gateway = JString::strtolower( $search_gateway );
			$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','desc', 'word' );
			$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order', 'id', 'string' );

		//End Added by Sagar For Gateway Filter
    $filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );



		$search = JString::strtolower( $search );
		if($search==null)
		$search='-1';

		// Get data from the model
		$total =  $this->get( 'Total');
		$pagination =  $this->get( 'Pagination' );
		$orders = $this->get('Orders');
		// search filter
		$lists['search_select']= $search;
		$lists['search_list']= $filter_state;
		$lists['search_gateway']		= $search_gateway;
		$lists['order_Dir'] 				= $filter_order_Dir;
		$lists['order']     				= $filter_type;
		// Get data from the model
		$this->lists=$lists;
		$this->pagination=$pagination;
		$this->orders=$orders;

		// FOR J3.0
		$this->_setToolBar();

		// FOR DISPLAY SIDE FILTER
		if(version_compare(JVERSION, '3.0', 'ge'))
		{
			$this->sidebar = JHtmlSidebar::render();
		}
		parent::display($tpl);

	}//function display ends here

	function _setToolBar()
	{	// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		$input=JFactory::getApplication()->input;
		$layout		= $input->get( 'layout','' );
		if($layout!='order'){
		JToolBarHelper::deleteList('', 'deleteorders');
		//$button = "<a class='toolbar' class='button' type='submit' onclick=\"javascript:document.getElementById('task').value = 'payment_csvexport';document.adminForm.submit();\" href='#'><span title='Export' class='icon-32-save'></span>".JText::_('CSV_EXPORT')."</a>";
		//$bar->appendButton( 'Custom', $button);
		}
		if($layout!='order'){
			JToolBarHelper::back( JText::_('COM_EWALLET_HOME') , 'index.php?option=com_ewallet');
		}

		JToolBarHelper::title( JText::_( 'COM_EWALLET_ORDERS' ), 'icon-48-ewallet.png' );
		//JToolBarHelper::cancel( 'cancel', 'Close' );

		//FILTER FOR J3.0
		if(!version_compare(JVERSION, '3.0', 'lt'))
		{
			//JHtmlSidebar class to render a list view sidebar //setAction::Set value for the action attribute of the filter form
			JHtmlSidebar::setAction('index.php?option=com_ewallet');

			//Method to add a filter to the submenu void addFilter (string $label, string $name, string $options, [bool $noDefault = false])
				JHtmlSidebar::addFilter(JText::_('COM_EWALLET_SELONE'),'search_select',JHtml::_('select.options',
				 $this->sstatus, "value", "text", $this->lists['search_select'],true)	);
		}

	}

}// class
