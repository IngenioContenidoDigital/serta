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

{$categorycount = 1}
<div  class="tmcategorylist container">
	<div class="category-container row">
		{foreach from=$tmcategoryinfos item=tmcategoryinfo}
			<div class="categoryblock{$categorycount} categoryblock">
				<div class="block_content">
					<div class="categorylist">
						<div class="cate-heading">
							<a href="#">{$tmcategoryinfo.name}</a>
						</div>
						<ul class="subcategory">
							{foreach $tmcategoryinfo.child_cate item=child}
								<li>
									<a href="{$link->getCategoryLink({$child.id_category},{$child.link_rewrite})|escape:'html':'UTF-8'}">{$child.name}</a>
								</li>
							{/foreach}
						</ul>
					</div>
					{if $tmcategoryinfo.cate_id > 0}
						{if $tmcategoryinfo.id== $tmcategoryinfo.cate_id.id_category}
							<div class="categoryimage">
								<img src="{$module_dir}views/img/{$tmcategoryinfo.cate_id.image}" alt="" class="img-responsive"/>
							</div>
						{/if}
					{/if}
				</div>
			
			</div>
			{$categorycount = $categorycount + 1}
		{/foreach}
	</div>
</div>