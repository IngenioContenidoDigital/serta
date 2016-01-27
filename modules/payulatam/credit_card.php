<?php

/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @version  Release: $Revision: 14011 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

$useSSL = true;
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');


class PayuCreditCard extends PayUControllerWS {

    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
    }

    public function process() {

        if (empty($this->context->cart->id)) {
            Tools::redirect('/');
        } //exit(print_r($_POST,TRUE));

            if ((isset($_POST['numerot']) && !empty($_POST['numerot']) && strlen($_POST['numerot']) > 13 && strlen((int) $_POST['numerot']) < 17
                    && isset($_POST['nombre']) && !empty($_POST['nombre']) && isset($_POST['codigot']) && !empty($_POST['codigot']) && 
                    isset($_POST['Month']) && !empty($_POST['Month']) && isset($_POST['year']) && !empty($_POST['year'])  && isset($_POST['cuotas']) && !empty($_POST['cuotas'])) 
                    || (isset($_POST['token_id']) && !empty($_POST['token_id']) && isset($_POST['openpay_device_session_id']) && !empty($_POST['openpay_device_session_id']) ) ) {

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

            $params = $this->initParams();
            // se optinen los datos del formulario de pago farmalisto    
            $post = array('nombre'  =>  (Tools::getValue('nombre')) ? Tools::getValue('nombre') : Tools::getValue('holder'),
                          'numerot' =>  (Tools::getValue('numerot')) ? Tools::getValue('numerot') : Tools::getValue('card'),
                          'codigot' =>  (Tools::getValue('codigot')) ? Tools::getValue('codigot') : Tools::getValue('cvv'),
                          'date'    =>  Tools::getValue('year').'/'.Tools::getValue('Month'),
                          'cuotas'  =>  Tools::getValue('cuotas'),
                          'Month'   =>  Tools::getValue('Month'),
                          'year'    =>  Tools::getValue('Year')
                        ); 
    

            $conf = new ConfPayu();
            
            if($conf->exist_cart_in_pagos($this->context->cart->id)){
               if(isset($this->context->cookie->{'url_confirmation'})){
                   Tools::redirectLink(json_decode($this->context->cookie->{'url_confirmation'}));
                }
                  Tools::redirectLink('/');
                exit();
            }
            
            $keysPayu = $conf->keys();
            $address = new Address($this->context->cart->id_address_delivery); 

            $id_order = 0;

            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;

            $dni = $conf->get_dni($this->context->cart->id_address_delivery);
            $reference_code = $customer->id . '_' . $id_cart . '_' . $id_order . '_' . $id_address;
            $_deviceSessionId = NULL;

            if (isset($this->context->cookie->deviceSessionId) && !empty($this->context->cookie->deviceSessionId) && strlen($this->context->cookie->deviceSessionId) === 32) {
                $_deviceSessionId = $this->context->cookie->deviceSessionId;
            } elseif (isset($_POST['deviceSessionId']) && !empty($_POST['deviceSessionId']) && strlen($_POST['deviceSessionId']) === 32) {
                $_deviceSessionId = $_POST['deviceSessionId'];
            } else {
                $_deviceSessionId = md5($this->context->cookie->timestamp);
            }

            $intentos = $conf->count_pay_cart($id_cart);
            
            $paymentMethod = $this->getFranquicia($post['numerot'], 'payulatam');

                $currency='';
                
                if($conf->isTest()){

                    $currency='USD';
                } else {

                    $currency=$params[9]['currency'];
                }

                $data = '{
       "language":"es",
       "command":"SUBMIT_TRANSACTION",
       "merchant":{
          "apiKey":"' . $keysPayu['apiKey'] . '",
          "apiLogin":"' . $keysPayu['apiLogin'] . '"
       },
       "transaction":{
         
          "order":{
             "accountId":"' . $keysPayu['accountId'] . '",
             "referenceCode":"' . $params[2]['referenceCode'] . '_'.$intentos.'",
             "description":"' . $reference_code . '",
             "language":"' . $params[10]['lng'] . '",
             "notifyUrl":"' . $conf->urlv() . '",
             "signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
             "additionalValues":{
                "TX_VALUE":{
                   "value":' . $params[4]['amount'] . ',
                   "currency":"'.$currency.'"
                }
             },
             
           "buyer": {
                "fullName": "'.$customer->firstname.' '.$customer->lastname.'",
                "contactPhone": "'.$address->phone_mobile.'",
                 "emailAddress":"'. $params[5]['buyerEmail'].'",
                 "dniNumber":"'.$dni.'",   
                 "shippingAddress": {
                 "street1": "'.$address->address1.'",
                 "street2":"N/A",    
                 "city": "'.$address->city.'",
                 "state": "'.$conf->get_state($address->id_state).'",
                 "country": "';
            if($conf->isTest()){
              $data.='PA';
              }else{
               $data.=$this->context->country->iso_code;
              }
            $data.='",
                 "postalCode": "'.$address->postcode.'",
                 "phone": "'.$address->phone.'"
                }
             },      
            
        "shippingAddress":{
            "street1":"'.$address->address1.'",
            "street2":"N/A",
            "city":"'.$address->city.'",
            "state":"'.$conf->get_state($address->id_state).'",
            "country":"';
            if($conf->isTest()){
              $data.='PA';
              }else{
               $data.=$this->context->country->iso_code;
              }
            $data.='",
            "postalCode":"'.$address->postcode.'",
            "phone":"'.$address->phone.'"
        }  
          },
          "payer":{

        "fullName":"'.$customer->firstname.' '.$customer->lastname.'",
        "emailAddress":"'. $params[5]['buyerEmail'].'",
        "contactPhone":"'.$address->phone_mobile.'",
        "dniNumber":"'.$dni.'",
        "billingAddress":{
            "street1":"'.$address->address1.'",
            "street2":"N/A",
            "city":"'.$address->city.'",
            "state":"'.$conf->get_state($address->id_state).'",
            "country":"';
            if($conf->isTest()){
              $data.='PA';
              }else{
               $data.=$this->context->country->iso_code;
              }
            $data.='",
            "postalCode":"'.$address->postcode.'",
            "phone":"'.$address->phone.'"
          }      
        },
          "creditCard":{
             "number":"' . $post['numerot'] . '",
             "securityCode":"' . $post['codigot'] . '",
             "expirationDate":"' . $post['date'] . '",
             "name":"';
        if($conf->isTest()){
              $data.='APPROVED';
              }else{
               $data.=$post['nombre'];
              }
        $data.='"
          },
          
            "extraParameters":{
              "INSTALLMENTS_NUMBER":'.$post['cuotas'].'
            },
            "type":"AUTHORIZATION_AND_CAPTURE",
            "paymentMethod":"' . $paymentMethod . '",
            "paymentCountry":"';
            if($conf->isTest()){
              $data.='PA';
              }else{
               $data.=$this->context->country->iso_code;
              }
            $data.='",
            "deviceSessionId": "'.$_deviceSessionId.'",
            "ipAddress": "'.$_SERVER['REMOTE_ADDR'].'",
            "userAgent": "'.$_SERVER['HTTP_USER_AGENT'].'",
            "cookie": "'.md5($this->context->cookie->timestamp).'"  
       },
       "test":';
            if($conf->isTest()){
              $data.='true';
              }else{
               $data.='false';
              }
    $data.='          
    }
    '; 
                $response = $conf->sendJson($data);
                $subs = substr($post['numerot'], 0, (strlen($post['numerot']) - 4));
                $nueva = '';

                for ($i = 0; $i <= strlen($subs); $i++) {
                    $nueva = $nueva . '*';
                }

                $data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
                $data = str_replace('"securityCode":"' . $post['codigot'], '"securityCode":"' . '****', $data);
                // colector Errores Payu
                $error_pay = array();

                if ($response['code'] === 'ERROR') {
                      $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                      $error_pay[]=$response;
                } elseif ($response['code'] === 'SUCCESS' && ( $response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED' ) && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {
                          $conf->pago_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);                       
                          if($response['transactionResponse']['state'] === 'APPROVED'){ //
                              $this->createPendingOrder(array(), 'Tarjeta_credito', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PS_OS_PAYMENT');
                          } else{
                                $this->createPendingOrder(array(), 'Tarjeta_credito', utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])), 'PAYU_OS_PENDING');  
                        }

                  $order = $conf->get_order($id_cart);
                  $id_order = $order['id_order'];
                  $payulatam = new PayULatam();
                  $url_confirmation = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $this->context->cart->id . '&id_module='.(int)$payulatam->id.'&id_order=' . (int) $order['id_order'];
                  $this->context->cookie->{'url_confirmation'} = json_encode($url_confirmation);
                  Tools::redirectLink($url_confirmation);
                  exit();
                } else {
                          $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                          $error_pay[]=array('ERROR' => utf8_encode($conf->getMessagePayu($response['transactionResponse']['responseCode'])));
                }
                $this->context->cookie->{'error_pay'} = json_encode($error_pay);
                  Tools::redirectLink($url_reintento);
                    exit();
            //$conf->getMessagePayu($response['transactionResponse']['responseCode'])

        }  else {
                $this->context->cookie->{'error_pay'} = json_encode(array('ERROR'=>'Valida tus datos he intenta de nuevo.'));
                  Tools::redirectLink($url_reintento); 
                    exit();   
        }

    }

    public function displayContent() {
        parent::displayContent();
        self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
    }

    
    /**
 * Retorna el la franquicia a la que pertenece un numero de TC
 */
public function getFranquicia($cart_number, $pasarela){

	require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

    $arraypaymentMethod =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AMEX','MASTERCARD'=>'MASTERCARD');
    $arraypaymentMethod2 =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AmEx','MASTERCARD'=>'MasterCard', 'DinersClub'=>'DinersClub','UnionPay'=>'UnionPay');
	$CCV = new CreditCardValidator();
    $CCV->Validate($cart_number);
    $key = $CCV->GetCardName($CCV->GetCardInfo()['type']); 
    if($CCV->GetCardInfo()['status'] == 'invalid'){
        return json_encode(array('ERROR'=>'El numero de la tarjeta no es valido.'));
    }

   switch ($pasarela) {
   	case 'payulatam':
   		return (array_key_exists(strtoupper($key), $arraypaymentMethod)) ? $arraypaymentMethod[strtoupper($key)] : 'N/A'; 
   		break;
   	default:
   		return (array_key_exists(strtoupper($key), $arraypaymentMethod2[strtoupper($key)])) ? $arraypaymentMethod2[strtoupper($key)] : 'N/A'; 
   		break;
   }
    
}

}

$farmaPayu = new  PayuCreditCard();
$farmaPayu->run();
