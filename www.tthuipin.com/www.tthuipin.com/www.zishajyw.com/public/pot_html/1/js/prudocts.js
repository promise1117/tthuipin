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
	$('#total').text(imgsMobile.length);


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
		autoplayDisableOnInteraction: true,
		on: {
			slideChange: function() {
				// 获取当前活动下标
				var index = this.realIndex;
				$('#realIndex').text(index + 1);
			}
		}
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

	// Tab切换

	$('.van-tab').click(function() {
		const index = $(this).index();
		$(this).addClass('van-tab--active').siblings().removeClass('van-tab--active');
		switch (index) {
			case 0:
				$('.goods_description ').show();
				$('.artist_description,.pug_description').hide();
				break;
			case 1:
				$('.artist_description ').show();
				$('.goods_description,.pug_description').hide();
				break;
			case 2:
				$('.pug_description ').show();
				$('.goods_description,.artist_description').hide();
				break;
		}
	});










})
