<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

if (!class_exists('comewalletHelper'))
{
class comewalletHelper
{
	function getVersion()
	{
		$recdata = file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=quick2cart');
		return  $recdata;
	}

	// pass the link for which you want the ItemId.
	function getitemid($link)
	{
		$itemid = 0;
		global $mainframe;
		$mainframe = JFactory::getApplication();
		if($mainframe->isAdmin()){
			$db=JFactory::getDBO();
			$query = "SELECT id FROM #__menu WHERE link LIKE '%".$link."%' AND published = 1 LIMIT 1";
			$db->setQuery($query);
			$itemid = $db->loadResult();
		}
		else{
			// getting MENU  Itemid
			$menu = $mainframe->getMenu();
			$items= $menu->getItems('link',$link);
			if(isset($items[0])){
				$itemid = $items[0]->id;
			}

			//IF NO MENU FOR LINK THEN FETCH FROM db
			if(empty($itemid))
			{
				$db=JFactory::getDBO();
				$query = "SELECT id FROM #__menu WHERE link LIKE '%".$link."%' AND published = 1 LIMIT 1";
				$db->setQuery($query);
				$itemid = $db->loadResult();
			}
		}
		// if Itemid is empty then get from request and return it
		if(!$itemid)
		{
			$jinput=JFactory::getApplication()->input;
			$itemid = $jinput->get('Itemid');
		}
		return $itemid;
	}
	/*
	 * Function to get current balance of user
	 * $userid int user id (optional)
	 *
	 * return int
	 * */
	function getUserBalance($userid=''){
		if($userid=='')
			$userid = JFactory::getUser()->id;
		$db = JFactory::getDBO();
		$query = "SELECT balance
		FROM #__wallet_transc
		WHERE time = (SELECT MAX(time)
						FROM #__wallet_transc
						WHERE user_id=".$userid."
					)";
		$db->setQuery($query);
		$balance = $db->loadresult();
		if(empty($balance))
			return 0;
		else
			return $balance;
	}

	function sendmail($recipient,$subject,$body,$bcc_string,$singlemail=1){
		jimport('joomla.utilities.utility');
		global $mainframe;
		$mainframe = JFactory::getApplication();
			$from = $mainframe->getCfg('mailfrom');
			$fromname = $mainframe->getCfg('fromname');
			$recipient = trim($recipient);
			$mode = 1;
			$cc = null;
			$bcc=array();
			if($singlemail==1)
			{
				if($bcc_string){
					$bcc = explode(',',$bcc_string);
				}
				else{
					$bcc = array('0'=>$mainframe->getCfg('mailfrom') );
				}
			}
			//$bcc = array('0'=>$mainframe->getCfg('mailfrom') );
			$attachment = null;
			$replyto = null;
			$replytoname = null;
			JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
	}

	function addUserSpent($userid,$spent,$client,$comment="COM_EWALLET_SPENT_DESC"){
		$db = JFactory::getDBO();
		$comewalletHelper = new comewalletHelper();
		$bal = $comewalletHelper->getUserBalance($userid);
		if(empty($comment))
			$comment="COM_EWALLET_SPENT_DESC";
		$amount_due = new stdClass;
		$amount_due->id = '';
		$amount_due->order_code = $this->getOrdercode();
		$amount_due->time = microtime(true);
		$amount_due->user_id = $userid;
		$amount_due->spent = $spent;
		$amount_due->earn = '';
		$amount_due->balance = $bal - $spent;
		$amount_due->type = 'D';
		$amount_due->parent = 'com_ewallet';
		$amount_due->type_id = 0;
		$amount_due->comment = $comment;
		if(!$db->insertObject('#__wallet_transc', $amount_due, 'id'))
		{
			echo $db->stderr();
			return false;
		}
		//return true;
		return $amount_due->order_code;
	}
  	function addTransaction($user_id,$amount,$type,$comment)
	{
		$db = JFactory::getDBO();
		$comewalletHelper = new comewalletHelper();
		$bal = $comewalletHelper->getUserBalance($user_id);
		if(empty($comment))
			$comment="COM_EWALLET_SPENT_DESC";
		$amount_due = new stdClass;
		$amount_due->id = '';
		$amount_due->time = microtime(true);
		$amount_due->user_id = $user_id;
		if($type == 'C')
		{
			$amount_due->spent = '';
			$amount_due->earn = $amount;
			$amount_due->balance = $bal + $amount;
			$amount_due->type = 'C';
			//$amount_due->comment = 'COM_WALLET_TRANSACTION_ADDED|'.$comment;
		}
		else
		{
			$amount_due->spent = $amount;
			$amount_due->earn = '';
			$amount_due->balance = $bal - $amount;
			$amount_due->type = 'D';
			//$amount_due->comment = 'COM_WALLET_TRANSACTION_DEDUCTED|'.$comment;
		}

		$amount_due->comment = $comment;
		$amount_due->parent = 'com_ewallet';
		$amount_due->type_id = 0;

		//print_r($amount_due);die();
		if(!$db->insertObject('#__wallet_transc', $amount_due, 'id'))
		{
			echo $db->stderr();
			return false;
		}
		return true;
	}
   function getFromattedPrice($price,$curr=NULL,$formatting=1)
	{
	        $params = JComponentHelper::getParams( 'com_ewallet' );
            $curr_sym=$params->get( "addcurrency_sym" ) ;
             $curr_nam=$params->get( "wallet_currency_code" ) ;
            $currency_display_format = $params->get('currency_display_format', "{SYMBOL} {AMOUNT} {CURRENCY}");
			$price = intval(str_replace(',', '', $price));
			$price = number_format($price,2);
			$currency_display_formatstr = str_replace('{AMOUNT}',$price,$currency_display_format);
		   	$currency_display_formatstr = str_replace('{SYMBOL}',$curr_sym,$currency_display_formatstr);
		   	 $currency_display_formatstr = str_replace('{CURRENCY}',$curr_nam,$currency_display_formatstr);
            $html=$currency_display_formatstr;
            return $html;
    }
    /* vishal - generate order code
		THIS function return UUID uniqe code
		@return string  randomly generated UUID code
	*/
	function getOrdercode()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('UUID()');
		$db->setQuery($query);
 
		// Load the results as a string.
		$code = $db->loadResult();
		
		return $code;
    
	}
}
}
