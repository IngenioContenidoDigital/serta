<div class="pos-logo-container col-md-8 col-xs-12">	
		<div class="leftLogo">
			<div class="pos_title">
				<h4>{l s='Popular brand' mod='poslogo'}</h4>
				<div class="boxprevnext">
					<a class="prev prevLogo"><i class="icon-chevron-left"></i></a>
					<a class="next nextLogo"><i class="icon-chevron-right"></i></a>
				</div>
			</div>
		</div>
		<div class="container-inner">
			<div class="pos-logo">
				<!-- <div class="pos-logo-title"><h2>{l s='Our Brands' mod='poslogo'}</h2></div> -->
				<div class="pos-conten-logo row">
					<div class="pos-logo-slide">
						{foreach from=$logos item=logo name=myLoop}
							{if $smarty.foreach.myLoop.index % 2 == 0 || $smarty.foreach.myLoop.first }
								<div class="item">
							{/if}
								  <div class="item-i">
									<a href ="{$logo.link}">
										<img src ="{$logo.image}" alt ="{l s='Logo'}" />
									</a>
								  </div>
							  {if $smarty.foreach.myLoop.iteration % 2 == 0 || $smarty.foreach.myLoop.last  }
								</div>
							{/if}
						{/foreach}
					</div>
					
				</div>
			</div>
			
		</div>
</div>
<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $(".pos-logo-slide");
		owl.owlCarousel({
		items :3,
		itemsDesktop : [1200,3],
		itemsDesktopSmall : [980,2], 
		itemsTablet: [767,2], 
		itemsMobile : [480,1],
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