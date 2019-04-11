<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$user =JFactory::getUser();
?>
<div class="techjoomla-bootstrap">
<?php

if (!$user->id)
{
	?>
	<div class="well" >
		<div class="alert alert-error">
			<?php echo JText::_('COM_EWALLET_PAY_LOGIN'); ?>
		</div>
	</div>
</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

$params = JComponentHelper::getParams( 'com_ewallet' );
$format = $params->get('currency_display_format');

$helperobj=new comewalletHelper;

$singleselect = array();
$singleselect[] = JHTML::_('select.option','0', JText::_('COM_EWALLET_NO'));
$singleselect[] = JHTML::_('select.option','1', JText::_('COM_EWALLET_YES'));
   // echo $params->get( "addcurrency_sym" );
?>

<script type="text/javascript">
	 function isNumberKeyWithDecimal(event){
	   
       if ((event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
        || (event.keyCode >= 96 && event.keyCode <= 105))
            {
				
			}
			else
			{
			  var n = jQuery('#amount').val();
              if(isNaN(n))
              {
                	jQuery('#amount').val('');
                	jQuery('.hidden_data').css("display", "none");
              }

			}
    }
function calculate(ele)
{

if(parseInt(ele.value))
{

  var flat = '';
  var percen = '';
  flat = <?php echo $params->get( "wallet_surcharge_flat" ).";"; ?>
  percen = <?php echo $params->get( "wallet_surcharge_percentage" ).";";?> ;
  var currency_display_format = <?php echo "'".$params->get('currency_display_format', "{SYMBOL} {AMOUNT} {CURRENCY}")."';";?>
 
  var symbol = <?php echo "'".$params->get( "addcurrency_sym" )."';"; ?>
  var currency = <?php echo "'".$params->get( "wallet_currency_code" )."';"; ?>
  var cal_per =   parseInt(ele.value) * (parseInt(percen)/ 100);
  var s_amt =  parseInt(flat) +  parseInt(cal_per);
  var t_amt =  parseInt(ele.value) +  parseInt(s_amt);
  var currency_display_formatstr = currency_display_format.replace('{AMOUNT}',s_amt);
	  currency_display_formatstr = currency_display_formatstr.replace('{SYMBOL}',symbol);
	  currency_display_formatstr = currency_display_formatstr.replace('{CURRENCY}',currency);
	  jQuery('.hidden_data').css("display", "block");
      jQuery('#span_surcharge').css("display", "block");
	  jQuery("#span_surcharge").text( currency_display_formatstr);   

 var currency_display_formatstr2 = currency_display_format.replace('{AMOUNT}',t_amt);
	  currency_display_formatstr2 = currency_display_formatstr2.replace('{SYMBOL}',symbol);
	  currency_display_formatstr2 = currency_display_formatstr2.replace('{CURRENCY}',currency);
	  jQuery('.hidden_data').css("display", "block");
     jQuery('#span_total_amount').css("display", "block");
	 jQuery("#span_total_amount").text( currency_display_formatstr2);  

 }
  else
  {

     alert('<?php echo JText::_('COM_EWALLET_PAYMENT_GREATERT_THAN_ZERO'); ?>');
     document.getElementById('amount').value = '';
    jQuery('#amount').focus();
    jQuery('#amount').addClass('invalid');
    jQuery('.hidden_data').css("display", "none");

     jQuery('#span_surcharge').css("display", "none");
     jQuery('#span_total_amount').css("display", "none");
  }
}
function show_hide(){
	techjoomla.jQuery('#pay_continue').show();
	techjoomla.jQuery('#order_summary_tab_table_html').html('');
}
function makepayment(){
    var cal = document.getElementById('amount').value;
   if(parseInt(cal) != 0)
   {
     var flat = '';   var percen = '';
    flat = <?php echo $params->get( "wallet_surcharge_flat" ); ?>

  percen = <?php echo $params->get( "wallet_surcharge_percentage" );?> ;
  var cal_per =   parseInt(cal) * (parseInt(percen)/ 100);

  var cal =parseInt(cal) + parseInt(flat) +  parseInt(cal_per);
	if(cal=='' || isNaN(cal))
	{
		alert("<?php echo JText::_('COM_EWALLET_CORR_AMT') ?>");
		document.getElementById('amount').focus();
		return false;
	}
	var pay_method = techjoomla.jQuery("input[type='radio'][name='payment_gateway']:checked").val();
	if(!pay_method)
		return false;
	
	var values=techjoomla.jQuery('#adminForm').serialize();
	jQuery.ajax({
		url: '?option=com_ewallet&controller=payment&task=makepayment&processor='+pay_method+'&amount='+cal,
		type: 'POST',
		data:values,
		dataType: 'json',
		beforeSend: function() {
			techjoomla.jQuery('#pay_continue').hide();
			techjoomla.jQuery('#order_summary_tab_table_html').html('');
			techjoomla.jQuery('#confirm-order').after('<div class=\"com_ewallet_ajax_loading\"><div class=\"com_ewallet_ajax_loading_text\"><?php echo JText::_('COM_EWALLET_LOADING_PAYMET_FORM_MSG')?></div><img class=\"com_ewallet_ajax_loading_img\" src=\"<?php echo JUri::base() ?>components/com_ewallet/assets/images/ajax.gif\"></div>');
		},
		complete: function() {
			techjoomla.jQuery('.com_ewallet_ajax_loading').remove();
		},
		success: function(data)
		{
			if(data['success']==1){
				techjoomla.jQuery('#pay_continue').hide();
				techjoomla.jQuery('#order_id').val(data['order_id']);
				techjoomla.jQuery('#order_summary_tab_table_html').html(data['orderHTML']);
			}
			else{
				techjoomla.jQuery('#pay_continue').show();
				techjoomla.jQuery('#order_summary_tab_table_html').html();
			}
		}
	});
    }
    else
    {
      alert('<?php echo JText::_('COM_EWALLET_PAYMENT_GREATERT_THAN_ZERO'); ?>');
      document.getElementById('amount').value = '';
    }
}

</script>



<div class="page-header">
	<h2><?php echo JText::_('COM_EWALLET_MAKE_PAYMENT'); ?></h2>
</div>
<form name="adminForm" class="form-validate form-horizontal" id="adminForm" action="" method="post" class="form-validate" enctype="multipart/form-data">
<div class="controls">
	<div class="form-inline" >
		<?php
		$amount_input = '<input id="amount" name="amount" type="text" class="input-mini number required" value="" onchange="calculate(this)" onkeyup="return isNumberKeyWithDecimal(event)" onfocus="show_hide()" onchange="show_hide()" >';
		$symbol_input = '<span class="add-on">'.$params->get( "addcurrency_sym" ).'</span>';
		$currency_input = '<span class="add-on">'.$params->get( "wallet_currency_nam" ).'</span>';
		 $curr_sym=$params->get( "addcurrency_sym" ) ;
            $currency_display_format = $params->get('currency_display_format', "{SYMBOL} {AMOUNT} {CURRENCY}");
			
			$currency_display_formatstr = str_replace('{AMOUNT}',$amount_input,$currency_display_format);
		   	$currency_display_formatstr = str_replace('{SYMBOL}',$symbol_input,$currency_display_formatstr);
		   	$currency_display_formatstr = str_replace('{CURRENCY}',$currency_input,$currency_display_formatstr);
          $input = '<div class="input-append ">'.$currency_display_formatstr.'</div>'; 
		$wallet_cur_name=$params->get( 'wallet_currency_nam' );
	   $img = JPATH_SITE.'components'.DS.'com_ewallet'.DS.'assets'.DS.'images'.DS.'default_currency.png';
	   $imgname = $params->get( 'wallet_currency_icon' );
		$imgfilepath = JPATH_SITE.'images'.DS.'ewallet'.DS;
	   	$imgfile=JUri::base().'images'.DS.'ewallet'.DS;
		if(!JFile::exists( $imgfilepath.$imgname)){
			$imgfile = $imgfile.'default_currency.png';
		}else{
			$imgfile = $imgfile.$imgname;
		}
		$wallet_cur_icon='<span class="com_wallet_currency"><img src="'.$imgfile.'" alt="'.JText::_('COM_EWALLET_NO_CURRIMG').'" ></span>';
		echo JText::sprintf('COM_EWALLET_PAY_ADD_COINS',$input, $wallet_cur_name, $wallet_cur_icon);
    // $params->get( "wallet_surcharge_flat" ) +
    ?>


	</div>
</div>

	<div  class="control-group hidden_data" style="display:none;"  >
     	<div class="control-label"> <?php echo JText::_('COM_EWALLET_SURCHAGE');?>  </div>
        <div class="controls">
        <span id="span_surcharge" style="margin-top: 5px; display:none;" > </span></div>
      </div>
     <div  class="control-group hidden_data" style="display:none;"  >
     	<div class="control-label"> <?php echo JText::_('COM_EWALLET_TOTAL_AMOUNT');?>  </div>
        <div class="controls">
        <span id="span_total_amount" style="margin-top: 5px; display:none;" > </span></div>
      </div>

	<div class="control-group" >

		<?php
		$lable=JHTML::tooltip(JText::_('COM_EWALLET_SELECT_GATEWAY_DES'), JText::_('COM_EWALLET_SELECT_GATEWAY'), '', JText::_('COM_EWALLET_SELECT_GATEWAY'));
		$gateway_div_style=1;
		if(!empty($this->gateways)) //if only one geteway then keep it as selected 
		{
			$default=''; // id and value is same
		}
		if(!empty($this->gateways) && count($this->gateways)==1) //if only one geteway then keep it as selected 
		{
			$default=$this->gateways[0]->id; // id and value is same
			$lable=JText::_( 'COM_EWALLET_GATEWAY_IS' );
			$gateway_div_style=0;
		}
		?>
		<label for="" class="control-label"><?php echo $lable ?></label>
		<div class="controls" style="<?php echo ($gateway_div_style==1)?"" : "display:none;" ?>">
			<?php
			if(empty($this->gateways)) 
				echo JText::_( 'COM_EWALLET_NO_PAYMENT_GATEWAY' );
			else 
			{
				$pg_list = JHtml::_('select.radiolist', $this->gateways, 'payment_gateway', 'class="inputbox required" onclick="show_hide()" ', 'id', 'name',$default,false);
				echo $pg_list;
			}
			?>
		</div>
		<?php
		if(empty($gateway_div_style))
		{
		?>
			<div class="controls qtc_left_top">	
			<?php echo $this->gateways[0]->name; // id and value is same ?>
			</div>
		<?php
		}
		?>
	</div>
	<div class="form-actions" id="pay_continue">
		<input type="button" class="btn btn-success" value="<?php echo JText::_('COM_EWALLET_PAY_CONTINUE'); ?>" onclick="makepayment()" />
	</div>
	<input type="hidden" name="order_id" id="order_id" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="confirm-order">
	<div id="order_summary_tab_table_html"> </div>
</div>
</div>
