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

class tmcategoryslider extends Module {
	
	private $_postErrors  = array();
	public function __construct() {
		$this->name 		= 'tmcategoryslider';
		$this->tab 			= 'front_office_features';
		$this->version 		= '1.6.0';
        $this->bootstrap    = true;
        $this->_html        = '';
		$this->author 		= 'Templatemela';
		$this->displayName 	= $this->l('TM - Category Product Tab Slider');
		$this->description 	= $this->l('Category Product Tab Slider');       
		parent :: __construct();
	}
	
	public function install() {
    Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'tm_categoryslider` (
			  `id_categoryslider` int(10)  NOT NULL AUTO_INCREMENT,
			  `image` varchar(128) NOT NULL,
			  `id_shop` int(10)  NOT NULL,
			  `name_category` varchar(128) NOT NULL,
			  `id_category` int(10)  NOT NULL,
			  PRIMARY KEY (`id_categoryslider`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 AUTO_INCREMENT=1'
        );
           
		$arrayDefault = array('CAT3','CAT4','CAT5');
		$cateDefault = implode(',',$arrayDefault);
		Configuration::updateGlobalValue($this->name.'category',$cateDefault);
		return parent :: install()
			&& $this->registerHook('header') && $this->registerHook('home');
	}
	
    public function uninstall() {
        Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'tm_categoryslider`');
		Configuration::deleteByName($this->name . 'category');
        $this->_clearCache('tmcategoryslider.tpl');
        return parent::uninstall();
    }
	

    public function hookHeader($params){
    }
	public function hookHome($params) {		
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')

			return;
		
		$catSelected = Configuration::get($this->name . 'category');
		$cateArray = explode(',', $catSelected); 
		$id_lang =(int) Context::getContext()->language->id;
		$id_shop = (int) Context::getContext()->shop->id;
		$arrayCategory = array();
		foreach($cateArray as $id_category) {
			$id_category = str_replace('CAT','',$id_category);
			$category = new Category((int) $id_category, (int) $id_lang, (int) $id_shop);
			$categoryProducts = $category->getProducts($this->context->language->id, 0, 20);              
               
				$html = '';
				
				if($categoryProducts) {
					$arrayCategory[] = array('id' => $id_category, 'html'=>$html, 'name'=> $category->name, 'product' => $categoryProducts);
					
				}
			
		}
		$this->smarty->assign(array(
			'tmcategoryinfos' => $arrayCategory,
		));
		return $this->display(__FILE__, 'tmcategoryslider.tpl');
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
            
        return $this->_html . $this->_displayForm();
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
        $this->_html .= $this->displayConfirmation($this->l('Configuration updated'));
    }
	public  function _displayForm(){
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
            ));
			
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
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'options' => $options,
        );
        $helper->override_folder = '/';
        return $helper->generateForm(array($fields_form));
	}


   
  
  
   
     public function getCategoryOption($id_category = 1, $id_lang = false, $id_shop = false, $recursive = true) {
		$cateCurrent = Configuration::get($this->name . 'category');
		$cateCurrent = explode(',', $cateCurrent);
		$id_lang = $id_lang ? (int)$id_lang : (int)Context::getContext()->language->id;
		$category = new Category((int)$id_category, (int)$id_lang, (int)$id_shop);
		if (is_null($category->id))
			return;
		if ($recursive)
		{
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', 5 * (int)$category->level_depth);
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
		if ($recursive)
		{
			$children = Category::getChildren((int)$id_category, (int)$id_lang, true, (int)$id_shop);
			$spacer = str_repeat('&nbsp;', 5 * (int)$category->level_depth);
		}
		$shop = (object)Shop::getShop((int)$category->getShopID());
					$this->html .= '<option value="'.(int)$category->id.'">'.(isset($spacer) ? $spacer : '').$category->name.' ('.$shop->name.')</option>';
		if (isset($children) && count($children))
			foreach ($children as $child)
				$this->getCategoryOptions((int)$child['id_category'], (int)$id_lang, (int)$child['id_shop']);
         return $this->html ;
    }

}
