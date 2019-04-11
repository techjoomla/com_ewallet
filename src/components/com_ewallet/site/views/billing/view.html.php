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
	function display($tpl = null) 
	{
		$mainframe= JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		//$post=$input->post;
		$option = $input->get('option','','STRING');
		//$filter_order=$mainframe->getUserStateFromRequest('com_socialads.filter_order','filter_order','','int');
		$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' );
		
		$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );
		
		$billing = $this->get('billing');
		$this->billing = $billing;

		$lists['month']=$month;
		$lists['year']=$year;
		$this->lists=$lists;
		$params = JComponentHelper::getParams( 'com_ewallet' );
		//getting GETWAYS
		if(  !is_array($params->get( 'gateways' )) ){ //$params->get( 'gateways' ) = array('0' => 'paypal','1'=>'Payu');
			$gateway_param[] = $params->get( 'gateways' );
		}
		else{
			$gateway_param = $params->get( 'gateways' );
		}
		if(!empty($gateway_param)){
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('payment');
			$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
		}
		$this->gateways = $gateways;

		parent::display($tpl);
	}
}

