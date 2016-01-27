<script type="text/javascript">
$(document).ready(function() {
	$(".tab_category").hide();
	$(".tab_category:first").show(); 
	$("ul.tab_cates li").click(function() {
		$("ul.tab_cates li").removeClass("active");
		$(this).addClass("active");
		$(".tab_category").stop().slideUp("slow");
		var activeTab = $(this).attr("rel"); 
		$("#"+activeTab).slideDown("slow");
	});
});
</script>
<div class="tab-category-container">
	<div class="container">
		<div class="container-inner">
			<div class="tab-category">
				<!-- {if $title}
					<div class ='cate_title'>
						<h2>{l s='New Arrivals' mod='postabcategory'}</h2>
					</div>
				{/if}
				<p class="des-tab">{l s='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua' mod='postabcategory'}</p>
				 -->
				 <ul class="tab_cates"> 
				{$count=0}
				{foreach from=$productCates item=productCate name=posTabCategory}
						<li rel="tab_{$productCate.id}" {if $count==0} class="active"  {/if}>
                            {$productCate.name}
                            {$productCate.html}
                        </li>
                         {*   {$productCate.subcate|print_r}*}
                          {*  {foreach $productCate.subcate as $subcate}
                                {if $subcate.id_image}
                                    <img class="replace-2x" src="{$link->getCatImageLink($subcate.link_rewrite,$subcate.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="" />
                                {else}
                                    <img class="replace-2x" src="{$img_cat_dir}default-medium_default.jpg" alt=""/>
                                {/if}
                            {/fore {$productCate.html}ach}*}
                          {*  {if $productCate.subcate.id_image}
                                <img class="replace-2x" src="{$link->getCatImageLink($productCate.subcate.link_rewrite,$productCate.subcate.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="" />
                            {else}
                                <img class="replace-2x" src="{$img_cat_dir}default-medium_default.jpg" alt=""/>
                            {/if}*}

						{$count= $count+1}
				{/foreach}
				</ul>
				<div class="tab_container">
				{foreach from=$productCates item=productCate name=posTabCategory}
					 <div id="tab_{$productCate.id}" class="tab_category">
                               <div class="productTabCategory">
									{foreach from=$productCate.product item=product name=myLoop}
										{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
											<div class="item">
										{/if}
												<div class="item-i">
													
													<div class="item-top">
														<a class ="bigpic_{$product.id_product}_tabcategory product_image" href="{$product.link|escape:'html'}" title="{$product.name|escape:html:'UTF-8'}">
															<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:html:'UTF-8'}" />
																
														</a>
														
														
													</div>
													<a class="quick-view" 
															href="{$product.link|escape:'html':'UTF-8'}"
															title="{l s='Quick view' mod='postabproductslider'}">
															<i class="icon icon-external-link"></i>
															<!-- <span>{l s='Quick view' mod='postabproductslider'}</span> -->
													</a>
															
													<div class="bottom">							
														<div class="center-produc">
															<h5 itemprop="name">
																{if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
																<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" >
																	{$product.name|truncate:45:'...'|escape:'html':'UTF-8'}
																</a>
															</h5>
															{hook h='displayProductListReviews' product=$product}
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
																{if isset($comparator_max_item) && $comparator_max_item}
																		<a class="add_to_compare" 
																			href="{$product.link|escape:'html':'UTF-8'}" 
																			data-id-product="{$product.id_product}"
																			title="{l s='Add to Compare' mod='postabproductslider'}">
																			<i class="icon-bar-chart"></i>
																			<span>{l s='Add to Compare' mod='postabproductslider'}</span>
																		</a>
																{/if}
																</li>	

															</ul>
														</div>
														
													</div>
												</div>
										{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
											</div>
										{/if}
									{/foreach}
                                </div>
							</div>
				{/foreach}
			</div> <!-- .tab_container -->
			</div>
		</div>
	</div>
</div>
