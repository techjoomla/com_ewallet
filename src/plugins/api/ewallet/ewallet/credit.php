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

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once( JPATH_SITE .DS.'components'.DS.'com_ewallet'.DS.'helper.php');

class EwalletApiResourceCredit extends ApiResource
{

	public function post()
	{
		//init variable
		$obj = new stdclass;

		//get user details
		$user = JFactory::getUser($this->plugin->get('user')->id);

		if($user->id)
		{
			$helperobj = new comewalletHelper();
			$user_credit = $helperobj->getUserBalance($user->id);

			if($user_credit > 0)
			$obj->credit = $user_credit;
			else
			{
				$obj->code = 403;
				$obj->code = 'Insufficient credits';
			}

		}
		else
		{
			$obj->code = 403;
			$obj->code = 'Bad request';
		}

		$this->plugin->setResponse($obj);

	}

}
