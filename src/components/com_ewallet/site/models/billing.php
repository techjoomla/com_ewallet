<?php
/*
  @package
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');
//jimport('joomla.filesystem.file');


class eWalletModelbilling extends JModelLegacy
{
	function getbilling()
	{

		$mainframe= JFactory::getApplication();
		$input=JFactory::getApplication()->input;
	//$post=$input->post;
	//$input->get
		$option = $input->get('option','','STRING');
		$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' );
		$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );

		$whr='';
		$whr1='';
		if($month && $year)
		{
			$whr = "  AND month(cdate) =" .$month."   AND year(cdate) =".$year."  " ;
			$whr1 = "  AND month(DATE(FROM_UNIXTIME(a.time))) =" .$month."  AND year(DATE(FROM_UNIXTIME(a.time))) =" .$year."  ";
		}
		else if($month=='' && $year)
		{
			$whr = "    AND year(cdate) =".$year."  " ;
			$whr1 = "   AND year(DATE(FROM_UNIXTIME(a.time))) =" .$year."  ";
		}
		$user =JFactory::getUser();
		$all_info = array();

		$query = "SELECT DATE(FROM_UNIXTIME(a.time)) as time,a.spent as spent,type_id,a.earn as credits,balance,comment
		FROM #__wallet_transc as a
		WHERE a.user_id = ".$user->id." ".$whr1." ORDER BY a.time ASC";
		$this->_db->setQuery($query);
		$ad_stat = $this->_db->loadobjectList();
		$camp_name = array();
		/*foreach($ad_stat as $key)
		{
				// to get campaign name
			$query = "SELECT campaign FROM #__ad_campaign WHERE camp_id=".$key->type_id;
			$this->_db->setQuery($query);
			$camp_name[$key->type_id] = $this->_db->loadresult();

			$ad_til = explode('|',$key->comment);
			if(isset($ad_til[1]))
			{
				$query = "SELECT ad_title FROM #__ad_data WHERE ad_id=".$ad_til[1];
				$this->_db->setQuery($query);
				$ad_title[$ad_til[1]] = $this->_db->loadresult();
			}
		}*/
		//array_push($all_info,$ad_stat,$camp_name,$ad_title);
		array_push($all_info,$ad_stat);
		return $all_info;
	}

}
