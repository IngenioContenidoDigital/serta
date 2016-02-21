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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class TmBlockSearch extends Module
{
	public function __construct()
	{
		$this->name = 'tmblocksearch';
		$this->tab = 'search_filter';
		$this->version = '1.5.3';
		$this->author = 'Templatemela';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('TM - Quick search block');
		$this->description = $this->l('Adds a quick search field to your website.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('displayTopColumn') || !$this->registerHook('header') || !$this->registerHook('displayMobileTopSiteMap'))
			return false;
		return true;
	}

	public function hookdisplayMobileTopSiteMap($params)
	{
		$this->smarty->assign(array('hook_mobile' => true, 'instantsearch' => false));
		$params['hook_mobile'] = true;
		return $this->hookTop($params);
	}

	/*
	public function hookDisplayMobileHeader($params)
	{
		if (Configuration::get('PS_SEARCH_AJAX'))
			$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
	}
	*/

	public function hookHeader($params)
	{
		$this->context->controller->addJqueryPlugin('autocomplete');
		$this->context->controller->addCSS(_THEME_CSS_DIR_.'product_list.css');
		Media::addJsDef(array('search_url' => $this->context->link->getPageLink('search', Tools::usingSecureMode())));
	}

	public function hookLeftColumn($params)
	{
		return $this->hookRightColumn($params);
	}

	public function hookRightColumn($params)
	{
		if (Tools::getValue('search_query') || !$this->isCached('tmblocksearch.tpl', $this->getCacheId()))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'tmblocksearch_type' => 'block',
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		Media::addJsDef(array('tmblocksearch_type' => 'block'));
		return $this->display(__FILE__, 'tmblocksearch.tpl', Tools::getValue('search_query') ? null : $this->getCacheId());
	}

	public function hookdisplayTopColumn($params)
	{
		$key = $this->getCacheId('tmblocksearch-top'.((!isset($params['hook_mobile']) || !$params['hook_mobile']) ? '' : '-hook_mobile'));
		if (Tools::getValue('search_query') || !$this->isCached('tmblocksearch-top.tpl', $key))
		{
			$this->calculHookCommon($params);
			$this->smarty->assign(array(
				'tmblocksearch_type' => 'top',
				'search_query' => (string)Tools::getValue('search_query')
				)
			);
		}
		Media::addJsDef(array('tmblocksearch_type' => 'top'));
		return $this->display(__FILE__, 'tmblocksearch-top.tpl', Tools::getValue('search_query') ? null : $key);
	}

	

	private function calculHookCommon($params)
	{
		$this->smarty->assign(array(
			'ENT_QUOTES' =>		ENT_QUOTES,
			'search_ssl' =>		Tools::usingSecureMode(),
			'instantsearch' =>	Configuration::get('PS_INSTANT_SEARCH'),
            'form_link' => 		$this->context->link->getModuleLink('tmblocksearch', 'search'),
			'self' =>			dirname(__FILE__),
			'category_html' => 	$this->getHtmlCategories(),
		));

		return true;
	}
	
	/**
	* Source From :
	* modules\blockcategories\blockcategories.php
	*/
	
	public function getHtmlCategories()
	{
        $maxdepth = Configuration::get('BLOCK_CATEG_MAX_DEPTH');
		
		// Get all groups for this customer and concatenate them as a string: "1,2,3..."
		$groups = implode(', ', Customer::getGroupsStatic((int)$this->context->customer->id));
		if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT c.id_parent, c.id_category, cl.name, cl.description, cl.link_rewrite
			FROM `'._DB_PREFIX_.'category` c
			'.Shop::addSqlAssociation('category', 'c').'
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('cl').')
			LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
            WHERE (c.`active` = 1 OR c.`id_category` = '.(int)Configuration::get('PS_HOME_CATEGORY').')
			AND c.`id_category` != '.(int)Configuration::get('PS_ROOT_CATEGORY').'
			'.((int)($maxdepth) != 0 ? ' AND `level_depth` <= '.(int)($maxdepth) : '').'
			AND cg.`id_group` IN ('.pSQL($groups).')
			ORDER BY `level_depth` ASC, '.(Configuration::get('BLOCK_CATEG_SORT') ? 'cl.`name`' : 'category_shop.`position`').' '.(Configuration::get('BLOCK_CATEG_SORT_WAY') ? 'DESC' : 'ASC')))
			return;
		$resultParents = array();
		$resultIds = array();

		foreach ($result as &$row)
		{
			$resultParents[$row['id_parent']][] = &$row;
			$resultIds[$row['id_category']] = &$row;
		}
		//$nbrColumns = Configuration::get('BLOCK_CATEG_NBR_COLUMNS_FOOTER');
		$nbrColumns = (int)Configuration::get('BLOCK_CATEG_NBR_COLUMN_FOOTER');
		if (!$nbrColumns or empty($nbrColumns))
			$nbrColumns = 3;
		$numberColumn = abs(count($result) / $nbrColumns);
		$widthColumn = floor(100 / $nbrColumns);
		$this->smarty->assign('numberColumn', $numberColumn);
		$this->smarty->assign('widthColumn', $widthColumn);

		$blockCategTree = $this->getTree($resultParents, $resultIds, Configuration::get('BLOCK_CATEG_MAX_DEPTH'));
		unset($resultParents, $resultIds);

		$this->smarty->assign('tmblockCategTree', $blockCategTree);
		$this->smarty->assign('current_category', Tools::getValue('id_category'));
		$this->smarty->assign('home_category', new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id));

		$this->smarty->assign('tmbranche_tpl_path', _PS_MODULE_DIR_.'tmblocksearch/category-tree-branch.tpl');
		
		$display = $this->display(__FILE__, 'categories.tpl');
	
		return $display;
	}
    
	public function getTree($resultParents, $resultIds, $maxDepth, $id_category = null, $currentDepth = 0, $spacer = '')
	{
		if (is_null($id_category))
			$id_category = $this->context->shop->getCategory();
		$spacer .= '&nbsp;&nbsp;';
		$children = array();
		if (isset($resultParents[$id_category]) && count($resultParents[$id_category]) && ($maxDepth == 0 || $currentDepth < $maxDepth))
			foreach ($resultParents[$id_category] as $subcat)
				$children[] = $this->getTree($resultParents, $resultIds, $maxDepth, $subcat['id_category'], $currentDepth + 1, $spacer);

		if (isset($resultIds[$id_category])) 
		{
			$link = $this->context->link->getCategoryLink($id_category, $resultIds[$id_category]['link_rewrite']);
			$name = $resultIds[$id_category]['name'];
			$desc = $resultIds[$id_category]['description'];
		}
		else
			$link = $name = $desc = '';
			
		$return = array(
			'id' => $id_category,
			'link' => $link,
			'name' => $name,
			'desc'=> $desc,
			'children' => $children,
			'spacer' => $spacer
		);
		return $return;
	}

}

