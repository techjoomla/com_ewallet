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

/**
 * Ewallet helper.
 */
class EwalletHelper
{
	/**
	 * Configure the Linkbar.
	 */
	public static function addSubmenu($vName = '')
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_EWALLET_DASHBOARD'),
			'index.php?option=com_ewallet&view=dashboard',
			$vName == 'transaction'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_EWALLET_ORDERS'),
			'index.php?option=com_ewallet&view=orders',
			$vName == 'transaction'
		);
		
JHtmlSidebar::addEntry(
			JText::_('COM_EWALLET_TRANSACTION'),
			'index.php?option=com_ewallet&view=billing',
			$vName == 'transaction'
		);


	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_ewallet';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
