<?php
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
jimport('joomla.application.component.controller');
class eWalletController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}
function getFromattedPrice()
	{
		$price = JRequest::getInt('price');
	        $params = JComponentHelper::getParams( 'com_ewallet' );
            $curr_sym=$params->get( "addcurrency_sym" ) ;
            $curr_nam=$params->get( "wallet_currency_nam" ) ;
            $currency_display_format = $params->get('currency_display_format', "{SYMBOL} {AMOUNT}");
			$price = intval(str_replace(',', '', $price));
			$price = number_format($price,2);
			$currency_display_formatstr = str_replace('{AMOUNT}',$price,$currency_display_format);
		   	$currency_display_formatstr = str_replace('{SYMBOL}',$curr_sym,$currency_display_formatstr);
		   	 $currency_display_formatstr = str_replace('{CURRENCY}',$curr_nam,$currency_display_formatstr);
            echo $html=$currency_display_formatstr;
            return $html;
    }
}		
