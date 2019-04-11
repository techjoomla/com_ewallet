<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');
 
/**
 * HTML View class for the Aniket Component
 */
 
 
class ewalletViewbilling extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $pagination;
	
	function display($tpl = null) 
	{
		//print_r($this);die();
		global $mainframe, $option;
		$mainframe= JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		$this->userid = $this->get('User');
		//$filter_order=$mainframe->getUserStateFromRequest('com_socialads.filter_order','filter_order','','int');
		$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' );
		$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );
		$filter_user = $mainframe->getUserStateFromRequest( $option.'filter_user', 'filter_user','', 'int' );
		//print_r($filter_user);
		$billing = $this->get('billing');
		$this->billing = $billing;
		$lists=array();
		$lists['month']=$month;
		$lists['year']=$year;
		$lists['filter_user']=$filter_user;
		//$this->lists=$lists;
		
		$User= $this->get('User');
		$this->assignRef('User',$User);
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('data');
		$this->form		= $this->get('Form');
		
		
		
		$option = $input->get('option','',"STRING");
		$filter_order_Dir_time = $mainframe->getUserStateFromRequest( "$option.filter_order_Dir_time",'filter_order_Dir',	'desc','word' );
		$filter_type_time = $mainframe->getUserStateFromRequest( "$option.filter_order_time",	'filter_order','time','string' );

		//
		$lists['order_Dir'] 				= $filter_order_Dir_time;
		$lists['order']     				= $filter_type_time;
		$this->lists=$lists;
		
		
		$items = $this->get('Data');   
		//$pagination = $this->get('Pagination');
		
		
		// push data into the template
		$this->assignRef('items', $items);      
		//$this->assignRef('pagination', $pagination);
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

			$this->addToolbar();

		parent::display($tpl);
	}
	protected function addToolbar()
	{
	   //
        //$this->item->id = '';
		$user		= JFactory::getUser();
		//$isNew		= ($this->item->id == 0);
        if (isset($this->item->checked_out)) {
		    $checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $checkedOut = false;
        }
        JLoader::import('ewallet', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_ewallet'.DS.'helpers');
		$canDo		= EwalletHelper::getActions();

		JToolBarHelper::title(JText::_('COM_EWALLET_TITLE_TRANSACTION'), 'billing.png');

		// If not checked out, can save the item.
		if($this->getLayout() == 'edit')
		{
		  JFactory::getApplication()->input->set('hidemainmenu', true);
			if (!$checkedOut && ($canDo->get('core.edit')||($canDo->get('core.create'))))
			{
				JToolBarHelper::apply('billing.apply', 'JTOOLBAR_APPLY');
			}
			
				JToolBarHelper::cancel('billing.cancel', 'JTOOLBAR_CLOSE');
				JToolbarHelper::save('billing.save', 'JTOOLBAR_SAVE');
				JToolBarHelper::back( JText::_('COM_EWALLET_HOME') , 'index.php?option=com_ewallet&view=dashboard');
		}

         	//JToolBarHelper::back( JText::_('COM_QUICK2CART_BACK') , 'onclick="javascript:history.back();');

	}
}

