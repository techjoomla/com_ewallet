<?php
/**
 *  @package    
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class eWalletControllerOrders extends eWalletController
{
	function __construct() 	{
		
		parent::__construct();

		// Register Extra tasks
		$this->registerTask( 'add' , 'edit' );
	}

	function save() {
		
		$model = $this->getModel('orders');
		$jinput=JFactory::getApplication()->input;
		
		$post	= JRequest::get('post');
		
		$model->setState( 'request', $post );
		$result = $model->store();
		if ($result == 1) {
			$msg = JText::_( 'COM_EWALLET_FIELD_SAVING_MSG' );
		} elseif($result == 3) {
			$msg = JText::_( 'COM_EWALLET_REFUND_SAVING_MSG' );
		} else {
			$msg = JText::_( 'COM_EWALLET_FIELD_ERROR_SAVING_MSG' );
		}
		
		$link = 'index.php?option=com_ewallet&view=orders';
		$this->setRedirect($link, $msg);
	}
/*
		//export order payment stats into a csv file
	function payment_csvexport(){
	//load language file for plugin frontend
$lang =  JFactory::getLanguage();
$lang->load('com_ewallet', JPATH_SITE);	
		$db =& JFactory::getDBO();
		$query = "SELECT i.id, i.name, i.email, i.user_info_id,i.cdate, i.transaction_id, i.processor,i.order_tax,i.order_tax_details,i.order_shipping,i.order_shipping_details, i.amount,i.status,i.ip_address 
		FROM  #__kart_orders AS i 
		ORDER BY i.id";

		$db->setQuery($query);
		$results = $db->loadObjectList();

		$csvData = null;
        $csvData.= "Order_Id,Order_Date,User_Name,User_IP,      Order_Tax,Order_Tax_details,Order_Shipping,Order_Shipping_details,Order_Amount,Order_Status,Payment_Gateway,Cart_Items,billing_email,billing_first_name,billing_last_name,billing_phone,billing_address,billing_city,billing_state,billing_country_name,billing_postal_code,shipping_email,shipping_first_name,shipping_last_name,shipping_phone,shipping_address,shipping_city,shipping_state,shipping_country_name,shipping_postal_code";
	
        $csvData .= "\n";
        $filename = "Orders_".date("Y-m-d_H-i",time());
        header("Content-type: application/vnd.ms-excel");
        header("Content-disposition: csv" . date("Y-m") .".csv");
        header("Content-disposition: filename=".$filename.".csv");
        foreach($results as $result ){
        	if( ($result->id ) ){
	       		$csvData .= '"'.$result->id.'"'.','.'"'.$result->cdate.'"'.','.'"'.JFactory::getUser($result->user_info_id)->username.'"'.','.'"'.$result->ip_address.'"'.','.'"'.$result->order_tax.'"'.','.'"'.str_replace ( ",", ";",$result->order_tax_details).'"'.','.'"'.$result->order_shipping.'"'.','.'"'.str_replace ( ",", ";",$result->order_shipping_details).'"'.','.'"'.$result->amount.'"'.',';

        		switch($result->status)
				 {
				case 'C' :
					$orderstatus =  JText::_('QTC_CONFR');
				break;
				case 'RF' :
					$orderstatus = JText::_('QTC_REFUN') ;
				break;
				case 'S' :
					$orderstatus = JText::_('QTC_SHIP') ;
				break;
				case 'P' :
					$orderstatus = JText::_('QTC_PENDIN') ;
				break;
				 }

			$query = "SELECT count(order_item_id) FROM #__kart_order_item WHERE order_id =".$result->id;
			$db->setQuery($query);
 			$cart_items	= $db->loadResult();	       			       		
			$csvData .= '"'.$orderstatus.'"'.','.'"'.$result->processor.'"'.','.'"'.$cart_items.'"'.',';			
				
			$query = "SELECT ou.* FROM #__kart_users as ou WHERE ou.address_type='BT' AND ou.user_id =".$result->user_info_id;
			$db->setQuery($query);
 			$billin	= $db->loadObject();
			$csvData .= '"'.$result->user_email.'"'.','.'"'.$result->firstname.'"'.','.'"'.$result->lastname	.'"'.','.'"'.$result->phone.'"'.','.'"'.$result->address.'"'.','.'"'.$result->city.'"'.','.'"'.$result->state_code.'"'.','.'"'.$result->country_code.'"'.','.'"'.$result->zipcode.'"'.',';		

			$query = "SELECT ou.* FROM #__kart_users as ou WHERE ou.address_type='ST' AND ou.user_id =".$result->user_info_id;
			$db->setQuery($query);
 			$shipin	= $db->loadObjectList();	
			$csvData .= '"'.$result->user_email.'"'.','.'"'.$result->firstname.'"'.','.'"'.$result->lastname	.'"'.','.'"'.$result->phone.'"'.','.'"'.$result->address.'"'.','.'"'.$result->city.'"'.','.'"'.$result->state_code.'"'.','.'"'.$result->country_code.'"'.','.'"'.$result->zipcode.'"'.',';
			
				$csvData .= "\n";
        	}
        }
		ob_clean();        
		print $csvData;

	exit();
	}	
*/
	function cancel() {
		
		$msg = JText::_( 'COM_EWALLET_FIELD_CANCEL_MSG' );
		$this->setRedirect( 'index.php?option=com_ewallet', $msg );
	}
}
