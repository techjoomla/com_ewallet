<?php
/**
 * @package	API
 * @version 1.5
 * @author 	Brian Edgerton
 * @link 	http://www.edgewebworks.com
 * @copyright Copyright (C) 2011 Edge Web Works, LLC. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.html.html');
jimport('joomla.user.helper');
jimport( 'joomla.application.component.helper' );
jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );

require_once( JPATH_SITE .'/components/com_ewallet/helper.php');

class EwalletApiResourceCharge extends ApiResource
{

	public function post()
	{
		//init variable
		$obj = new stdclass;
		$jinput = JFactory::getApplication()->input;

		//get amount for deduction
		$amount = $jinput->post->get('amount',0,'FLOAT');

		$amount = round($amount,2);

		//get user details
		$user = JFactory::getUser($this->plugin->get('user')->id);

		if($user->id && $amount > 0)
		{
			$helperobj = new comewalletHelper();

			$user_credit = $helperobj->getUserBalance($user->id);
			$order_code = $helperobj->addUserSpent($user->id,$amount,'');

			if($user_credit > $amount)
			{
				$obj->order_code = $order_code;
				$obj->remaining = $user_credit - $amount;
			}
			else
			{
				$obj->code = 403;
				$obj->message = 'Insufficient credits';
			}
		}
		else
		{
			$obj->code = 403;
			$obj->message = 'Bad request';
		}

		$this->plugin->setResponse($obj);

	}

}
