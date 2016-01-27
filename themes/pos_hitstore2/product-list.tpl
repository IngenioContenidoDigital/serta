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
{if isset($products) && $products}
	{*define number of products per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
		{assign var='nbItemsPerLine' value=3}
		{assign var='nbItemsPerLineTablet' value=2}
		{assign var='nbItemsPerLineMobile' value=3}
	{else}
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbItemsPerLineTablet' value=3}
		{assign var='nbItemsPerLineMobile' value=2}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
	<!-- Products list -->
	<ul{if isset($id) && $id} id="{$id}"{/if} class="listproducts product_list grid row{if isset($class) && $class} {$class}{/if}">
	{foreach from=$products item=product name=products}
		{math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
		{math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
		{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
		{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
		{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
		<li class="item ajax_block_product{if $page_name == 'index' || $page_name == 'product'} col-xs-12 col-sm-3 col-md-3{else} col-xs-12 col-sm-6 col-md-3{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModulo)} last-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.products.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.products.iteration > ($smarty.foreach.products.total - $totModuloMobile)} last-mobile-line{/if}">
			<div class="item-i product-container" itemscope itemtype="https://schema.org/Product">
				<div class="item-top left-block">
					<div class="product-image-container">
						<a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
						</a>
						{if isset($product.new) && $product.new == 1}
															
								<span class="new-label">{l s='New' mod='postabcategory'}</span>
						
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="sale-label">{l s='Sale!' mod='postabcategory'}</span>
						{/if}
						
					</div>
					{if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
					{hook h="displayProductPriceBlock" product=$product type="weight"}
				</div>
				<div class="bottom">
				<div class="center-produc">
					{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
					<div class="content_price">
						{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                            {hook h="displayProductPriceBlock" product=$product type='before_price'}
							<span class="price product-price">
								{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
							</span>
							{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
								{hook h="displayProductPriceBlock" product=$product type="old_price"}
								<span class="old-price product-price">
									{displayWtPrice p=$product.price_without_reduction}
								</span>
								{hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
								<!-- {if $product.specific_prices.reduction_type == 'percentage'}
									<span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
								{/if} -->
							{/if}
							{hook h="displayProductPriceBlock" product=$product type="price"}
							{hook h="displayProductPriceBlock" product=$product type="unit_price"}
                            {hook h="displayProductPriceBlock" product=$product type='after_price'}
						{/if}
					</div>
					{/if}
					<h5 itemprop="name">
						{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
						<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
							{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
						</a>
					</h5>
					
					<p class="product-desc" itemprop="description">
						{$product.description_short|strip_tags:'UTF-8'|truncate:360:'...'}
					</p>
					
					
					{if isset($product.color_list)}
						<div class="color-list-container">{$product.color_list}</div>
					{/if}
					<div class="product-flags">
						{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
							{if isset($product.online_only) && $product.online_only}
								<span class="online_only">{l s='Online only'}</span>
							{/if}
						{/if}
						{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
							{elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
								<span class="discount">{l s='Reduced price!'}</span>
							{/if}
					</div>
					{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
						{if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
							<span class="availability">
								{if ($product.allow_oosp || $product.quantity > 0)}
									<span class="{if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} label-warning{else} label-success{/if}">
										{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
									</span>
								{elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
									<span class="label-warning">
										{l s='Product available with different options'}
									</span>
								{else}
									<span class="label-danger">
										{l s='Out of stock'}
									</span>
								{/if}
							</span>
						{/if}
					{/if}
					{hook h='displayProductListReviews' product=$product}
					<div class="button-container {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}specific_prices{/if}">
						<ul class="button-container-i">
							<li class="posbuttonCart">
							{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
								{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
									{if isset($static_token)}
										<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
											rel="nofollow" 
											data-id-product="{$product.id_product|intval}"
											title="{l s='Add to cart' mod='postabcategory'}" >
												<i class="icon-shopping-cart"></i>
												<span>{l s='Add to cart' mod='postabcategory'}</span>
										</a>
									{else}
										<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
											rel="nofollow"
											data-id-product="{$product.id_product|intval}"
											title="{l s='Add to cart' mod='postabcategory'}">
												<i class="icon-shopping-cart"></i>
												<span>{l s='Add to cart' mod='postabcategory'}</span>
										</a>
									{/if}
								{else}
									<span class="button ajax_add_to_cart_button btn btn-default disabled">
										<i class="icon-shopping-cart"></i>
										<span>{l s='Add to cart' mod='postabcategory'}</span>
									</span>
								{/if}
							{/if}
							</li>
							<li>
								{hook h='displayProductListFunctionalButtons' product=$product}
							</li>
							<li>
								<a class="quick-view" 
										href="{$product.link|escape:'html':'UTF-8'}"
										title="{l s='Quick view' mod='postabcategory'}">
										<i class="icon icon-eye"></i>
										<!-- <span>{l s='Quick view' mod='postabcategory'}</span> -->
								</a>
							</li>
							<li class="compare">
							{if isset($comparator_max_item) && $comparator_max_item}
									<a class="add_to_compare" 
										href="{$product.link|escape:'html':'UTF-8'}" 
										data-id-product="{$product.id_product}"
										title="{l s='Add to Compare' mod='postabcategory'}">
										<i class="icon-refresh"></i>
										<span>{l s='Add to Compare' mod='postabcategory'}</span>
									</a>
							{/if}
							</li>	
							
						</ul>
					</div>
				</div>
				</div>
				
			</div><!-- .product-container> -->
		</li>
	{/foreach}
	</ul>
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
