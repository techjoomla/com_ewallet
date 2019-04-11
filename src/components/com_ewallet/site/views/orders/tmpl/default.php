<?php
defined( '_JEXEC' ) or die( ';)' );
?>
<?php
// 1.check user is logged or not
$user=JFactory::getUser();
if(!$user->id){
?>
<div class="techjoomla-bootstrap" >
<div class="well" >
	<div class="alert alert-error">
		<span ><?php echo JText::_('COM_EWALLET_ORDER_LOGIN'); ?> </span>
	</div>
</div>
</div><!-- eoc techjoomla-bootstrap -->
<?php
	return false;
}

//JHTML::_('behavior.tooltip');
$params = JComponentHelper::getParams( 'com_ewallet' );
$db=JFactory::getDBO();
$result=$this->orders;
//print_r($result);die();
$orders_site=( isset($this->orders_site) )?$this->orders_site:0;
$Itemid=( isset($this->Itemid) )?$this->Itemid:0;
$vendor_order_view=(!empty($this->store_id))?1:0;

$document = JFactory::getDocument();
$document->addScript(JURI::root().'components/com_ewallet/assets/js/ewallet.js'); 

$document->addStyleSheet(JURI::base().'components/com_ewallet/assets/css/ewallet.css' );

$totalamount=0;
?>
<script type="text/javascript">
function submitbutton( action ) {
	if(action=='deleteorders')
	{
		if (document.adminForm.boxchecked.value==0){
			alert('<?php echo JText::_("COM_EWALLET_MAKE_SEL");?>');
			return;}
		
		var r=confirm('<?php echo JText::_("COM_EWALLET_DELETE_CONFIRM");?>');
		if (r==true)		
		{
			var aa;
		}
		else return;
		
	}
	var form = document.adminForm;
	submitform( action );
	return;	
}


</script>

<style type="text/css">
 .pagination a{
   text-decoration:none;
 }
</style>

<div class="techjoomla-bootstrap" >
<form  name="adminForm" id="adminForm" class="form-validate" method="post">
	<?php
	/*$app =JFactory::getApplication();
	if ($app->isAdmin())
	{
// @ sice version 3.0 Jhtmlsidebar for menu
    if(JVERSION>=3.0):
         if (!empty( $this->sidebar)) : ?>
            <div id="j-sidebar-container" class="span2">
                <?php echo $this->sidebar; ?>
            </div>
            <div id="j-main-container" class="span10">
        <?php else : ?>
            <div id="j-main-container">
        <?php endif;
    endif;
	}*/
    ?>

	
