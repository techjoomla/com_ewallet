<?php
defined('_JEXEC') or die('Restricted access');
 if(JVERSION>=3.0)
{
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
}
include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
TjAkeebaStrapper::bootstrap();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_ewallet/assets/css/ewallet.css' );
$document->addStyleSheet(JURI::base().'components/com_ewallet/assets/css/ewallet_override.css' );
include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
TjAkeebaStrapper::bootstrap();
$path = dirname(__FILE__).DS.'helper.php';
if(!class_exists('comewalletHelper'))
{
	//require_once $path;
	JLoader::register('comewalletHelper', $path );
	JLoader::load('comewalletHelper');
}
require_once (JPATH_COMPONENT.DS.'controller.php');

// Require specific controller if requested
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}
$classname	= 'eWalletController'.$controller;
$controller = new $classname();

// Perform the Request task
$controller->execute(JRequest::getVar('task'));

// Redirect if set by the controller
$controller->redirect();
