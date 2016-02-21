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

class Tmblocklink1 extends Module
{
	/* @var boolean error */
	protected $error = false;

	public function __construct()
	{
		$this->name = 'tmblocklink1';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Templatemela';
		$this->need_instance = 0;

		$this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('TM - Static Link block1');
		$this->description = $this->l('Adds a block with additional links.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete all your links?');
	}

	public function install()
	{
		if (!parent::install() || !$this->registerHook('footer') || !$this->registerHook('leftColumn'))
			return false;
		$success = Configuration::updateValue('PS_TMBLOCKLINK1_TITLE', array('1' => 'Block Title1', '2' => 'Bloc lien'));
		$success &= Db::getInstance()->execute('
		CREATE TABLE '._DB_PREFIX_.'tmblocklink1 (
		`id_tmblocklink1` int(10) NOT NULL AUTO_INCREMENT, 
		`new_window` TINYINT(1) NOT NULL,
		PRIMARY KEY(`id_tmblocklink1`))
		ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
		$success &= Db::getInstance()->execute('
		CREATE TABLE '._DB_PREFIX_.'tmblocklink1_shop (
		`id_tmblocklink1` int(10) NOT NULL AUTO_INCREMENT, 
		`id_shop` int(10) NOT NULL,
		PRIMARY KEY(`id_tmblocklink1`, `id_shop`))
		ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
		$success &= Db::getInstance()->execute('
		CREATE TABLE '._DB_PREFIX_.'tmblocklink1_lang (
		`id_tmblocklink1` int(10) NOT NULL,
		`id_lang` int(10) NOT NULL,
		`text` varchar(62) NOT NULL,
		`url` varchar(254) NOT NULL,
		PRIMARY KEY(`id_tmblocklink1`, `id_lang`))
		ENGINE='._MYSQL_ENGINE_.' default CHARSET=utf8');
		if (!$success)
		{
			parent::uninstall();

			return false;
		}
		// Hook the module either on the left or right column
		return true;
	}
	public function uninstall()
	{
		if (!parent::uninstall() || !Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'tmblocklink1') 
		|| !Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'tmblocklink1_lang') 
		|| !Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.'tmblocklink1_shop') 
		|| !Configuration::deleteByName('PS_TMBLOCKLINK1_TITLE') 
		|| !Configuration::deleteByName('PS_TMBLOCKLINK1_URL'))
		return false;
		return true;
	}
	public function hookLeftColumn($params)
	{
		$links = $this->getLinks();

		$this->smarty->assign(array(
			'tmblocklink1_links' => $links,
			'title' => Configuration::get('PS_TMBLOCKLINK1_TITLE', $this->context->language->id),
			'url' => Configuration::get('PS_TMBLOCKLINK1_URL'),
			'lang' => 'text_'.$this->context->language->id,
			'linkurl' => 'url_'.$this->context->language->id
		));
		if (!$links)
			return false;
		return $this->display(__FILE__, 'tmblocklink1.tpl');
	}
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	public function hookFooter($params)
	{
		$links = $this->getLinks();
		$this->smarty->assign(array(
			'tmblocklink1_links' => $links,
			'title' => Configuration::get('PS_TMBLOCKLINK1_TITLE', $this->context->language->id),
			'url' => Configuration::get('PS_TMBLOCKLINK1_URL', $this->context->language->id),
			'lang' => 'text_'.$this->context->language->id,
			'linkurl' => 'url_'.$this->context->language->id
		));
		if (!$links)
			return false;
		return $this->display(__FILE__, 'tmblocklink1_footer.tpl');
	}
	/**
	 * @param $id
	 *
	 * @return bool|array
	 */
	public function getLinkById($id)
	{
		$link = '';
		if ((int)$id > 0)
		{
			$sql = 'SELECT b.`id_tmblocklink1`, b.`new_window` FROM `'._DB_PREFIX_.'tmblocklink1` b WHERE b.id_tmblocklink1='.$id;
			if (!$results = Db::getInstance()->getRow($sql))
				return false;

				$link['id_tmblocklink1'] = $results['id_tmblocklink1'];
				/*$link['url'] = $results['url'];*/
				$link['newWindow'] = $results['new_window'];

			$results_lang = Db::getInstance()->executeS('SELECT `id_lang`, `text` , `url` FROM '._DB_PREFIX_.'tmblocklink1_lang WHERE `id_tmblocklink1`='.(int)$link['id_tmblocklink1']);
			foreach ($results_lang as $result_lang)
			{
				$link['text'][$result_lang['id_lang']] = $result_lang['text'];
				$link['url'][$result_lang['id_lang']] = $result_lang['url'];
			}
			return $link;
		}
		return false;
	}
	public function getLinks()
	{
		$result = array();
		// Get id and url
		$sql = 'SELECT b.`id_tmblocklink1`, b.`new_window`
				FROM `'._DB_PREFIX_.'tmblocklink1` b';
		if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			$sql .= ' JOIN `'._DB_PREFIX_.'tmblocklink1_shop` bs ON b.`id_tmblocklink1` = bs.`id_tmblocklink1` AND bs.`id_shop` IN ('.implode(', ', Shop::getContextListShopID()).') ';
		$sql .= (int)Configuration::get('PS_TMBLOCKLINK1_ORDERWAY') == 1 ? ' ORDER BY `id_tmblocklink1` DESC' : '';

		if (!$links = Db::getInstance()->executeS($sql))
			return false;

		$i = 0;
		foreach ($links as $link)
		{
			$result[$i]['id'] = $link['id_tmblocklink1'];
			/*$result[$i]['url'] = $link['url'];*/
			$result[$i]['newWindow'] = $link['new_window'];
			// Get multilingual text

			if (!$texts = Db::getInstance()->executeS('SELECT `id_lang`, `text` ,`url` FROM '._DB_PREFIX_.'tmblocklink1_lang WHERE `id_tmblocklink1`='.(int)$link['id_tmblocklink1']))
				return false;
			foreach ($texts as $text)
			{
				$result[$i]['text_'.$text['id_lang']] = $text['text'];
				$result[$i]['url_'.$text['id_lang']] = $text['url'];
			}	
			$i++;
		}
		return $result;
	}
	public function addLink()
	{
		$row = '';
		if (!($languages = Language::getLanguages(true)))
			return false;
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		
		
		if ((int)Tools::getValue('id_tmblocklink1') > 0)
		{
			$id_link = (int)Tools::getValue('id_tmblocklink1');
			if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'tmblocklink1 SET `new_window` = '.(Tools::getIsset('newWindow') ? 1 : 0).' WHERE `id_tmblocklink1` = '.(int)$id_link))
				return false;
			if (!Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tmblocklink1_lang WHERE `id_tmblocklink1` = '.(int)$id_link))
				return false;

			foreach ($languages as $language)
				if (Tools::getValue('text_'.$language['id_lang']) != false && Tools::getValue('url_'.$language['id_lang']) != false)
				{
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'tmblocklink1_lang VALUES ('.(int)$id_link.', '.(int)($language['id_lang']).', \''.pSQL(Tools::getValue('text_'.$language['id_lang'])).'\',  \''.pSQL(Tools::getValue('url_'.$language['id_lang'])).'\')'))
						return false;
				}
				else
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'tmblocklink1_lang VALUES ('.(int)$id_link.', '.$language['id_lang'].', \''.pSQL(Tools::getValue('text_'.$id_lang_default)).'\' , \''.pSQL(Tools::getValue('url_'.$id_lang_default)).'\')'))
						return false;
		}
		else
		{
			if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'tmblocklink1 
														VALUES (NULL, '.((Tools::getValue('newWindow') && Tools::getValue('newWindow')) == 'on' ? 1 : 0).')') || !$id_link = Db::getInstance()->Insert_ID())
				return false;

			foreach ($languages as $language)
				if (Tools::getValue('text_'.$language['id_lang']) != false && Tools::getValue('url_'.$language['id_lang']) != false)
				{
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'tmblocklink1_lang 
																VALUES ('.(int)$id_link.', '.(int)$language['id_lang'].', \''.pSQL(Tools::getValue('text_'.$language['id_lang'])).'\' , \''.pSQL(Tools::getValue('url_'.$language['id_lang'])).'\')'))
						return false;
				}
				else
					if (!Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'tmblocklink1_lang VALUES ('.(int)$id_link.', '.(int)($language['id_lang']).', \''.pSQL(Tools::getValue('text_'.$id_lang_default)).'\' , \''.pSQL(Tools::getValue('url_'.$id_lang_default)).'\')'))
						return false;
		}
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tmblocklink1_shop WHERE id_tmblocklink1='.(int)$id_link);

		if (!Shop::isFeatureActive())
		{
			Db::getInstance()->insert('tmblocklink1_shop', array(
				'id_tmblocklink1' => (int)$id_link,
				'id_shop' => (int)Context::getContext()->shop->id,
			));
		}
		else
		{
			$assos_shop = Tools::getValue('checkBoxShopAsso_configuration');
			if (empty($assos_shop))
				return false;
			foreach ($assos_shop as $id_shop => $row)
				Db::getInstance()->insert('tmblocklink1_shop', array(
					'id_tmblocklink1' => (int)$id_link,
					'id_shop' => (int)$id_shop,
				));
		}
		return true;
	}

	public function deleteLink()
	{
		return (Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tmblocklink1 WHERE `id_tmblocklink1` = '.(int)Tools::getValue('id')) && 
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tmblocklink1_shop WHERE `id_tmblocklink1` = '.(int)Tools::getValue('id')) && 
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tmblocklink1_lang WHERE `id_tmblocklink1` = '.(int)Tools::getValue('id')));
	}
	public function updateTitle()
	{
		$languages = Language::getLanguages();
		$result = array();
		foreach ($languages as $language)
			$result[$language['id_lang']] = Tools::getValue('title_'.$language['id_lang']);
		if (!Configuration::updateValue('PS_TMBLOCKLINK1_TITLE', $result))
			return false;

		return Configuration::updateValue('PS_TMBLOCKLINK1_URL', Tools::getValue('title_url'));
	}	
	public function getContent()
	{
		$this->_html = '';
		// Add a link
		if (Tools::isSubmit('submitLinkAdd') /* || Tools::isSubmit('updateblocklink')*/)
		{
			if (Tools::getValue('text_'.Configuration::get('PS_LANG_DEFAULT')) != true || Tools::getValue('url_'.Configuration::get('PS_LANG_DEFAULT')) != true)
				$this->_html .= $this->displayError($this->l('You must fill in all fields.'));
			elseif (!Validate::isUrl(str_replace('http://', '', Tools::getValue('url_'.Configuration::get('PS_LANG_DEFAULT')))))
				$this->_html .= $this->displayError($this->l('Bad URL'));
			else
				if ($this->addLink())
					$this->_html .= $this->displayConfirmation($this->l('The link has been added.'));
				else
					$this->_html .= $this->displayError($this->l('An error occurred during link creation.'));
		}
		
		// Update the block title
		elseif (Tools::isSubmit('submitTitle'))
		{
			if (Tools::getValue('title_'.Configuration::get('PS_LANG_DEFAULT')) != true)
				$this->_html .= $this->displayError($this->l('"title" field cannot be empty.'));
			elseif ((!preg_match('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', Tools::getValue('title_url'))) && (!preg_match('/#/', Tools::getValue('title_url'))))
				$this->_html .= $this->displayError($this->l('The \'URL\' field is invalid'));
			elseif (!Validate::isGenericName( Tools::getValue('title_'.Configuration::get('PS_LANG_DEFAULT'))))
				$this->_html .= $this->displayError($this->l('The \'title\' field is invalid'));
			elseif (!$this->updateTitle())
				$this->_html .= $this->displayError($this->l('An error occurred during title updating.'));
			else
				$this->_html .= $this->displayConfirmation($this->l('The block title has been updated.'));
		}
		// Delete a link
		elseif (Tools::isSubmit('deletetmblocklink1') && Tools::getValue('id'))
		{

			if (!is_numeric(Tools::getValue('id')) || !$this->deleteLink())
				$this->_html .= $this->displayError($this->l('An error occurred during link deletion.'));
			else
				$this->_html .= $this->displayConfirmation($this->l('The link has been deleted.'));
		}
		if (Tools::getValue('submitOrderWay') != false)
		{
			if (Configuration::updateValue('PS_TMBLOCKLINK1_ORDERWAY', (int)(Tools::getValue('orderWay'))))
				$this->_html .= $this->displayConfirmation($this->l('Sort order updated'));
			else
				$this->_html .= $this->displayError($this->l('An error occurred during sort order set-up.'));
		}
		$this->_html .= $this->renderForm();
		$this->_html .= $this->renderList();
		return $this->_html;
	}
	private function _displayForm()
	{
		/* Language */
		$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
		$languages = Language::getLanguages(false);
		$div_lang_name = 'text¤title¤url';
		$links = '';
		/* Title */
		$title_url = Configuration::get('PS_TMBLOCKLINK1_URL');
		if (!Tools::isSubmit('submitLinkAdd'))
		{
			if ($id_link = (int)Tools::getValue('id_link'))
			{
				$res = Db::getInstance()->executeS('
				SELECT *
				FROM '._DB_PREFIX_.'tmblocklink1 b
				LEFT JOIN '._DB_PREFIX_.'tmblocklink1_lang bl ON (b.id_tmblocklink1 = bl.id_tmblocklink1)
				WHERE b.id_tmblocklink1='.(int)$id_link);
				if ($res)
					foreach ($res as $row)
					{
						$links['text'][(int)$row['id_lang']] = $row['text'];
						$links['url'][(int)$row['id_lang']] = $row['url'];
						/*$links['url'] = $row['url'];*/
						$links['new_window'] = $row['new_window'];
					}
			}
		}
		$this->_html .= '
		<script type="text/javascript">
			id_language = Number('.(int)$id_lang_default.');
		</script>
		<fieldset>
			<legend><img src="'.$this->_path.'add.png" alt="" title="" /> '.$this->l('Add a new link').'</legend>
			<form method="post" action="index.php?controller=adminmodules&configure='.Tools::safeOutput(Tools::getValue('configure')).'&token='.Tools::safeOutput(Tools::getValue('token')).'&tab_module='.Tools::safeOutput(Tools::getValue('tab_module')).'&module_name='.Tools::safeOutput(Tools::getValue('module_name')).'">
				<input type="hidden" name="id_link" value="'.(int)Tools::getValue('id_link').'" />
				<label>'.$this->l('Text:').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
					<div id="text_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left;">
						<input type="text" name="text_'.$language['id_lang'].'" id="textInput_'.$language['id_lang'].'" value="'.((isset($links) && isset($links['text'][$language['id_lang']])) ? $links['text'][$language['id_lang']] : '').'" /><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($languages, $id_lang_default, $div_lang_name, 'text', true);
		$this->_html .= '
					<div class="clear"></div>
				</div>
				<label>'.$this->l('URL:').'</label>
				
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
					<div id="url_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left;">
						<input type="text" name="url_'.$language['id_lang'].'" id="urlInput_'.$language['id_lang'].'" value="'.((isset($links) && isset($links['url'][$language['id_lang']])) ? $links['url'][$language['id_lang']] : '').'" /><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($languages, $id_lang_default, $div_lang_name, 'url', true);
		$this->_html .= '
					<div class="clear"></div>
				</div>';
		$shops = Shop::getShops(true, null, true);
		if (Shop::isFeatureActive() && count($shops) > 1)
		{
			$helper = new HelperForm();
			$helper->id = (int)Tools::getValue('id_link');
			$helper->table = 'tmblocklink1';
			$helper->identifier = 'id_tmblocklink1';

			$this->_html .= '<label for="shop_association">'.$this->l('Shop association:').'</label><div id="shop_association" class="margin-form">'.$helper->renderAssoShop().'</div>';
		}
		$this->_html .= '
				<div class="margin-form">
					<input type="submit" class="button" name="submitLinkAdd" value="'.$this->l('Add this link').'" />
				</div>
			</form>
		</fieldset>
		<fieldset class="space">
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->l('Block title').'</legend>
			<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'">
				<label>'.$this->l('Block title:').'</label>
				<div class="margin-form">';
		foreach ($languages as $language)
			$this->_html .= '
					<div id="title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $id_lang_default ? 'block' : 'none').'; float: left;">
						<input type="text" name="title_'.$language['id_lang'].'" 
						value="'.Tools::safeOutput(($this->error && Tools::getIsset('title')) ? Tools::getIsset('title') : Configuration::get('PS_TMBLOCKLINK1_TITLE', $language['id_lang'])).'" /><sup> *</sup>
					</div>';
		$this->_html .= $this->displayFlags($languages, $id_lang_default, $div_lang_name, 'title', true);
		$this->_html .= '
				<div class="clear"></div>
				</div>
				<label>'.$this->l('Block URL:').'</label>
				<div class="margin-form"><input type="text" name="title_url" value="'.Tools::safeOutput(($this->error && Tools::getIsset('title_url')) ? Tools::getValue('title_url') : $title_url).'" /></div>
				<div class="margin-form"><input type="submit" class="button" name="submitTitle" value="'.$this->l('Update').'" /></div>
			</form>
		</fieldset>';		
	}
	public function renderList()
	{
		$fields_list = array(
			'id' => array(
				'title' => $this->l('Id'),
				'type' => 'text',
			),
			'text_'.$this->context->language->id => array(
				'title' => $this->l('Text'),
				'type' => 'text',
			),
			'url_'.$this->context->language->id => array(
				'title' => $this->l('Url'),
				'type' => 'text',
			),
		);
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Link list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$links = $this->getLinks();
		if (is_array($links) && count($links))
			return $helper->generateList($this->getLinks(), $fields_list);
		else
			return false;
	}
	public function renderForm()
	{
		$fields_form_1 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Add a new link'),
					'icon' => 'icon-plus-sign-alt'
				),
				'input' => array(
					array(
						'type' => 'hidden',
						'name' => 'id_tmblocklink1',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Text'),
						'name' => 'text',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Url'),
						'name' => 'url',
						'lang' => true,
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitLinkAdd',
				)
			),
		);
		$shops = Shop::getShops(true, null, true);
		if (Shop::isFeatureActive())
		{
			$fields_form_1['form']['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association'),
				'name' => 'checkBoxShopAsso',
			);
		}
		$fields_form_2 = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Add Title'),
					'icon' => 'icon-plus-sign-alt'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Title'),
						'name' => 'title',
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Url'),
						'name' => 'title_url',
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'name' => 'submitTitle',
				)
			),
		);
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = 'id_tmblocklink1';
		$helper->submit_action = 'submit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');

		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form_1, $fields_form_2));
	}
	public function getConfigFieldsValues()
	{
		$fields_values = array(
			'id_tmblocklink1' => Tools::getValue('id_tmblocklink1'),
			'url' => Tools::getValue('url'),
			'newWindow' => Tools::getValue('newWindow'),
			'title_url' => Tools::getValue('title_url', Configuration::get('PS_TMBLOCKLINK1_URL')),
		);
		$languages = Language::getLanguages(false);

		foreach ($languages as $lang)
		{
			$fields_values['text'][$lang['id_lang']] = Tools::getValue('text_'.(int)$lang['id_lang']);
			$fields_values['title'][$lang['id_lang']] = Tools::getValue('title', Configuration::get('PS_TMBLOCKLINK1_TITLE', $lang['id_lang']));
		}

		if (Tools::getIsset('updatetmblocklink1') && (int)Tools::getValue('id') > 0)
			$fields_values = array_merge($fields_values, $this->getLinkById((int)Tools::getValue('id')));
		return $fields_values;
	}
}