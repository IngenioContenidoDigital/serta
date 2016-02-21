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
						<div id="right_column" class="col-xs-12 column"  style="width:{$right_column_size}%;">{$HOOK_RIGHT_COLUMN}</div>
					{/if}
					</div>	
					</div><!-- .row -->
				</div><!-- #columns -->
			</div><!-- .columns-container -->
			{if isset($HOOK_FOOTER)}
				<!-- Footer -->
				<div class="footer-container">
					{hook h='TmFooterTop'}
					<footer id="footer"  class="container">
						<div class="row">{$HOOK_FOOTER}
					</div>
					</footer>
				</div><!-- #footer -->
			{/if}
		</div><!-- #page -->
		<a class="top_button" href="#" style="display:none;">&nbsp;</a>		
{/if}
{include file="$tpl_dir./global.tpl"}
{literal}
        <script>
            $(document).ready(function(){
                $('a[title="iSeries"]').first().html('iSeries<br><img src="/img/cms/Serta/iseries.jpg" alt="" />');
                $('a[title="iSeries"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Nuestros colchones combinan la &uacute;ltima tecnolog&iacute;a de espumas inteligentes y resortes.</p>');
                $('a[title="iSeries"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});
                
                $('a[title="iComfort"]').first().html('iComfort<br><img src="/img/cms/Serta/icomfort.jpg" alt="" />');
                $('a[title="iComfort"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Serta&reg; con lo &uacute;ltimo en tecnolog&iacute;a de espumas inteligentes, brinda mayor soporte y confort.</p>');
                $('a[title="iComfort"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});
                
                $('a[title="Perfect sleeper"]').first().html('Perfect Sleeper<br><img src="/img/cms/Serta/perfect-sleeper.jpg" alt="" />');
                $('a[title="Perfect sleeper"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Un colch&oacute;n dise&ntildeado para ayudar a resolver los 5 problemas m&aacute;s comunes del sue&ntildeo.</p>');
                $('a[title="Perfect sleeper"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});
                
                $('a[title="Sertapedic"]').first().html('Sertapedic<br><img src="/img/cms/Serta/Sertapedic.jpg" alt="" />');
                $('a[title="Sertapedic"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">La colecci&oacute;n de colchones Sertapedic&reg; est&aacute; dise&ntilde;ada para ofrecer la calidad que se espera de la marca Serta&reg; a un precio excepcional.</p>');
                $('a[title="Sertapedic"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});
            })
            
            $(document).scroll(function(){
                /*
                 * Archivos Modificados
                 * tmleftmenu.php
                 * global.css
                 * tm_superfish-modified.css
                 * tmleftmenu.tpl
                 * blockcart.css
                 */
                if($(this).scrollTop()>100){
                    var menu = $('.tm_sf-menu');
                    var logo = $('.logo');
                    var cart = $('.shopping_cart');
                    menu.removeClass("tm_sf-menu");
                    menu.addClass("tm_sc-menu");
                    logo.removeClass("img-responsive");
                    logo.addClass("img-responsive80");
                    cart.removeClass("shopping_cart");
                    cart.addClass("shopping_cart-s");
                }else{
                    var menu = $('.tm_sc-menu');
                    var logo = $('.logo');
                    var cart = $('.shopping_cart-s');
                    menu.removeClass("tm_sc-menu");
                    menu.addClass("tm_sf-menu");
                    logo.removeClass("img-responsive80");
                    logo.addClass("img-responsive");
                    cart.removeClass("shopping_cart-s");
                    cart.addClass("shopping_cart");
                }
            })
        </script>
    {/literal}
	</body>
</html>