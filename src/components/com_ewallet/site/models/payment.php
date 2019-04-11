<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.database.table.user' );

class eWalletModelpayment extends JModelLegacy
{
	function makeOrder(){
		$params = JComponentHelper::getParams( 'com_ewallet' );
		$input=JFactory::getApplication()->input;
		$post=$input->post;
		$params = JComponentHelper::getParams( 'com_ewallet' );
		$comewalletHelper= new comewalletHelper();
		
		$user=JFactory::getUser();
		$db = JFactory::getDBO();
		$order_id = $post->get('order_id','','INT');
		$paymentdata = new stdClass;

		$paymentdata->id = '';
		$paymentdata->mdate =  date('Y-m-d H:i:s');
		$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];
		$paymentdata->payee_id = $user->id;
		$paymentdata->name = $user->name;
		$paymentdata->processor = $post->get('payment_gateway','','STRING');
		$input->set('processor',$paymentdata->processor);
	  	$mamount = $post->get('amount','','FLOAT');
		$amount = $mamount +  $params->get( "wallet_surcharge_flat" ) ;
		$per = $mamount * ( $params->get( "wallet_surcharge_percentage" )/100) ;
        $amount = $amount +$per  ;
		$amt = round($amount,2);
		$paymentdata->amount = $amt;
        $amt = round($mamount,2);
		$paymentdata->original_amount =$amt;

		$paymentdata->status = 'P';
		$paymentdata->currency = $params->get( "addcurrency" );

