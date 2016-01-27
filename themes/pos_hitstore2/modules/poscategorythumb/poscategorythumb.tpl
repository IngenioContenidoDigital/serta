{$cate_count = 1}
<div class="listcategory listproducts pos-block">
<div class ='pos_title'>
	<h4>{l s='Top categories' mod='poslistcategory'}</h4>
</div>			
<div class="row"> 
<div class="categoryContainer"> 
{foreach from=$productCates item=productCate name=posTabCategory}
	<div class="item">
		<div class="category-i">
		<div class="thumbimg">
			{if $productCate.id== $productCate.cate_id.id_category}
				<a href="{$link->getCategoryLink({$productCate.id},{$productCate.link_rewrite})|escape:'html':'UTF-8'}">
					<img src="{$module_dir}images/{$productCate.cate_id.image}" alt="" class="img-responsive"/>
				</a>
			{/if}
		</div>
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
</div>
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
		itemsDesktopSmall : [980,1], 
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