<?php
/*
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class TmImageHover extends Module
{
	public function __construct()
	{
		$this->name = 'tmimagehover';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->need_instance = 0;
		$this->author = 'Templatemela';
		parent::__construct();
		$this->displayName = $this->l('TM - Product Hover Image ');
		$this->description = $this->l('Adds addtional Image to Product');
	}

	public function install()
	{
		return parent::install() && $this->registerHook('header') && $this->registerHook('displayTmHoverImage');
	}
	
	public function hookDisplayTmHoverImage($params) {
	
		if (!$this->isCached('tmimagehover.tpl', $this->getCacheId($params['id_product']))) {
			$id_lang = $this->context->language->id;
			$obj = new Product((int) ($params['id_product']), false, $id_lang);
			$images = $obj->getImages($this->context->language->id);
			$_images = array();
			if (!empty($images)) {
				foreach ($images as $k => $image) {
					if(!$image['cover']) {
						$_images[] = $obj->id . '-' . $image['id_image'];
					}
				}
			}
			$this->smarty->assign(array(
				'link_rewrite' => $params['link_rewrite'],
				'images' => $_images
			));
		}
		return $this->display(__FILE__, 'tmimagehover.tpl', $this->getCacheId($params['id_product']));
	}
}