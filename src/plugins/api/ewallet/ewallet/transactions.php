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
require_once( JPATH_SITE .'/components/com_ewallet/models/billing.php');

class EwalletApiResourceTransactions extends ApiResource
{

	public function post()
	{
		//init variable
		$obj = new stdclass;
		$output_arr = array();
		$jinput = JFactory::getApplication()->input;

		//get post data
		$date_range_start = $jinput->post->get('date_range_start',JFactory::getDate()->format('d-m-Y'),'STRING');
		$date_range_end = $jinput->post->get('date_range_end',JFactory::getDate()->format('d-m-Y'),'STRING');
		$amount_range_start = round($jinput->post->get('amount_range_start',0,'FLOAT'),2);
		$amount_range_end = round($jinput->post->get('amount_range_end',0,'FLOAT'),2);
		$description = $jinput->post->get('description',0,'STRING');
		$order = $jinput->post->get('order_by','time','STRING');
		$order = ($order=='amount')?'spent':'time';

		$start_limit = $jinput->post->get('start_limit',0,'INT');
		$end_limit = $jinput->post->get('end_limit',10,'INT');

		//get user details
		$user = JFactory::getUser($this->plugin->get('user')->id);

		if($user->id)
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select(array('DATE(FROM_UNIXTIME(a.time)) as time','id','a.order_code','a.spent as debit', 'a.earn as credits','balance','comment as description'));
			$query->from($db->quoteName('#__wallet_transc','a'));
			$query->where($db->quoteName('a.user_id') . ' = '. $db->quote($user->id));

			if(!empty($date_range_start) && !empty($date_range_end) )
			{
				$sdate = strtotime($date_range_start.'00.00.00');
				$edate = strtotime($date_range_end.'23.59.59');

				$query->where('a.time BETWEEN '.$sdate.' AND '.$edate);
			}

			if(!empty($amount_range_start) && !empty($amount_range_end) )
			{
				$query->where('a.spent BETWEEN '.$amount_range_start.' AND '.$amount_range_end);
			}

			if(!empty($description))
			{
				$query->where("a.comment LIKE '%".$description."%'");
			}

			$query->order('a.'.$order.' DESC');

			$db->setQuery($query);
			$obj->total_count = count($db->loadObjectList());

			// Reset the query using our newly populated query object.
			if($end_limit)
				$db->setQuery($query, $start_limit,$end_limit);

			// Load the results as a list of stdClass objects (see later for more options on retrieving data).
			$results = $db->loadObjectList();
			$obj->data = $results;

		}
		else
		{
			$obj->code = 403;
			$obj->message = 'Bad request';
		}

		$this->plugin->setResponse($obj);

	}

}
