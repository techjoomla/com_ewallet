<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class eWalletControllerOrders extends eWalletController
{
	function __construct() 	{
		
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add' , 'edit' );
	}

	function cancel() {
		
		$msg = JText::_( 'COM_EWALLET_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_ewallet', $msg );
	}
}
