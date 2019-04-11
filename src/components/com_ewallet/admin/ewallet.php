<?php


defined( '_JEXEC' ) or die( ';)' );
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once( JPATH_COMPONENT.DS.'controller.php' );

$path = JPATH_SITE.DS.'components'.DS.'com_ewallet'.DS.'helper.php';
if(!class_exists('comewalletHelper'))
{
	JLoader::register('comewalletHelper', $path );
	JLoader::load('comewalletHelper');
}

	include_once JPATH_ROOT.'/media/techjoomla_strapper/strapper.php';
	TjAkeebaStrapper::bootstrap();
if( $controller = JRequest::getWord('controller'))
{
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if( file_exists($path))
		require_once $path;
	else
		$controller = '';
}
// Create the controller
$classname    = 'eWalletController'.$controller;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( JRequest::getVar( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
