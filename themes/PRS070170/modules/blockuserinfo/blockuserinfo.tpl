<!-- Block user information module NAV  -->

<div class="header_user_info">
	<div class="tm_userinfotitle current"> </div>
	<ul class="toogle_content">
	{if $is_logged}
		<li class="welcome_text">	
		<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span>{$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
		</li>
		<li class="li_logout">
			<a class="logout" href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Log me out' mod='blockuserinfo'}">
				{l s='Sign out' mod='blockuserinfo'}
			</a>
		</li>
	{else}
		<li class="li_login last">
			<a class="login" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Login to your customer account' mod='blockuserinfo'}">
				{l s='Sign in' mod='blockuserinfo'}
			</a>
		</li>
	{/if}
	</ul>
</div>
<!-- /Block usmodule NAV -->