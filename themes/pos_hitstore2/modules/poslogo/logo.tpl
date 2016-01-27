<div class="pos-logo-container pos-block listproducts">	
			<div class="pos_title">
				<h4><span>{l s='Popular brand' mod='poslogo'}</span></h4>
			</div>
		<div class="container-inner">
			<div class="pos-logo">
				<!-- <div class="pos-logo-title"><h2>{l s='Our Brands' mod='poslogo'}</h2></div> -->
				<div class="pos-conten-logo row">
					<div class="pos-logo-slide">
						{foreach from=$logos item=logo name=myLoop}
							{if $smarty.foreach.myLoop.index % 1 == 0 || $smarty.foreach.myLoop.first }
								<div class="item">
							{/if}
								  <div class="item-i">
									<a href ="{$logo.link}">
										<img src ="{$logo.image}" alt ="{l s='Logo'}" />
									</a>
								  </div>
							  {if $smarty.foreach.myLoop.iteration % 1 == 0 || $smarty.foreach.myLoop.last  }
								</div>
							{/if}
						{/foreach}
					</div>
					
				</div>
			</div>
			
		</div>
		<div class="boxnp">
			<a class="prev prevLogo"><i class="icon-angle-left"></i></a>
			<a class="next nextLogo"><i class="icon-angle-right"></i></a>
		</div>
</div>
<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $(".pos-logo-slide");
		owl.owlCarousel({
		addClassActive : true,
		autoPlay:true,
		items :6,
		itemsDesktop : [1200,5],
		itemsDesktopSmall : [980,4], 
		itemsTablet: [767,3], 
		itemsMobile : [480,2],
		autoPlay : false,
		});
		 
		// Custom Navigation Events
		$(".nextLogo").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevLogo").click(function(){
		owl.trigger('owl.prev');
		})     
    });


</script>