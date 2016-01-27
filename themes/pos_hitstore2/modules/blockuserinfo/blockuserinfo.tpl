<div class="poslogin pull-right">

			{if $is_logged}
				<!-- <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a> -->
				<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
					<i class="icon-sign-out"></i>
				</a>
			{else}
				<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log in to your customer account' mod='blockuserinfo'}">
					<i class="icon-key"></i>
				</a>
			{/if}
</div>
<div class="pos-link-wishlist pull-right">
	<a class="link-wishlist wishlist_block" href="{$link->getModuleLink('blockwishlist', 'mywishlist')}" title="{l s='My wishlist' mod='blockuserinfo'}">
		<i class="icon icon-heart-o"></i></a>
</div>