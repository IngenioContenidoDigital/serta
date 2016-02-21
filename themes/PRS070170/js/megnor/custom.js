/*! Customized Jquery from Mahesh Vaghani.  mahesh@templatemela.com  : www.templatemela.com

Authors & copyright (c) 2013: TemplateMela - Megnor Computer Private Limited. */

// Megnor Start

$(document).ready(function () {

	$('p.alert').addClass('container');
	$('.tmcategorylist').before($('p.alert'));
	$('.header-container').before($('#layer_cart'));
	$('#layer_cart').before($('.layer_cart_overlay'));
	
	$('#header .cart_block dl.products').slimScroll({
        height: '100%'
    });
	
	
	
	





	//=================== Show or hide Go Top button ========================//

	$(window).scroll(function() {

		if ($(this).scrollTop() > 480) {

			$('.top_button').fadeIn(500);

		} else {

			$('.top_button').fadeOut(500);

		}

	});							

	$('.top_button').click(function(event) {

		event.preventDefault();		

		$('html, body').animate({scrollTop: 0}, 800);

	});	





	/*======  curosol For Manufacture ==== */

	 var tmbrand = $("#manufacturer-carousel");

      tmbrand.owlCarousel({

     	 items : 6, //10 items above 1000px browser width

     	 itemsDesktop : [1199,5], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [320,1] 

      });

      // Custom Navigation Events

      $(".manufacturer_next").click(function(){

        tmbrand.trigger('owl.next');

      })

      $(".manufacturer_prev").click(function(){

        tmbrand.trigger('owl.prev');

      })

	  


	  

	  /*======  Curosol For New PRoduct ==== */

	 var tmnewProduct = $("#newproduct-carousel");

      tmnewProduct.owlCarousel({

     	 items : 5, //10 items above 1000px browser width

     	 itemsDesktop : [1199,4], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [480,1] 

      });

      // Custom Navigation Events

      $(".newproduct_next").click(function(){

        tmnewProduct.trigger('owl.next');

      })

      $(".newproduct_prev").click(function(){

        tmnewProduct.trigger('owl.prev');

      })

	  

	  

	  /*======  curosol For Bestseller Product ==== */



	 var tmbestseller = $("#topsellerproduct-carousel");

      tmbestseller.owlCarousel({

     	 items : 5, //10 items above 1000px browser width

     	 itemsDesktop : [1199,4], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [480,1] 

      });



      // Custom Navigation Events

      $(".topsellerproduct_next").click(function(){

        tmbestseller.trigger('owl.next');

      })

      $(".topsellerproduct_prev").click(function(){

        tmbestseller.trigger('owl.prev');

      });
  
  	  /*======  curosol For Accessories Product ==== */

	 var tmaccessories = $("#accessories-carousel");

      tmaccessories.owlCarousel({

     	 items :4, 

     	 itemsDesktop : [1199,3], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [480,1] 

      });

      $(".accessories_next").click(function(){

        tmaccessories.trigger('owl.next');

      })

      $(".accessories_prev").click(function(){

        tmaccessories.trigger('owl.prev');

      })


	  /*======  curosol For Category Product ==== */

	 var tmproductcategory = $("#productscategory-carousel");

      tmproductcategory.owlCarousel({

     	 items : 4, //10 items above 1000px browser width

     	 itemsDesktop : [1199,3], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [480,1] 

      });

      // Custom Navigation Events

      $(".productcategory_next").click(function(){

        tmproductcategory.trigger('owl.next');

      })

      $(".productcategory_prev").click(function(){

        tmproductcategory.trigger('owl.prev');

      })

	  

	  /*======  curosol For Crosssel Product ==== */

	 var tmcrossselling = $("#crossselling-carousel");

      tmcrossselling.owlCarousel({

     	 items : 4, //10 items above 1000px browser width

     	 itemsDesktop : [1199,3], 

     	 itemsDesktopSmall : [991,3], 

     	 itemsTablet: [550,2], 

     	 itemsMobile : [480,1] 

      });

      // Custom Navigation Events

      $(".crossselling_next").click(function(){

        tmcrossselling.trigger('owl.next');

      })

      $(".crossselling_prev").click(function(){

        tmcrossselling.trigger('owl.prev');

      })

	  

	  

	   /*======  curosol For Blog Product ==== */

		 var tmblog = $("#tmblog-carousel");

		  tmblog.owlCarousel({

			 items : 4, //10 items above 1000px browser width

			 itemsDesktop : [1199,3], 

			 itemsDesktopSmall : [991,2], 

			 itemsTablet: [480,1], 

			 itemsMobile : [320,1],

			 

		  });

		  // Custom Navigation Events

		  $(".tmblog_next").click(function(){

			tmblog.trigger('owl.next');

		  })

		  $(".tmblog_prev").click(function(){

			tmblog.trigger('owl.prev');

		  })



	  
	   /*======  Add Item in Menu ==== */
		$('ul.tm_sf-menu > li').addClass('main');
		
		var wi = 1;
		$('ul.tm_sf-menu li.main').each( function() {
			$(this).addClass( 'menu-' + wi );
			wi = wi + 1;
		});		

			 
		$('.flexslider .tmhomeslider-container').chameleon();



});

/*function scrollHeader() {
	
    window.addEventListener('scroll', function(e){
	
		if ($(window).width() >= 992) {
	 		
			if ($(this).scrollTop() > 580) 
				
				  $('.header-container').addClass('fixed');
	 		
			else
		 		
				 $('.header-container').removeClass('fixed');
				
		}
		
    });
	if ($(window).width() <= 991) {
		$(".header-container").removeClass('fixed'); 	
	}
}*/

/*$(document).ready(function() {scrollHeader();});

$(window).resize(function() {scrollHeader();});*/






function responsivecolumn()

{

	if ($(document).width() <= 991)

	{

		$('.container#columns_inner > .row #left_column').appendTo('.container#columns_inner > .row');

	}

	else if($(document).width() >= 992)

	{

		$('.container#columns_inner > .row #left_column').prependTo('.container#columns_inner > .row');

	}

}

$(document).ready(function(){responsivecolumn();});

$(window).resize(function(){responsivecolumn();});


// Megnor End

