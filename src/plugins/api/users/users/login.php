<?php
/**
 * @package	K2 API plugin
 * @version 1.0
 * @author 	Rafael Corral
 * @link 	http://www.rafaelcorral.com
 * @copyright Copyright (C) 2011 Rafael Corral. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.plugin.plugin');
jimport('joomla.html.html');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.user.helper');
jimport('joomla.user.user');

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
JModelLegacy::addIncludePath(JPATH_SITE.DS.'components'.DS.'com_api'.DS.'models');
require_once JPATH_SITE.DS.'components'.DS.'com_api'.DS.'libraries'.DS.'authentication'.DS.'user.php';
require_once JPATH_SITE.DS.'components'.DS.'com_api'.DS.'libraries'.DS.'authentication'.DS.'login.php';

//require_once JPATH_SITE.DS.'components'.DS.'com_api'.DS.'libraries'.DS.'authentication'.DS.'login.php';
require_once JPATH_SITE.DS.'components'.DS.'com_api'.DS.'models'.DS.'key.php';
require_once JPATH_SITE.DS.'components'.DS.'com_api'.DS.'models'.DS.'keys.php';

class UsersApiResourceLogin extends ApiResource
{
	public function get()
	{
		$this->plugin->setResponse("unsupported method,please use post method");
	}

	public function post()
	{

	   $this->plugin->setResponse($this->keygen());
	}

	function keygen()
	{
		//init variable
		$obj = new stdclass;
		$umodel = new JUser;
		$user = $umodel->getInstance();
		//$user = JFactory::getUser($this->plugin->user->id);

		if(!$user->id)
		{
			$user = JFactory::getUser($this->plugin->get('user')->id);
			/*
			$username = JRequest::getVar('username');
			$db = JFactory::getDBO();
			$query = "SELECT id FROM #__users Where username = '{$username}' ";
			$db->setQuery($query);
			$uid = $db->loadResult();
			$user =& JFactory::getUser( $uid );*/
		}

		$kmodel = new ApiModelKey;
		$model = new ApiModelKeys;
		$key = null;
		$keys_data = $model->getList();
		foreach($keys_data as $val)
		{
		 if($val->user_id == $user->id)
		 {
			$key = $val->hash;
		 }

		}

		//create new key for user
		if($key==null)
		{
			$data = array(
			'user_id' =>$user->id,
			'domain' =>'' ,
			'published' => 1,
			'id' => '',
			'task' => 'save',
			'c' => 'key',
			'ret' => 'index.php?option=com_api&view=keys',
			'option' => 'com_api',
			JSession::getFormToken() => 1
			);

			$result = $kmodel->save($data);
			$key = $result->hash;
			//$userid = $result->user_id;

		}

		/*$obj->userid = $user->id;
		$obj->success = (!empty($key))?1:0;
		$obj->key = (!empty($key))?$key:"invalid user";*/

		if(!empty($key))
		{
			$obj->auth_key = $key;
		}
		else
		{
			$obj->code = 403;
			$obj->message = 'Bad request';
		}

		return( $obj );
	}

}
