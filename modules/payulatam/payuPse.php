<?php



$useSSL = true;
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class PayuPse extends PayUControllerWS{
    
    
    
   
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process() {
            
     if(empty( $this->context->cart->id)){
         Tools::redirect('/');  
     }            

        parent::process();

        // url para re intentos de pago
        $url_reintento=$_SERVER['HTTP_REFERER'];
          if(!strpos($_SERVER['HTTP_REFERER'], 'step=3')){
             if(!strpos($_SERVER['HTTP_REFERER'], '?')){
              $url_reintento.='?step=3';
            }else{
              $url_reintento.='&step=3';
            }
            }
          // vaciar errores en el intento de pago anterior  
        if(isset($this->context->cookie->{'error_pay'})){
            unset($this->context->cookie->{'error_pay'});
        }

        if (isset($_POST['pse_bank']) && isset($_POST['name_bank']) && !empty($_POST['pse_bank'])) {

            // reglas de carrito para bines
            $payulatam = new PayULatam(); 
            $params = $this->initParams();
            $conf = new ConfPayu();
            $keysPayu = $conf->keys();
            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;
            //$this->createPendingOrder();
            //$order = $conf->get_order($id_cart);
            $id_order = 0; //$order['id_order'];
            $description = $customer->id . '_' . $id_cart . '_' . $id_order . '_' . $id_address;
            $varRandn = $conf->randString();
            $varRandc = $conf->randString();
            setcookie($varRandn, $varRandc, time() + 900);


            $browser = array('ipAddress' => $_SERVER['SERVER_ADDR'],
                'userAgent' => $_SERVER['HTTP_USER_AGENT']);

            $address = new Address($this->context->cart->id_address_delivery); 
            $dni = $conf->get_dni($this->context->cart->id_address_delivery);
            $intentos = $conf->count_pay_cart($id_cart);

            $currency='';
                
                if($conf->isTest()){

                    $currency='USD';
                } else {

                    $currency=$params[9]['currency'];
                }

            $url  = '';
            if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'))
                {
                if (method_exists('Tools', 'getShopDomainSsl'))
                    $url = 'https://'.Tools::getShopDomainSsl().__PS_BASE_URI__.'modules/'.$payulatam->name.'/';
                else
                    $url = 'https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$payulatam->name.'/';
            }
            else
                $url = 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$payulatam->name.'/';    

            $reference_code = $params[2]['referenceCode'] . '_'.$intentos;
            $token_orden = md5($reference_code);

$data = '{
"test":false,
"language":"es",
"command":"SUBMIT_TRANSACTION",
"merchant":{
"apiLogin":"' . $keysPayu['apiLogin'] . '",
"apiKey":"' . $keysPayu['apiKey'] . '"
},
"transaction":{
"order":{
"accountId":"' . $keysPayu['pse-CO'] . '",
"referenceCode":"' .$reference_code.'",
"description":"' . $description . '",
"language":"es",
"notifyUrl":"' . $conf->urlv() . '",
"signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
"buyer":{
"fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
"emailAddress":"' . $params[5]['buyerEmail'] . '",
"dniNumber":"'.$dni.'",
"shippingAddress":{
"street1":"'.$address->address1.'",
"city":"'.$address->city.'",
"state":"'.$conf->get_state($address->id_state).'",
"country":"' . $this->context->country->iso_code . '",
"phone":"'.$address->phone.'"
}
},
"additionalValues":{
"TX_VALUE":{
"value":' . $params[4]['amount'] . ',
"currency":"' . $currency . '"
}
}
},
"payer":{
"fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
"emailAddress":"' . $params[5]['buyerEmail'] . '",
"dniNumber":"' . $dni. '",
"contactPhone":"'.$address->phone.'"
},
"ipAddress":"' . $browser['ipAddress'] . '",
"cookie":"' . $varRandn . '",
"userAgent":"' . $browser['userAgent'] . '",
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"PSE",
"extraParameters":{
"PSE_REFERENCE1":"' . $browser['ipAddress'] . '",
"FINANCIAL_INSTITUTION_CODE":"' . $_POST['pse_bank'] . '",
"FINANCIAL_INSTITUTION_NAME":"' . $_POST['name_bank'] . '",
"USER_TYPE":"' . $_POST['pse_tipoCliente'] . '",
"PSE_REFERENCE2":"' . $_POST['pse_docType'] . '",
"PSE_REFERENCE3":"' . $_POST['pse_docNumber'] . '",
"RESPONSE_URL": "'.$url.'url_confirm.php?token='.$token_orden.'"
}
}
}
';

$response = $conf->sendJson($data);

            if ($response['code'] === 'ERROR') {

                        $conf->error_payu($id_order, $customer->id, $data, $response, 'PSE', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                        $error_pay[]=$response;
            }
                elseif ($response['code'] === 'SUCCESS' && $response['transactionResponse']['state'] === 'PENDING' && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {
                 $this->createPendingOrder(array(), 'PSE', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PAYU_OS_PENDING');
                $order = $conf->get_order($id_cart);
                $id_order = $order['id_order'];    
                $conf->pago_payu($id_order, $customer->id, $data, $response, 'Pse',$response['code'], $id_cart, $id_address);
                $url_base64 = strtr(base64_encode($response['transactionResponse']['extraParameters']['BANK_URL']), '+/=', '-_,');
                $string_send = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $id_cart . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order'] . '&bankdest2=' . $url_base64;
                $conf->url_confirm_payu($token_orden,__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $id_cart . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order']);
                Tools::redirectLink($string_send);
                exit();
            } else {
                        $conf->error_payu($id_order, $customer->id, $data, $response, 'PSE', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                        $error_pay[]=array('ERROR'=>utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode']))); 
            }
                $this->context->cookie->{'error_pay'} = json_encode($error_pay);
                Tools::redirectLink($url_reintento);
                exit();
        }else {
                $this->context->cookie->{'error_pay'} = json_encode(array('ERROR'=>'Valida tus datos he intenta de nuevo.'));
                Tools::redirectLink($url_reintento); 
                exit();   
        }
    } 

    public function displayContent() {
        parent::displayContent();

        self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
    }


}


$payuPse = new PayuPse();

$payuPse->run();
