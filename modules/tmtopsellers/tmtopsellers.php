<?php
/*
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class Tmtopsellers extends Module
{
	protected static $cache_best_sellers;

	public function __construct()
	{
		$this->name = 'tmtopsellers';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Templatemela';
		$this->need_instance = 0;
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('TM - Bestseller Block');
		$this->description = $this->l('Adds a block displaying your store\'s top-selling products.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		$this->_clearCache('*');

		if (!parent::install()
			|| !$this->registerHook('header')
			|| !$this->registerHook('displayHome')
			|| !$this->registerHook('actionOrderStatusPostUpdate')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
			|| !ProductSale::fillProductSales()
		)
			return false;

		Configuration::updateValue('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY', 10);
		Configuration::updateValue('PS_BLOCK_TMTOPSELLERS_SLIDER', true);

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		return parent::uninstall();
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

	public function hookActionOrderStatusPostUpdate($params)
	{
		$this->_clearCache('*');
	}

	public function _clearCache($template, $cache_id = null, $compile_id = null)
	{
		parent::_clearCache('tmtopsellers.tpl', $this->getCacheId('tmtopsellers-col'));
	}

	/**
	 * Called in administration -> module -> configure
	 */
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('submitBestSellers'))
		{
			Configuration::updateValue('PS_BLOCK_TMTOPSELLERS_SLIDER', (int)Tools::getValue('PS_BLOCK_TMTOPSELLERS_SLIDER'));
			Configuration::updateValue('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY', (int)Tools::getValue('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY'));
			$this->_clearCache('*');
			$output .= $this->displayConfirmation($this->l('Settings updated'));
		}

		return $output.$this->renderForm();
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
						'name' => 'PS_BLOCK_TMTOPSELLERS_TO_DISPLAY',
						'desc' => $this->l('Determine the number of product to display in this block'),
						'class' => 'fixed-width-xs',
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Display Topseller Product as Slider'),
						'name' => 'PS_BLOCK_TMTOPSELLERS_SLIDER',
						'desc' => $this->l('Display Slider or Grid..'),
						'is_bool' => true,
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
					'title' => $this->l('Save')
				)
			)
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitBestSellers';
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
			'PS_BLOCK_TMTOPSELLERS_TO_DISPLAY' => (int)Tools::getValue('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY', Configuration::get('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY')),
			'PS_BLOCK_TMTOPSELLERS_SLIDER' => (int)Tools::getValue('PS_BLOCK_TMTOPSELLERS_SLIDER', Configuration::get('PS_BLOCK_TMTOPSELLERS_SLIDER')),
		);
	}

	public function hookHeader($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return;
		if (isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index')
			$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		$this->context->controller->addCSS($this->_path.'css/tmtopsellers.css', 'all');
	}

	public function hookDisplayHome($params)
	{
		if (!$this->isCached('tmtopsellers.tpl', $this->getCacheId('tmtopsellers-col')))
		{
			if (!isset(Tmtopsellers::$cache_best_sellers))
				Tmtopsellers::$cache_best_sellers = $this->getBestSellers($params);
			$this->smarty->assign(array(
				'best_sellers' => Tmtopsellers::$cache_best_sellers,
				'display_link_bestsellers' => Configuration::get('PS_DISPLAY_BEST_SELLERS'),
				'display_slider' => Configuration::get('PS_BLOCK_TMTOPSELLERS_SLIDER'),
				'display_product' => Configuration::get('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY'),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home'))
			));
		}

		if (Tmtopsellers::$cache_best_sellers === false)
			return false;

		return $this->display(__FILE__, 'tmtopsellers.tpl', $this->getCacheId('tmtopsellers-col'));
	}

	protected function getBestSellers($params)
	{
		if (Configuration::get('PS_CATALOG_MODE'))
			return false;

		if (!($result = ProductSale::getBestSalesLight((int)$params['cookie']->id_lang, 0, (int)Configuration::get('PS_BLOCK_TMTOPSELLERS_TO_DISPLAY'))))
			return (Configuration::get('PS_BLOCK_TMTOPSELLERS_SLIDER') ? array() : false);

		$currency = new Currency($params['cookie']->id_currency);
		$usetax = (Product::getTaxCalculationMethod((int)$this->context->customer->id) != PS_TAX_EXC);
		foreach ($result as &$row)
			$row['price'] = Tools::displayPrice(Product::getPriceStatic((int)$row['id_product'], $usetax), $currency);

		return $result;
	}
}