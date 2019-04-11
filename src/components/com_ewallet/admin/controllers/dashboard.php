<?php
/**
 *  @package    
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class eWalletControllerDashboard extends eWalletController
{
	function __construct() 	{

		parent::__construct();
	}

	function save() {
		
		$model = $this->getModel('dashboard');
		$jinput=JFactory::getApplication()->input;
		
		$post	= JRequest::get('post');
		$model->setState( 'request', $post );
		if ($model->store()==1) {
			$msg = JText::_( 'FIELD_SAVING_MSG' );
		} elseif($model->store()==3) {
			$msg = JText::_( 'REFUND_SAVING_MSG' );
		} else {
			$msg = JText::_( 'FIELD_ERROR_SAVING_MSG' );
		}
		
		$link = 'index.php?option=com_ewallet&view=adorders';
		$this->setRedirect($link, $msg);
	}
	
	function cancel() {
		
		$msg = JText::_( 'FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_ewallet', $msg );
	}
	
function SetsessionForGraph()
	{
		$periodicorderscount='';
	 	$fromDate =  $_GET['fromDate'];
	 	$toDate =  $_GET['toDate'];
		$periodicorderscount=0;
		
		$session = JFactory::getSession();
		$session->set('qtc_graph_from_date', $fromDate);
		$session->set('ewallet_end_date', $toDate);
		
		$model = $this->getModel('dashboard');
		$statsforpie=$model->statsforpie();
//		$ignorecnt=$model->getignoreCount();
		$periodicorderscount=$model->getperiodicorderscount();
		$session->set('statsforpie', $statsforpie);
//		$session->set('ignorecnt', $ignorecnt);
		$session->set('periodicorderscount', $periodicorderscount);
		
		header('Content-type: application/json');
	  	echo (json_encode(array("statsforpie"=>$statsforpie/*,
	  							"ignorecnt"=>$ignorecnt	  							
*/	  	
	  				)));
	  
		jexit();
	}
	
	function makechart()
	{
		$month_array_name = array(JText::_('SA_JAN'),JText::_('SA_FEB'),JText::_('SA_MAR'),JText::_('SA_APR'),JText::_('SA_MAY'),JText::_('SA_JUN'),JText::_('SA_JUL'),JText::_('SA_AUG'),JText::_('SA_SEP'),JText::_('SA_OCT'),JText::_('SA_NOV'),JText::_('SA_DEC')) ;
		$session = JFactory::getSession();
		$qtc_graph_from_date='';
		$ewallet_end_date='';

		$qtc_graph_from_date= $session->get('fromDate', '');
		$ewallet_end_date=$session->get('ewallet_end_date', '');
		$total_days = (strtotime($ewallet_end_date) - strtotime($qtc_graph_from_date)) / (60 * 60 * 24);	
		$total_days=$total_days+1;

		$statsforpie = $session->get('statsforpie','');
		$model = $this->getModel('dashboard');
		$statsforpie=$model->statsforpie();
		
		$ignorecnt = $session->get('ignorecnt', '');
		$periodicorderscount=$session->get('periodicorderscount');
		$imprs=0;
		$clicks=0;
		$max_invite=100;
		$cmax_invite=100;
		$yscale="";
		$titlebar="";
		$daystring="";
		$finalstats_date=array();
		$finalstats_clicks=array();
		$finalstats_imprs=array();
		$day_str_final='';
		$emptylinechart=0;
		$barchart='';
		$fromDate= $session->get('qtc_graph_from_date', '');
		$toDate=$session->get('ewallet_end_date', '');
	 
		$dateMonthYearArr = array();
		$fromDateSTR = strtotime($fromDate);
		$toDateSTR = strtotime($toDate);
		$pending_orders=$confirmed_orders=$shiped_orders=$refund_orders=0;
		
			if(empty($statsforpie[0]) && empty($statsforpie[1]) && empty($statsforpie[2]))
			{
				$barchart=JText::_('NO_STATS');
				$emptylinechart=1;
			}
			else
			{
			  if(!empty($statsforpie[0]))
				{
					$pending_orders= $statsforpie[0][0]->orders;
				}
				if(!empty($statsforpie[1]))
				{
					$confirmed_orders = $statsforpie[1][0]->orders;
					$shiped_orders = $statsforpie[3][0]->orders;
				}	
				if(!empty($statsforpie[1]))
				{
					$refund_orders = $statsforpie[2][0]->orders;
				}  
			}
			//$barchart='<img src="http://chart.apis.google.com/chart?cht=lc&chtt=+'.$titlebar.'|'.JText::_('NUMICHITSMON').'  	+&chco=0000ff,ff0000&chs=900x310&chbh=a,25&chm='.$chm_str.'&chd=t:'.$imprs.'|'.$clicks.'&chxt=x,y&chxr=0,0,200&chds=0,'.$max_invite.',0,'.$cmax_invite.'&chxl=1:|'.$yscale.'|0:|'. $daystring.'|" />';
			header('Content-type: application/json');
		  	echo (json_encode(array("pending_orders"=>$pending_orders,
		  						"confirmed_orders"=>$confirmed_orders,
		  						"shiped_orders"=>$shiped_orders,
		  						"refund_orders"=>$refund_orders,
		  						"periodicorderscount"=>$periodicorderscount,
		  						"emptylinechart"=>$emptylinechart
		  						)));
		  	jexit();
	}
}
