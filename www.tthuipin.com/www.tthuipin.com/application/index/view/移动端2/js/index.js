$(function() {
	// 搜索弹窗
	$('.search-icon').click(function() {
		$('.search-layer').show();
	});
	$('.close-btn').click(function() {
		$('.search-layer').hide();

	});
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

	// 推荐活动上下切换
	let recommend_index = 0;
	const recommend_height = $('#down li').height();
	const recommend_length = $('#down li').length;

	function recommend_move() {
		recommend_index = recommend_index > recommend_length - 1 ? 0 : recommend_index;
		if (recommend_index == 0) {
			$('#down ul').css({
				top: 0
			})
		}
		$('#down ul').stop().animate({
			top: -recommend_height * recommend_index
		}, 400);
	};

	const time = setInterval(function() {
		recommend_index++;
		recommend_move();
	}, 4000);


	//Swiper轮播图
	const imgsMobile = [{
		"src": "01.jpg"
	}, {
		"src": "02.jpg"
	}, {
		"src": "03.jpg"
	}];

	let tupian = "";
	for (let i of imgsMobile) {
		tupian += '<div class="swiper-slide"><a href=""> <img src = "img/' + i.src + '" > </a></div>';
	};
	$("section .swiper-wrapper").html(tupian);



	const mySwiper = new Swiper('section .swiper-container', {
		autoplay: {
			delay: 5000,
		}, //自动滑动，5秒切换一次
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


	let startX, startY;
	let keyword_index = 0;
	const keyword_width = $('.keyword li').width() + 5;
	const keyword_length = $('.keyword li').length;
	$('.keyword ul').width(keyword_width * keyword_length);

	function move() {
		keyword_index = keyword_index > keyword_length - 4 ? keyword_length - 4 : keyword_index;
		$('.keyword ul').stop().animate({
			left: -keyword_width * keyword_index
		}, 200);

	};

	$(".keyword").on("touchstart", (e) => {
		startX = e.originalEvent.changedTouches[0].pageX;
		startY = e.originalEvent.changedTouches[0].pageY;
	});

	$(".keyword").on("touchend", (e) => {

		const moveEndX = e.originalEvent.changedTouches[0].pageX;

		const moveEndY = e.originalEvent.changedTouches[0].pageY;

		const X = moveEndX - startX;

		const Y = moveEndY - startY;

		if (X > 0) { //左滑
			keyword_index--;
			keyword_index = keyword_index < 0 ? 0 : keyword_index;
			move();
			console.log(keyword_index)

		} else if (X < 0) { //右滑
			keyword_index++;
			move()
			console.log(keyword_index)

		} else if (Y > 0) { //下滑
		} else if (Y < 0) { //上滑
		} else { //单击
		}

		if (e.cancelable) { //判断默认行为是否可以被禁用    

			if (!e.defaultPrevented) { //判断默认行为是否已经被禁用            

				e.preventDefault();

			}

		}

	});




	const classification_swiper = new Swiper('.classification .swiper-container', {
		slidesPerView: 5,
		effect: 'slide', //滑动效果
		speed: 800, //滑动或者自动换页时的速度

	});

	const rush_swiper = new Swiper('.rush_to_buy .swiper-container', {
		slidesPerView: 4,
		effect: 'slide', //滑动效果
		speed: 800, //滑动或者自动换页时的速度
	});

	const recommend_swiper = new Swiper('.recommend_bottom .swiper-container', {
		slidesPerView: 5,
		effect: 'slide', //滑动效果
		speed: 800, //滑动或者自动换页时的速度
	});








	// Tab切换
	$('.navigation-tab__item').click(function() {
		const index = $(this).attr('data-key');
		$(this).addClass('active').siblings().removeClass('active');
		$('.list-wrap').eq(index).show().siblings('.list-wrap').hide();
	});








})
window.addEventListener("resize", () => location.reload());
