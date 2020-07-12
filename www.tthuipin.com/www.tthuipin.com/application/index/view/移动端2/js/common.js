$(function() {
	// 侧边栏
	$('.header__aside-switch').click(function() {
		$('.slide').show();
		$('body').addClass('ohm');
	});
	$('.go_back').click(function() {
		$('.slide').hide();
		$('body').removeClass('ohm');
	});

	$('.slide_nav li').click(function() {
		const index = $(this).index();
		$(this).addClass('active').siblings().removeClass('active');
		$('.menu_li').eq(index).addClass('active').siblings().removeClass('active');
	});

	// 导航
	$('.home-nav a').click(function() {
		$(this).addClass('active').siblings().removeClass('active');
		return false;
	});

	function getQueryVariable(variable) {

		var query = window.location.search.substring(1);

		var vars = query.split("&");

		for (var i = 0; i < vars.length; i++) {

			var pair = vars[i].split("=");

			if (pair[0] == variable) {

				return pair[1];

			}

		}

		return (false);

	}


	function get_nav_id() {
		const nav_id_index = getQueryVariable("nav_id");
		const nav_id_width = $('.home-nav a').width();
		const nav_id_size = parseInt(($(window).width() / 375 * 10), 12) * 2;

		$('.home-nav a').eq(nav_id_index).addClass('active').siblings().removeClass('active');
		if (nav_id_index < 6) {
			$('.swiper-wrapper').css({
				'transform': 'translate3d(-' + (nav_id_width * nav_id_index) + 'px, 0px, 0px)',
			});
		} else {
			$('.swiper-wrapper').css({
				'transform': 'translate3d(-' + (nav_id_size * nav_id_index) + 'px, 0px, 0px)',
			});
		}
		const swiper = new Swiper('.home-nav .swiper-container', {
			slidesPerView: 6,
			effect: 'slide', //滑动效果
			speed: 800, //滑动或者自动换页时的速度
		});


	}

	window.addEventListener("resize", get_nav_id);
	get_nav_id();



















})
