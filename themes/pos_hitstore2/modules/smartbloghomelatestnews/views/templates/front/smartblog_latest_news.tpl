<div class="pos-new-blog listproducts  pos-block">
	<div class="pos_title">
		<h4>
		<span>{l s='Latest News' mod='smartbloghomelatestnews'}</span></h4>
	</div>
	<div class="posblog block_content row">
		<div class="sdsblog-box-content ">
			{if isset($view_data) AND !empty($view_data)}
				{assign var='i' value=1}
				{foreach from=$view_data item=post name=myLoop}
						{assign var="options" value=null}
						{$options.id_post = $post.id}
						{$options.slug = $post.link_rewrite}
						
						{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
							<div class="item">
						{/if}
							
							<div class="item-ii">
							
								<div class="news_module ">
									<a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"><img alt="{$post.title}" class="feat_img_small" src="{$modules_dir}smartblog/images/{$post.post_img}-home-default.jpg"></a>
								</div>
								 
								<div class="description ">
									<h2 class="post_title"><a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}">{$post.title}</a></h2>
									<!-- <p>
										{$post.short_description|truncate:100:'...'|escape:'html':'UTF-8'}
									</p> -->
									<div class="date_added">{$post.date_added}</div>
									<!-- <a href="{smartblog::GetSmartBlogLink('smartblog_post',$options)}"  class="r_more">{l s='Read More' mod='smartbloghomelatestnews'}</a> -->
								</div>
						
							</div>
				
						{if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
							</div>
						{/if}
					{$i=$i+1}
				{/foreach}
			{/if}
		 </div>
		<div class="boxnp">
			<a class="prev prevblog"><i class="icon-angle-left"></i></a>
			<a class="next nextblog"><i class="icon-angle-right"></i></a>
		</div>
	</div>
</div>
<script>
    $(document).ready(function() {
    var owl = $(".sdsblog-box-content");
    owl.owlCarousel({
	autoPlay : false,
    items : 3,
		itemsDesktop : [1024,3],
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