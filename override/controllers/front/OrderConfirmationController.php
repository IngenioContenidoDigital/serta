<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderConfirmationControllerCore extends FrontController
{
	public $php_self = 'order-confirmation';

	public $id_cart;
	public $id_module;
	public $id_order;
	public $reference;
	public $secure_key;
	public $url_banco;
        public $url_iframe;

	/**
	 * Initialize order confirmation controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();
                
                if(isset($this->context->cookie->{'url_confirmation'})){
                   unset($this->context->cookie->{'url_confirmation'});
                }

		$this->id_cart = (int)(Tools::getValue('id_cart', 0));
		$is_guest = false;

		/* check if the cart has been made by a Guest customer, for redirect link */
		if (Cart::isGuestCartByCartId($this->id_cart))
		{
			$is_guest = true;
			$redirectLink = 'index.php?controller=guest-tracking';
		}
		else
			$redirectLink = 'index.php?controller=history';

		$this->id_module = (int)(Tools::getValue('id_module', 0));
		$this->id_order = Order::getOrderByCartId((int)($this->id_cart));
		$this->secure_key = Tools::getValue('key', false);
		$order = new Order((int)($this->id_order));
		if ($is_guest)
		{
			$customer = new Customer((int)$order->id_customer);
			$redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
		}
		if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key))
			Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
		$this->reference = $order->reference;
		if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key)
			Tools::redirect($redirectLink);
		$module = Module::getInstanceById((int)($this->id_module));
		if (isset($order->payment) && isset($module->displayName) && $order->payment != $module->displayName && ( !in_array($order->payment, $this->get_mediosp()) ))
			Tools::redirect($redirectLink);
                        
                
		$url_banco2 = Tools::getValue('bankdest2', 0);
		$url_dec_64 = base64_decode(strtr($url_banco2, '-_,', '+/='));

		if ($url_banco2 != false) {	
			$this->url_banco = $url_dec_64;	
                        Tools::redirect($this->url_banco);
		}
                $URL_PAYMENT_RECEIPT_HTML = Tools::getValue('URL_PAYMENT_RECEIPT_HTML', 0);
		$url_iframe = base64_decode(strtr($URL_PAYMENT_RECEIPT_HTML, '-_,', '+/='));
                if ($URL_PAYMENT_RECEIPT_HTML != false) {
                    $this->url_iframe = $url_iframe;
                 }

	}
	/**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */        
        public function initContent()
	{
		parent::initContent();
                
                $pse = Tools::getValue('PAYU_PSE', 0);
		
                if ($pse != false) {                    
                   $payu_pse = json_decode(gzuncompress(base64_decode(strtr($pse, '-_,', '+/='))),TRUE); //echo '<pre>'.  print_r($payu_pse,TRUE).'</pre>';
                   $message_payu = $this->get_messagePayu($payu_pse['lapResponseCode']).' '.$payu_pse['lapPaymentMethod'].'-'.$payu_pse['pseBank'];
                 }  else {
                    $message_payu = $this->get_messagePayu($this->get_state_transaction($this->id_cart));    
                 }
                
		

		$this->context->smarty->assign(array(
			'is_guest' => $this->context->customer->is_guest,
			'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
			'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn(),
			'message_payu' => $message_payu
		));

		if ($this->context->customer->is_guest)
		{
			$this->context->smarty->assign(array(
				'id_order' => $this->id_order,
				'reference_order' => $this->reference,
				'id_order_formatted' => sprintf('#%06d', $this->id_order),
				'email' => $this->context->customer->email
			));
			/* If guest we clear the cookie for security reason */
			$this->context->customer->mylogout();
		}
                
                if(isset($this->url_iframe) && !empty($this->url_iframe) && $this->url_iframe != '' ){
                    $this->context->smarty->assign(array('payu' => true,'URL_PAYMENT_RECEIPT_HTML' => $this->url_iframe)); 
                }
                
		$this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
	}
        
	/**
	 * Execute the hook displayPaymentReturn
	 */
	public function displayPaymentReturn()
	{
		if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayPaymentReturn', $params, $this->id_module);
			}
		}
		return false;
	}
        
	/**
	 * Execute the hook displayOrderConfirmation
	 */
	public function displayOrderConfirmation()
	{
		if (Validate::isUnsignedId($this->id_order))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}        
        
        /*
         * Lista medios de pago 
         */
        public  function get_mediosp(){
          //in_array("Irix", $this->get_mediosp())
            $mediosp=array();
               
                   $query="select nombre from ps_medios_de_pago limit 100;";
            $results = Db::getInstance()->ExecuteS($query);
            if (count($results)>0 ) {
               
                foreach ($results as $value) {
                    $mediosp[]=$value["nombre"];
                    
                }
                              
            }
            return $mediosp;  
        }

/**
 * Retorna el mensaje correspondiente al código de payu
 */
