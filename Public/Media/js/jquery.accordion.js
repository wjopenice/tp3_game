
;(function($){
	$.fn.accordion = function(opts){
		//默认值
		var defaults = {
				max: "640px",
				min: "140px",
				speed: "1000"
		}

		var opts = $.extend(defaults, opts);

		this.each(function(){
			var t = $(this),
					m = t.children(),
					c = m.children();

			//触发事件
			m.find(".active a").hide();
			m.on("mouseenter","li",function(){
				$(this).addClass('active')	
							.stop(true,true)			
							.animate({width:opts.max},opts.speed)
							.find("a").fadeOut()
							.parent().siblings().removeClass('active')	
							.stop(true,true)						
							.animate({width:opts.min},opts.speed)
							.find("a").show();
			})
		})
	}

})(jQuery);