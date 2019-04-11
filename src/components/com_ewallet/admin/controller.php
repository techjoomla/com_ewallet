<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');

class eWalletController extends JControllerLegacy
{
	public function  __construct()
	{
		parent::__construct();
	}
	public function display($cachable = false, $urlparams = false)
	{
		$jinput = JFactory::getApplication()->input;
		$vName = $jinput->get('view', 'dashboard');
		/*require_once JPATH_COMPONENT.'/helpers/ewallet.php';
		EwalletHelper::addSubmenu($vName);*/
		$orders	=	'';
		$cp = '';
		$dashboard = '';
		$transaction = '';
		$billing ='';
		 $queue	= $jinput->get('layout','');
		if(!$queue)
		{	$layout = 'default';
			switch($vName)
			{
				case 'orders':
					$orders	=	true;
				break;
				case 'dashboard':
					$dashboard = true;
					break;
				/*case 'transaction':
					$transaction = true;
					break;*/
				case 'billing':
					$billing = true;
					break;
			}
		}
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
		$mainframe = JFactory::getApplication();
		$input=$mainframe->input;
		$option = $input->get('option');
		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );
		$lists['search_select']= $search;
		$lists['search_list']= $filter_state;
		
		// Get data from the model
		$this->lists=$lists;

	 JSubMenuHelper::addEntry(JText::_('COM_EWALLET_DASHBOARD'), 'index.php?option=com_ewallet&view=dashboard',$dashboard);
	JSubMenuHelper::addEntry(JText::_('COM_EWALLET_ORDERS'), 'index.php?option=com_ewallet&view=orders',$orders);
	JSubMenuHelper::addEntry(JText::_('COM_EWALLET_TRANSACTION'), 'index.php?option=com_ewallet&view=billing',$billing);
	if($vName == 'orders')
	{
	JSubMenuHelper::addFilter(JText::_('COM_EWALLET_SELONE'),'search_select',JHtml::_('select.options',
				$this->sstatus, "value", "text", $this->lists['search_select'],true)	);
	}
		
		switch ($vName)
		{
			case 'orders':
				$mName = 'orders';
				$vLayout = $jinput->get( 'layout', $layout );
			break;
			case 'dashboard':
				$mName = 'Dashboard';
				$vLayout = $jinput->get( 'layout', 'dashboard' );
			break;
			/*case 'transaction':
				$mName = 'transaction';
				$vLayout = $jinput->get( 'layout', 'edit' );
			break;*/
			case 'billing':
				$mName = 'billing';
				$vLayout = $jinput->get( 'layout', 'default' );
			break;
		}
	
		$document = JFactory::getDocument();
		$vType	  = $document->getType();
		$view = $this->getView( $vName, $vType);
		
		if ($model = $this->getModel($mName)) 
		{
			$view->setModel($model, true);
		}
		
		if($mName=="orders")
		{
			switch($this->getTask())
			{
				case 'view':
				{
					$jinput->set( 'hidemainmenu', 1 );
					$jinput->set( 'layout', 'order'  );
					$jinput->set( 'view', 'orders' );
					$vLayout="order";
				} break;
			}
		}
		
		$view->setLayout($vLayout);
		$view->display();
	}// function

	function getVersion()
	{
		echo $recdata = file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=ewallet');
		jexit();
	}	

	/*function to delete order*/	
	function deleteorders(){
		$model	= $this->getModel( 'Orders' );
		$cid=JFactory::getApplication()->input->post->get('cid','', 'array');
		JArrayHelper::toInteger($cid);
		if ($model->deleteorders($cid)) {
			$msg = JText::_( 'COM_EWALLET_ORDER_DELETED' );
		}
		else{
			$msg = JText::_( 'COM_EWALLET_ERR_ORDER_DELETED' );
		}
		$this->setRedirect( JURI::base()."index.php?option=com_ewallet&view=orders",$msg);
	}
}// class
