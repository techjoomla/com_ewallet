<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
require_once( JPATH_COMPONENT.DS.'controller.php' );

jimport('joomla.application.component.controller');

class eWalletControllerpayment extends eWalletController
{
	function makepayment()
	{
		$data['success_msg'] = JText::_( 'COM_EWALLET_INVAILD_TOK' );
		$data['success'] = 0;
		JSession::checkToken() or jexit(json_encode($data));

		$mod = $this->getModel('payment');
		$orderid = $mod->makeOrder();

		if($orderid && $orderid!=-1)
		{
			$this->setOrderINsession($orderid);
			$data['success_msg'] =JText::_( 'COM_EWALLET_CONFIG_SAV' );
			$data['success'] = 1;
			$data['order_id'] =$orderid;
			$html = $this->getHTML();
			$data['orderHTML'] = $html;
		}else{
			$msg = JText::_( 'COM_EWALLET_ERR_CONFIG_SAV' );
			$data['success_msg'] = $msg;
			$data['success'] = 0;
		}
		echo json_encode($data);
		jexit();
	}//make payment ends here...

	function setOrderINsession($orderid){
		$session = JFactory::getSession();
		$session->clear('order_id');
		$session->set('order_id',$orderid);
	}
	function getHTML() {
		$model= $this->getModel( 'payment');
		$jinput=JFactory::getApplication()->input;
		$pg_plugin = $jinput->get('processor');
		$session =JFactory::getSession();
		$order_id = $session->get('order_id');
		$html=$model->getHTML($pg_plugin,$order_id);
		if(!empty($html[0]))
			return $html[0];

		return '';
	}
	function confirmpayment(){
		$model= $this->getModel( 'payment');
		$session =JFactory::getSession();
		$jinput=JFactory::getApplication()->input;
		$order_id = $session->get('order_id');
		$pg_plugin = $jinput->get('processor');

		$response=$model->confirmpayment($pg_plugin,$order_id);
	}
	function processpayment()
	{
		$mainframe=JFactory::getApplication();
		$jinput=JFactory::getApplication()->input;
		$session =JFactory::getSession();
		if($session->has('payment_submitpost')){
			$post = $session->get('payment_submitpost');
			$session->clear('payment_submitpost');
		}
		else
			$post = JRequest::get('post');
		$pg_plugin = $jinput->get('processor');
		$model= $this->getModel('payment');
		$order_id = $jinput->get('order_id','','STRING');

		if(empty($post) || empty($pg_plugin) ){
			JFactory::getApplication()->enqueueMessage(JText::_('COM_EWALLET_SOME_ERROR_OCCURRED'), 'error');
			return;
		}
		$response=$model->processpayment($post,$pg_plugin,$order_id);
		$mainframe->redirect($response['return'],$response['msg']);
	}
}
