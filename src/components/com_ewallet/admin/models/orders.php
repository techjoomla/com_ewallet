<?php
/**
 *  @package
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class ewalletModelOrders extends JModelLegacy
{
	var $_data;
	var $_total = null;
	var $_pagination = null;
	var $store_id = null;
	var $customer_count=null;

	/* Constructor that retrieves the ID from the request*/
	function __construct()
	{
		parent::__construct();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function _buildQuery($store_id=0,$customer_id=0)
	{
		$db=JFactory::getDBO();
		// Get the WHERE and ORDER BY clauses for the query
		$where = $this->_buildContentWhere($store_id,$customer_id);
//$where= '';
		$query = "SELECT i.order_code,i.processor, i.amount,i.original_amount, i.cdate, i.payee_id,i.status,i.id,i.prefix,i.currency
		FROM #__wallet_orders AS i ". $where;

		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$jinput=$mainframe->input;
		$option = $jinput->get('option');
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','cdate','cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'filter_order_Dir','filter_order_Dir','desc','word' );
		if ($filter_order) {
			$qry1 = "SHOW COLUMNS FROM #__wallet_orders";
			$db->setQuery($qry1);
			$exists1 = $db->loadobjectlist();
			foreach($exists1 as $key1=>$value1){
				$allowed_fields[]=$value1->Field;
			}
			if(in_array($filter_order,$allowed_fields))
				 $query .= " ORDER BY $filter_order $filter_order_Dir";
		}
		return $query;
	}

	function _buildContentWhere($store_id=0,$customer_id=0)
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$jinput=$mainframe->input;
 		$option = $jinput->get('option');
		$db=JFactory::getDBO();

		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );

		//For Filter Based on Gateway
		$search_gateway ='';
		$search_gateway = $mainframe->getUserStateFromRequest( $option.'search_gateway', 'search_gateway', '', 'string' );
		$search_gateway = JString::strtolower( $search_gateway);
		//For Filter Based on Gateway

		$where = array();

		if($mainframe->getName()=='site'){
			$user=JFactory::getuser();
			if(!empty($store_id))
			{
				$order_ids=$this->getOrderIds($store_id);
				$order_ids=(!empty($order_ids)?$order_ids: 0);
					$where[] = "i.id IN ( ".$order_ids. ")";
			}
			elseif(!empty($customer_id)){
				if(is_numeric($customer_id))
					$where[] = "i.payee_id = ".$customer_id;
				else
					$where[] = "i.email LIKE '".$customer_id."'";
			}
			else
				$where[] = "i.payee_id = ".$user->id;
		}
		if($search_gateway){
			 $where[] = " (i.processor LIKE '".$search_gateway."')";
		}
		if($search=='P' || $search=='C' || $search=='RF'){
			$where[] = 'i.status = '.$this->_db->Quote($search);
		}
		if($filter_state){
			$where[] = " UPPER( CONCAT( i.prefix, i.id )) LIKE UPPER('%".trim($filter_state)."%')";

		}
		return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
	}

	function getOrders($store_id=0,$customer_id=0)
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery($store_id,$customer_id);
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		return $this->_data;
	}

	function getTotal()
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->_total))
		{
		$query = $this->_buildQuery();
		$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getPagination($count=0)
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->_pagination) || $count)  // NOTE :: COUNT PRESENT MEAN->CALLING FROM MYCUSTOMER VIEW
		{
			if(empty($count))
			{
				$count=$this->getTotal();  // use count from of order for my order view
			}
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $count, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}


	/* function store start*/
	/**
	@param $store_id :: INTEGER if we are updating store product status

	@return
	if 1 = success
		2= error
		3 = refund order
	*/
	function store($store_id=0)
	{
			/*load language file for plugin frontend*/
		$lang = JFactory::getLanguage();
		$lang->load('com_ewallet', JPATH_SITE);
		$returnvaule = 1;
		$jinput=JFactory::getApplication()->input;

		$data	= JRequest::get('post');
		if(isset($data['notify_chk'.'|'.$data['id']]) ) {
			$notify_chk = 1;
		}else{
			$notify_chk = 0;
		}
		if(isset($data['comment']) && $data['comment'] ) {
			$comment = $data['comment'];
		}else{
			$comment = '';
		}
		JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_ewallet'.DS.'models');
		$eWalletModelpayment = new eWalletModelpayment();
		$eWalletModelpayment->updateOrderStatus($data['id'],$data['status'],$comment,$notify_chk);

		if($status=='RF')
		{
			$returnvaule = 3;
		}
		return $returnvaule;
	}//function store ends

	function gatewaylist()
	{
		$db = JFactory::getDBO();
		$query = "SELECT DISTINCT(`processor`) FROM #__wallet_orders";
		$db->setQuery($query);
		$gatewaylist = $db->loadObjectList();
		if(!$gatewaylist)
		return 0;
		else
		return $gatewaylist;
	}

	function deleteorders($odid)
	{
		$odid_str=implode(',',$odid);
		$query = "DELETE FROM #__wallet_orders where id IN (".$odid_str.")";
		$this->_db->setQuery( $query );
		if (!$this->_db->execute()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

}

