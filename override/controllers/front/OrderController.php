<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderController extends OrderControllerCore
{
	public $step;


	public function setMedia()
	{
		parent::setMedia();
		if ($this->step == 2)
			$this->addJS(_THEME_JS_DIR_.'order-carrier.js');

		if ($this->step == 3){
			$this->addJqueryUI('ui.datepicker');
			$this->addJqueryPlugin('validate');

		if(isset($this->context->cookie->{'error_pay'}) && !empty($this->context->cookie->{'error_pay'}) ) { 
			$error_pay = json_decode($this->context->cookie->{'error_pay'},true);
			$this->context->smarty->assign( array('errors_msgs' => $error_pay));
			$this->context->smarty->assign( array('errors_pay'=> 'true' ));
			//echo ('<pre>'.print_r($error_pay,true).'</pre>');			
			 // unset($this->context->cookie->{'error_pay'}); 
			
		}else{
  			$this->context->smarty->assign(array('errors_pay'=> 'false' ));
  		}
		}
	}
}
