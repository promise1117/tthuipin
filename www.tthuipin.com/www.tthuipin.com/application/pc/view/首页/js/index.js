$(function() {

	//Swiper轮播图
	const imgs = [{
		"src": "15601.jpg"
	}, {
		"src": "15602.jpg"
	}, {
		"src": "15603.jpg"
	}, {
		"src": "15604.jpg"
	}];

	let tupian = "";
	for (let i of imgs) {
		tupian += '<div class="swiper-slide"><a href=""> <img src = "img/' + i.src + '" > </a></div>';
	};
	$(".swiper-wrapper").html(tupian);

	const mySwiper = new Swiper('.swiper-container', {
		autoplay: {
			delay: 5000,
		}, //自动滑动，1秒切换一次
		effect: 'slide', //滑动效果
		touchAngle: 30, //滑动的角度超过30度无效
		// 轮播图的方向，也可以是vertical方向
		direction: 'horizontal',
		//环形切换关闭
		loop: true,
		// 切换的速度
		speed: 800, //滑动或者自动换页时的速度
		// 如果需要分页器
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
			type: 'bullets',
		},
		// 这样，即使我们滑动之后， 定时器也不会被清除
		autoplayDisableOnInteraction: true
	});
	mySwiper.pagination.bullets.css('background', 'white');
	mySwiper.el.onmouseover = function() { //鼠标放上暂停轮播
		mySwiper.autoplay.stop();
	};
	mySwiper.el.onmouseleave = function() {
		mySwiper.autoplay.start();
	};

	// Tab切换
	$('.navigation-tab__item').click(function() {
		const index = $(this).index();
		$(this).addClass('active').siblings().removeClass('active');
		$('.navigation-content').eq(index).show().siblings('.navigation-content').hide();
	});



	$('.goods-size__list span').click(function() {
		$(this).addClass('active').siblings().removeClass('active');
	});
	$('.list__btn-addcart').click(function() {
		$('.dialog-wrap').show();
	});
	$('.dialog-header__close').click(function() {
		$('.dialog-wrap').hide();
	});







})
