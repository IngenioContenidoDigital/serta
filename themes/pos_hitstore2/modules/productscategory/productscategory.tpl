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
<div  class="row">
<div  class="page-product-box blockproductscategory pos-block listproducts">
	<div class="pos_title">
		<h4>
			<span>
			{if $categoryProducts|@count == 1}
				{l s='%s products in category:' sprintf=[$categoryProducts|@count] mod='productscategory'}
			{else}
				{l s='%s products in category:' sprintf=[$categoryProducts|@count] mod='productscategory'}
			{/if}
			</span>
		</h4>
	</div>
	<div id="productscategory_list" class="clearfix row">
		<div class="pos-productscategory">
		{foreach from=$categoryProducts item='product' name=myLoop}
			{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
					<div class="item">
				{/if}
				<div class="item-i">
													
					<div class="item-top">
						<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
							<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:html:'UTF-8'}" />
								
						</a>
						{hook h='displayProductListReviews' product=$product}
					</div>
					
							
					<div class="bottom">							
						<div class="center-produc">
							<h5 itemprop="name">
								{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
								<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
									{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
								</a>
							</h5>
							
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
							{hook h='displayProductListReviews' product=$product}
						</div>
						<div class="button-container">
							<ul class="button-container-i">
								<li class="posbuttonCart">
								{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
									{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
										{if isset($static_token)}
											<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
												rel="nofollow" 
												data-id-product="{$product.id_product|intval}"
												title="{l s='Add to cart' mod='productscategory'}" >
													<i class="icon-shopping-cart"></i>
													<span>{l s='Add to cart' mod='productscategory'}</span>
											</a>
										{else}
											<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
												rel="nofollow"
												data-id-product="{$product.id_product|intval}"
												title="{l s='Add to cart' mod='productscategory'}">
													<i class="icon-shopping-cart"></i>
													<span>{l s='Add to cart' mod='productscategory'}</span>
											</a>
										{/if}
									{else}
										<span class="button ajax_add_to_cart_button btn btn-default disabled">
											<i class="icon-shopping-cart"></i>
											<span>{l s='Add to cart' mod='productscategory'}</span>
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
											title="{l s='Quick view' mod='productscategory'}">
											<i class="icon icon-eye"></i>
											<!-- <span>{l s='Quick view' mod='productscategory'}</span> -->
									</a>
								</li>
								<!--<li>
								 {if isset($comparator_max_item) && $comparator_max_item}
										<a class="add_to_compare" 
											href="{$product.link|escape:'html':'UTF-8'}" 
											data-id-product="{$product.id_product}"
											title="{l s='Add to Compare' mod='productscategory'}">
											<i class="icon-bar-chart"></i>
											<span>{l s='Add to Compare' mod='productscategory'}</span>
										</a>
								{/if} 
								</li>	-->
								
							</ul>
						</div>
						
					</div>
				</div>
				{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
					</div>
				{/if}
		{/foreach}

		</div>
		<div class="boxnp">
			<a class="prev prevproductscategory"><i class="icon-angle-left"></i></a>
			<a class="next nextproductscategory"><i class="icon-angle-right"></i></a>
		</div>
		<script type="text/javascript"> 
		$(document).ready(function() {
			 
			var owl = $(".pos-productscategory");
			 
			owl.owlCarousel({
			addClassActive : true,
			items :6,
			itemsDesktop : [1560,5],
			itemsDesktopSmall : [1024,3], 
			itemsTablet: [767,2], 
			itemsMobile : [480,1]
			});
			 
			// Custom Navigation Events
			$(".nextproductscategory").click(function(){
			owl.trigger('owl.next');
			})
			$(".prevproductscategory").click(function(){
			owl.trigger('owl.prev');
			})     
		});

	</script>
	</div>
</div>
</div>
{/if}
