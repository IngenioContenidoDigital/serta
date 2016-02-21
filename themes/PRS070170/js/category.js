/*
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
*/
$(document).ready(function(){
	resizeCatimg();
        $('a[title="iSeries"]').first().html('iSeries<br><img src="/serta/img/cms/Serta/iseries.jpg" alt="" />');
        $('a[title="iSeries"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Nuestros colchones combinan la &uacute;ltima tecnolog&iacute;a de espumas inteligentes y resortes.</p>');
        $('a[title="iSeries"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});

        $('a[title="iComfort"]').first().html('iComfort<br><img src="/serta/img/cms/Serta/icomfort.jpg" alt="" />');
        $('a[title="iComfort"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Serta&reg; con lo &uacute;ltimo en tecnolog&iacute;a de espumas inteligentes, brinda mayor soporte y confort.</p>');
        $('a[title="iComfort"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});

        $('a[title="Perfect sleeper"]').first().html('Perfect Sleeper<br><img src="/serta/img/cms/Serta/perfect-sleeper.jpg" alt="" />');
        $('a[title="Perfect sleeper"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">Un colch&oacute;n dise&ntildeado para ayudar a resolver los 5 problemas m&aacute;s comunes del sue&ntildeo.</p>');
        $('a[title="Perfect sleeper"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});

        $('a[title="Sertapedic"]').first().html('Sertapedic<br><img src="/serta/img/cms/Serta/Sertapedic.jpg" alt="" />');
        $('a[title="Sertapedic"]').first().after('<p style="font-size:13px; text-transform:capitalize; text-align:justify; width: 175px;">La colecci&oacute;n de colchones Sertapedic&reg; est&aacute; dise&ntilde;ada para ofrecer la calidad que se espera de la marca Serta&reg; a un precio excepcional.</p>');
        $('a[title="Sertapedic"]').first().parent().css({'margin-right':'15px', 'border-right': '1px rgba(0,0,0,0.15) solid', 'border-left':'1px rgba(255,255,255,0.7) solid','width':'200px','padding':'5px 5px 5px 5px'});
});

$(window).resize(function(){
	resizeCatimg();
});

/*$(document).on('click', '.lnk_more', function(e){
	e.preventDefault();
	$('#category_description_short').hide(); 
	$('#category_description_full').show(); 
	$(this).hide();
});*/

function resizeCatimg()
{
	var div = $('.content_scene_cat div:first');

	if (div.css('background-image') == 'none')
		return;

	var image = new Image;

	$(image).load(function(){
	    var width  = image.width;
	    var height = image.height;
		var ratio = parseFloat(height / width);
		var calc = Math.round(ratio * parseInt(div.outerWidth(false)));

		div.css('min-height', calc);
	});
	if (div.length)
		image.src = div.css('background-image').replace(/url\("?|"?\)$/ig, '');
}