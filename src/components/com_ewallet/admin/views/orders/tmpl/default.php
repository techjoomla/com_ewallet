<?php
/**
 *  @package    Quick2Cart
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( ';)' );
?>
<script>
jQuery('#search_select').removeClass('small');
</script>
<?php
	$app = JFactory::getApplication();
	$override = JPATH_BASE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'com_ewallet'.DS.'orders'.DS.'default.php';
	if(JFile::exists($override) )
		$view = $override;
	else
	{	
		$view=JPATH_SITE.DS.'components'.DS.'com_ewallet'.DS.'views'.DS.'orders'.DS.'tmpl'.DS.'default.php';
	}
	ob_start();
	include($view);
	$html = ob_get_contents();
	ob_end_clean();
	echo $html;
?>


