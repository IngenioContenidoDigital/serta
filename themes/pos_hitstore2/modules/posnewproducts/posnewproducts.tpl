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

<!-- MODULE Block new products -->
<div class="col-lg-8  col-md-8  col-sm-8 col-xs-12">
<div id="new-products_block_right" class="pos-block listproducts">
	<div class="pos_title">
		<h4>
			<span>{l s='New products' mod='posnewproducts'}</span>
		</h4>
	</div>
	<div class="block_content row">
	{if $new_products !== false}
		<div class="new_products">
		{foreach from=$new_products item=product name=myLoop}
			{if $smarty.foreach.myLoop.index % 2 == 0 || $smarty.foreach.myLoop.first }
					<div class="item">
				{/if}
				<div class="item-i">
													
													<div class="item-top">
														<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
															<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:html:'UTF-8'}" />
																
														</a>
														{if isset($product.new) && $product.new == 1}
															
																<span class="new-label">{l s='Nuevo'}</span>
														
														{/if}
														{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
																<span class="sale-label">{l s='Sale!'}</span>
														{/if}
														
													</div>
													
															
													<div class="bottom">							
														<div class="center-produc">
															{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
															<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="content_price price-box">
																{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
																	<span itemprop="price" class="price product-price">
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
																	
																	
																	
																	<meta itemprop="priceCurrency" content="{$currency->iso_code}" />
																	{hook h="displayProductPriceBlock" product=$product type="price"}
																	{hook h="displayProductPriceBlock" product=$product type="unit_price"}
																{/if}
															</div>
															{/if}
															<h5 itemprop="name">
																{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
																<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
																	{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
																</a>
															</h5>
															{hook h='displayProductListReviews' product=$product}
														</div>
														<div class="button-container {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}specific_prices{/if}">
															<ul class="button-container-i">
																<li class="posbuttonCart">
																{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
																	{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
																		{if isset($static_token)}
																			<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
																				rel="nofollow" 
																				data-id-product="{$product.id_product|intval}"
																				title="{l s='Add to cart' mod='postabproductslider'}" >
																					<i class="icon-shopping-cart"></i>
																					<span>{l s='Add to cart' mod='postabproductslider'}</span>
																			</a>
																		{else}
																			<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
																				rel="nofollow"
																				data-id-product="{$product.id_product|intval}"
																				title="{l s='Add to cart' mod='postabproductslider'}">
																					<i class="icon-shopping-cart"></i>
																					<span>{l s='Add to cart' mod='postabproductslider'}</span>
																			</a>
																		{/if}
																	{else}
																		<span class="button ajax_add_to_cart_button btn btn-default disabled">
																			<i class="icon-shopping-cart"></i>
																			<span>{l s='Add to cart' mod='postabproductslider'}</span>
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
																			title="{l s='Quick view' mod='postabproductslider'}">
																			<i class="icon icon-eye"></i>
																			<!-- <span>{l s='Quick view' mod='postabproductslider'}</span> -->
																	</a>
																</li>	
																
															</ul>
														</div>
														
													</div>
												</div>
				{if $smarty.foreach.myLoop.iteration % 2 == 0 || $smarty.foreach.myLoop.last  }
					</div>
				{/if}
		{/foreach}
		</div>
		
		<!-- <p><a href="{$link->getPageLink('new-products')|escape:'html'}" title="{l s='All new products' mod='posnewproducts'}" class="button_large">&raquo; {l s='All new products' mod='posnewproducts'}</a></p> -->
	{else}
		<p>&raquo; {l s='Do not allow new products at this time.' mod='posnewproducts'}</p>
	{/if}

</div>
	<div class="boxnp">
		<a class="prev prevnew"><i class="icon-angle-left"></i></a>
		<a class="next nextnew"><i class="icon-angle-right"></i></a>
	</div>
</div>
</div>
<script>
    $(document).ready(function() {
    var owl = $(".new_products");
    owl.owlCarousel({
		addClassActive : true,
		lazyLoad : true,
		items :3,
		itemsDesktop : [1024,3],
		itemsDesktopSmall : [980,2], 
		itemsTablet: [767,2], 
		itemsMobile : [480,1]
	});
		$(".nextnew").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevnew").click(function(){
		owl.trigger('owl.prev');
		})     
    });
</script>
<!-- /MODULE Block new products -->