		if(!empty($order_id)){ //update
			$paymentdata->id = $order_id;
			$paymentdata->mdate =  date('Y-m-d H:i:s');
			$paymentdata->ip_address = $_SERVER["REMOTE_ADDR"];
			if(!$this->_db->updateObject('#__wallet_orders', $paymentdata, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
		}else{ //insert
			$paymentdata->order_code = $comewalletHelper->getOrdercode();
			$paymentdata->cdate =  date('Y-m-d H:i:s');
           // print_r($paymentdata);die();
			if(!$this->_db->insertObject('#__wallet_orders', $paymentdata, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
			$order_id=$this->_db->insertid();

			// code to pad zero's to $order_id and append to prefix and update
			$prefix = $this->generate_prefix($order_id);
			$row1 = new stdClass;
			$row1->prefix 		= $prefix;
			$row1->id 			= $order_id;
			if(!$this->_db->updateObject('#__wallet_orders', $row1, 'id'))
			{
				echo $this->_db->stderr();
				return 0;
			}
			$site = JFactory::getApplication()->getCfg('sitename');
			$wallet_curr = $params->get( "wallet_currency_nam" );
			$body = JText::_('COM_EWALLET_ORDER_BODY');
			$subject = JText::sprintf('COM_EWALLET_ORDER_SUBJECT',$site,$prefix.$order_id);
			$find = array ('{WALLETCURR}','{SITENAME}','{NAME}');
			$replace= array($wallet_curr,$site,$user->name);
			$body = str_replace($find, $replace, $body);
			$body = nl2br($body);
			
			$comewalletHelper->sendmail($user->email,$subject,$body,$params->get( 'sale_mail' ));
		}
		return $order_id;
	}
	
	

	/*
	 * i/p : $oid eg. 78
	 * o/p : $prefix eg.QTC-ZLO36-000
	 * */
	function generate_prefix($oid){
		$params = JComponentHelper::getParams( 'com_ewallet' );
		/*##############################################################*/
		// Lets make a random char for this order
		//take order prefix set by admin
		$order_prefix=(string)$params->get('order_prefix');
		$order_prefix=substr($order_prefix,0,5);//string length should not be more than 5
		//take separator set by admin
		$separator=(string)$params->get('separator');
		$prefix=$order_prefix.$separator;
		//check if we have to add random number to order id
		$use_random_orderid=(int)$params->get('random_orderid');
		if($use_random_orderid)
		{
			$random_numer=$this->_random(5);
			$prefix.=$random_numer.$separator;
			//this length shud be such that it matches the column lenth of primary key
			//it is used to add pading
			$len=(23-5-2-5);//order_id_column_field_length - prefix_length - no_of_underscores - length_of_random number
		}else{
			//this length shud be such that it matches the column lenth of primary key
			//it is used to add pading
			$len=(23-5-2);//order_id_column_field_length - prefix_length - no_of_underscores
		}
		/*##############################################################*/

		$maxlen=23-strlen($prefix)-strlen($oid);
		$padding_count=(int)$params->get('padding_count');
		//use padding length set by admin only if it is les than allowed(calculate) length
		if($padding_count>$maxlen){
			$padding_count=$maxlen;
		}
		$append='';
		if(strlen((string)$oid)<=$len)
		{
			for($z=0;$z<$padding_count;$z++){
				$append.='0';
			}
			//$append=$append.$oid;
		}
		$prefix .= $append;

	return $prefix;
	}

	function _random( $length = 5 )
	{
		$salt = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$random = '';

		$stat = @stat(__FILE__);
		if(empty($stat) || !is_array($stat)) $stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i ++) {
			$random .= $salt[mt_rand(0, $len -1)];
		}

		return $random;
	}
	function getHTML($pg_plugin,$tid)
	{
		$vars = $this->getPaymentVars($pg_plugin,$tid);
		JPluginHelper::importPlugin('payment', $pg_plugin);
		$dispatcher = JDispatcher::getInstance();
		$html = $dispatcher->trigger('onTP_GetHTML', array($vars));
		return $html;
	}
	function getPaymentVars($pg_plugin, $orderid)
	{
		$comewalletHelper = new comewalletHelper();
		$params = JComponentHelper::getParams( 'com_ewallet' );
		$orderItemid = $comewalletHelper->getitemid('index.php?option=com_ewallet&view=orders');
		$chkoutItemid = $comewalletHelper->getitemid('index.php?option=com_ewallet&view=payment');
		$pass_data = $this->getdetails($orderid);
		$vars = new stdClass;
		$vars->client="ewallet";
		$vars->order_id=$pass_data['prefix'].$orderid; //append prefix and order_id
		$vars->user_id=$pass_data['user_id'];
		$vars->user_email=JFactory::getUser($pass_data['user_id'])->email;
		$vars->user_firstname = $pass_data['name'];
		$vars->item_name = JText::_('COM_EWALLET_ORDER_PAYMENT_DESC');
		$vars->submiturl = JRoute::_("index.php?option=com_ewallet&controller=payment&task=confirmpayment&processor=".$pg_plugin);
		$vars->return = JURI::root().substr(JRoute::_("index.php?option=com_ewallet&view=payment&layout=success&orderid=".($orderid)."&processor=".$pg_plugin."&Itemid=".$chkoutItemid),strlen(JURI::base(true))+1);
		$vars->cancel_return = JURI::root().substr(JRoute::_("index.php?option=com_ewallet&view=payment&Itemid=".$chkoutItemid),strlen(JURI::base(true))+1);
		$vars->url=$vars->notify_url= JRoute::_(JURI::root()."index.php?option=com_ewallet&controller=payment&task=processpayment&processor=".$pg_plugin);
		$vars->currency_code = $pass_data['currency'];
		$vars->comment = '';
		$vars->payment_description = JText::_('COM_EWALLET_ORDER_PAYMENT_DESC');
		$vars->amount = $pass_data['order_amt'];
		return $vars;
	}

	function getdetails($tid){
	   $query="SELECT o.prefix,o.name,o.payee_id,o.processor,o.amount,o.currency
				FROM #__wallet_orders as o
				where o.id=".$tid;
		$this->_db->setQuery($query);
		$details=$this->_db->loadObjectlist();
		$orderdata=array('payment_type'=>'',
		'order_id'=>$tid,
		'prefix'=>$details[0]->prefix,
		'pg_plugin'=>$details[0]->processor,
		'user_id'=>$details[0]->payee_id,
		'name'=>$details[0]->name,
		'order_amt'=>$details[0]->amount,
		'currency'=>$details[0]->currency);
		return $orderdata;

	}

	function confirmpayment($pg_plugin,$oid)
	{
		$post	= JRequest::get('post');
		$vars = $this->getPaymentVars($pg_plugin,$oid);
		if(!empty($post) && !empty($vars) ){
			JPluginHelper::importPlugin('payment', $pg_plugin);
			$dispatcher = JDispatcher::getInstance();

			$result = $dispatcher->trigger('onTP_ProcessSubmit', array($post,$vars));
		}
		else{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_EWALLET_SOME_ERROR_OCCURRED'), 'error');
		}
	}

	function processpayment($post,$pg_plugin,$order_id)
	{
		$comewalletHelper = new comewalletHelper();
		$chkoutItemid = $comewalletHelper->getitemid('index.php?option=com_ewallet&view=billing');
		$return_resp=array();
		//Authorise Post Data
		if(!empty($post['plugin_payment_method']) && $post['plugin_payment_method']=='onsite')
			$plugin_payment_method=$post['plugin_payment_method'];
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $pg_plugin);
		$data = $dispatcher->trigger('onTP_Processpayment', array($post));
		$data = $data[0];
		$res=@$this->storelog($pg_plugin,$data);

		if(empty($order_id))
			$order_id=$data['order_id'];
		$order_id = $this->extract_prefix($order_id);
		$return_resp['return']=$data['return'];

		$data['processor']=$pg_plugin;
		$data['status']=trim($data['status']);
		$query="SELECT o.amount
				FROM #__wallet_orders  as o
				where o.id=".$order_id;
		$this->_db->setQuery($query);
		$order_amount=$this->_db->loadResult();
		$return_resp['status']='0';

		if($data['status']=='C' && ($order_amount == $data['total_paid_amt']))
		{
			$data['status'] = 'C';
			$return_resp['status']='1';
		}
		/*else if($order_amount != $data['total_paid_amt']){
			$data['status'] = 'E';
			$return_resp['status']='0';
		}
		else if(empty($data['status'])){
			$data['status'] = 'P';
			$return_resp['status']='0';
		}
		if($data['status']!='C' && !empty($data['error']) ){
			$return_resp['msg']=$data['error']['code']." ".$data['error']['desc'];
		}*/

		$this->updateOrder($data);
		$this->updateOrderStatus($order_id,$data['status']);
		$return_resp['return']=JURI::root().substr(JRoute::_("index.php?option=com_ewallet&view=payment&layout=success&orderid=".($order_id)."&processor={$pg_plugin}&Itemid=".$chkoutItemid,false),strlen(JURI::base(true))+1);

		return $return_resp;
	}
		/*
	 * i/p : $oid eg. QTC-ZLO36-00078
	 * o/p : $prefix eg. 78
	 * */
	function extract_prefix($prefix_orderid){
		$params = JComponentHelper::getParams( 'com_ewallet' );
		$separator=(string)$params->get('separator');
		$prefix_array = explode($separator,$prefix_orderid);
		if(count($prefix_array)==1){
			return $prefix_array[0];
		}
		else{
			$use_random_orderid=(int)$params->get('random_orderid');
			if($use_random_orderid){
				$order_id = $prefix_array[2];
			}else{
				$order_id = $prefix_array[1];
			}
			$order_id = ltrim($order_id,"0");
			/* @TODO trim the padded zero's from order id*/
			return $order_id;
		}
	}
	function storelog($name,$data)
	{
		$data1=array();
		$data1['raw_data']=$data['raw_data'];
		$data1['JT_CLIENT']="com_ewallet";

		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment', $name);
		$data = $dispatcher->trigger('onTP_Storelog', array($data1));
	}
	function updateOrder($data)
	{
		$res = new stdClass();
		$res->id=$this->extract_prefix($data['order_id']); // $eoid means extracted order id
		$res->mdate 			= date("Y-m-d H:i:s");
		$res->transaction_id	= $data['transaction_id'];
//			$res->status 			= $data['status']; /*changed by dipti since there is already a update status function*/
		$res->processor		= $data['processor'];
		//appending raw data to orders's extra field data
		$extra['form_post'] = $data['raw_data'];
		$res->extra = $this->appendExtraFieldData($extra,$res->id);
		if(!$this->_db->updateObject( '#__wallet_orders', $res, 'id' ))
		{
			//return false;
		}
	}

	/**
	THIS function take orderid and array of data to be store in extra field of order table
	@data array :: data in array index to be store in extra field example array('form_post'=>[all the data])
	@order_id INTERGER :: order id
	@return json string  :: json_encoded extra field data
	*/
	function appendExtraFieldData($data,$order_id)
	{
		$q="SELECT `extra` FROM  `#__wallet_orders` WHERE `id` =".$order_id;
		$this->_db->setQuery($q);
		$oldres = $this->_db->loadResult();
		if(!empty($oldres))
		{
			// Take already exist extra data
			$extradata=json_decode($oldres);
		}
		foreach($data as $k=>$v){
			$extradata[$k] = $v;
		}
		return json_encode($extradata);
	}// end of appendExtraFieldData

	/*
	 * Function to update status of order
	 *
	 	   Parameters:
	 	   order_id : int id of order
	 	   status : string status of order
	 	   comment : string default='' comment added if any
	 	   $send_mail : int default=1 weather to send status change mail or not.
	 	   @param $store_id :: INTEGER (1/0) if we are updating store product status
	*/
	function updateOrderStatus($order_id,$status,$comment='',$send_mail=1){
		global $mainframe;
		$params = JComponentHelper::getParams( 'com_ewallet' );
		$comewalletHelper = new comewalletHelper();
		$mainframe = JFactory::getApplication();

		$query = "SELECT o.status,o.amount,o.original_amount FROM #__wallet_orders as o WHERE o.id =".$order_id;
		$this->_db->setQuery($query);
		$order_data = $this->_db->loadAssoc();
		 //print_r($order_data);die();
		if($order_data['status'] != $status && $status =='C'){	//add balance
		   //	$order_data['amount'] = $order_data['amount'] * $params->get('wallet_exchange',10);
			$this->add_balance($order_data['amount'],$order_data['original_amount'],$order_id);
		}
		// IF admin changes ORDER status
		$res = new stdClass();
		$res->status=$status;
		$res->id 		= $order_id;
		if(!$this->_db->updateObject( '#__wallet_orders', $res, 'id' ))
		{
			return 2;
		}

		{

		//START Q2C Sample development
		$query = "SELECT o.* FROM #__wallet_orders as o WHERE o.id =".$order_id;
		$this->_db->setQuery($query);
		$orderobj	= $this->_db->loadObject();
		/*
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('system');
		$result=$dispatcher->trigger('Onq2cOrderUpdate',array($orderobj));//Call the plugin and get the result
		//END Q2C Sample development
		*/
		}
		if($send_mail == 1 && $order_data['status'] != $status)
		{
			//$adminemails = comewalletHelper::adminMails();
		   $query = "SELECT ou.payee_id,ou.name,ou.amount FROM #__wallet_orders as ou WHERE ou.id = ".$order_id;
			$this->_db->setQuery($query);
 			$orderuser	= $this->_db->loadObject();
 			$orderuser->user_email = JFactory::getUser($orderuser->payee_id)->email;
 			$helperobj=new comewalletHelper;
            $orderuser->amount = $helperobj->getFromattedPrice($orderuser->amount);

			switch($status)
			{
				case 'C' :
					$orderstatus =  JText::_('COM_EWALLET_CONFR');
				break;
				case 'RF' :
					$orderstatus = JText::_('COM_EWALLET_REFUN') ;
				break;
				case 'E' :
					$orderstatus = JText::_('COM_EWALLET_ERR') ;
				break;
				case 'P' :
					$orderstatus = JText::_('COM_EWALLET_PENDIN') ;
				break;
				default:
					$orderstatus = $status;
				break;
			}

			$fullorder_id = $orderobj->prefix.$order_id;
		   	$body = JText::_('COM_EWALLET_STATUS_CHANGE_BODY');
			//$body = JText::_('COM_EWALLET_STATUS_CHANGE_BODY_NEW');
			$site = $mainframe->getCfg('sitename');
		   	if($comment)
			{
				$comment	= str_replace('{COMMENT}', $comment, JText::_('COM_EWALLET_COMMENT_TEXT'));
				$find 	= array ('{ORDERNO}','{STATUS}','{SITENAME}','{NAME}', '{COMMENTTEXT}','{AMOUNT}');
				$replace= array($fullorder_id,$orderstatus,$site,$orderuser->name,$comment,$orderuser->amount);
			}
			else
			{
				$find 	= array ('{ORDERNO}','{STATUS}','{SITENAME}','{NAME}', '{COMMENTTEXT}','{AMOUNT}');
				$replace= array($fullorder_id,$orderstatus,$site,$orderuser->name,'',$orderuser->amount);
			}

			$body	= str_replace($find, $replace, $body);

			$Itemid = $comewalletHelper->getitemid('index.php?option=com_ewallet&view=orders');
			$link = JURI::root().substr(JRoute::_('index.php?option=com_ewallet&view=orders&layout=order&orderid='.$order_id.'&Itemid='.$Itemid),strlen(JURI::base(true))+1);
			$order_link = '<a href="'.$link.'">'.JText::_('COM_EWALLET_ORDER_GUEST_LINK').'</a>';
			$body	= str_replace('{LINK}', $order_link, $body);
			$body = nl2br($body);
			$subject = JText::sprintf('COM_EWALLET_STATUS_CHANGE_SUBJECT',$fullorder_id);
			$comewalletHelper->sendmail($orderuser->user_email,$subject,$body,$params->get( 'sale_mail' ));
		}
	}

	function add_balance($amt,$org_amt,$order_id,$comment="COM_EWALLET_ADDED_BALANCE")
	{
	  $params = JComponentHelper::getParams( 'com_ewallet' );
         $helperobj=new comewalletHelper;
         $org_amt_format = $helperobj->getFromattedPrice($org_amt);
	  //echo "here";die();
		$query = "SELECT payee_id
		FROM #__wallet_orders
		WHERE id =".$order_id;
		$this->_db->setQuery($query);
		$userid = $this->_db->loadresult();

		$date = microtime(true);
		$date1 = date('Y-m-d');
		$comewalletHelper = new comewalletHelper();
		$bal = $comewalletHelper->getUserBalance($userid);
        $apply_amt = $org_amt * $params->get('wallet_exchange',10);
		$balance= $bal + $apply_amt;
$surchange_amt = $amt -$org_amt;
$surchange_amt_format = $helperobj->getFromattedPrice($surchange_amt);
		$amount_due = new stdClass;
		$amount_due->id = '';
		$amount_due->time = $date;
		$amount_due->user_id = $userid;
		$amount_due->spent = '';
		$amount_due->earn = $apply_amt;
		$amount_due->balance = $balance;
		$amount_due->type = 'C';
		$amount_due->parent = 'com_ewallet';
		$amount_due->type_id = $order_id;
        $comment= "COM_EWALLET_ADDED_SURCHANGE|".$params->get( "wallet_currency_nam" )."|".$org_amt_format."|".$surchange_amt_format;
		$amount_due->comment = $comment;
       // print_r($amount_due);die();
		if(!$this->_db->insertObject('#__wallet_transc', $amount_due, 'id'))
		{
			echo $this->_db->stderr();
			return false;
		}
		return $this->_db->insertID();
	}

	function SendOrderMAil($orderid,$pg_nm){
		$db = JFactory::getDBO();
		$query = "SELECT ad_id	FROM #__ad_payment_info WHERE id =".$orderid;
		$db->setQuery($query);
		 $adid = $db->loadResult();

		//for payment details send through email
		$details = socialadshelper::paymentdetails($adid);
		if($details)
		{
			$details[0]->payment_method=$pg_nm;
			$mail = socialadshelper::newadmail($adid, $details);
			//for send mail to admin approval when new ad created
		}
	}


}
