<?php

/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
  0* This source file is subject to the Academic Free License (AFL 3.0)
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

class PayUControllerWS extends FrontController {

    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
    }

    public function process() {
        parent::process();

        $params = $this->initParams();
        self::$smarty->assign(array(
            'formLink' => Configuration::get('PAYU_DEMO') != 'yes' ? 'https://gateway.payulatam.com/ppp-web-gateway/' : 'https://gateway.payulatam.com/ppp-web-gateway/',
            'payURedirection' => $params
        ));
    }

    public function initParams() {

        $tax = (float) self::$cart->getOrderTotal() - (float) self::$cart->getOrderTotal(false);
        $base = (float) self::$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) + (float) self::$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) - (float) $tax;
        if ($tax == 0)
            $base = 0;

        $currency = new Currency(self::$cart->id_currency);

        $language = new Language(self::$cart->id_lang);

        $customer = new Customer(self::$cart->id_customer);
             
        $ref = 'payU_' . md5(Configuration::get('PS_SHOP_NAME')) . '_' . (int) self::$cart->id;

        $token = md5(Tools::safeOutput(Configuration::get('PAYU_API_KEY')) . '~' . Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')) . '~' . $ref . '~' . (float) self::$cart->getOrderTotal() . '~' . Tools::safeOutput($currency->iso_code));

        $params = array(
            array('test' => (Configuration::get('PAYU_DEMO') == 'yes' ? 1 : 0), 'name' => 'test'),
            array('merchantId' => Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')), 'name' => 'merchantId'),
            array('referenceCode' => $ref, 'name' => 'referenceCode'),
            array('description' => substr(Configuration::get('PS_SHOP_NAME') . ' Order', 0, 255), 'name' => 'description'),
            array('amount' => $this->context->cart->getOrderTotal(), 'name' => 'amount'),
            array('buyerEmail' => Tools::safeOutput($customer->email), 'name' => 'buyerEmail'),
            array('tax' => (float) $tax, 'name' => 'tax'),
            array('extra1' => 'PRESTASHOP', 'name' => 'extra1'),
            array('taxReturnBase' => (float) $base, 'name' => 'taxReturnBase'),
            array('currency' => Tools::safeOutput($currency->iso_code), 'name' => 'currency'),
            array('lng' => Tools::safeOutput($language->iso_code), 'name' => 'lng'),
            array('signature' => Tools::safeOutput($token), 'name' => 'signature'),
            array('value' => 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'history.php', 'name' => 'responseUrl'),
            array('value' => 'http://' . htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8') . __PS_BASE_URI__ . 'modules/payulatam/validation.php', 'name' => 'confirmationUrl'),
        );

        if (Configuration::get('PAYU_ACCOUNT_ID') != 0)
            $params[] = array('accountId' => (int) Configuration::get('PAYU_ACCOUNT_ID'), 'name' => 'accountId');

        if (Db::getInstance()->getValue('SELECT `token` FROM `' . _DB_PREFIX_ . 'payu_token` WHERE `id_cart` = ' . (int) self::$cart->id))
            Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'payu_token` SET `token` = "' . pSQL($token) . '" WHERE `id_cart` = ' . (int) self::$cart->id);
        else
            Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'payu_token` (`id_cart`, `token`) VALUES (' . (int) self::$cart->id . ', \'' . pSQL($token) . '\')');

        return $params;
    }

    public function createPendingOrder($extra_vars = array(), $metodo_de_pago, $mensaje, $order_state ) {
        try {
                $payu = new PayULatam();
                $date = date("Y-m-d H:i:s");
                    $sql="INSERT INTO "._DB_PREFIX_."sonda_payu (id_cart,date_add,`interval`,last_update, pasarela)
                    VALUES(".(int)$this->context->cart->id.",'".$date."',";
                        if($metodo_de_pago === 'Tarjeta_credito' || $metodo_de_pago === 'PSE'){
                             $sql.=11;         
                        }else{
                            $sql.=61; 
                        }
                    $sql.=", '".$date."','".$payu->name."');"; 
                    if(!Db::getInstance()->Execute($sql))
                        Logger::AddLog('Error al guardar sonda_payu id_cart: '.$this->context->cart->id, 2, null, null, null, true);
       


            $payu->validateOrder((int) self::$cart->id, (int) Configuration::get($order_state), (float) self::$cart->getOrderTotal(), $metodo_de_pago, $mensaje, $extra_vars, NULL, false, self::$cart->secure_key);
        } catch (Exception $e) {
            exit('<pre>'.  print_r($e,TRUE).'</pre>');
        } 
    }

}