protected function get_messagePayu($cod_payu){

  $messages = array('APPROVED' => 'Transacción aprobada.',
               'PAYMENT_NETWORK_REJECTED' => 'Transacción rechazada por entidad financiera.',
               'ENTITY_DECLINED' => 'Transacción rechazada por el banco',
               'INSUFFICIENT_FUNDS' => 'Fondos insuficientes',
               'INVALID_CARD' => 'Tarjeta inválida',
               'CONTACT_THE_ENTITY' => 'Contactar entidad financiera',
               'BANK_ACCOUNT_ACTIVATION_ERROR' => 'Débito automático no permitido',
               'BANK_ACCOUNT_NOT_AUTHORIZED_FOR_AUTOMATIC_DEBIT' => 'Débito automático no permitido',
               'INVALID_AGENCY_BANK_ACCOUNT' => 'Débito automático no permitido',
               'INVALID_BANK_ACCOUNT' => 'Débito automático no permitido',
               'INVALID_BANK' => 'Débito automático no permitido',
               'EXPIRED_CARD' => 'Tarjeta vencida',
               'RESTRICTED_CARD' => 'Tarjeta restringida',
               'INVALID_EXPIRATION_DATE_OR_SECURITY_CODE' => 'Fecha de expiración o código de seguridad inválidos',
               'REPEAT_TRANSACTION' => 'Reintentar pago',
               'INVALID_TRANSACTION' => 'Transacción inválida',
               'EXCEEDED_AMOUNT' => 'El valor excede el máximo permitido por la entidad',
               'ABANDONED_TRANSACTION' => 'Transacción abandonada por el pagador',
               'CREDIT_CARD_NOT_AUTHORIZED_FOR_INTERNET_TRANSACTIONS' => 'Tarjeta no autorizada para comprar por internet',
               'ANTIFRAUD_REJECTED' => 'Transacción rechazada por sospecha de fraude',
               'DIGITAL_CERTIFICATE_NOT_FOUND' => 'Certificado digital no encontrado',
               'BANK_UNREACHABLE' => 'Error tratando de comunicarse con el banco',
               'ENTITY_MESSAGING_ERROR' => 'Error comunicándose con la entidad financiera',
               'NOT_ACCEPTED_TRANSACTION' => 'Transacción no permitida al tarjetahabiente',
               'INTERNAL_PAYMENT_PROVIDER_ERROR' => 'Error',
               'INACTIVE_PAYMENT_PROVIDER' => 'Error',
               'ERROR' => 'Error',
               'ERROR_CONVERTING_TRANSACTION_AMOUNTS' => 'Error',
               'BANK_ACCOUNT_ACTIVATION_ERROR' => 'Error',
               'FIX_NOT_REQUIRED' => 'Error',
               'AUTOMATICALLY_FIXED_AND_SUCCESS_REVERSAL' => 'Error',
               'AUTOMATICALLY_FIXED_AND_UNSUCCESS_REVERSAL' => 'Error',
               'AUTOMATIC_FIXED_NOT_SUPPORTED' => 'Error',
               'NOT_FIXED_FOR_ERROR_STATE' => 'Error',
               'ERROR_FIXING_AND_REVERSING' => 'Error',
               'ERROR_FIXING_INCOMPLETE_DATA' => 'Error',
               'PAYMENT_NETWORK_BAD_RESPONSE' => 'Error',
               'PAYMENT_NETWORK_NO_CONNECTION' => 'No fue posible establecer comunicación con la entidad financiera',
               'PAYMENT_NETWORK_NO_RESPONSE' => 'No se recibió respuesta de la entidad financiera',
               'EXPIRED_TRANSACTION' => 'Transacción expirada',
               'PENDING_TRANSACTION_REVIEW' => 'Transacción en validación manual',
               'PENDING_TRANSACTION_CONFIRMATION' => 'Recibo de pago generado. En espera de pago',
               'PENDING_TRANSACTION_TRANSMISSION' => 'Transacción no permitida',
               'PENDING_PAYMENT_IN_ENTITY' => 'Recibo de pago generado. En espera de pago',
               'PENDING_PAYMENT_IN_BANK' => 'Recibo de pago generado. En espera de pago',
               'PENDING_SENT_TO_FINANCIAL_ENTITY' => 'Pendiente de envió la entidad financiera',
               'PENDING_AWAITING_PSE_CONFIRMATION' => 'En espera de confirmación de PSE',
               'PENDING_NOTIFYING_ENTITY' => 'Recibo de pago generado. En espera de pago');

  if(isset($messages[$cod_payu]))
  {
    return $messages[$cod_payu];
  }
  return '';

}

/**
 * Retorna un código de estado relacionado al carrito 
 */
protected function get_state_transaction($id_cart){
            $query=  "	SELECT IF(ISNULL(response.message), pagos.message, response.message) as state
						FROM 
						"._DB_PREFIX_."pagos_payu pagos LEFT JOIN "._DB_PREFIX_."log_payu_response response ON (pagos.orderIdPayu = response.orderIdPayu)
						WHERE pagos.id_cart =".(int)$id_cart;

            $row = Db::getInstance()->getRow($query);
            if(isset($row['state']) && !empty($row['state'])){
                return $row['state'];
            }else{
              return FALSE;
            }

    }


}

