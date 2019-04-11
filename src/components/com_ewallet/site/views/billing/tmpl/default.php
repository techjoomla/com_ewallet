<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');

$user =JFactory::getUser();
$ssession = JFactory::getSession();
$mainframe = JFactory::getApplication();
?>
<div class="techjoomla-bootstrap">
<?php
if (!$user->id)
{
	?>
	<div class="well" >
		<div class="alert alert-error">
			<?php echo JText::_('COM_EWALLET_BILL_LOGIN'); ?>
		</div>
	</div>
</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}

$params = JComponentHelper::getParams( 'com_ewallet' );
$stat_info = $this->billing[0];


/* month filter try */
	## An array of $key=>$value pairs ##
	$months = array(0=>JText::_('COM_EWALLET_MONH'), 1 => JText::_('COM_EWALLET_JAN'), 2 => JText::_('COM_EWALLET_FEB'), 3 => JText::_('COM_EWALLET_MAR'), 4 => JText::_('COM_EWALLET_APR'), 5 => JText::_('COM_EWALLET_MAY'), 6 => JText::_('COM_EWALLET_JUN'), 7 => JText::_('COM_EWALLET_JUL'), 8 => JText::_('COM_EWALLET_AUG'), 9 => JText::_('COM_EWALLET_SEP'), 10 => JText::_('COM_EWALLET_OCT'), 11 => JText::_('COM_EWALLET_NOV'), 12 => JText::_('COM_EWALLET_DEC'));

	## Initialize array to store dropdown options ##
	$month = array();

	foreach($months as $key=>$value){
		## Create $value ##
		$month[] = JHTML::_('select.option', $key, $value);
	}
	// year filter
	$year = array();
	$year = range(2013, 2000, 1);
	foreach($year as $key=>$value){
		unset($year[$key]);
		$year[$value]= $value;
	}
	foreach($year as $key=>$value){
		## Create $value ##
		$year1[] = JHTML::_('select.option', $key, $value);
	}
?>
<script type="text/javascript" >

function change_table(id){
	//alert(id);
	var a = document.getElementById("pay_table");
	var b = document.getElementById("spent_table");
	var c = document.getElementById("all_table");
//	var d = document.getElementById("add_balance");
	if(id=='pay_tab_change_cr')
	{
		a.style.display="block";
		b.style.display="none";
		c.style.display="none";
		//d.style.display="none";
	}
	else if(id=='pay_tab_change_de')
	{
		a.style.display="none";
		b.style.display="block";
		c.style.display="none";
		//d.style.display="none";
	}
	else if(id=='pay_tab_add_balance')
	{
		a.style.display="none";
		b.style.display="none";
		c.style.display="none";
		//d.style.display="block";

	}
	else
	{
		a.style.display="none";
		b.style.display="none";
		c.style.display="block";
		//d.style.display="none";
	}

}
</script>
<legend>
		<?php echo JText::_('COM_EWALLET_BILLING');?>
	</legend>


<form class="navbar-form navbar-left"  action="" method="post" name="adminForm3" id="adminForm3">

	<div class="form-group">
				<?php
				$startYear =date('Y');
				$endYear = date('Y')-5;
				unset($year);

				//$years[0] = "Year";
				for ($i=$startYear;$i>=$endYear;$i--){
				$year[$i] = $i;
				}
					echo JHTML::_('select.genericlist', $month,'month', 'name="filter_order" ', "value", "text",$this->lists['month']);
					echo JHTML::_('select.genericlist', $year,'year', 'name="filter_order" ', "value", "text",$this->lists['year']);
					?>
				 <button type="button" name="go" title="<?php echo JText::_('COM_EWALLET_GO'); ?>" class="btn btn-success" id="go" onclick="this.form.submit();"><?php echo JText::_('COM_EWALLET_GO'); ?></button>
				   <button type="button" name="clear" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" class="btn" id="go" onclick="jQuery('#filter_user').val(0);jQuery('#month').val(0); jQuery('#year').val(0);this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

		<input type="button" class="btn btn-success" value="<?php echo JText::_('COM_EWALLET_ADD_BALANCE'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_ewallet&view=payment')?>'; " />

	</div>
