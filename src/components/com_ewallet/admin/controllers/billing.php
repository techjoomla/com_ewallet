<?php
/**
 * @version     1.0.0
 * @package     com_ewallet
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Transaction controller class.
 */
class EwalletControllerbilling extends JControllerForm
{

    function __construct() {
		
        $this->view_list = 'billing';
        parent::__construct();
    }
    function cancel()
	{
		$this->setRedirect('index.php?option=com_ewallet&view=billing');
	}
	function store()
	{
		$input = JFactory::getApplication()->input;
		
		JLoader::import('helper', JPATH_SITE.DS.'components'.DS.'com_ewallet');
		$helper = new comewalletHelper();
		if($_POST['jform']['type'] === 'C')
		{
			$comment = 'COM_WALLET_TRANSACTION_ADDED|'.$_POST['jform']['comment'];
		}
		else
		{
			$comment = 'COM_WALLET_TRANSACTION_DEDUCTED|'.$_POST['jform']['comment'];
		}
		$result = $helper->addTransaction($_POST['jform']['user_id'],$_POST['jform']['amount'],$_POST['jform']['type'],$comment);
		//print_r($result);die();
		if($result == '1')
		{
			$this->setRedirect('index.php?option=com_ewallet&view=billing&layout=edit', 'Save Successfully.');
		}
		else
		{
			$this->setRedirect('index.php?option=com_ewallet&view=billing&layout=edit', 'Error in save transaction.');
		}
	}
	function storeclose()
	{
		$input = JFactory::getApplication()->input;
		
		JLoader::import('helper', JPATH_SITE.DS.'components'.DS.'com_ewallet');
		$helper = new comewalletHelper();
		if($_POST['jform']['type'] === 'C')
		{
			$comment = 'COM_WALLET_TRANSACTION_ADDED|'.$_POST['jform']['comment'];
		}
		else
		{
			$comment = 'COM_WALLET_TRANSACTION_DEDUCTED|'.$_POST['jform']['comment'];
		}
		$result = $helper->addTransaction($_POST['jform']['user_id'],$_POST['jform']['amount'],$_POST['jform']['type'],$comment);
		//print_r($result);die();
		if($result == '1')
		{
			$this->setRedirect('index.php?option=com_ewallet&view=billing&layout=billing', 'Save Successfully.');
		}
		else
		{
			$this->setRedirect('index.php?option=com_ewallet&view=billing&layout=edit', 'Error in save transaction.');
		}
	}
}
