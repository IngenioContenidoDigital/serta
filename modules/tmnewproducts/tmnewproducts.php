<?php
/**
* 2007-2014 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2014 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class TmNewProducts extends Module
{
	protected static $cache_new_products;

	public function __construct()
	{
		$this->name = 'tmnewproducts';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Templatemela';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('TM- NewProduct Slider or Grid in Homepage');
		$this->description = $this->l('Displays New products as Slider or Grid on center column.');
	}
	public function install()
	{
		$this->_clearCache('*');
		Configuration::updateValue('NEW_PRODUCTS_NBR_TM', 10);
		Configuration::updateValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', 1);

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !$this->registerHook('categoryUpdate')
			|| !$this->registerHook('displayHome'))
			return false;
		return true;
	}
	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
	}
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitTmNewProducts'))
		{
			if (!($productNbr = Tools::getValue('NEW_PRODUCTS_NBR_TM')) || empty($productNbr))
				$output .= $this->displayError($this->l('Please complete the "products to display" field.'));
			elseif ((int)($productNbr) == 0)
				$output .= $this->displayError($this->l('Invalid number.'));
			else
			{
				Configuration::updateValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', (int)(Tools::getValue('PS_BLOCK_NEWPRODUCTS_DISPLAY')));
				Configuration::updateValue('NEW_PRODUCTS_NBR_TM', (int)($productNbr));
				$output .= $this->displayConfirmation($this->l('Settings updated'));
			}
		}
		return $output.$this->renderForm();
	}
	private function getNewProducts()
	{
		if (!Configuration::get('NEW_PRODUCTS_NBR_TM'))
			return;
		$newProducts = Product::getNewProducts((int) $this->context->language->id, 0, (int) Configuration::get('NEW_PRODUCTS_NBR_TM'));
		if (!$newProducts && Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'))
			return;
		return $newProducts;
	}
	public function hookDisplayHome($params)
	{
		if (!$this->isCached('tmnewproducts.tpl', $this->getCacheId()))
		{
			if (!isset(TmNewProducts::$cache_new_products))
				TmNewProducts::$cache_new_products = $this->getNewProducts();

			$this->smarty->assign(array(
				'new_products' => TmNewProducts::$cache_new_products,
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'newslider' => (int)Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY'),
				'no_prod' => (int)Configuration::get('NEW_PRODUCTS_NBR_TM')
			));
		}

		if (TmNewProducts::$cache_new_products === false)
			return false;

		return $this->display(__FILE__, 'tmnewproducts.tpl', $this->getCacheId());
	}
	protected function getCacheId($name = null)
	{
		if ($name === null)
			$name = 'tmnewproducts';
		return parent::getCacheId($name.'|'.date('Ymd'));
	}
	public function hookHeader($params)
	{
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');

		$this->context->controller->addCSS($this->_path.'tmnewproducts.css', 'all');
	}
	public function hookAddProduct($params)
	{
		$this->_clearCache('*');
	}
	public function hookUpdateProduct($params)
	{
		$this->_clearCache('*');
	}
	public function hookDeleteProduct($params)
	{
		$this->_clearCache('*');
	}
	public function _clearCache($template, $cache_id = null, $compile_id = null)
	{
		parent::_clearCache('tmnewproducts.tpl');
	}
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Products to display'),
						'name' => 'NEW_PRODUCTS_NBR_TM',
						'class' => 'fixed-width-xs',
						'desc' => $this->l('Define the number of products to be displayed in this block.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Display New Product as Slider'),
						'name' => 'PS_BLOCK_NEWPRODUCTS_DISPLAY',
						'desc' => $this->l('Display Slider or Grid.(Note:Slider is working if "Number of product" is set more than 5)'),
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Enabled')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('Disabled')
							)
						),
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitTmNewProducts';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'PS_BLOCK_NEWPRODUCTS_DISPLAY' => Tools::getValue('PS_BLOCK_NEWPRODUCTS_DISPLAY', Configuration::get('PS_BLOCK_NEWPRODUCTS_DISPLAY')),
			'NEW_PRODUCTS_NBR_TM' => Tools::getValue('NEW_PRODUCTS_NBR_TM', Configuration::get('NEW_PRODUCTS_NBR_TM')),
		);
	}
}
