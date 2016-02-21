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
{if isset($orderProducts) && count($orderProducts)}
    <section id="crossselling" class="page-product-box">
		<h2 class="centertitle_block">
            {if $page_name == 'product'}
                {l s='Customers who bought this product also bought:' mod='crossselling'}
            {else}
                {l s='We recommend' mod='crossselling'}
            {/if}
        </h2>
    	<div id="crossselling_list"  class="block">
		

		<!-- Megnor start -->
			{assign var='sliderFor' value=count($orderProducts)}
			{if $sliderFor >= 5}
			<div class="customNavigation">
				<a class="btn prev crossselling_prev"><i class="icon-angle-left"></i></a>
				<a class="btn next crossselling_next"><i class="icon-angle-right"></i></a>
			</div>
			{/if}
			<!-- Megnor End -->
		<div class="block_content">
			<ul id="{if $sliderFor >= 5}crossselling-carousel{/if}" class="{if $sliderFor >= 5}tm-carousel{else}product_list grid{/if} clearfix">
                {foreach from=$orderProducts item='orderProduct' name=orderProduct}
                    <li class="{if $sliderFor >= 5}item{else}ajax_block_product col-xs-12 col-sm-4 col-md-3 {/if} product-box item" itemprop="isRelatedTo" itemscope itemtype="http://schema.org/Product">
				     <div class="product-container" itemtype="http://schema.org/Product" itemscope="">
                       <div class="left-block">
				<div class="product-image-container">
					    <a class="lnk_img product-image" href="{$orderProduct.link|escape:'html':'UTF-8'}" title="{$orderProduct.name|htmlspecialchars}" >
                            <img itemprop="image" src="{$orderProduct.image}" alt="{$orderProduct.name|htmlspecialchars}" />
                       			{hook h="displayTmHoverImage" link_rewrite=$orderProduct.link_rewrite id_product=$orderProduct.id_product}
					    </a>
                       <div class="hoverimage">
				<div class="functional-buttons clearfix">
					    <div class="button-container">
                            {if !$PS_CATALOG_MODE && ($orderProduct.allow_oosp || $orderProduct.quantity > 0)}
                               
                                    <a class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$orderProduct.id_product|intval}&amp;token={$static_token}&amp;add")|escape:'html':'UTF-8'}" data-id-product="{$orderProduct.id_product|intval}" title="{l s='Add to cart' mod='crossselling'}">
                                        <span>{l s='Add to cart'}</span>
                                    </a>
                               
                            {/if}
                        </div>
						</div>
						</div>
						</div>
						</div>
					    <div class="right-block">
                            <h5 itemprop="name">
                                <a itemprop="url" href="{$orderProduct.link|escape:'html':'UTF-8'}" title="{$orderProduct.name|htmlspecialchars}" class="product-name">
                                    {$orderProduct.name|truncate:25:'...'|escape:'html':'UTF-8'}
                                </a>
                            </h5>
                       
                        {if $crossDisplayPrice AND $orderProduct.show_price == 1 AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}
                            <div class="price_display">
                                <span class="price product-price">{convertPrice price=$orderProduct.displayed_price}</span>
                            </div>
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
