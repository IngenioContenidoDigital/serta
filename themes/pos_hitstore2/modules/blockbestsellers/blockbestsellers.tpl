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

<!-- MODULE Block best sellers -->
<div id="best-sellers_block" class=" products_block pos-block">
    <div class="pos_title">
    <h4>
        <span>
            {l s='Top sellers' mod='posbestsellers'}
        </span>
    </h4>
    </div>
	<div class="listproducts2">
    <div class="block_content best-sellers">
        {if $best_sellers && $best_sellers|@count > 0}
            <div class="posbestseller">
                {foreach from=$best_sellers item=product name=myLoop}
				{if $smarty.foreach.myLoop.index % 4 == 0 || $smarty.foreach.myLoop.first }
					<div class="item-inner">
				{/if}
				<div class="item">
					<div class="item-top pull-left">
						<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
							<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:html:'UTF-8'}" />						
						</a>
					</div>
					<div class="center-produc">
						<div class="right">
							<h5 itemprop="name">
								{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
								<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
									{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
								</a>
							</h5>
							<!-- {hook h='displayProductListReviews' product=$product} -->
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
							<!-- <div class="posbuttonCart">
								{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
									{if (!isset($product.customization_required) || !$product.customization_required) && ($product.allow_oosp || $product.quantity > 0)}
										{if isset($static_token)}
											<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
												rel="nofollow" 
												data-id-product="{$product.id_product|intval}"
												title="{l s='Add to cart' mod='posbestsellers'}" >
													<i class="icon-shopping-cart"></i>
													<span>{l s='Add to cart' mod='posbestsellers'}</span>
											</a>
										{else}
											<a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}"
												rel="nofollow"
												data-id-product="{$product.id_product|intval}"
												title="{l s='Add to cart' mod='posbestsellers'}">
													<i class="icon-shopping-cart"></i>
													<span>{l s='Add to cart' mod='posbestsellers'}</span>
											</a>
										{/if}
									{else}
										<span class="button ajax_add_to_cart_button btn btn-default disabled">
											<i class="icon-shopping-cart"></i>
											<span>{l s='Add to cart' mod='posbestsellers'}</span>
										</span>
									{/if}
								{/if}
								</div> -->
						</div>
						
					</div>
				</div>
				{if $smarty.foreach.myLoop.iteration % 4 == 0 || $smarty.foreach.myLoop.last  }
					</div>
				{/if}
                {/foreach}
            </div>
			
           <!--  <p class="lnk"><a href="{$link->getPageLink('best-sales')|escape:'html'}"
                              title="{l s='All best sellers' mod='posbestsellers'}"
                              class="button_large">&raquo; {l s='All best sellers' mod='posbestsellers'}</a></p> -->
        {else}
            <p>{l s='No best sellers at this time' mod='posbestsellers'}</p>
        {/if}
    </div>
    </div>
	<div class="boxprevnext">
			<a class="prev prevsales"><i class="icon-angle-left"></i></a>
			<a class="next nextsales"><i class="icon-angle-right"></i></a>
	</div> 
</div>
 <script>
    $(document).ready(function() {
    var owl = $(".posbestseller");
    owl.owlCarousel({
	autoPlay : false,
    items : 1,
		itemsDesktop : [1200,1],
		itemsDesktopSmall : [1024,1],
		itemsTablet: [767,1],
		itemsMobile : [480,1]
    });
		$(".nextsales").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevsales").click(function(){
		owl.trigger('owl.prev');
		})     
    });
</script>
