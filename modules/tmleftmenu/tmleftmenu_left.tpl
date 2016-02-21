{if $TMMENU != ''}
	<!-- Menu -->
	<div id="tmmenu_block_left" class="sf-contener12 block">
            <div id="header_logo">
                <a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
                        <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                </a>
            </div>
            <!--<h4 class="title_block cat-title">{l s="Categories" mod="tmleftmenu"}</h4>-->
            <ul class="tm_sf-menu clearfix">
                    {$TMMENU}
            </ul>
	</div>
	<!--/ Menu -->
{/if}