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

class tmcategorylist extends Module {

	private $spacer_size = '5';	
	private $_postErrors  = array();

	public function __construct() {
		$this->name = 'tmcategorylist';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Templatemela';
		$this->bootstrap = true;
		parent::__construct();
		$this->_html = '';
		$this->html = '';
		$this->displayName = $this->l('TM - Category List Block');
		$this->description = $this->l('Adds a block with sub category.');
	}

	public function install() {
		 Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tmcategorylist` (
			`id_categoryslider` int(10)  NOT NULL AUTO_INCREMENT,
			`image` varchar(128) NOT NULL,
			`id_shop` int(10)  NOT NULL,
			`name_category` varchar(128) NOT NULL,
			`id_category` int(10)  NOT NULL,
			PRIMARY KEY (`id_categoryslider`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'
		);
	
		$arrayDefault = array('CAT3','CAT4','CAT5','CAT7');
		$cateDefault = implode(',',$arrayDefault);
		Configuration::updateGlobalValue($this->name.'category',$cateDefault);
		return parent :: install() && $this->registerHook('header') && $this->registerHook('home');
	}
	
	public function uninstall() {
		Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'tmcategorylist`');
		Configuration::deleteByName($this->name . 'category');
		$this->_clearCache('tmcategorylist.tpl');
		return parent::uninstall();
	}

	public function hookHeader($params){
		$this->context->controller->addCSS(($this->_path).'views/css/tmcategorylist.css');
	}

	public function hookDisplayHome($params) {
		$cateSelected = Configuration::get($this->name . 'category');
		$cateArray = explode(',', $cateSelected); 
		$id_lang =(int) Context::getContext()->language->id;
		$id_shop = (int) Context::getContext()->shop->id;
		$arrayCategory = array();
		foreach($cateArray as $id_category) {
			$id_category = str_replace('CAT','',$id_category);
			$category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
			$child_cate = Category::getChildren($id_category,$id_lang);
			$categoryids = $this->getimage($id_category,$id_shop);
			foreach ($categoryids as $categoryid);{
				$html = '';
				if(isset($categoryid)) {
					$arrayCategory[] = array('id' => $id_category, 'html'=>$html, 'name'=> $category->name, 'category'=> $category, 'child_cate'=>$child_cate,'cate_id' =>$categoryid);
				} else {
					$arrayCategory[] = array('id' => $id_category, 'html'=>$html, 'name'=> $category->name, 'category'=> $category, 'child_cate'=>$child_cate,'cate_id' => 0);
				}
			}
		}
		$this->smarty->assign(array(
			'tmcategoryinfos' => $arrayCategory,
		));
		return $this->display(__FILE__, 'tmcategorylist.tpl');
	}
	
	public function getContent() {
		if (Tools::isSubmit('submitUpdate')) {
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else {
				foreach ($this->_postErrors AS $err) {
					$this->_html .= '<div class="alert error">' . $err . '</div>';
				}
			}
		}
		if(Tools::isSubmit('submitUpdatethumb')){
			$id_cate = Tools::getValue('id_cate');
			$id_lang = (int) Context::getContext()->language->id;
			$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
			$id_shop = (int) Context::getContext()->shop->id;
			$category = new Category((int)$id_cate, (int)$id_lang, (int)$id_shop);
			$name_cate = $category->name;
			if($_FILES['imagethumb']['tmp_name']!='') {
				$upload_path = _PS_MODULE_DIR_.$this->name.'/views/img/';
				$filename = rand(0,1000).'-'.Tools::strtolower($_FILES['imagethumb']['name']);
				if(move_uploaded_file($_FILES['imagethumb']['tmp_name'],$upload_path .$filename)) {
					$cate_exit = $this->getimage($id_cate,$id_shop);
					if($cate_exit ==null) {
						$this->addcategoryicon($id_cate,$name_cate,$filename,$id_shop);
						$this->_html .= $this->displayConfirmation($this->l('Updated Successfully'));
					} else {
						$this->updatecategoryicon($id_cate,$name_cate,$filename);
						$this->_html .= $this->displayConfirmation($this->l('Add Image Successfully'));
					}
				}
			}
		}
		if (Tools::isSubmit('deletetmcategorylist') && Tools::getValue('id_categoryslider')) {
			$this->deleteCategoryId(Tools::getValue('id_categoryslider'));
			$this->_html .= $this->displayConfirmation($this->l('Deleted Successfully'));
		}
		return $this->_html .$this->_displayForm() .$this->imageForm() .$this->renderList();
	}

