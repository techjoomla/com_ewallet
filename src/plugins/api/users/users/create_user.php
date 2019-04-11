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

require_once( JPATH_ADMINISTRATOR .DS.'components'.DS.'com_users'.DS.'models'.DS.'users.php');
require_once( JPATH_SITE .DS.'components'.DS.'com_api'.DS.'libraries'.DS.'resource.php');
require_once( JPATH_SITE .DS.'libraries'.DS.'joomla'.DS.'filesystem'.DS.'folder.php');

class UsersApiResourceCreate_user extends ApiResource
{
	public function get()
	{
		$this->plugin->setResponse($this->error_obj());
	}
	public function post()
	{
		$error_messages = array();
		$fieldname		= array();
		$response       = NULL;
		$validated      = true;
		$userid = NULL;
		$app = JFactory::getApplication();

		$data['name'] = $app->input->post->get('name','','string');
		$data['username']= $data['email'] = $app->input->post->get('username','','string');
		$data['password'] = $app->input->post->get('password','','string');

		//chk data
		if($data['username']=="" )
		{
			throw new Exception("Username cannot be blank", 403);
		} elseif( !filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
			throw new Exception("Username is invalid", 403);
		}

		//com_user settings
		$usersConfig = JComponentHelper::getParams( 'com_users' );

		//for rest api only
		unset($data['format']);
		unset($data['resource']);
		unset($data['app']);
		unset($data['key']);
		//
		if( true == $validated)
		{
			//to create new user for joomla
			$this->registerUser($data['name'], $data['username'], $data['email'], $data['password']);
		}
		else
		{
			$this->plugin->setResponse($this->error_obj());
		}
	}

	public function put()
	{
		$this->plugin->setResponse($this->error_obj());
	}

	public function delete()
	{
		$this->plugin->setResponse($this->error_obj());
    }

    public function registerUser($name, $username, $email, $password)
    {
		//init variable
		$obj = new stdclass;
		$mainframe = JFactory::getApplication('site');
        $mainframe->initialise();
        $user = clone(JFactory::getUser());
        $pathway =  $mainframe->getPathway();
        $config =  JFactory::getConfig();
        $authorize =  JFactory::getACL();
        $document =  JFactory::getDocument();

        $response = array();
        $usersConfig = JComponentHelper::getParams( 'com_users' );

        if($usersConfig->get('allowUserRegistration') == '1')
        {
            // Initialize new usertype setting
            jimport('joomla.user.user');
            jimport('joomla.application.component.helper');

            $useractivation = $usersConfig->get('useractivation');

            $db = JFactory::getDBO();
            // Default group, 2=registered
            $defaultUserGroup = 2;

            $acl = JFactory::getACL();

            jimport('joomla.user.helper');
            $salt     = JUserHelper::genRandomPassword(32);

            $crypted  = JUserHelper::getCryptedPassword($password, $salt);
            $password = $crypted.':'.$salt;
            $instance = JUser::getInstance();
            $instance->set('id'         , 0);
            $instance->set('name'           , $name);
            $instance->set('username'       , $username);
            $instance->set('password' , $password);
            $instance->set('password_clear' , $password);
            $instance->set('email'          , $email);
            $instance->set('usertype'       , 'deprecated');
            $instance->set('groups'     , array($defaultUserGroup));

            // Email with activation link
            if($useractivation == 1)
            {
				//old code
                //$instance->set('block'    , 1);
                //$instance->set('activation'    , JApplication::getHash(JUserHelper::genRandomPassword()));

                $instance->set('block',0);
                $instance->set('activation',0);
            }

            if (!$instance->save())
            {
			 	$this->plugin->setResponse($this->error_obj());
			}
            else
            {

                $db->setQuery("update #__users set email='$email' where username='$username'");
                $db->query();

                $db->setQuery("SELECT id FROM #__users WHERE email='$email'");
                $db->query();
                $newUserID = $db->loadResult();

                $user = JFactory::getUser($newUserID);

                // Everything OK!
                if ($user->id != 0)
                {
                    $obj->user_id =  $user->id;
                    //set plugin responce
                    $this->plugin->setResponse($obj);
                }
                else
                {
					return $this->error_obj();
				}
            }

        }
        else
        {
			return $this->error_obj();
        }
    }

    public function error_obj()
    {
		$obj = new stdclass;
		$obj->code = 403;
		$obj->message = 'Bad request';

		return $obj;
	}

	function isValidEmail( $email )
	{
		$pattern = "/([\w\-]+\@[\w\-]+\.[\w\-]+)/";

    	if (  preg_match( $pattern, $email ) )
    	 {
			return true;
		  } else {
        return false;
      }
	}

}
