/*

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

*/



$(document).ready(function(){
function responsiveMenu()

{

	if ($(document).width() <= 991)
	{
		
		$('#tm_topmenu ul.tree').css('display','none');

		$('#tm_topmenu h4.title_block').on('click', function(){

			$(this).toggleClass('active').parent().find('ul.tree').stop().slideToggle('medium');

		})
		$('#tm_topmenu').addClass('accordion').find('ul.tree').slideUp('fast');

		$('.tm_sf-menu > li:last-child').addClass('last_class');
	}
		
	else if($(document).width() >= 992){
		
		$('#tmmenu_block_left ul.tm_sf-menu').css('display','block');

		$('#tmmenu_block_left h4.title_block').on('click', function(){

			$(this).toggleClass('active').parent().find('ul.tm_sf-menu').stop().slideToggle('medium');
			

		})

	//	$('#tmmenu_block_left').addClass('accordion').find('ul.tm_sf-menu').slideUp('fast');



		$('.tm_sf-menu > li:last-child').addClass('last_class');
		
	}


}

$(document).ready(function(){responsiveMenu();});

$(window).resize(function(){responsiveMenu();});


});



