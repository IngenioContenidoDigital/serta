<div class="listcategory listproducts pos-block">
			<div class ='pos_title'>
				<h4>{l s='Top categories' mod='poslistcategory'}</h4>
			</div>			
		<div class="row"> 
			<div class="categoryContainer"> 
				{foreach from=$productCates item=productCate name=posTabCategory}
					<div class="item">
						{*   {$productCate.subcate|print_r}*}
						{*  {foreach $productCate.subcate as $subcate }
							{if $subcate.id_image}
								<img class="replace-2x" src="{$link->getCatImageLink($subcate.link_rewrite,$subcate.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="" />
							{else}
								<img class="replace-2x" src="{$img_cat_dir}default-medium_default.jpg" alt=""/>
							{/if}
						{/fore {$productCate.html}ach}*}
						{*  {if $productCate.subcate.id_image}
							<img class="replace-2x" src="{$link->getCatImageLink($productCate.subcate.link_rewrite,$productCate.subcate.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="" />
						{else}
							<img class="replace-2x" src="{$img_cat_dir}default-medium_default.jpg" alt=""/>
						{/if}*}
						
						<div class="category-i">
							{if $productCate.html}
							<div class="thumbimg">
								<a href="{$link->getCategoryLink({$productCate.id},{$productCate.link_rewrite})|escape:'html':'UTF-8'}">{$productCate.html}</a>
							</div>
							{/if}
							<div class="contentCate">
								<h2><a href="{$link->getCategoryLink({$productCate.id},{$productCate.link_rewrite})|escape:'html':'UTF-8'}" title="{$productCate.name}"> {$productCate.name}</a></h2>
								<ul class="pos_child">
								{foreach $productCate.child_cate as $child  }
									 <li class="child_name">
										<i class="icon icon-angle-right"></i><a href="{$link->getCategoryLink({$child.id_category},{$child.link_rewrite})|escape:'html':'UTF-8'}">  {$child.name} </a>
									 </li>
								{/foreach}
								</ul>
								<a class="viewMore" title="{$productCate.name}" href="{$link->getCategoryLink({$productCate.id},{$productCate.link_rewrite})|escape:'html':'UTF-8'}">{l s='view more' mod='poslistcategory'}</a>
							</div>
						</div>
						
						
					</div>
				{/foreach}	
			 </div> <!-- .tab_container -->
		 </div>
		<div class="boxnp">
			<a class="prev prevcateList"><i class="icon-angle-left"></i></a>
			<a class="next nextcateList"><i class="icon-angle-right"></i></a>
		</div>
</div>
<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $(".categoryContainer");
		owl.owlCarousel({
		items :2,
		itemsDesktop : [1200,2],
		itemsDesktopSmall : [980,2], 
		itemsTablet: [767,1], 
		itemsMobile : [480,1],
		autoPlay : false,
		});
		 
		// Custom Navigation Events
		$(".nextcateList").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevcateList").click(function(){
		owl.trigger('owl.prev');
		})     
    });


</script>