</form>
<br>
	<div id="tabContainer" class="">
		<ul id="myTab " class="nav nav-tabs ">
			<li id="spent_tab_change" onclick="change_table(this.id)" class="active"><a href="#tab1fb" data-toggle="tab"><?php echo JText::_('COM_EWALLET_ACC_HIS'); ?></a></li>
			<li id="pay_tab_change_cr" onclick="change_table(this.id)" class=""><a href="#tab2fb" data-toggle="tab"><?php echo JText::_('COM_EWALLET_PAY_CREDTIS_ONLY'); ?></a> </li>
			<li id="pay_tab_change_de" onclick="change_table(this.id)" class=""><a href="#tab4fb" data-toggle="tab"><?php echo JText::_('COM_EWALLET_PAY_SPNT_ONLY'); ?></a></li>
			<!--li id="pay_tab_add_balance" onclick="change_table(this.id)" class=""><a href="#tab4fb" data-toggle="tab"><?php echo JText::_('COM_EWALLET_ADD_BALANCE'); ?></a></li-->
		</ul>
	</div>


		<!--div class="form-actions" >
		<input type="button" class="btn btn-success" value="<?php echo JText::_('COM_EWALLET_ADD_BALANCE'); ?>" onclick="window.location='<?php echo JRoute::_('index.php?option=com_ewallet&view=payment')?>'; " />
	</div-->
	<!--div  class="row-fluid">
		<div class="table-tool">
			<div class="span7">
				<div class="btn-group">
				   	<button type="button" title="<?php echo JText::_('COM_EWALLET_ACC_HIS'); ?>" class="btn btn-info" id="spent_tab_change" onclick="change_table(this.id)"><?php echo JText::_('COM_EWALLET_ACC_HIS'); ?></button>
					<button type="button" title="<?php echo JText::_('COM_EWALLET_PAY_CREDTIS_ONLY'); ?>" class="btn btn-info" id="pay_tab_change_cr" onclick="change_table(this.id)"><?php echo JText::_('COM_EWALLET_PAY_CREDTIS_ONLY'); ?></button>
					<button type="button" title="<?php echo JText::_('COM_EWALLET_PAY_SPNT_ONLY'); ?>" class="btn btn-info" id="pay_tab_change_de" onclick="change_table(this.id)"><?php echo JText::_('COM_EWALLET_PAY_SPNT_ONLY'); ?></button>


				</div>
			</div>
			<div class="span4 filter-search pull-right form-inline" id="month_filter">
				<?php
				echo JHTML::_('select.genericlist', $month,'month', 'name="filter_order" ', "value", "text",$this->lists['month']);
				echo JHTML::_('select.genericlist', $year,'year', 'name="filter_order" ', "value", "text",$this->lists['year']);
				?>
				<button type="button" name="go" title="<?php echo JText::_('COM_EWALLET_GO'); ?>" class="btn btn-success" id="go" onclick="this.form.submit();"><?php echo JText::_('COM_EWALLET_GO'); ?></button>
			</div>
		</div>
	</div-->

      	<table id="all_table" class="table table-condensed " >
		<thead>
			<tr>
				<th>
				   	<?php echo JHTML::tooltip(JText::_('COM_EWALLET_DATE_WISE_RECORD'), '','', JText::_('COM_EWALLET_DATE')); ?>
				</th>
				<th>
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAY_DONE'),'', '', JText::_('COM_EWALLET_PAY_DONE_DESCRIPTION_LBL')); ?>
				</th>
				<th>
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAYMENT_AMOUNT'), '', '',JText::sprintf('COM_EWALLET_PAYMENT', $params->get( "wallet_currency_nam" ) )); ?>
				</th>
				<th>
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_TOTAL_SPENT'),'', '', JText::sprintf('COM_EWALLET_TOTAL_SPENT',$params->get( "wallet_currency_nam" ) )); ?>
				</th>
				<th>
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_AMOUNT_DUE_REMAINING'), '', '', JText::sprintf('COM_EWALLET_AMOUNT_DUE',$params->get( "wallet_currency_nam" ))); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$balance = 0;
			//print_r($stat_info);
		if($stat_info ){
			foreach($stat_info as $key)
			{
				$comment = explode('|',$key->comment);
			?>
			<tr>
				<td style="width:15%">
					<?php $date = new DateTime($key->time);
					echo $date->format('m-d-Y');?>
				</td>
				<td>
				<?php

				if(count($comment) > 1){
					switch(count($comment))	//@JUGAD done
					{
						case 2:
							echo JText::sprintf($comment[0],$comment[1]);
						break;
						case 3:
							echo JText::sprintf($comment[0],$comment[1],$comment[2]);
						break;
						case 4:
							echo JText::sprintf($comment[0],$comment[1],$comment[2],$comment[3]);
						break;
					}
				}
				elseif($comment[0]=='COM_EWALLET_ADD_BALANCE')
				{
					echo JText::_($comment[0]);
				}
				else
				{
					echo JText::_($comment[0]);
				}
				?>
				</td>
				<td style="width:10%">
					<?php echo $key->credits; ?>
				</td>
				<td style="width:10%">
					<?php echo $key->spent; ?>
				</td>
				<td style="width:10%">
					<?php echo $key->balance; ?>
				</td>
			</tr>
			<?php
			}
		}else
			{
			?>
			<tr>
			<td colspan="5">
				<div class="well">
					<div class="alert alert-error">
						<span><?PHP echo JText::_("NO_RECORD_FOUND"); ?></span>
					</div>
				</div>
			</td>
		</tr>
			<?php
			}
			?>
		</tbody>
		<tfoot>
		<tr >
			<td colspan="5">
				<?php //echo $this->pagination->getListFooter(); ?>&nbsp;&nbsp;<?php //echo $this->pagination->getLimitBox(); ?>
			</td>
		</tr>
		</tfoot>
	</table>

	<table id="pay_table" class="table table-condensed " style="display:none">
		<thead>
			<tr>
				<th width="10%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_DATE_WISE_RECORD'), '','', JText::_('COM_EWALLET_DATE')); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAY_DONE'),'', '', JText::_('COM_EWALLET_PAY_DONE_DESCRIPTION_LBL')); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAYMENT_AMOUNT'), '', '', JText::sprintf('COM_EWALLET_PAYMENT', $params->get( "wallet_currency_nam" ) ) ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if($stat_info ){
			foreach($stat_info as $key)
			{
				$comment = explode('|',$key->comment);
				if(!empty($key->credits) && $key->credits!=0.00)
				{
				?>
			<tr>
				<td>
					<?php $date = new DateTime($key->time);
					echo $date->format('m-d-Y');?>
				</td>
				<td>
					<?php
				if(count($comment) > 1){
					switch(count($comment))	//@JUGAD done
					{
						case 2:
							echo JText::sprintf($comment[0],$comment[1]);
						break;
						case 3:
							echo JText::sprintf($comment[0],$comment[1],$comment[2]);
						break;
						case 4:
							echo JText::sprintf($comment[0],$comment[1],$comment[2],$comment[3]);
						break;
					}
				}
				elseif($comment[0]=='COM_EWALLET_ADD_BALANCE')
				{
					echo JText::_($comment[0]);
				}
				else
				{
					echo JText::_($comment[0]);
				}
				?>
				</td>
				<td>
					<?php echo $key->credits; ?>
				</td>
			</tr>
			<?php }

			}
		}else
			{
			?>
			<tr>
			<td colspan="5">
				<div class="well">
					<div class="alert alert-error">
						<span><?PHP echo JText::_("NO_RECORD_FOUND"); ?></span>
					</div>
				</div>
			</td>
		</tr>
			<?php
			}
			?>
		</tbody>
	</table>
