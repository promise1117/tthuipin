$(function() {
		 

	//Swiper轮播图
	const imgsMobile = [{
		"src": "http://static.sxzisha.com/UploadFiles/ZiShaGoods/pictures_37012_1.jpg"
	}, {
		"src": "http://static.sxzisha.com/UploadFiles/ZiShaGoods/pictures_37012_2.jpg"
	}, {
		"src": "http://static.sxzisha.com/UploadFiles/ZiShaGoods/pictures_37012_3.jpg"
	}, {
		"src": "http://static.sxzisha.com/UploadFiles/ZiShaGoods/pictures_37012_4.jpg"
	}];

	let tupian = "";
	for (let i of imgsMobile) {
		tupian += '<div class="swiper-slide"><a href=""> <img src = "' + i.src + '" > </a></div>';
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
		// 这样，即使我们滑动之后， 定时器也不会被清除
		autoplayDisableOnInteraction: true
	});
	mySwiper.el.onmouseover = function() { //鼠标放上暂停轮播
		mySwiper.autoplay.stop();
	};
	mySwiper.el.onmouseleave = function() {
		mySwiper.autoplay.start();
	};


	// 询价
	$('.btn_qry').click(function() {
		$('.consult_body').show();
	});
	$('.van-overlay,.colse_consult').click(function() {
		$('.consult_body').hide();
	})












})
