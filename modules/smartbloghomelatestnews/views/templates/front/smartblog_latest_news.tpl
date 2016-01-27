<div class="pos-new-blog listProducts  col-md-12 col-ms-12 col-sms-12">
	<div class="pos_title">
		<h4><span>{l s='Latest News' mod='smartbloghomelatestnews'}</span></h4>
	</div>
	<div class="posblog row">
	<div class="sdsblog-box-content">
        {if isset($view_data) AND !empty($view_data)}
            {assign var='i' value=1}
            {foreach from=$view_data item=post name=myLoop}
               
                    {assign var="options" value=null}
                    {$options.id_post = $post.id}
                    {$options.slug = $post.link_rewrite}
					{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
						<div class="item">
					{/if}
						<div class="item-i">
						<div class="row">
							<div class="news_module col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"><img alt="{$post.title}" class="feat_img_small" src="{$modules_dir}smartblog/images/{$post.post_img}-home-default.jpg"></a>
							</div>
							 
							<div class="description col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<h2 class="post_title"><a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a></h2>
								<p>
									{$post.short_description|truncate:100:'...'|escape:'html':'UTF-8'}
								</p>
								<div class="date_added">{$post.date_added}</div>
								<!-- <a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"  class="r_more">{l s='Read More' mod='smartbloghomelatestnews'}</a> -->
							</div>
						</div>
						</div>
					{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
						</div>
					{/if}
                {$i=$i+1}
            {/foreach}
        {/if}
     </div>
	 <div class="boxprevnext">
		<a class="prev prevblog"><i class="icon-chevron-left"></i></a>
		<a class="next nextblog"><i class="icon-chevron-right"></i></a>
	</div>
</div>
</div>
<script>
    $(document).ready(function() {
    var owl = $(".sdsblog-box-content");
    owl.owlCarousel({
	autoPlay : false,
    items : 2,
		itemsDesktop : [1200,2],
		itemsDesktopSmall : [980,2],
		itemsTablet: [767,2],
		itemsMobile : [480,1]
    });
		$(".nextblog").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevblog").click(function(){
		owl.trigger('owl.prev');
		})     
    });
</script>