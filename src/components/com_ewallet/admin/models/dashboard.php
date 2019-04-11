<?php
/**
 *  @package    
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class eWalletModelDashboard extends JModelLegacy
{
     /* function for periodic orders count*/

	function getbox($title,$content,$type=NULL)
	{
	$html ='
		<div class="row-fluid">
			<div class="span12"><h5>'.$title.'</h5></div>
		</div>
		<div class="row-fluid">
			<div class="span12">'.$content.'</div>
		</div>';
	return $html;
	}

	function getOrdersArray()
	{
		$db=JFactory::getDBO();
		$query="SELECT amount,status
		FROM #__wallet_orders";
		$db->setQuery($query);
		$data=$db->loadObjectList();
		$count=count($data);
		//set default counts
		$orders['P']=$orders['C']=$orders['D']=$orders['RF']=$orders['UR']=$orders['RV']=$orders['CRV']=$orders['F']=0;


		if($data)
		{
			for($i=0;$i<$count;$i++)
			{
				if($data[$i]->status=='P')
					$orders['P']+=$data[$i]->amount;
				if($data[$i]->status=='C')
					$orders['C']+=$data[$i]->amount;
				if($data[$i]->status=='D')
					$orders['D']+=$data[$i]->amount;
				if($data[$i]->status=='RF')
					$orders['RF']+=$data[$i]->amount;
				if($data[$i]->status=='UR')
					$orders['UR']+=$data[$i]->amount;
				if($data[$i]->status=='RV')
					$orders['RV']+=$data[$i]->amount;
				if($data[$i]->status=='CRV')
					$orders['CRV']+=$data[$i]->amount;
					if($data[$i]->status=='F')
					$orders['F']+=$data[$i]->amount;
			}
		}

		return $orders;
	}
	function getTicketSalesLastweek()
	{
		$db=JFactory::getDBO();
		$date_today=date('Y-m-d');//PHP date format Y-m-d to match sql date format is 2013-05-15

		//get dates for past 6 days
		$msgsPerDay=array();
		for($i=6,$k=0;$i>0;$i--,$k++){
			$msgsPerDay[$k]=new stdClass();
			$msgsPerDay[$k]->date=date('Y-m-d', strtotime(date('Y-m-d').' - '.$i.' days'));
		}
		//get today's date
		$msgsPerDay[$k]=new stdClass();
		$msgsPerDay[$k]->date=date('Y-m-d');

		//find number of messages per day
		for($i=6;$i>=0;$i--){
			//date format here is 2013-05-15
			$query="SELECT count(id) AS count
			FROM #__wallet_orders AS cm
			WHERE status='C' AND date(mdate)='".$msgsPerDay[$i]->date."'";
			$db->setQuery($query);
			$count=$db->loadResult();
			if($count){
				$msgsPerDay[$i]->count=$count;
			}else{
				$msgsPerDay[$i]->count=0;
			}
		}
		return $msgsPerDay;
	}
    	function getAllOrderIncome()
	{
		$query = "SELECT FORMAT(SUM(amount),2) FROM #__wallet_orders WHERE status ='C'";
		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();
		return $result;
		
	}
	function getMonthIncome()
	{
		$db   = JFactory::getDBO();
		
		// $backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
		  
		$curdate = date('Y-m-d'); 
		$back_year=date('Y')-1;
		$back_month=date('m')+1;
		$backdate=$back_year.'-'.$back_month.'-'.'01';
		//echo $query = "SELECT FORMAT(SUM(amount),2) FROM #__kart_orders WHERE status ='C' AND cdate between (".$curdate.",".$backdate." ) GROUP BY YEAR(cdate), MONTH(cdate) order by YEAR(cdate), MONTH(cdate) 
		//"; 
	 	 $query="SELECT FORMAT( SUM( amount ) , 2 ) AS amount, MONTH( cdate ) AS MONTHSNAME, YEAR( cdate ) AS YEARNM
				FROM `#__wallet_orders`
				WHERE DATE(cdate)
				BETWEEN  '".$backdate."'
				AND  '".$curdate."' 
				AND (
				processor NOT 
				IN (
				'payment_jomsocialpoints',  'payment_alphapoints'
				)
				OR extra =  'points'
				)
				AND ( STATUS =  'C' OR STATUS =  'S') 
				GROUP BY YEARNM, MONTHSNAME
				ORDER BY YEAR( cdate ) , MONTH( cdate ) ASC";
	  
		$db->setQuery($query);
		$result =$db->loadObjectList(); 
		return $result;
	
	}
  /* returns total income amount for date range*/	
	function getperiodicorderscount()
	{ 
	 	$db=JFactory::getDBO();
		$session = JFactory::getSession();
		
		$qtc_graph_from_date=$session->get('qtc_graph_from_date');
		$socialads_end_date=$session->get('ewallet_end_date');
		 $where='';
		 $groupby='';
		if($qtc_graph_from_date)
		{
	 		
	 		$where=" AND DATE(mdate) BETWEEN DATE('".$qtc_graph_from_date."') AND DATE('".$socialads_end_date."')";
			
		}
		else 
		{
		
			$qtc_graph_from_date=date('Y-m-d');		
			$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));
			$where=" AND DATE(mdate) BETWEEN DATE('".$backdate."') AND DATE('".$qtc_graph_from_date."')";
			$groupby="";
			
		}
		
		$query = "SELECT FORMAT(SUM(amount),2) FROM #__wallet_orders WHERE (status ='C' OR status ='S')  ".$where;
			$this->_db->setQuery($query);
			$result = $this->_db->loadResult();
			return $result;
			
	 }
	 function notShippedDetails()
	{
		$where = array();
		$where[]=' o.`status`="C" ';
		$where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		$db=JFactory::getDBO();
		$query='SELECT o.id,o.prefix,o.`name`,amount FROM `#__wallet_orders` AS o 
		  '.$where.' ORDER BY o.`mdate` LIMIT 0,7';
		$db->setQuery($query);
		return $result=$db->loadAssocList();
		
	}
	
	 /*** function for pie chart*/	
	function statsforpie()
	{ 
	 	$db=JFactory::getDBO();
		$session = JFactory::getSession();
		//getting current currency
	 	
		 $com_params=JComponentHelper::getParams('com_ewallet');
		$currency = $com_params->get('addcurrency');
		$qtc_graph_from_date=$session->get('qtc_graph_from_date');
  	$socialads_end_date=$session->get('ewallet_end_date');
  	
		
		 $where="AND currency='".$currency."'";
		 $groupby='';
		if($qtc_graph_from_date)
		{
	 		// for graph 
	 		$where .=" AND DATE(mdate) BETWEEN DATE('".$qtc_graph_from_date."') AND DATE('".$socialads_end_date."')";
		}
		else 
		{
			$day = date('d');
			$month = date('m');
			$year = date('Y');			
			$statsforpie = array();

			$backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));
			$groupby="";
			
		}
		
		 	$query = " SELECT COUNT(id) AS orders FROM #__wallet_orders WHERE status= 'P' AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') ".$where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //pending
			
			
					
			$query = " SELECT COUNT(id) AS orders FROM #__wallet_orders WHERE status= 'C' AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') ".$where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //confirm
			
			$query = " SELECT COUNT(id) AS orders FROM #__wallet_orders WHERE status= 'RF' AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') ".$where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //rejected		

			$query = " SELECT COUNT(id) AS orders FROM #__wallet_orders WHERE status= 'S' AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') ".$where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //shipped	
			
			$query = " SELECT COUNT(id) AS orders FROM #__wallet_orders WHERE status= 'E' AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') ".$where;
			$db->setQuery($query);
			$statsforpie[] = $db->loadObjectList(); //cancel	

			return $statsforpie; 
	 }
  /*returns array of month names with year */
  function getAllmonths() {  
  	$date2 = date('Y-m-d'); 
	$back_year=date('Y')-1;
	$back_month=date('m')+1;
	$date1=$back_year.'-'.$back_month.'-'.'01';
    //convert dates to UNIX timestamp  
    $time1  = strtotime($date1);  
    $time2  = strtotime($date2);  
    $tmp     = date('mY', $time2);  
      
    $months[] = array("month"    => date('F', $time1), "year"    => date('Y', $time1));  
      
    while($time1 < $time2) {  
      $time1 = strtotime(date('Y-m-d', $time1).' +1 month');  
      if(date('mY', $time1) != $tmp && ($time1 < $time2)) {  
         $months[] = array("month"    => date('F', $time1), "year"    => date('Y', $time1));  
      }  
    }  
    //$months[] = array("month"    => date('F', $time2), "year"    => date('Y', $time2));  
    $months[] = array("month"    => date('F', $time2), "year"    => date('Y', $time2));
    return $months; //returns array of month names with year  
    }  
    

}