	public static function deleteCategoryId($id) {
		$sql = 'DELETE FROM `'._DB_PREFIX_.'tmcategorylist` WHERE `id_categoryslider` = '.(int)$id;
		Db::getInstance()->execute($sql);
	}

	public function getSelectOptionsHtml($options = NULL, $name = NULL, $selected = NULL) {
		$html = "";
		$html .='<select name =' . $name . ' style="width:130px">';
		if (count($options) > 0) {
			foreach ($options as $key => $val) {
				if (trim($key) == trim($selected)) {
					$html .='<option value=' . $key . ' selected="selected">' . $val . '</option>';
				} else {
					$html .='<option value=' . $key . '>' . $val . '</option>';
				}
			}
		}
		$html .= '</select>';
		return $html;
	}

	private function _postProcess() {
		Configuration::updateValue($this->name . 'category', implode(',', Tools::getValue('list_cate')));
		$this->_html .= $this->displayConfirmation($this->l('Configuration Updated'));
	}

	public  function addcategoryicon($id_cate,$name_cate,$filename,$id_shop){
		$res = Db::getInstance()->execute('INSERT  INTO `'._DB_PREFIX_.'tmcategorylist`(`id_category`,`name_category`,`image`,`id_shop`) VALUES ('.(int)$id_cate.', \''.pSQL($name_cate).'\', \''.pSQL($filename).'\','.(int)$id_shop.')');
		return $res ;
	}

