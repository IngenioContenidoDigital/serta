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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2014 PrestaShop SA
 *  @version  Release: $Revision: 7060 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_'))
	exit;

include_once _PS_MODULE_DIR_.'tmheadershippingcmsblock/tminfoHeaderClass.php';

class Tmheadershippingcmsblock extends Module
{
	public function __construct()
	{
		$this->name = 'tmheadershippingcmsblock';
		$this->tab = 'front_office_features';
		$this->version = '2.0.5';
		$this->author = 'Templatemela';
		$this->bootstrap = true;
		$this->need_instance = 0;
		parent::__construct();
		$this->displayName = $this->l('TM - Header Shipping CMS Block');
		$this->description = $this->l('Adds CMS Content anywhere');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}
	public function install()
	{
		return parent::install() && $this->installDB() && Configuration::updateValue('BLOCKCMSINFO_NBBLOCKS', 1) && $this->registerHook('displayTop') && $this->registerHook('displayHeader') && $this->installFixtures() && $this->disableDevice(Context::DEVICE_TABLET | Context::DEVICE_MOBILE);
	}
	public function installDB()
	{
		$return = true;
		$return &= Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tmheaderinfo` (
				`id_info` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL ,
				PRIMARY KEY (`id_info`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
		);
		$return &= Db::getInstance()->execute('
				CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tmheaderinfo_lang` (
				`id_info` INT UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL ,
				`text` text NOT NULL,
				PRIMARY KEY (`id_info`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;'
		);
		return $return;
	}
	public function uninstall()
	{
		// Delete configuration
		return Configuration::deleteByName('BLOCKCMSINFO_NBBLOCKS') && $this->uninstallDB() && parent::uninstall();
	}
	public function uninstallDB()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmheaderinfo`') && 
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tmheaderinfo_lang`');
	}
	public function addToDB()
	{
		if (Tools::getValue('nbblocks') != true)
		{
			for ($i = 1; $i <= (int)Tools::getValue('nbblocks'); $i++)
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'tmheaderinfo` (`text`)
					VALUES ("'.(( Tools::getValue('info'.$i.'_text') != true ) ? pSQL(Tools::getValue('info'.$i.'_text') != true) : '').'")'
				);

			return true;
		}
		return false;
	}
	public function removeFromDB()
	{
		return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'tmheaderinfo`');
	}
	public function getContent()
	{
		$html = '';
		$id_info = (int)Tools::getValue('id_info');

		if (Tools::isSubmit('savetmheadershippingcmsblock'))
		{
			if ($id_info = Tools::getValue('id_info'))
				$info = new tminfoHeaderClass((int)$id_info);
			else
				$info = new tminfoHeaderClass();
			$info->copyFromPost();
			$info->id_shop = $this->context->shop->id;

			if ($info->validateFields(false) && $info->validateFieldsLang(false))
			{
				$info->save();
				$this->_clearCache('tmheadershippingcmsblock.tpl');
			}
			else
				$html .= '<div class="conf error">'.$this->l('An error occurred while attempting to save.').'</div>';
		}
		if (Tools::isSubmit('updatetmheadershippingcmsblock') || Tools::isSubmit('addtmheadershippingcmsblock'))
		{
			$helper = $this->initForm();
			foreach (Language::getLanguages(false) as $lang)
				if ($id_info)
				{
					$info = new tminfoHeaderClass((int)$id_info);
					$helper->fields_value['text'][(int)$lang['id_lang']] = $info->text[(int)$lang['id_lang']];
				}
				else
					$helper->fields_value['text'][(int)$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang'], '');
			if ($id_info = Tools::getValue('id_info'))
			{
				$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_info');
				$helper->fields_value['id_info'] = (int)$id_info;
			}

			return $html.$helper->generateForm($this->fields_form);
		}
		else if (Tools::isSubmit('deletetmheadershippingcmsblock'))
		{
			$info = new tminfoHeaderClass((int)$id_info);
			$info->delete();
			$this->_clearCache('tmheadershippingcmsblock.tpl');
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		else
		{
			$helper = $this->initList();

			$content = $this->getListContent((int)Configuration::get('PS_LANG_DEFAULT'));
			foreach ($content as $key => $value)
				$content[$key]['text'] = Tools::substr(strip_tags($value['text']), 0, 200);

			return $html.$helper->generateList($content, $this->fields_list);
		}
		 if (Tools::getIsset(Tools::getValue('submitModule'))) {
			Configuration::updateValue('BLOCKCMSINFO_NBBLOCKS', ((Tools::getIsset(Tools::getValue('nbblocks')) && Tools::getValue('nbblocks') != '') ? (int)Tools::getValue('nbblocks') : ''));
			if ($this->removeFromDB() && $this->addToDB())
			{
				$this->_clearCache('tmheadershippingcmsblock.tpl');
				$output = '<div class="conf confirm">'.$this->l('The block configuration has been updated.').'</div>';
			}
			else
				$output = '<div class="conf error"><img src="../img/admin/disabled.gif"/>'.$this->l('An error occurred while attempting to save.').'</div>';
		}
	}
	protected function getListContent($id_lang, $id_shop = null)
	{
		$content = Db::getInstance()->executeS('
			SELECT r.`id_info`, r.`id_shop`, rl.`text`
			FROM `'._DB_PREFIX_.'tmheaderinfo` r
			LEFT JOIN `'._DB_PREFIX_.'tmheaderinfo_lang` rl ON (r.`id_info` = rl.`id_info`)
			WHERE `id_lang` = '.(int)$id_lang.($id_shop ? ' AND id_shop='.bqSQL((int)$id_shop) : '').
			(Tools::getIsset('tmheadershippingcmsblockOrderby') && Tools::getIsset('tmheadershippingcmsblockOrderway') ?
				' ORDER BY `'.bqSQL(Tools::getValue('tmheadershippingcmsblockOrderby')).'` '.bqSQL(Tools::getValue('tmheadershippingcmsblockOrderway')) : '')
		);
		return $content;
	}
	protected function initForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('New custom CMS block'),
			),
			'input' => array(
				array(
					'type' => 'textarea',
					'label' => $this->l('Text'),
					'lang' => true,
					'name' => 'text',
					'cols' => 40,
					'rows' => 10,
					'class' => 'rte',
					'autoload_rte' => true,

				)
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

		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'tmheadershippingcmsblock';
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
		$helper->submit_action = 'savetmheadershippingcmsblock';
		$helper->toolbar_btn = array(
			'save' =>
				array(
					'desc' => $this->l('Save'),
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				),
			'back' =>
				array(
					'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
					'desc' => $this->l('Back to list')
				)
		);

		return $helper;
	}

	protected function initList()
	{
		$this->fields_list = array(
			'id_info' => array(
				'title' => $this->l('Custom block number'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			),
			'text' => array(
				'title' => $this->l('Custom block text'),
				'type' => 'text',
				'search' => false,
				'orderby' => false,
			),
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_list['id_shop'] = array(
				'title' => $this->l('ID Shop'),
				'align' => 'center',
				'width' => 25,
				'type' => 'int',
				'search' => false
			);
		}

		$helper = new HelperList();
		$helper->shop_link_type = '';
		$helper->simple_header = false;
		$helper->identifier = 'id_info';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = true;
		$helper->image_type = 'jpg';
		$helper->toolbar_btn['new'] = array(
			'href' => AdminController::$currentIndex.'&configure='.$this->name.'&add'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			'desc' => $this->l('Add new')
		);

		$helper->title = $this->displayName;
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

		return $helper;
	}

	public function hookdisplayTop()
	{
		if (!$this->isCached('tmheadershippingcmsblock.tpl', $this->getCacheId()))
		{
			$infos = $this->getListContent($this->context->language->id, $this->context->shop->id);
			$this->context->smarty->assign(array('infos' => $infos, 'nbblocks' => count($infos)));
		}

		return $this->display(__FILE__, 'tmheadershippingcmsblock.tpl', $this->getCacheId());
	}


	public function hookHeader($params)
	{
	}
	public function installFixtures()
	{
		$return = true;
		$tab_texts = array(
			array(
				'text' => '<div class="header_shipping">

<div class="free_shipping">

<div class="first">Free Shipping</div>

<div class="second">on orders over $99</div>

</div>

</div>'
			),
		);
		foreach ($tab_texts as $tab)
		{
			$info = new tminfoHeaderClass();
			foreach (Language::getLanguages(false) as $lang)
				$info->text[$lang['id_lang']] = $tab['text'];
			$info->id_shop = $this->context->shop->id;
			$return &= $info->save();
		}

		return $return;
	}
}
