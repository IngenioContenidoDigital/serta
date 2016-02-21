{*
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
*}
<!-- Block links module -->
<section class="footer-block col-xs-12 col-sm-3" id="tm_links_block1_footer">
	<h4 class="title_block">
	{if $url}
		<a href="{$url|escape}" title="{$title|escape}">{$title|escape}</a>
	{else}
		{$title|escape}
	{/if}
	</h4>
	<div class="block_content toggle-footer">
		<ul class="bullet">
		{foreach from=$tmblocklink1_links item=tmblocklink1_link}
		{if isset($tmblocklink1_link.$lang)} 
			<li>
				<a href="{$tmblocklink1_link.$linkurl|escape}" title="{$tmblocklink1_link.$lang|escape}" target="_self">{$tmblocklink1_link.$lang|escape}</a>
			</li>
		{/if}
	{/foreach}</ul>
	</div>
</section>
<!-- /Block links module -->
