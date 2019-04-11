<?php

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
		$orders_site ='1';
		$this->orders_site=$orders_site;

		$model = $this->getModel('Orders');
		$pstatus=array();
		$pstatus[]=JHTML::_('select.option','P', JText::_('COM_EWALLET_PENDIN'));
		$pstatus[]=JHTML::_('select.option','C', JText::_('COM_EWALLET_CONFR'));
		$pstatus[]=JHTML::_('select.option','RF', JText::_('COM_EWALLET_REFUN'));
		$pstatus[]=JHTML::_('select.option','E', JText::_('COM_EWALLET_ERR'));
		/*$pstatus[]=JHTML::_('select.option','3','Cancelled');*/
		$this->pstatus=$pstatus;

		$sstatus = array();
		$sstatus[] = JHTML::_('select.option','-1',  JText::_('COM_EWALLET_SELONE'));
		$sstatus[] = JHTML::_('select.option','P',  JText::_('COM_EWALLET_PENDIN'));
		$sstatus[] = JHTML::_('select.option','C',  JText::_('COM_EWALLET_CONFR'));
		$sstatus[] = JHTML::_('select.option','RF',  JText::_('COM_EWALLET_REFUN'));
		$sstatus[]=JHTML::_('select.option','E', JText::_('COM_EWALLET_ERR'));
		$this->sstatus=$sstatus;

		$layout = $input->get( 'layout','' );

		// my orders view is not releated to store (any user can access it)
		$this->storeReleatedView=0;
		$orders=$model->getOrders();
		$this->orders=$orders;
		$pagination = $this->get( 'Pagination' );
		$this->pagination=$pagination;
		
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",'filter_order_Dir','desc','word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_order",'filter_order','id','string' );
		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );
	/*	$search = JString::strtolower( $search );*/
		if($search==null)
			$search='-1';
	
		// search filter
		$lists['search_select']= $search;
		$lists['search_list']= $filter_state;
		// $lists['search_gateway']		= $search_gateway;
		$lists['order_Dir']				= $filter_order_Dir;
		$lists['order']				= $filter_type;
		// Get data from the model
		$this->lists=$lists;
		
		parent::display($tpl);
	}//function display ends here
	
}// class
