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
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="page-product-box blockproductscategory">
	<h2 class="centertitle_block">{$categoryProducts|@count} {l s='other products in the same category:' mod='productscategory'}</h2>
	<div id="productscategory_list" class="clearfix block">
		
		<!-- Megnor start -->
		
			{assign var='sliderFor' value=5} <!-- Define Number of product for SLIDER -->
			{assign var='productCount' value=count($categoryProducts)}
			{if $productCount >= $sliderFor}
			<div class="customNavigation">
				<a class="btn prev  productcategory_prev"><i class="icon-angle-left"></i></a>
				<a class="btn next productcategory_next"><i class="icon-angle-right"></i></a>
			</div>
			{/if}
		<!-- Megnor End -->	  
		<div class="block_content">
		<ul id="{if $productCount >= $sliderFor}productscategory-carousel{else}productcategory-grid{/if}" class="{if $productCount >= $sliderFor}tm-carousel{else}product_list grid{/if} clearfix">
		{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
			<li class="{if $productCount >= $sliderFor}item{else}col-xs-12 col-sm-4 col-md-3{/if} product-box">
			 <div class="product-container" itemtype="http://schema.org/Product" itemscope="">
				<div class="left-block">
				<div class="product-image-container">
				<a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}"><img src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
				{hook h="displayTmHoverImage" link_rewrite=$categoryProduct.link_rewrite id_product=$categoryProduct.id_product}
				</a>
				
				<div class="hoverimage">
				<div class="functional-buttons clearfix">
				<div class="button-container">
					{if !$PS_CATALOG_MODE && ($categoryProduct.allow_oosp || $categoryProduct.quantity > 0)}
						
							<a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$categoryProduct.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" data-id-product="{$categoryProduct.id_product|intval}" title="{l s='Add to cart' mod='productscategory'}">
								<span>{l s='Add to cart' mod='productscategory'}</span>
							</a>
						
					{/if}
				</div>
				</div>
				</div>
				</div>
				</div>
				<div class="right-block">
				<h5 itemprop="name">
					<a class="product-name" href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)|escape:'html':'UTF-8'}" title="{$categoryProduct.name|htmlspecialchars}">{$categoryProduct.name|truncate:25:'...'|escape:'html':'UTF-8'}</a>
				</h5>
				{if $ProdDisplayPrice && $categoryProduct.show_price == 1 && !isset($restricted_country_mode) && !$PS_CATALOG_MODE}
					<div class="price_display content_price">
					{if isset($categoryProduct.specific_prices) && $categoryProduct.specific_prices
					&& ($categoryProduct.displayed_price|number_format:2 !== $categoryProduct.price_without_reduction|number_format:2)}

						<span class="price special-price">{convertPrice price=$categoryProduct.displayed_price}</span>
						{if $categoryProduct.specific_prices.reduction && $categoryProduct.specific_prices.reduction_type == 'percentage'}
							<span class="price-percent-reduction small">-{$categoryProduct.specific_prices.reduction * 100}%</span>
						{/if}
						<span class="old-price">{displayWtPrice p=$categoryProduct.price_without_reduction}</span>
					{else}
						<span class="price product-price">{convertPrice price=$categoryProduct.displayed_price}</span>
					{/if}
					</div>
				{else}
				{/if}
				
				</div>
			 </div>	
			</li>
		{/foreach}
		</ul>
	</div>
	</div>
</section>
{/if}
