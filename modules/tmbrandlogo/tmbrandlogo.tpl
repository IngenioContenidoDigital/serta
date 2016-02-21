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
</div>
{if $manufacturers}
{if $tm_slider}
<div id="tm_manufacturer" class="container">
<div id="manufacturer_slider" class="block products_block">
<h2 class="centertitle_block">{if $display_link_manufacturer}<a href="{$link->getPageLink('manufacturer')|escape:'html'}" title="{l s='Manufacturers' mod='tmbrandlogo'}">{/if}{l s='Manufacturers' mod='tmbrandlogo'}{if $display_link_manufacturer}</a>{/if}</h2>
	<div class="block_content">
	
		<!-- Megnor start -->
			{assign var='sliderFor' value=7} <!-- Define Number of product for SLIDER -->
			{assign var='productCount' value=count($manufacturers)}
			{if $productCount >= $sliderFor}
			<div class="customNavigation">
				<a class="btn prev manufacturer_prev"><i class="icon-angle-left"></i></a>
			<a class="btn next manufacturer_next"><i class="icon-angle-right"></i></a>
			</div>
			{/if}
		<!-- Megnor End -->
		<ul id="{if $productCount >= $sliderFor}manufacturer-carousel{/if}" class="{if $productCount >= $sliderFor}tm-carousel {else} grid {/if} clearfix">
		{foreach from=$manufacturers item=manufacturer name=manufacturer_list}

         <li class="{if $productCount >= $sliderFor}item {else} col-xs-12 col-sm-4 col-md-3{/if}">
			<div class="manu_image">
				<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" title="{l s='Learn more about' mod='tmbrandlogo'} {$manufacturer.name}"><img src="{$img_manu_dir}{$manufacturer.id_manufacturer}.jpg" alt="{$manufacturer.name|escape:'html':'UTF-8'}" /></a>
			</div>
			{if $tm_logoname}
			<div class="manu_name">	
					<h5 itemprop="name">
						<a class="product-name" itemprop="url"  href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)|escape:'html'}" title="{l s='More about %s' sprintf=[$manufacturer.name] mod='tmbrandlogo'}">{$manufacturer.name|escape:'html':'UTF-8'}</a>
					</h5>
			</div>
			{/if}
		</li>
	{/foreach}
	
	</ul>
	
	</div>
	</div>
</div>
{/if}
{else}
	<p>{l s='No manufacturer' mod='tmbrandlogo'}</p>
{/if}
	
