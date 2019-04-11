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
            	<div class="span12">
					<div class="well">
                   	<?php
						echo "<div><strong>".JText::_('COM_EWALLET_TOTAL_ORDER_REPORT')." ".$this->currency."</strong></div>";
							//draw chart

							if($this->OrdersArray)
							{
								?>
								<script type="text/javascript">
									google.load("visualization", "1", {packages:["corechart"]});
									google.setOnLoadCallback(drawChart);
									function drawChart() {
										var data = google.visualization.arrayToDataTable([
											['<?php echo JText::_("COM_EWALLET_ORDERSDATA");?>', '<?php echo JText::_("COM_EWALLET_ORDERSDATA");?>'],
											['<?php echo JText::_("EWALLET_PSTATUS_PENDING");?>',<?php echo $this->OrdersArray['P'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_COMPLETED");?>',<?php echo $this->OrdersArray['C'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_DECLINED");?>',<?php echo $this->OrdersArray['D'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_REFUNDED");?>',<?php echo $this->OrdersArray['RF'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_FAILED");?>',<?php echo $this->OrdersArray['F'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_REVERSED");?>',<?php echo $this->OrdersArray['RV'];?>],
											['<?php echo JText::_("EWALLET_PSTATUS_CANCEL_REVERSED");?>',<?php echo $this->OrdersArray['CRV'];?>],

										]);
										var options = {
											title:'<?php echo JText::_("COM_EWALLET_ORDERSDATA")."  ".$this->currency;?>',
											slices: {

												0: {color:'#DF0101'},
												1: {color:'#01DF74'},


											},
											backgroundColor:'transparent'
										};
										var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
										chart.draw(data, options);
									}
								</script>
								<div id="chart_div2" style="width:100%;height:150px;"></div>
								<?php
							}
							else{
								echo '<div><strong>'.JText::_('COM_EWALLET_TOTAL_ORDER_REPORT').'</strong></div>';
								echo '<div class="alert alert-warning">'.JText::_('COM_EWALLET_NO_DATA_FOUND').'</div>';
							}
							?>
                   </div><!--well-->
					</div><!--span12-->
				</div><!--rowfluid-->
                	<div class="row-fluid">
				<div class="span12" >
					<div class="well">
						<?php
						//draw chart
						if($this->ticketSalesLastweek)
						{
						?>
							<script type="text/javascript">
								google.load("visualization", "1", {packages:["corechart"]});
								google.setOnLoadCallback(drawChart);
								function drawChart() {
									var data = google.visualization.arrayToDataTable([
										['<?php echo JText::_("COM_EWALLET_DATE");?>', '<?php echo JText::_("COM_EWALLET_WEEK_PER_DAY_COMPATED_ORDERS");?>'],
										<?php
										foreach($this->ticketSalesLastweek as $mpd){
											echo "['".$mpd->date."',".$mpd->count."],";
										}
										?>
									]);
									var options = {
										title: '<?php echo JText::_("COM_EWALLET_WEEK_PER_DAY_COMPATED_ORDERS");?>',
										vAxis: {title:'<?php echo JText::_("COM_EWALLET_WEEK_PER_DAY_COMPATED_ORDERS");?>'},
										hAxis: {title:'<?php echo JText::_("COM_EWALLET_DATE");?>'},
										backgroundColor:'transparent'
									};
									var chart = new google.visualization.LineChart(document.getElementById('chart_div4'));
									chart.draw(data, options);
								}
							</script>
							<div id="chart_div4" style="width:auto;height:350px;"></div>
						<?php
						}
						else{
							echo '<div><strong>'.JText::_('COM_EWALLET_WEEK_PER_DAY_CNT').'</strong></div>';
							echo '<div class="alert alert-warning">'.JText::_('COM_EWALLET_NO_DATA_FOUND').'</div>';
						}
						?>
					</div><!--well-->
				</div><!--span12-->
			</div><!--rowfluid-->
	  	<div class="row-fluid">
				<div class="span12">
					<div class="well">

						<?php
						echo $title = "<p><strong>".JText::_('MONTHLY_ORDERS_INCOME')."&nbsp;".JHTML::tooltip(JText::_('MONTHLY_ORDERS_INCOME_DESC') . '.', JText::_('MONTHLY_ORDERS_INCOME'))."</strong></p>" ;
						echo $data = "<p>".$this->allincome."&nbsp;".$this->currency."</p>";
						echo $text = "<div id=\"monthin\" style=\"text-align: center; font-size: 16px; font-weight: bold;\"> </div>";
						?>
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
									 {title:'<?php echo JText::_("MONTHLY_INCOME_MONTH");?>',
									  width:'48%', height:300,
									  fontSize:'13px',
									  hAxis: {title: "<?php echo JText::_('BAR_CHART_HAXIS_TITLE');?>"},
									  vAxis: {title: "<?php echo JText::_('BAR_CHART_VAXIS_TITLE').' ('.$this->currency.')';?>"}

									  }
								);
							}
							</script>
					</div><!--well-->
				</div><!--span6-->
			</div><!--rowfluid-->
		</div><!--span8-->

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

</div><!--END techjoomla-bootstrap-->
