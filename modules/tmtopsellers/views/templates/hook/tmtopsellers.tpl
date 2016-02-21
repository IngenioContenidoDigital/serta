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
<div class="graybg">
<div id="best-sellers_block_right" class="block products_block container">
	<h2 class="centertitle_block"><a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='Best Sellers' mod='tmtopsellers'}">{l s='Best Sellers' mod='tmtopsellers'}</a></h2>

		
	
       
	    {if $best_sellers && $best_sellers|@count > 0}
		<!-- Megnor start -->
		{if $display_slider == 1 && $display_product >= 6}
				<div class="customNavigation">
					<a class="btn prev topsellerproduct_prev"><i class="icon-angle-left"></i></a>
					<a class="btn next topsellerproduct_next"><i class="icon-angle-right"></i></a>
				</div>
  <div class="block_content">
			<ul id="topsellerproduct-carousel" class="tm-carousel product_list">
		{else}
			<ul class="topsellerproduct_gird product_list grid row">
		{/if}		
		<!-- Megnor End -->
			
				
			
			
				
					{assign var='nbItemsPerLine' value=5}
					{assign var='nbItemsPerLineTablet' value=3}
					{assign var='nbItemsPerLineMobile' value=2}
				
				
					{foreach from=$best_sellers item=product name=myLoop}
						{math equation="(total%perLine)" total=$smarty.foreach.myLoop.total perLine=$nbItemsPerLine assign=totModulo}
						{math equation="(total%perLineT)" total=$smarty.foreach.myLoop.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
						{math equation="(total%perLineT)" total=$smarty.foreach.myLoop.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
						{if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
						{if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
						{if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
								<li class="ajax_block_product {if $display_slider == 1 && $display_product >= 6} item {else}  col-xs-12 col-sm-6 col-md-3 {/if} {if $smarty.foreach.myLoop.iteration%$nbItemsPerLine == 0} last-in-line{elseif $smarty.foreach.myLoop.iteration%$nbItemsPerLine == 1} first-in-line{/if}{if $smarty.foreach.myLoop.iteration > ($smarty.foreach.myLoop.total - $totModulo)} last-line{/if}{if $smarty.foreach.myLoop.iteration%$nbItemsPerLineTablet == 0} last-item-of-tablet-line{elseif $smarty.foreach.myLoop.iteration%$nbItemsPerLineTablet == 1} first-item-of-tablet-line{/if}{if $smarty.foreach.myLoop.iteration%$nbItemsPerLineMobile == 0} last-item-of-mobile-line{elseif $smarty.foreach.myLoop.iteration%$nbItemsPerLineMobile == 1} first-item-of-mobile-line{/if}{if $smarty.foreach.myLoop.iteration > ($smarty.foreach.myLoop.total - $totModuloMobile)} last-mobile-line{/if}">
									{include file="$tpl_dir./product-slider.tpl" products=$product class='tmtopsellers' id='tmtopsellers'}
								</li>
					{/foreach}
				
	      </ul>

        {else}
            <p>{l s='No best sellers at this time' mod='tmtopsellers'}</p>
        {/if}
    </div>
</div>
<!-- /MODULE Block best sellers -->
