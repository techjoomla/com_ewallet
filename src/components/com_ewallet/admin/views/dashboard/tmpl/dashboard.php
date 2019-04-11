<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( ';)' );
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');

$params = JComponentHelper::getParams( 'com_ewallet' );
$db=JFactory::getDBO();
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_ewallet/assets/css/ewallet.css');

$model=$this->getModel('dashboard');
$mntnm_cnt=1;
$i=0;
$helperobj=new comewalletHelper;
 $model=$this->getModel('dashboard');   
 $backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));
 $session = JFactory::getSession();
$session->set('tj_from_date','');
$session->set('tj_end_date', '');

$session->set('statsforpie', '');
$session->set('ignorecnt', '');
$session->set('statsfor_line_day_str_final', '');
$session->set('statsfor_line_imprs', '');
$session->set('statsfor_line_clicks', '');
$session->set('periodicorderscount', '');
 foreach($this->AllMonthName as $AllMonthName)
{
	$AllMonthName_final[$i]=$AllMonthName['month'];
	$curr_MON=$AllMonthName['month'];
	$month_amt_val[$curr_MON]=0;
		$i++;

}

$emptybarchart=1;
foreach($this->MonthIncome as $MonthIncome)
{
	$month_year='';
	 $month_year=$MonthIncome->YEARNM;
	$month_name=$MonthIncome->MONTHSNAME;

	$month_int = (int)$month_name;
	$timestamp = mktime(0, 0, 0, $month_int);
	$curr_month=date("F", $timestamp);
	foreach($this->AllMonthName as $AllMonthName)
	{

	if(($curr_month==$AllMonthName['month']) and ($MonthIncome->amount) and ($month_year==$AllMonthName['year']))
	$month_amt_val[$curr_month]=str_replace(",",'',$MonthIncome->amount);

	if($MonthIncome->amount)
	$emptybarchart=0;
	else
	$emptybarchart=1;


	}

}        
//echo $emptybarchart;die;
 $month_amt_str=implode(",",$month_amt_val);
 $month_name_str=implode("','",$AllMonthName_final);
 $month_name_str="'".$month_name_str."'";
 $month_array_name=array();
 /////////////////////
  $js = "
  
  var linechart_imprs;
	var linechart_clicks;
	var linechart_day_str=new Array();
  
  function refreshViews()
	{
		fromDate = document.getElementById('from').value; 
		toDate = document.getElementById('to').value; 
		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());		
		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));
		if(parseInt(days)<=0)
		{
			alert('".JText::_('DATELESS')."');
			return;
		
		}
		//Set Session Variables
		//techjoomla.jQuery(document).ready(function(){
		var info = {};
		techjoomla.jQuery.ajax({
		    type: 'GET',
		    url: '?option=com_ewallet&controller=dashboard&task=SetsessionForGraph&fromDate='+fromDate+'&toDate='+toDate,
		    dataType: 'json',
		    async:false,
		  
		    success: function(data) {
		    		//window.location.reload();
		    }
		});
		//Make Chart and Get Data
		techjoomla.jQuery.ajax({
		    type: 'GET',
		    url: '?option=com_ewallet&controller=dashboard&task=makechart',
		    async:false,
		    dataType: 'json',
		     beforeSend: function() {
		    techjoomla.jQuery('#order_summary_tab_table_html').html('');
			techjoomla.jQuery('#confirm-order').html('<div class=\"com_ewallet_ajax_loading center\"><div class=\"com_ewallet_ajax_loading_text\">".JText::_('COM_EWALLET_LOADING_PAYMET_FORM_MSG')."</div><img class=\"com_ewallet_ajax_loading_img\" src=\"".JURI::ROOT()."components/com_ewallet/assets/images/ajax.gif\"></div>');
		},
		complete: function() {
			techjoomla.jQuery('.com_ewallet_ajax_loading').remove();
		},
		    success: function(data) {
		  
			techjoomla.jQuery('#bar_chart_graph').html(''+data.barchart);
			
				document.getElementById('pending_orders').value=data.pending_orders;
				document.getElementById('confirmed_orders').value=data.confirmed_orders;
				document.getElementById('shiped_orders').value=data.shiped_orders;
				document.getElementById('refund_orders').value=data.refund_orders;
				document.getElementById('periodic_orders').innerHTML = data.periodicorderscount;
						
			google.setOnLoadCallback(drawPieChart);
			drawPieChart();
		
		    }
		});
		
	//	});
	}
	";
  $document->addScriptDeclaration($js);


