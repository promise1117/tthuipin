$(function() {
	// hover事件
	$('.header-cart').hover(function() {
		setTimeout(() => {
			$(this).find('.cart-box,.category-list-arrow').show()
		}, 200);
	}, function() {
		$('.cart-box,.category-list-arrow').hide()
	});
	// $('.menu-item').hover(function() {
	// 	$('.menu-item__content,.menu-item__arrow').hide();
	// 	setTimeout(() => {
	// 		$(this).find('.menu-item__content,.menu-item__arrow').show();
	// 	}, 300);
	// }, function() {
	// 	$('.menu-item__content,.menu-item__arrow').hide()
	// });
	// $('.menu-item__content').hover(function() {
	// 	// $(this).show()
	// }, function() {
	// 	// $(this).hide()
	// });
	$('.cart-box').hover(function() {
		$(this).show()
	}, function() {
		$(this).hide()
	});

	// 国定导航返回顶部
	$(document).on('scroll', function() {
		var scr = $(document).scrollTop();
		if (scr >= 100) {
			$('nav').css({
				position: 'fixed'
			});
			$('.header-cart').last().fadeIn(40);
		} else {
			$('nav').css({
				position: 'relative'
			});
			$('.header-cart').last().hide();
		}
		if (scr >= 650) {
			$('.return-top').show(500);
		} else {
			$('.return-top').hide();
		};
		$('.return-top').click(function() {
			$(document).scrollTop(0);
		});
	});



});
