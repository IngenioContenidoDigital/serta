{*
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
*}

<option value="{$node.id}" {if $current_category == $node.id}selected="selected"{/if}>
    {$node.spacer} {$node.name|escape:'html':'UTF-8'}
</option>
{if $node.children|@count > 0}
    {foreach from=$node.children item=child name=tmcategoryTreeBranch}
		{if $smarty.foreach.tmcategoryTreeBranch.last}
			{include file="$tmbranche_tpl_path" node=$child last='true'}
		{else}
			{include file="$tmbranche_tpl_path" node=$child last='false'}
		{/if}
	{/foreach}
{/if}