<table id="spent_table" class="table table-condensed " style="display:none">
		<thead>
			<tr>
				<th width="10%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_DATE_WISE_RECORD'), '','', JText::_('COM_EWALLET_DATE')); ?>
				</th>
				<th width="20%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAY_DONE'),'', '', JText::_('COM_EWALLET_PAY_DONE_DESCRIPTION_LBL')); ?>
				</th>
				<th width="10%">
					<?php echo JHTML::tooltip(JText::_('COM_EWALLET_PAYMENT_AMOUNT'), '', '', JText::sprintf('COM_EWALLET_TOTAL_SPENT', $params->get( "wallet_currency_nam" ) ) ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if($stat_info ){
			foreach($stat_info as $key)
			{
				$comment = explode('|',$key->comment);
				if(!empty($key->spent) && $key->spent!=0.00)
				{
				?>
			<tr>
				<td>
					<?php $date = new DateTime($key->time);
					echo $date->format('m-d-Y');?>
				</td>
				<td>
						<?php
				if(count($comment) > 1){
					switch(count($comment))	//@JUGAD done
					{
						case 2:
							echo JText::sprintf($comment[0],$comment[1]);
						break;
						case 3:
							echo JText::sprintf($comment[0],$comment[1],$comment[2]);
						break;
						case 4:
							echo JText::sprintf($comment[0],$comment[1],$comment[2],$comment[3]);
						break;
					}
				}
				elseif($comment[0]=='COM_EWALLET_ADD_BALANCE')
				{
					echo JText::_($comment[0]);
				}
				else
				{
					echo JText::_($comment[0]);
				}
				?>
				</td>
				<td>
					<?php echo $key->spent; ?>
				</td>
			</tr>
			<?php
			}
		} }else
			{
			?>
			<tr>
			<td colspan="5">
				<div class="well">
					<div class="alert alert-error">
						<span><?PHP echo JText::_("NO_RECORD_FOUND"); ?></span>
					</div>
				</div>
			</td>
		</tr>
			<?php
			}
			?>
		</tbody>
	</table>


</div>