	public function  updatecategoryicon($id_cate,$name_cate,$filename){
		$res = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'tmcategorylist` SET `name_category` = \''.pSQL($name_cate).'\',`img` =\''.pSQL($filename).'\' WHERE `id_category` = '.(int)$id_cate);
		return $res ;
	}

	public  function _displayForm(){
		$spacer = str_repeat('&nbsp;', $this->spacer_size);
		$id_lang = (int) Context::getContext()->language->id;
		$options =    $this->getCategoryOption(1, (int)$id_lang, (int)Shop::getContextShopID());
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Category List'),
					'icon' => 'icon-link'
				),
				'input' => array(
					array(
						'type' => 'selectlist',
						'label' => 'Show Link/Label Category:',
						'name' => 'list_cate',
						'multiple'=>true,
						'size' => 500
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitUpdate',
				),
			)
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->submit_action = 'submitUpdate';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->options = $options;
		$helper->identifier = $this->identifier;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$module = _PS_MODULE_DIR_ ;
		$helper->tpl_vars = array(
			'module' =>$module,
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'options' => $options,
		);
		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}

	public  function imageForm(){
		$id_lang = (int)Context::getContext()->language->id;
		$fields_form = array(
			'form' => array(
				'legend' => array(
				'title' => $this->l('Upload Image For Categories'),
				'icon' => 'icon-link'
			),
			'input' => array(
				array(
					'type' => 'file',
					'label' => 'Upload Category Image',
					'name' => 'imagethumb',
					'id' => 'imagethumb',
				),
				array(
					'type' => 'cateimage',
					'label' => 'Categories for Image:',
					'name' => 'id_cate',
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'name' => 'submitUpdatethumb',
			),
		));
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$helper->submit_action = 'submitUpdatethumb';
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$module = _PS_MODULE_DIR_ ;
		$helper->tpl_vars = array(
			'module' =>$module,
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'options_image' => $this->getCategoryOptions(1, (int)$id_lang, (int)Shop::getContextShopID()),
		);
		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}

	public function renderList() {
		$links = $this->getcategoryicon();
		$fields_list = array(
			'id_categoryslider' => array(
				'title' => $this->l(' ID'),
				'type' => 'text',
			),
			'id_shop' => array(
				'title' => $this->l('ID shop'),
				'type' => 'text',
			),
			'image' => array(
				'title' => $this->l('Icon Category '),
				'type' => 'text',
			),
			'id_category' => array(
				'title' => $this->l('ID Category'),
				'type' => 'text',
			),
			'name_category' => array(
				'title' => $this->l('Name Category'),
				'type' => 'text',
			),
		);
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_categoryslider';
		$helper->table = 'tmcategorylist';
		$helper->actions = array( 'delete');
		$helper->show_toolbar = false;
		$helper->module = $this;
		$helper->title = $this->l('Link list');
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		return $helper->generateList($links, $fields_list);
	}

	public function  getcategoryicon() {
		$sql = 'SELECT id_categoryslider ,image,id_category,name_category, id_shop FROM '._DB_PREFIX_.'tmcategorylist';
		return Db::getInstance()->executeS($sql);
	}

	public function  getimage($id,$id_shop){
		$sql = 'SELECT id_categoryslider ,image,id_category,name_category FROM '._DB_PREFIX_.'tmcategorylist WHERE id_category = '.(int)$id.' and id_shop ='.(int)$id_shop.'' ;
		return Db::getInstance()->executeS($sql);
	}

	public function getImageId($id) {
		if ((int)$id > 0) {
			$sql = 'SELECT b.`image`, b.`id_category`, FROM `'._DB_PREFIX_.'tmcategorylist` b WHERE b.id_categoryslider ='.(int)$id.'';

			if (!$results = Db::getInstance()->getRow($sql))
				return false;
		}
		return false;
	}

	public function getConfigFieldsValues() {
		$fields_values = array(
			'list_cate' => Tools::getValue('list_cate', Configuration::get($this->name.'category')),
			'id_cate' => Tools::getValue('id_cate', Configuration::get('id_cate')),
			'name_category' => Tools::getValue('name_category', Configuration::get($this->name.'_name_category')),
		);

		if (Tools::getIsset('updatetmcategorylist') && (int)Tools::getValue('id_categoryslider') > 0)
			$fields_values = array_merge($fields_values, $this->getImageId((int)Tools::getValue('id_categoryslider')));
			return $fields_values ;
	}

	public function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true) {
		$cateCurrent = Configuration::get($this->name . 'category');
		$cateCurrent = explode(',', $cateCurrent);
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
		if (is_null($category->id))
			return;
		if ($recursive) {
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$category->level_depth);
		}

		$shop = (object) Shop::getShop((int)$category->getShopID());
		if (in_array('CAT'.(int)$category->id, $cateCurrent)) {
			$this->_html .= '<option value="CAT'.(int)$category->id.'" selected ="selected" >'.(isset($spacer) ? $spacer : '').$category->name.' ('.$shop->name.')</option>';
		} else {
			$this->_html .= '<option value="CAT'.(int)$category->id.'">'.(isset($spacer) ? $spacer : '').$category->name.' ('.$shop->name.')</option>';
		}

		if (isset($children) && count($children))
			foreach ($children as $child)
				$this->getCategoryOption((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
				return $this->_html ;
	}

	public function getCategoryOptions($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true) {
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
		if (is_null($category->id))
			return;
		if ($recursive) {
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', $this->spacer_size * (int)$category->level_depth);
		}
		$shop = (object)Shop::getShop((int)$category->getShopID());
		$this->html .= '<option value="'.(int)$category->id.'">'.(isset($spacer) ? $spacer : '').$category->name.' ('.$shop->name.')</option>';
		if (isset($children) && count($children))
			foreach ($children as $child)
				$this->getCategoryOptions((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
				return $this->html ;
	}
}