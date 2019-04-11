<?php
/**
 * @version     1.0.0
 * @package     com_api_my
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
if(JVERSION>=3.0)
{
JHtml::_('formbehavior.chosen', 'select');
}


JHtml::_('behavior.keepalive');

// Import CSS
$document = JFactory::getDocument();
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function(){
        
    });
    
    Joomla.submitbutton = function(task)
    {
        if(task == 'billing.cancel'){
			task = "cancel";
            Joomla.submitform(task, document.getElementById('billing-form'));
        }
        else
        {
			var t = document.getElementById('jform_user_id_id').value;
			if(t == "0")
			{
				jQuery('#jform_user_id_name').addClass('required');
				jQuery('#jform_user_id_name').addClass('invalid');
				jQuery('#jform_user_id-lbl').addClass('required');
				jQuery('#jform_user_id-lbl').addClass('invalid');
			}
			//Amount decimal validation
			value =  document.getElementById('jform_amount').value;
			var regex = /^[1-9]\d*(((,\d{3}){1})?(\.\d{0,2})?)$/;
			if (!regex.test(value))
			{
					jQuery('#jform_amount-lbl').addClass('required');
					jQuery('#jform_amount-lbl').addClass('invalid');
					jQuery('#jform_amount').addClass('required');
					jQuery('#jform_amount').addClass('invalid');
					return false;
			}
			if (task != 'billing.cancel' && document.formvalidator.isValid(document.id('billing-form'))) {
				
              if(task == 'billing.apply'){
						task = "store";
						Joomla.submitform(task, document.getElementById('billing-form'));
				}
				if(task == 'billing.save'){
						task = "storeclose";
						Joomla.submitform(task, document.getElementById('billing-form'));
				}
            }
            else {
                alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>
<legend><?php echo JText::_('COM_EWALLET_ADD_BALANCE'); ?></legend>
<form action="<?php echo JRoute::_('index.php?option=com_ewallet&view=billing&layout=edit'); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="billing-form" class="form-validate">
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">

			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_EWALLET_CAMPER_NAME'); // $this->form->getLabel('user_id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_EWALLET_TRANSACTION_AMOUNT');//$this->form->getLabel('amount'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('amount'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_EWALLET_TRANSACTION_TYPE');//$this->form->getLabel('type'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_EWALLET_COMMENET');//$this->form->getLabel('comment'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('comment'); ?></div>
			</div>
            </fieldset>
        </div>
        <input type="hidden" name="task" id="task" value="" />
        <input type="hidden" name="controller" value="billing" />
        <?php echo JHtml::_('form.token'); ?>

    </div>
</form>
