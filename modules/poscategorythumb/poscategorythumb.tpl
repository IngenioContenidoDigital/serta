{$cate_count = 1}
{foreach from=$productCates item=productCate name=posTabCategory}
<div class="cate_thumb_container">
<div class="cate_thumb cate_thumb{$productCate.id} cate_thumb_{$cate_count}">
	<div class="title_block">
			<h3>{$productCate.name}</h3>
			<div class="navi">
				<a class="prevtab"><i class="icon-chevron-left"></i></a>
				<a class="nexttab"><i class="icon-chevron-right"></i></a>
			</div>
		
	</div>
	<div class="block_content">
		<div class="row">
			<div class="hidden-xs col-sm-3">
				{if $productCate.id== $productCate.cate_id.id_category}
					<div class="cate_img_inner ">
						<img src="{$module_dir}images/{$productCate.cate_id.image}" alt="" class="img-responsive"/>
					</div>
				{/if}
			</div>
			<div class="col-xs-12 col-sm-9">
				<div class="productCategory">
					<div class="row">
						<div class="productCategory{$productCate.id}">
							{foreach from=$productCate.product item=product name=posTabCategory}
									<div class="item">
										<div class="home_tab_img">
											<a class="product_img_link"	href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
												<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html'}"
												alt="{$product.legend|escape:'html':'UTF-8'}"
												class="img-responsive"/>
											</a>
											<a 	title="{l s='Quick view' mod='poscategorythumb'}"
												class="quick-view"
												href="{$product.link|escape:'html':'UTF-8'}"
												rel="{$product.link|escape:'html':'UTF-8'}">
												{l s='Quick view' mod='poscategorythumb'}
											</a>
											{if isset($product.new) && $product.new == 1}
												<a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
													<span class="new-label">{l s='New'}</span>
												</a>
											{/if}
											{if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
												<a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
													<span class="sale-label">{l s='Sale!'}</span>
												</a>
											{/if}
										</div>
										<div class="home_tab_info">
											<a class="product-name" href="{$product.link|escape:'html'}" title="{$product.name|truncate:50:'...'|escape:'htmlall':'UTF-8'}">
												{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}
											</a>
											<div class="comment_box">
												{hook h='displayProductListReviews' product=$product}
											</div>
											<div class="price-box">
												<meta itemprop="priceCurrency" content="{$priceDisplay}" />
												{if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
													<span class="old-price product-price">
														{displayWtPrice p=$product.price_without_reduction}
													</span>
												{/if}
												<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
											</div>
										</div>
										<div class="btn_content">
											<div class="col-xs-4">
												{if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
													{if ($product.allow_oosp || $product.quantity > 0)}
														{if isset($static_token)}
															<a class="exclusive ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='poscategorythumb'}" data-id-product="{$product.id_product|intval}">
																<i class="icon-shopping-cart"></i>
															</a>
														{else}
															<a class="exclusive ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='poscategorythumb'}" data-id-product="{$product.id_product|intval}">
																<i class="icon-shopping-cart"></i>
															</a>
														{/if}
													{else}
														<span class="exclusive ajax_add_to_cart_button btn btn-default disabled">
															<i class="icon-shopping-cart"></i>
														</span>
													{/if}
												{/if}
											</div>
											<div class="col-xs-4 mid">
												<a class="add_to_compare" 
													href="{$product.link|escape:'html':'UTF-8'}" 
													title="{l s='Add to compare' mod='poscategorythumb'}"
													data-id-product="{$product.id_product}">
													<i class="icon-retweet"></i>
												</a>
											</div>
											<div class="col-xs-4">
												<a 	title="{l s='Add to wishlist' mod='poscategorythumb'}"
													class="addToWishlist wishlistProd_{$product.id_product|intval}"
													href="#"
													onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;">
													<i class="icon-heart-o"></i>
												</a>
											</div>
										</div>
									</div>
							{/foreach}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
	<script>
		$(document).ready(function() {
			var cate{$productCate.id} = $(".productCategory{$productCate.id}");
			cate{$productCate.id}.owlCarousel({
				items : 4,
				itemsDesktop : [1199,3],
				itemsDesktopSmall : [991,2],
				itemsTablet: [767,2],
				itemsMobile : [480,1],
				autoPlay :  false,
				stopOnHover: false,
			});

			// Custom Navigation Events
			$(".cate_thumb{$productCate.id} .nexttab").click(function(){
				cate{$productCate.id}.trigger('owl.next');})
			$(".cate_thumb{$productCate.id} .prevtab").click(function(){
				cate{$productCate.id}.trigger('owl.prev');})
		});
	</script>
	{$cate_count = $cate_count + 1}
{/foreach}