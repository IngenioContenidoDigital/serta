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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


{if $page_name =='index'}
 <!-- Module TmHomeSlider -->
   
    {if isset($tmhomeslider_slides)}
        <div class="flexslider">
		<div class="loadingdiv spinner" ></div>
		{assign var=item value=1}
           <ul class="slides">
                {foreach from=$tmhomeslider_slides key=slide_id item=slide}
                    {if $slide.active}
                        <li class="tmhomeslider-container" id="slide_{$item}">
                            <a class="chmln" href="{$slide.url|escape:'html':'UTF-8'}" title="{$slide.legend|escape:'html':'UTF-8'}">
                                <img class="chmln_img" src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`tmhomeslider/images/`$slide.image|escape:'htmlall':'UTF-8'`")}"
                                     alt="{$slide.legend|escape:'htmlall':'UTF-8'}"/>
                            </a>
                        </li>
					{/if}
					{assign var=item value=$item+1}
				{/foreach}
            </ul>
        </div>

    {/if}
    <!-- /Module TmHomeSlider -->
{/if}