?>
	<?php
	if(version_compare(JVERSION, '3.0', 'lt')) {
			$qtc_dashboard_style="qtc_dashboard_graph25";
		}
		else
		{
			$qtc_dashboard_style="qtc_dashboard_graph30";
		}
		?>
		
<script type="text/javascript">

	function vercheck()
	{
		callXML('<?php echo $this->currentVersion; ?>');
		if(document.getElementById('newVersionChild').innerHTML.length<220)
		{
			document.getElementById('newVersionChild').style.display='inline';
		}
	}

	function callXML(currversion)
	{
		if (window.XMLHttpRequest)
			{
		 	 xhttp=new XMLHttpRequest();
			}
		else // Internet Explorer 5/6
			{
		 	xhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}

	xhttp.open("GET","<?php echo JURI::base(); ?>index.php?option=com_quick2cart&task=getVersion",false);
	xhttp.send("");
	latestver=xhttp.responseText;

	if(latestver!=null)
	  {
		if(currversion == latestver)
		{
			document.getElementById('newVersionChild').innerHTML='<span style="display:inline; color:#339F1D;">&nbsp;<?php echo JText::_("COM_QUICK2CART_LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
		else
		{
			document.getElementById('newVersionChild').innerHTML='<span style="display:inline; color:#FF0000;">&nbsp;<?php echo JText::_("COM_QUICK2CART_LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
	  }
     }
     function addCustomElement(basepath)
     {
		 var appname=techjoomla.jQuery("#selectzooapps").val();
		 //alert(appname);
		if(appname)	
		{
			var apppath=basepath+'<?php echo DS ?>'+appname+'<?php echo DS ?>'+'application.xml';
			techjoomla.jQuery.ajax({
			url: '?option=com_quick2cart&controller=cp&task=addCustomElement&filepath='+apppath+'&tmpl=component&format=raw',
			type: 'POST',
			success: function(msg)
			{
				//window.location.reload();
				alert("<?php echo JText::_('QTC_TAG_SAVED_MSG'); ?>  "+appname);                                                                                                                          
			}
			});
		}
			
	  }
</script>
<script type="text/javascript">

	function vercheck()
	{
		callXML('<?php echo $this->currentVersion; ?>');
		if(document.getElementById('newVersionChild').innerHTML.length<220)
		{
			document.getElementById('newVersionChild').style.display='inline';
		}
	}

	function callXML(currversion)
	{
		if (window.XMLHttpRequest)
			{
		 	 xhttp=new XMLHttpRequest();
			}
		else // Internet Explorer 5/6
			{
		 	xhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}

	xhttp.open("GET","<?php echo JURI::base(); ?>index.php?option=com_ewallet&task=getVersion",false);
	xhttp.send("");
	latestver=xhttp.responseText;

	if(latestver!=null)
	  {
		if(currversion == latestver)
		{
			document.getElementById('newVersionChild').innerHTML='<span style="display:inline; color:#339F1D;">&nbsp;<?php echo JText::_("COM_EWALLET_LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
		else
		{
			document.getElementById('newVersionChild').innerHTML='<span style="display:inline; color:#FF0000;">&nbsp;<?php echo JText::_("COM_EWALLET_LAT_VERSION");?> <b>'+latestver+'</b></span>';
		}
	  }
     }

</script>
<!--load google chart js-api-->
<script type="text/javascript" src="https://www.google.com/jsapi"></script>

<div class="techjoomla-bootstrap"><!--START techjoomla-bootstrap-->
<form  name="adminForm" id="adminForm" class="form-validate" method="post">
<?php

if(JVERSION>=3.0):

	if(empty($this->sidebar)): ?>
	<div id="sidebar">
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>

	</div>

		<div id="j-main-container" class="span10">

	<?php else : ?>
		<div id="j-main-container">
	<?php endif;
endif;

?>
</form>
		<div class="row-fluid">
		<!-- All GRAPHS STARTS	-->
		<div class="span8">
			
			
			<!-- START :: BART CHART -->
			<div class="row-fluid">
				<div class="span12" style="width:99%;height:">
					<div class="well">
						
						
						<?php 
							if(!$this->allincome)
									$this->allincome=0;
									
							$title = "<div class=\"qtc_marginleft\">". JHTML::tooltip(JText::_('ALL_TIME_INCOME_DESC'), JText::_('ALL_TIME_INCOME'), 
											'', JText::_('ALL_TIME_INCOME'))."</div>";
							$text = "<div id=\"ordersTotalLife\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"> "."<span style=\"margin-left: 5px;\">".$helperobj->getFromattedPrice($this->allincome)."</span></div>";	
							echo $html = $model->getbox($title,$text); ?>
							<hr class="hr-condensed qtc_dashboard_hr">
	
							<script type="text/javascript" src="http://www.google.com/jsapi"></script>
							<script type="text/javascript">

							// Load the Visualization API and the piechart package.
							google.load('visualization', '1', {'packages':['corechart']});

							// Set a callback to run when the Google Visualization API is loaded.
							google.setOnLoadCallback(drawChart);
							// Create and populate the data table.
							function drawChart() {

							<?php if(!$this->allincome) {?>

							document.getElementById("monthin").innerHTML='<?php  echo '<h5>'.JText::_("NO_STATS").'<h5>'; ?>';
							return;
							<?php } ?>
							var data = new google.visualization.DataTable();


							var raw_dt1=[<?php echo $month_amt_str;?>];
							var raw_data = [raw_dt1];
							var Months = [<?php echo $month_name_str;?>];
							data.addColumn("string", "<?php echo JText::_('BAR_CHART_HAXIS_TITLE');?>");
							data.addColumn("number","<?php echo JText::_('BAR_CHART_VAXIS_TITLE').' ('.$this->currency.')';?>");
							data.addRows(Months.length);

							for (var j = 0; j < Months.length; ++j) {
							  data.setValue(j, 0, Months[j].toString());
							}
							for (var i = 0; i  < raw_data.length; ++i) {
							  for (var j = 1; j  <=(raw_data[i].length); ++j) {
								data.setValue(j-1, i+1, raw_data[i][j-1]);

							  }
							}


							// Create and draw the visualization.
							new google.visualization.ColumnChart(document.getElementById("monthin")).
								draw(data,
									 {
										//title:'<?php echo JText::_("MONTHLY_INCOME_MONTH");?>',
									  fontSize:'13px',
												backgroundColor: 'transparent',
									  hAxis: {title: "<?php echo JText::_('BAR_CHART_HAXIS_TITLE');?>"},
									  vAxis: {title: "<?php echo JText::_('BAR_CHART_VAXIS_TITLE').' ('.$this->currency.')';?>"}

									  }
								);
							}
							</script>
							<?php
								if(version_compare(JVERSION, '3.0', 'lt')) {
										$qtc_dashboard_style="qtc_dashboard_graph25";
									}
									else
									{
										$qtc_dashboard_style="qtc_dashboard_graph30";
									}
									$title = "<div class=\"qtc_marginleft\"><span id=\"totalOrdersLifeTitle\" style=\"float: left;\">". JHTML::tooltip(JText::_('MONTHLY_ORDERS_INCOME_DESC'), JText::_('MONTHLY_INCOME_MONTH'), 
                    '', JText::_('MONTHLY_INCOME_MONTH'))."</span></div>";
								$text = "<div class=\"".$qtc_dashboard_style."\" id=\"monthin\" style=\"text-align: center; font-size: 16px; font-weight: bold;width: auto; height: 250px; position: relative;\"> </div>"; 
								
								echo $html = $model->getbox($title,$text);
							?>
							
					</div> <!-- well end-->
				</div>
			</div>
			<!-- END	 :: BART CHART -->
			<div class="row-fluid">
				<!-- 	PERIODIC INCOME -->
				<div class="span12" id="pie">
			
					<div class="well">
						<!-- CALENDER ND REFRESH BTN  -->
						<div class="form-inline">
							<label class=""><?php  echo JText::_('FROM_DATE'); ?>  </label>
							<?php echo JHTML::_('calendar', $backdate, 'fromDate', 'from', '%Y-%m-%d', array('class'=>'inputbox input-mini')); ?>
							<label class=""><?php  echo JText::_('TO_DATE'); ?>  </label>
								 <?php echo JHTML::_('calendar', date('Y-m-d'), 'toDate', 'to', '%Y-%m-%d', array('class'=>'inputbox input-mini')); ?>
								<input id="btnRefresh" class="btn  btn-mini btn-primary" type="button" value=">>" style="font-weight: bold;" onclick="refreshViews();"/>
							 
						</div>
							
						<!--END::CALENDER ND REFRESH BTN  -->
					<br/>
						<?php
 						if(!$this->tot_periodicorderscount)
						{
							$this->tot_periodicorderscount=0;
						}
						$title = "<div class=\"qtc_marginleft\" >
												". JHTML::tooltip(JText::_('PERIODIC_INCOME_DESC'), JText::_('PERIODIC_INCOME'), 
                    '', JText::_('PERIODIC_INCOME'))."
											</div>";
						$text = "<div id=\"periodic_orders\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"><span style=\"margin-left: 5px;\">".$helperobj->getFromattedPrice($this->tot_periodicorderscount)."</span></div>"; 
						
						echo $html = $model->getbox($title,$text); 
						?>	
						<hr class="hr-condensed qtc_dashboard_hr">
						<?php
						// PIE CHART CODE STARTS
						$statsforpie = $this->statsforpie;	
						//print_r($statsforpie );
						$currentmonth='';
						$pending_orders=$confirmed_orders=$shiped_orders=$refund_orders=0;
						if(empty($statsforpie[0][0]) && empty($statsforpie[1][0]) && empty($statsforpie[2][0])&& empty($statsforpie[3][0]))
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
						 // echo "clk=";echo $clicks;
							if(!empty($statsforpie[1]))
							{
								$confirmed_orders = $statsforpie[1][0]->orders;
								$shiped_orders = $statsforpie[3][0]->orders;
							}
							if(!empty($statsforpie[1]))
							{
								$refund_orders = $statsforpie[2][0]->orders;
							}
							if(!empty($statsforpie[1]))
							{
								$cancel_orders = $statsforpie[4][0]->orders;
							}
						}

						$emptypiechart=0;
						if(!$pending_orders and !$confirmed_orders and !$refund_orders and !$shiped_orders)
						$emptypiechart=1;
						
						 ?>
				
						<script type="text/javascript" src="http://www.google.com/jsapi"></script>
								<script type="text/javascript">
							
								// Load the Visualization API and the piechart package.
								//google.load('visualization', '1', {'packages':['piechart']});

								// Set a callback to run when the Google Visualization API is loaded.
								google.setOnLoadCallback(drawPieChart);

								// Callback that creates and populates a data table,
								// instantiates the pie chart, passes in the data and
								// draws it.
								function drawPieChart() {
									var pending_orders=0;
									var confirmed_orders=0;
									var shiped_orders=0;
									var refund_orders=0;
									var cancel_orders=0;				
									pending_orders=parseInt(document.getElementById("pending_orders").value);
									confirmed_orders=parseInt(document.getElementById("confirmed_orders").value);
									shiped_orders=parseInt(document.getElementById("shiped_orders").value);				
									refund_orders=parseInt(document.getElementById("refund_orders").value);	
									cancel_orders=parseInt(document.getElementById("cancel_orders").value);	
									<?php if($emptypiechart) {?>

											document.getElementById("chart_div").innerHTML='<?php  echo '<h5>'.JText::_("NO_STATS").'<h5>'; ?>';
											return;
									<?php } ?>
										
									
								// Create our data table.
									var data = new google.visualization.DataTable();
									data.addColumn('string', 'Event');
									data.addColumn('number', 'Amount');
									data.addRows([
										
										['<?php echo JText::_("PENDING_ORDS");?>',pending_orders],
										['<?php echo JText::_("CONFIRM_ORDS");?>',confirmed_orders],
										['<?php echo JText::_("SHIPPED_ORDS");?>',shiped_orders],					
										['<?php echo JText::_("REFUND_ORDS");?>',refund_orders],
										['<?php echo JText::_("CENCEL_ORDS");?>',refund_orders]
									]);
									//	width:"575",
									var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
									chart.draw(data, { 
											width:"700",
											height:"300",
												backgroundColor: 'transparent',
												is3D:true,fontSize:'10px',colors: ['#FE2E2E','#04B45F','#FF8000','#0174DF'], 
												// title: '<?php echo JText::_("PERIODIC_ORDERS").' '.$currentmonth;?> '
												 });
								}
								</script>
								
						<div id="qtc_piechart1" style="width: 100%;">
	
							<?php
							$title = "<div class=\"qtc_marginleft\">
							<span id=\"totalOrdersLifeTitle\" style=\"float: left;\">". JHTML::tooltip(JText::_('PERIODIC_ORDERS_DESC'), JText::_('PERIODIC_ORDERS'), 
                    '', JText::_('PERIODIC_ORDERS'))."</span></div>";
							$text = "<div class=\"".$qtc_dashboard_style."\"  id=\"chart_div\" style=\"width:100%;text-align: center; font-size: 16px; font-weight: bold;\"></div>"; 
							///width: 500px; height: 500px;
							echo $html = $model->getbox($title,$text); ?>
		<div id="confirm-order">
									<div id="order_summary_tab_table_html"> </div>
											</div>	
						</div><!--End container_right2-->
					</div><!--END well -->
				</div><!--END SPAN6 -->
				
			
					
			</div>
		</div>
		<!--INFO,HELP + ETC START -->
		<div class="span4">
			<div class="well well-small">
				<div class="module-title nav-header">
					<?php
					if(JVERSION >= '3.0')
						echo '<i class="icon-comments-2"></i>';
					else
						echo '<i class="icon-comment"></i>';
					?> <strong><?php echo JText::_('COM_EWALLET_NAME'); ?></strong>
				</div>
				<hr class="hr-condensed"/>

				<div class="row-fluid">
					<div class="span12 alert alert-success"><?php echo JText::_('COM_EWALLET_ABOUT1'); ?></div>
				</div>

				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right"><span class="label label-info"><?php echo JText::_('COM_EWALLET_LINKS'); ?></span></p>
					</div>
				</div>

				<div class="row-striped">
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com" target="_blank"><i class="icon-file"></i> <?php echo JText::_('COM_EWALLET_DOCS');?></a>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-help"></i>';
								else
									echo '<i class="icon-question-sign"></i>';
								?>
								<?php echo JText::_('COM_EWALLET_FAQS');?>
							</a>
						</div>
					</div>
					<!--
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com/jbolo.-chat-for-cb-jomsocial-joomla/feed/rss.html" target="_blank">
								<?php
								/*if(JVERSION >= '3.0')
									echo '<i class="icon-feed"></i>';
								else
									echo '<i class="icon-bell"></i>';
								?> <?php echo JText::_('COM_EWALLET_RSS');
								 */?></a>
						</div>
					</div>
					-->
					<div class="row-fluid">
						<div class="span12">
							<a href="http://techjoomla.com/index.php?option=com_billets&view=tickets&layout=form&Itemid=18" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-support"></i>';
								else
									echo '<i class="icon-user"></i>';
								?> <?php echo JText::_('COM_EWALLET_TECHJOOMLA_SUPPORT_CENTER'); ?></a>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<a href="http://extensions.joomla.org/extensions/communication/instant-messaging/9344" target="_blank">
								<?php
								if(JVERSION >= '3.0')
									echo '<i class="icon-quote"></i>';
								else
									echo '<i class="icon-bullhorn"></i>';
								?> <?php echo JText::_('COM_EWALLET_LEAVE_JED_FEEDBACK'); ?></a>
						</div>
					</div>
				</div>

				<br/>
				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right">
							<span class="label label-warning"><?php echo JText::_('COM_EWALLET_CHECK_LATEST_VERSION'); ?></span>
						</p>
					</div>
				</div>

				<div class="row-striped">
					<div class="row-fluid">
						<div class="span6"><?php echo JText::_('COM_EWALLET_HAVE_INSTALLED_VER'); ?></div>
						<div class="span6"><?php echo $this->currentVersion; ?></div>
					</div>

					<div class="row-fluid">
						<div class="span6">
							<button class="btn btn-small" type="button" onclick="vercheck();"><?php echo JText::_('COM_EWALLET_CHECK_LATEST_VERSION');?></button>
						</div>
						<div class="span6" id='newVersionChild'></div>
					</div>
				</div>

				<br/>
				<div class="row-fluid">
					<div class="span12">
						<p class="pull-right">
							<span class="label label-info"><?php echo JText::_('COM_EWALLET_STAY_TUNNED'); ?></span>
						</p>
					</div>
				</div>

				<div class="row-striped">
					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_EWALLET_FACEBOOK'); ?></div>
						<div class="span8">
							<!-- facebook button code -->
							<div id="fb-root"></div>
							<script>(function(d, s, id) {
							  var js, fjs = d.getElementsByTagName(s)[0];
							  if (d.getElementById(id)) return;
							  js = d.createElement(s); js.id = id;
							  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							  fjs.parentNode.insertBefore(js, fjs);
							}(document, 'script', 'facebook-jssdk'));</script>
							<div class="fb-like" data-href="https://www.facebook.com/techjoomla" data-send="true" data-layout="button_count" data-width="250" data-show-faces="false" data-font="verdana"></div>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_EWALLET_TWITTER'); ?></div>
						<div class="span8">
							<!-- twitter button code -->
							<a href="https://twitter.com/techjoomla" class="twitter-follow-button" data-show-count="false">Follow @techjoomla</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
						</div>
					</div>

					<div class="row-fluid">
						<div class="span4"><?php echo JText::_('COM_EWALLET_GPLUS'); ?></div>
						<div class="span8">
							<!-- Place this tag where you want the +1 button to render. -->
							<div class="g-plusone" data-annotation="inline" data-width="300" data-href="https://plus.google.com/102908017252609853905"></div>
							<!-- Place this tag after the last +1 button tag. -->
							<script type="text/javascript">
							(function() {
							var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
							})();
							</script>
						</div>
					</div>
				</div>

				<br/>
				<div class="row-fluid">
					<div class="span12 center">
						<?php
						$logo_path='<img src="'.JURI::base().'components/com_ewallet/assets/images/techjoomla.png" alt="TechJoomla" class="jbolo_vertical_align_top"/>';
						?>
						<a href='http://techjoomla.com/' target='_blank'>
							<?php echo $logo_path;?>
						</a>
						<p><?php echo JText::_('COM_EWALLET_COPYRIGHT'); ?></p>
					</div>
				</div>
			</div>
		</div><!--END span4 -->
	</div><!--END outermost row-fluid -->
<!-- Extra code for zone -->
 	 <input type="hidden" name="pending_orders" id="pending_orders" value="<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>">
 	 <input type="hidden" name="confirmed_orders" id="confirmed_orders" value="<?php if($confirmed_orders) echo $confirmed_orders; else echo '0';  ?>">
 	 <input type="hidden" name="shiped_orders" id="shiped_orders" value="<?php if($shiped_orders) echo $shiped_orders; else echo '0';  ?>"> 	 
 	  <input type="hidden" name="refund_orders" id="refund_orders" value="<?php if($refund_orders) echo $refund_orders; else echo '0'; ?>">
 	  <input type="hidden" name="cancel_orders" id="cancel_orders" value="<?php if($cancel_orders) echo $cancel_orders; else echo '0'; ?>">
 	  <!-- Extra code for zone -->
			
<script type="text/javascript">
			techjoomla.jQuery(document).ready(function(){
				document.getElementById("pending_orders").value=<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>;
				document.getElementById("confirmed_orders").value=<?php if($confirmed_orders) echo $confirmed_orders; else echo '0'; ?>;
				document.getElementById("shiped_orders").value=<?php if($shiped_orders) echo $shiped_orders; else echo '0'; ?>;				
				document.getElementById("refund_orders").value=<?php  if($refund_orders) echo $refund_orders; else echo '0'; ?>;
				document.getElementById("cancel_orders").value=<?php  if($cancel_orders) echo $cancel_orders; else echo '0'; ?>;
			});
</script>
</div><!--END techjoomla-bootstrap-->
