$(function() {

	//Swiper轮播图
	const imgsMobile = [{
		"src": "201904021426194150_2019_12_17_20_34.jpg"
	}, {
		"src": "201904011705305934_2020_01_29_17_26.jpg"
	}, {
		"src": "201904101403088430_2019_12_17_20_34.jpg"
	}, {
		"src": "201904011704383180_2019_12_17_20_34.jpg"
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
	mySwiper.el.onmouseover = function() { //鼠标放上暂停轮播
		mySwiper.autoplay.stop();
	};
	mySwiper.el.onmouseleave = function() {
		mySwiper.autoplay.start();
	};
	// Tab切换
	$('.sort_tab').click(function() {
		// const index = $(this).attr('data-key');
		$(this).addClass('active').siblings().removeClass('active');
		// $('.list-wrap').eq(index).show().siblings('.list-wrap').hide();
	});

	// 询价
	$('.btn_qry').click(function() {
		$('.consult_body').show();
	});
	$('.van-overlay,.colse_consult').click(function() {
		$('.consult_body').hide();
	})








})
