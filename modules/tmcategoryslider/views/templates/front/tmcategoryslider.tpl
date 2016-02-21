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

<div class="tm-hometabcontent container">
	{$categorycount = 1}
	<h2 class="centertitle_block">{l s='Top Categories' mod='tmcategoryslider'}</h2>
	<ul id="home-page-tabs" class="nav nav-tabs clearfix">
		{foreach from=$tmcategoryinfos item=tmcategoryinfo name=tmTabCategory}
				{if $categorycount == 1}
					<li class="cate_thumb{$tmcategoryinfo.id} cate_thumb_{$categorycount} active">
				{else}
					<li class="cate_thumb{$tmcategoryinfo.id} cate_thumb_{$categorycount}">
				{/if}					
						<a data-toggle="tab" href=".cate_thumb_{$categorycount}" class="tm-hometab">{$tmcategoryinfo.name} </a>				
				</li>			
				{$categorycount = $categorycount + 1}
		{/foreach}
	</ul>		
	<div class="tab-content">
		{$categorycount = 1}		
		{foreach from=$tmcategoryinfos item=tmcategoryinfo name=tmTabCategory}
			{if $categorycount == 1}	
				<div class="cate_thumb cate_thumb{$tmcategoryinfo.id} cate_thumb_{$categorycount} block tab-pane active">	
			{else}
				<div class="cate_thumb cate_thumb{$tmcategoryinfo.id} cate_thumb_{$categorycount} block tab-pane">{/if}				
					<div class="customNavigation">
						<a class="btn prev tab_prev"><i class="icon-angle-left"></i></a>
						<a class="btn next tab_next"><i class="icon-angle-right"></i></a>
					</div>	
					<div class="block_content">
						<ul id="tmcategoryinfogory{$tmcategoryinfo.id}" class="tm-carousel product_list">
							{foreach from=$tmcategoryinfo.product item=product name=tmTabCategory}
								<li class="item">
									{include file="$tpl_dir./product-slider.tpl" products=$tmcategoryinfos class='tmTabCategory'}					
								</li>
							{/foreach}
						</ul>
					</div>
				</div>
				<script>
				$(document).ready(function() {
					var category{$tmcategoryinfo.id} = $("#tmcategoryinfogory{$tmcategoryinfo.id}");
					category{$tmcategoryinfo.id}.owlCarousel({
						items : 5,
						itemsDesktop : [1199,4],
						itemsDesktopSmall : [991,3],
						itemsTablet: [550,2],
						itemsMobile : [480,1],
						autoPlay :  false,
						stopOnHover: false,
					});
		
					// Custom Navigation Events
					$(".cate_thumb{$tmcategoryinfo.id} .tab_next").click(function(){
						category{$tmcategoryinfo.id}.trigger('owl.next');})
					$(".cate_thumb{$tmcategoryinfo.id} .tab_prev").click(function(){
						category{$tmcategoryinfo.id}.trigger('owl.prev');})
				});
			</script>
			{$categorycount = $categorycount + 1}
		{/foreach}
	</div>
</div>