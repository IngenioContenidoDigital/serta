<?php
/**
* 2007-2015 PrestaShop
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
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

require_once _PS_MODULE_DIR_.'tmfootercmsblock/classes/TmfooterCMSInfoBlock.php';

class Tmfootercmsblock extends Module
{
	public $html = '';

	public function __construct()
	{
		$this->name = 'tmfootercmsblock';
		$this->tab = 'front_office_features';
		$this->version = '1.5.2';
		$this->author = 'Templatemela';
		$this->bootstrap = true;
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('TM Footer CMS Block');
		$this->description = $this->l('Adds custom information blocks in your store.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		return 	parent::install() &&
				$this->installDB() &&
				$this->registerHook('TmFooterTop') &&
				$this->registerHook('displayHeader') &&
				$this->installFixtures() &&
				$this->disableDevice(Context::DEVICE_TABLET | Context::DEVICE_MOBILE);
	}

	public function installDB()
	{
		$return = true;
		$return &= Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tmcmsinfo` (
				`id_tmcmsinfo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned DEFAULT NULL,
				PRIMARY KEY (`id_tmcmsinfo`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
		);

		$return &= Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tmcmsinfo_lang` (
				`id_tmcmsinfo` INT UNSIGNED NOT NULL,
				`id_lang` int(10) unsigned NOT NULL ,
				`text` text NOT NULL,
				PRIMARY KEY (`id_tmcmsinfo`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
		);

		return $return;
	}

	public function uninstall()
	{
		return parent::uninstall() && $this->uninstallDB();
	}

	public function uninstallDB($drop_table = true)
	{
		$ret = true;
		if($drop_table)
			$ret &=  Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmcmsinfo`') && Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmcmsinfo_lang`');

		return $ret;
	}

	public function getContent()
	{
		$id_tmcmsinfo = (int)Tools::getValue('id_tmcmsinfo');

		if (Tools::isSubmit('savetmfootercmsblock'))
		{
			if ($this->processSaveCmsInfo())
				return $this->html . $this->renderList();
			else
				return $this->html . $this->renderForm();
		}
		elseif (Tools::isSubmit('updatetmfootercmsblock') || Tools::isSubmit('addtmfootercmsblock'))
		{
			$this->html .= $this->renderForm();
			return $this->html;
		}
		else if (Tools::isSubmit('deletetmfootercmsblock'))
		{
			$tmcmsinfo = new TmfooterCMSInfoBlock((int)$id_tmcmsinfo);
			$tmcmsinfo->delete();
			$this->_clearCache('tmfootercmsblock.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
		{
			$this->html .= $this->renderList();
			return $this->html;
		}

	}

	public function processSaveCmsInfo()
	{
		if ($id_tmcmsinfo = Tools::getValue('id_tmcmsinfo'))
			$tmcmsinfo = new TmfooterCMSInfoBlock((int)$id_tmcmsinfo);
		else
		{
			$tmcmsinfo = new TmfooterCMSInfoBlock();
			if (Shop::isFeatureActive())
			{
				$shop_ids = Tools::getValue('checkBoxShopAsso_configuration');
				if (!$shop_ids)
				{
					$this->html .= '<div class="alert alert-danger conf error">'.$this->l('You have to select at least one shop.').'</div>';
					return false;
				}
			}
			else
				$tmcmsinfo->id_shop = Shop::getContextShopID();
		}

		$languages = Language::getLanguages(false);
		$text = array();
		foreach ($languages AS $lang)
			$text[$lang['id_lang']] = Tools::getValue('text_'.$lang['id_lang']);
		$tmcmsinfo->text = $text;

		if (Shop::isFeatureActive() && !$tmcmsinfo->id_shop)
		{
			$saved = true;
			foreach ($shop_ids as $id_shop)
			{
				$tmcmsinfo->id_shop = $id_shop;
				$saved &= $tmcmsinfo->add();
			}
		}
		else
			$saved = $tmcmsinfo->save();

		if ($saved)
			$this->_clearCache('tmfootercmsblock.tpl');
		else
			$this->html .= '<div class="alert alert-danger conf error">'.$this->l('An error occurred while attempting to save.').'</div>';

		return $saved;
	}


	protected function renderForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('New custom CMS block'),
			),
			'input' => array(
				'id_tmcmsinfo' => array(
					'type' => 'hidden',
					'name' => 'id_tmcmsinfo'
				),
				'content' => array(
					'type' => 'textarea',
					'label' => $this->l('Text'),
					'lang' => true,
					'name' => 'text',
					'cols' => 40,
					'rows' => 10,
					'class' => 'rte',
					'autoload_rte' => true,
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			),
			'buttons' => array(
				array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'title' => $this->l('Back to list'),
					'icon' => 'process-icon-back'
				)
			)
		);

		if (Shop::isFeatureActive() && Tools::getValue('id_tmcmsinfo') == false)
		{
			$fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association'),
				'name' => 'checkBoxShopAsso_theme'
			);
		}


		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'tmfootercmsblock';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);

		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'savetmfootercmsblock';

		$helper->fields_value = $this->getFormValues();

		return $helper->generateForm(array(array('form' => $fields_form)));
	}

	protected function renderList()
	{
		$this->fields_list = array();
		$this->fields_list['id_tmcmsinfo'] = array(
				'title' => $this->l('Block ID'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);

		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_SHOP)
			$this->fields_list['shop_name'] = array(
					'title' => $this->l('Shop'),
					'type' => 'text',
					'search' => false,
					'orderby' => false,
				);

		$this->fields_list['text'] = array(
				'title' => $this->l('Block text'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			);

		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = false;
		$helper->identifier = 'id_tmcmsinfo';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = true;
		$helper->imageType = 'jpg';
		$helper->toolbar_btn['new'] = array(
			'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new')
		);

		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		$content = $this->getListContent($this->context->language->id);

		return $helper->generateList($content, $this->fields_list);
	}

	protected function getListContent($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');

		$sql = 'SELECT r.`id_tmcmsinfo`, rl.`text`, s.`name` as shop_name
			FROM `'._DB_PREFIX_.'tmcmsinfo` r
			LEFT JOIN `'._DB_PREFIX_.'tmcmsinfo_lang` rl ON (r.`id_tmcmsinfo` = rl.`id_tmcmsinfo`)
			LEFT JOIN `'._DB_PREFIX_.'shop` s ON (r.`id_shop` = s.`id_shop`)
			WHERE `id_lang` = '.(int)$id_lang.' AND (';

		if ($shop_ids = Shop::getContextListShopID())
			foreach ($shop_ids as $id_shop)
				$sql .= ' r.`id_shop` = '.(int)$id_shop.' OR ';

		$sql .= ' r.`id_shop` = 0 )';

		$content = Db::getInstance()->executeS($sql);

		foreach ($content as $key => $value)
			$content[$key]['text'] = Tools::substr(strip_tags($value['text']), 0, 200);

		return $content;
	}

	public function getFormValues()
	{
		$fields_value = array();
		$id_tmcmsinfo = (int)Tools::getValue('id_tmcmsinfo');

		foreach (Language::getLanguages(false) as $lang)
			if ($id_tmcmsinfo)
			{
				$tmcmsinfo = new TmfooterCMSInfoBlock((int)$id_tmcmsinfo);
				$fields_value['text'][(int)$lang['id_lang']] = $tmcmsinfo->text[(int)$lang['id_lang']];
			}
			else
				$fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');

		$fields_value['id_tmcmsinfo'] = $id_tmcmsinfo;

		return $fields_value;
	}
	
	public function hookdisplayHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'css/tmstyle.css');
	}
	
	public function hookTmFooterTop($params)
	{
		if (!$this->isCached('tmfootercmsblock.tpl', $this->getCacheId()))
		{
			$tmcmsinfos = $this->getInfos($this->context->language->id, $this->context->shop->id);
			$this->context->smarty->assign(array('tmcmsinfos' => $tmcmsinfos, 'nbblocks' => count($tmcmsinfos)));
		}

		return $this->display(__FILE__, 'tmfootercmsblock.tpl', $this->getCacheId());
	}

	public function getInfos($id_lang, $id_shop)
	{
		$sql = 'SELECT r.`id_tmcmsinfo`, r.`id_shop`, rl.`text`
			FROM `'._DB_PREFIX_.'tmcmsinfo` r
			LEFT JOIN `'._DB_PREFIX_.'tmcmsinfo_lang` rl ON (r.`id_tmcmsinfo` = rl.`id_tmcmsinfo`)
			WHERE `id_lang` = '.(int)$id_lang.' AND  `id_shop` = '.(int)$id_shop;

		return Db::getInstance()->executeS($sql);
	}

	public function installFixtures()
	{
		$return = true;
		$tab_texts = array(
			array(
				'text' => '<div class="footer-percent footer-percent1">
<div class="footer-percent-inner">
<div class="icon icon1"></div>
<div class="service-content">
<div class="title service-text">special gift cards</div>
<div class="description other-font">give the perfect gift</div>
</div>
</div>
</div>
<div class="footer-percent footer-percent2">
<div class="footer-percent-inner">
<div class="icon icon2"></div>
<div class="service-content">
<div class="title service-text">free shipping</div>
<div class="description other-font">on order over $99</div>
</div>
</div>
</div>
<div class="footer-percent footer-percent3">
<div class="footer-percent-inner">
<div class="icon icon3"></div>
<div class="service-content">
<div class="title service-text">order online</div>
<div class="description other-font">hours 8am - 11px</div>
</div>
</div>
</div>
<div class="footer-percent footer-percent4">
<div class="footer-percent-inner">
<div class="icon icon4"></div>
<div class="service-content">
<div class="title service-text">shop and save</div>
<div class="description other-font">for all spices & herbs</div>
</div>
</div>
</div>'
			),
		);

		$shops_ids = Shop::getShops(true, null, true);
		$return = true;
		foreach ($tab_texts as $tab)
		{
			$tmcmsinfo = new TmfooterCMSInfoBlock();
			foreach (Language::getLanguages(false) as $lang)
				$tmcmsinfo->text[$lang['id_lang']] = $tab['text'];
			foreach ($shops_ids as $id_shop)
			{
				$tmcmsinfo->id_shop = $id_shop;
				$return &= $tmcmsinfo->add();
			}
		}

		return $return;
	}
}