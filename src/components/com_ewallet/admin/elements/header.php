<?php
defined('JPATH_BASE') or die();
jimport('joomla.html.parameter.element');
jimport('joomla.html.html');
jimport('joomla.form.formfield');
class JFormFieldHeader extends JFormField
{
	var	$type='Header';
	function getInput()
	{
		$document=JFactory::getDocument();
		$document->addStyleSheet(JUri::base().'components/com_ewallet/assests/css/ewallet.css');
		$return='
		<div class="quick2cartOptionHeaderDiv_outer">
			<div class="quick2cartOptionHeaderDiv_inner">
				'.JText::_($this->value).'
			</div>
		</div>';
		return $return;
	}
}
?>
