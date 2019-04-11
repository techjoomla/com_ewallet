<?php
/*
  @package	
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */
	
// no direct access
defined('_JEXEC') or die('Restricted access');
if(JVERSION>=3.0)
{
jimport('joomla.application.component.model');
}
else
{
 jimport('joomla.application.component.modeladmin');
}
//jimport('joomla.filesystem.file');


class eWalletModelbilling extends JModelAdmin
{	 /**
   * Items total
   * @var integer
   */
  var $_total = null;
 
  /**
   * Pagination object
   * @var object
   */
  var $_pagination = null;
  
       function __construct()
		  {
				parent::__construct();
		 
				$mainframe = JFactory::getApplication();
		 
				// Get pagination request variables
				$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
				$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		 
				// In case limit has been changed, adjust it
				$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		 
				$this->setState('limit', $limit);
				$this->setState('limitstart', $limitstart);
		  } 
	function _buildQuery()
	{
		//print_r($month); die('asd');
		$mainframe= JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		
		
		$month = $mainframe->getUserStateFromRequest( $option.'month', 'month','', 'int' ); 
		$year = $mainframe->getUserStateFromRequest( $option.'year', 'year','', 'int' );
		$filter_user = $mainframe->getUserStateFromRequest( $option.'filter_user', 'filter_user','', 'int' ); 
		//print_r();die();
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
		$userids = '';
		if($filter_user )
		{
			$userids = $filter_user;
		}
	   else
		{
			$userids =JRequest::getInt('uid');
			//$userids = $filter_user;
		}

		$all_info = array();
			
		$query = "SELECT DATE(FROM_UNIXTIME(a.time)) as time,a.spent as spent,type_id,a.earn as credits,balance,comment
		FROM #__wallet_transc as a
		WHERE a.user_id = '".$userids."' ".$whr1."";
		$filter_order	= $mainframe->getUserStateFromRequest( $option.'filter_order_time', 'filter_order','time','cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'filter_order_Dir_time','filter_order_Dir','desc','word' );
	    $query .= " ORDER BY  $filter_order $filter_order_Dir ";
        // echo $query;die();
		return $query;
	}
	function getData()
		  {
				// if data hasn't already been obtained, load it
				if (empty($this->_data)) {
					 $query = $this->_buildQuery();
					$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));    
				}
				return $this->_data;
		  }
		    function getTotal()
		  {
				// Load the content if it doesn't already exist
				if (empty($this->_total)) {
					$query = $this->_buildQuery();
					$this->_total = $this->_getListCount($query);       
				}
				return $this->_total;
		  }
		function getPagination()
		  {
				// Load the content if it doesn't already exist
				if (empty($this->_pagination)) {
					jimport('joomla.html.pagination');
					$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
				}
				return $this->_pagination;
		  }
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_EWALLET';

	/**
	 * Returns a reference to the a Table object, always creating it.
	 */
	public function getUser()
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT DISTINCT b.id, b.username FROM #__wallet_transc as a, #__users as b where a.user_id = b.id and b.block = 0' ) ;
		$list = $db->loadObjectlist();
		$testt[] =array(
			"value" =>0,
			"text" =>'Select User',
			"selected"=>"selected"
			);
		for($i =0;$i < count($list); $i++)
		{
			$testt[] =array(
			"value" =>$list[$i]->id,
			"text" =>$list[$i]->username
			);
		}
		return $testt;
	}
 
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Transaction', $prefix = 'EwalletTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		An optional array of data for the form to interogate.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	JForm	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app	= JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm('com_ewallet.billing', 'billing', array('control' => 'jform', 'load_data' => $loadData));
        
        
		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_ewallet.edit.billing.data', array());

		if (empty($data)) {
			$data = $this->getItem();
            
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param	integer	The id of the primary key.
	 *
	 * @return	mixed	Object on success, false on failure.
	 * @since	1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk)) {

			//Do any procesing on fields here if needed

		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @since	1.6
	 */
   /*	protected function prepareTable(&$table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id)) {

			// Set ordering to the last item if not set
			if (@$table->ordering === '') {
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__wallet_transc');
				$max = $db->loadResult();
				$table->ordering = $max+1;
			}

		}
	}*/


}
