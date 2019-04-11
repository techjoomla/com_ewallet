<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class eWalletViewpayment extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
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