<?php if($orders_site) {	
?>	
		<legend><?php echo JText::_('COM_EWALLET_MYORDERS')?></legend>
<?php } ?>

	<div  class="row-fluid">
		<div class="table-tool">
			<div class="span5">
				<div class="filter-search pull-left">
					<?php JHTML::_('behavior.tooltip'); echo JText::_( 'COM_EWALLET_FILTER' ); ?>:
					<input type="text" name="search_list" style="margin-right:5px;" id="search_list" value="<?php echo $this->lists['search_list']; ?>" class="input-small" placeholder="<?php echo JText::_('COM_EWALLET_FILTER_SEARCH_DESC'); ?>" onchange="document.adminForm.submit();" />
					
				</div>
				<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" onclick="this.form.submit();" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.getElementById('search_list').value='';this.form.submit();"  title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			</div>
			<div class="span7">
				<span class="btn-group pull-right hidden-phone">
					<?php
					if(version_compare(JVERSION, '3.0', 'ge'))
					{ 
					 echo $this->pagination->getLimitBox(); 
					} ?>
				</span>
				<?php
				//FILTER FOR J3.0
				
				if(!empty($orders_site))
				{/// view called from front end then show any way
					?>
					<span class="btn-group pull-right hidden-phone qtc_putmarginright">
						<?php 
						echo JHTML::_('select.genericlist', $this->sstatus, "search_select", 'class="ad-status" size="1" onchange="document.adminForm.submit();" name="search_select"',"value", "text", $this->lists['search_select']);
						?>
					</span>
					<?php
				}
				else
				{
					// CALLED FROM BACKEND THEN CK J3.0 THEN DONT SHOW
					if(version_compare(JVERSION, '3.0.0', 'lt'))
					{
					?>
					<span class="btn-group pull-right hidden-phone qtc_putmarginright">
						<?php 
						echo JHTML::_('select.genericlist', $this->sstatus, "search_select", 'class="ad-status" size="1" onchange="document.adminForm.submit();" name="search_select"',"value", "text", $this->lists['search_select']);
						?>
					</span>
					<?php
					}
				}?>
			</div>
		</div>
	</div>
	<hr class="hr-condensed qtc_dashboard_hr" />
	<table class="table table-striped table-condensed" >
		<thead>
		<tr>
		<?php if(!$orders_site){ ?>
			<th width="2%" align="center" class="title">
				<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($result)+1; ?>);" />
			</th>		
				<?php } ?>
			<th width="20%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_EWALLET_ORDER_ID'),'id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="20%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_EWALLET_USERNAME'),'payee_id', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="20%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_EWALLET_ORDER_STATUS'),'status', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="25%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_EWALLET_ORDER_DATE'),'cdate', $this->lists['order_Dir'], $this->lists['order']); ?></th>
			<th width="20%"><?php echo JHTML::_( 'grid.sort', JText::_('COM_EWALLET_AMOUNT'),'amount', $this->lists['order_Dir'], $this->lists['order']); ?></th>
		</tr>
	</thead>
	<?php
	if(!empty($result))
	{	
		$id=1;
		foreach($result as $orders) { ?>
		<tr class="row0">
		<?php if(!$orders_site){ ?>
				<td align="center">
					<?php echo JHTML::_('grid.id', $id, $orders->id ); ?>
				</td>
		<?php } ?>
			<td>
				<?php echo $orders->prefix.$orders->id; ?>
			</td>
			<td>
				<?php 
			$table   = JUser::getTable();
			$user_id = intval( $orders->payee_id );
			if($user_id){
				$creaternm = '';
				if($table->load( $user_id ))
				{			
					$creaternm = JFactory::getUser($orders->payee_id);
				}
//			 print_r($orders->ad_creator);
			 	echo (!$creaternm)?JText::_('COM_EWALLET_NO_USER'): $creaternm->name; 
			}
			else{
				echo $orders->email;
			}
			 ?>
			</td>
			<td class="qtc_pending_action" >
				<?php
				// CODE START FOR ORDER STATUS || STATUS SELECT BOX START
				$whichever = '';
				switch($orders->status)
				{
					case 'C' :
						$whichever =  JText::_('COM_EWALLET_CONFR');
					break;
					case 'RF' :
						$whichever = JText::_('COM_EWALLET_REFUN') ;
					break;
					case 'E' :
						$whichever = JText::_('COM_EWALLET_ERR') ;
					break;
					case 'P' :
					if($orders_site) {
						$whichever = JText::_('COM_EWALLET_PENDIN') ;
					}
					break;
					default:
					$whichever = $orders->status;
					break;
				}
				if(  !($orders_site)) // admin side
				{
					echo JHTML::_('select.genericlist',$this->pstatus,"pstatus",'class="pad_status span7" size="1" onChange="selectstatusorder('.$orders->id.',this);"',"value","text",$orders->status,'pstatus'.$orders->id);
				}
				else{
					echo $whichever ;
				}
				 // CODE END FOR ORDER STATUS || STATUS SELECT BOX END
				 ?>
			</td>
			<td>
				<?php echo $orders->cdate; ?>
			</td>
			<!-- Order price-->
			<td>
				<span>
					<span><?php
                    $helperobj=new comewalletHelper;
         $orders_amount_format = $helperobj->getFromattedPrice($orders->amount);
                     echo $orders_amount_format; ?></span>&nbsp;<?php //echo $orders->currency ?>
				</span>
				<?php $totalamount=$totalamount+$orders->amount; ?>
			</td>
		</tr>
		<?php } // end of foreach
		}  // end of !empty (result)
		else{
			?>
		<tr>
			<td colspan="6">
				<div class="well" >
					<div class="alert alert-error">
						<span ><?php echo JText::_('COM_EWALLET_NO_ORDERS'); ?> </span>
					</div>
				</div>
			</td>
		</tr>
		<?php
		}
		?>

<?php if($orders_site) { ?>
		<!-- Total amount of all orders-->
		<tr>
			<td colspan="3"></td>
			<td align="center"><b><?php echo JText::_('COM_EWALLET_TOTAL'); ?></b></td>
			<td><span><b><?php echo number_format($totalamount,2);?></b></span></td>
		</tr>
<?php } ?>
		<tr>
			<td colspan="6">
				<div class="pager">
					<?php 
					// ffooter list
					echo $this->pagination->getListFooter(); 
					?>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="option" value="com_ewallet" />
	<input type="hidden" id='hidid' name="id" value="" />
	<input type="hidden" id='hidstat' name="status" value="" />
	<input type="hidden" name="task" id="task" value="" />
	<input type="hidden" name="view" value="orders" />
	<input type="hidden" name="controller" value="orders" />
	<input type="hidden" name="boxchecked" value="0" />	
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

</div><!-- eoc techjoomla-bootstrap -->
