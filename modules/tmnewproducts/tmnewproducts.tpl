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
*  International Registred Trademark & Property of PrestaShop SA
*}
<!-- TM - NewProduct -->
<div id="newproducts_block" class="block products_block container">
	<h2 class="centertitle_block"><a href="{$link->getPageLink('new-products')|escape:'html'}" title="{l s='New products' mod='tmnewproducts'}">{l s='New products' mod='tmnewproducts'}</a></h2>
	{if $new_products !== false}
		
		
			<!-- Megnor start -->
			{if $newslider == 1 && $no_prod >= 6}
				<div class="customNavigation">
					<a class="btn prev newproduct_prev"><i class="icon-angle-left"></i></a>
					<a class="btn next newproduct_next"><i class="icon-angle-right"></i></a>
				</div>
				<div class="block_content">
				<ul id="newproduct-carousel" class="tm-carousel product_list">
			{else}
				<ul class="product_list grid">
			{/if}		
			<!-- Megnor End -->
				
			{assign var='nbItemsPerLine' value=5}
			{assign var='nbItemsPerLineTablet' value=3}
			{assign var='nbItemsPerLineMobile' value=2}


		{foreach from=$new_products item='product' name='newProducts'}
			{math equation="(total%perLine)" total=$smarty.foreach.newProducts.total perLine=$nbItemsPerLine assign=totModulo}
			{math equation="(total%perLineT)" total=$smarty.foreach.newProducts.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
			{math equation="(total%perLineT)" total=$smarty.foreach.newProducts.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
			{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
			{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
			{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}

			{if $product.condition == new}
				<li class=" ajax_block_product {if $newslider == 1 && $no_prod >= 5}item {else} col-xs-12 col-sm-4 col-md-3 {/if} {if $smarty.foreach.newProducts.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.newProducts.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.newProducts.iteration > ($smarty.foreach.newProducts.total - $totModulo)} last-line{/if}{if $smarty.foreach.newProducts.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.newProducts.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.newProducts.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.newProducts.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.newProducts.iteration > ($smarty.foreach.newProducts.total - $totModuloMobile)} last-mobile-line{/if}">
				{include file="$tpl_dir./product-slider.tpl" products=$new_products class='tmnewproducts' id='tmnewproducts'}
			</li>

			{/if}
		{/foreach}
			</ul>
		</div>
	{else}
		<p>&raquo; {l s='Do not allow new products at this time.' mod='tmnewproducts'}</p>
	{/if}
</div>
<!-- TM - NewProduct -->
