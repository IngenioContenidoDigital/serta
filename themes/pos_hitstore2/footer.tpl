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
{if !isset($content_only) || !$content_only}
					</div><!-- #center_column -->
					{if isset($right_column_size) && !empty($right_column_size)}
						<div id="right_column" class="col-xs-12 col-sm-{$right_column_size|intval} column">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->
			
			{if $page_name =='index'}
				{if Hook::exec(blockPosition4)}
					<div class="blockPosition4 blockPosition">
						<div class="container">
							<div class="row">
								{hook h="blockPosition4"}
							</div>	
						</div>	
					</div>	
				{/if}
			{/if}	
			
			{if Hook::exec('brandSlider')}
			<div class="pos-brandslider">
				<div class="container">
					<div class="row">
						{hook h="brandSlider"}
					</div>
				</div>
			</div>
			{/if}
			{if $page_name =='index'}
				{if Hook::exec(blockPosition5)}
					<div class="blockPosition5 blockPosition">
						{hook h="blockPosition5"}
					</div>	
				{/if}
			{/if}
			<div id="footer" class="footer-container">
				<div class="pos-footer-top">
					<div class="container">
						<div class="footer-top row">
							{hook h = "blockFooter1"}
						</div>	
					</div>
				</div>
				<div class="pos-footer-center">
					<div class="container">
						<div class="footer-center">
							{hook h = "blockFooter2"}
						</div>	
					</div>	
				</div>	
				<div class="pos-footer-footer">
					<div class="container">
						<div class="footer-footer row">
							{hook h = "blockFooter3"}
						</div>
					</div>
				</div>
				<div class="pos-copyright">
					<div class="container">
						<div class="copyright">
							{hook h = "blockFooter4"}
						</div>
					</div>
				</div>
			</div><!-- #footer -->
		</div><!-- #page -->
		{hook h = "posscroll"}
{/if}
{if $page_name == 'index' || $page_name == 'product'}
	{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
	{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
	{addJsDef comparator_max_item=$comparator_max_item}
	{addJsDef comparedProductsIds=$compared_products}
{/if}
{include file="$tpl_dir./global.tpl"}
<script type="text/javascript">
	$(document).ready(function(){
		$(".pt_vmegamenu").hide();
		$(".pt_vmegamenu_title h2").click(function(){
			if($(".pt_vmegamenu_title h2" ).hasClass("active" ))
				 $(".pt_vmegamenu_title h2").removeClass("active");
			else
				 $(".pt_vmegamenu_title h2").addClass("active");
				 
			 if($(".pt_vmegamenu" ).hasClass("active" ))
				 $(".pt_vmegamenu").removeClass("active").slideUp().delay(400 );
			 else
				 $(".pt_vmegamenu").addClass("active").slideDown().delay( 400 );
		});
	});
</script>
	</body>
</html>