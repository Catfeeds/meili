var newSlideSize = function slideSize(){
        var w = Math.ceil($(".banner_con").width()/5);
        $(".banner_con,.banner_cx,.banner_slide").height(w);
		};
     newSlideSize();
     $(window).resize(function(){
       newSlideSize();

     });
window.onload = function(){
		var mySwiper = new Swiper('.banner_con',{   
			paging: '.paging',
			loop:true,
			autoplay:5000,	
			pagingClickable: true,
			onSlideChangeStart: function(){
			}
		});  
	};