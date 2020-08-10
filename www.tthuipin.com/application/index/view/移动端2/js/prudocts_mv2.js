$(function() {


	

	//Swiper轮播图
	const mySwiper = new Swiper('section .swiper-container', {
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


	$('.goods-meal__list input').eq(0).attr('checked', true).prop('checked', true);

	$('.goods-meal__list label').first().addClass('active');

	$('.specifications').each(function() {
		const src = $(this).find('.goods-color__list label').attr('data-color');
		console.log($(this).parents('.taocan').find('.goods-color-img img')[0].src = src)


		$('.specifications').first().find('.goods-color__list').find('label').first().addClass('active');
		$('.specifications').first().find('.goods-color__list').find('input').eq(0)
			.attr('checked', true).prop('checked', true).siblings()
			.attr('checked', false).prop('checked', false);

		$('.specifications').first().find('.goods-size').eq(0).find('label').first().addClass('active');
		$('.specifications').first().find('.goods-size').eq(0).find('input').eq(0)
			.attr('checked', true).prop('checked', true).siblings()
			.attr('checked', false).prop('checked', false);
		$(this).find('.goods-size').eq(0).show().siblings('.goods-size').hide();
	})

	// 套餐选择
	$('.goods-meal__list label').click(function() {
		const i = $(this).attr('data-key');

		$('.taocan').eq(i).show().siblings('.taocan').hide();

		$(this).addClass('active').siblings().removeClass('active');

		$(this).prev().attr('checked', true).prop('checked', true).siblings()
			.attr('checked', false).prop('checked', false);

		$('.taocan').eq(i).find('.goods-color').each(function() {
			$(this).find('input').eq(0).attr('checked', true).prop('checked', true).siblings()
				.attr('checked', false).prop('checked', false);

			$(this).find('label').eq(0).addClass('active').siblings().removeClass('active');
			$(this).find('.color').html($(this).find('label.active').text());
			const src = $(this).find('label').eq(0).attr('data-color');
			$(this).parents('.specifications').prev().find('img').attr('src', src);

		})
		$('.taocan').eq(i).find('.specifications').each(function() {
			$(this).find('.goods-size__list').eq(0).find('input').eq(0).attr('checked', true).prop('checked', true)
				.siblings().attr('checked', false).prop('checked', false);
			$(this).find('.goods-size__list').eq(0).find('label').eq(0).addClass('active').siblings().removeClass('active');
		})

		$('.taocan').eq(i).siblings('.taocan').find('input').attr('checked', false).prop('checked', false);
	});

	// 颜色
	$('.goods-color__list label').click(function() {
		const i = $(this).attr('key');
		const src = $(this).attr('data-color');
		// $(this).parents('.taocan').find('.goods-color-img').css({'background': 'url('+src+') no-repeat','background-size':'cover'});
		$(this).parents('.taocan').find('.goods-color-img img')[0].src = src;

		$(this).parents('.taocan ').find('.attr1').html($(this).text().trim());
		$(this).addClass('active').siblings().removeClass('active');
		$(this).prev().attr('checked', true).prop('checked', true).siblings()
			.attr('checked', false).prop('checked', false);

		$(this).parents('.specifications').find('.goods-size').eq(i).show().siblings('.goods-size').hide();
		$(this).parents('.specifications').find('.goods-size').eq(i).find('label').first().addClass('active').siblings()
			.removeClass('active');
		$(this).parents('.specifications').find('.goods-size').eq(i).find('.size').html($(this).parents('.specifications')
			.find('.goods-size').eq(i).find('label').first().text());

		$(this).parents('.specifications').find('.goods-size').eq(i).find('input').first()
			.attr('checked', true).prop('checked', true).parents('.goods-size').siblings('.goods-size').find('input')
			.attr('checked', false).prop('checked', false);

	});

	// 尺寸
	$('.goods-size__list label').click(function() {
		$(this).addClass('active').siblings().removeClass('active');
		$(this).parents('.taocan ').find('.attr2').html($(this).text());

		$(this).prev().attr('checked', true).prop('checked', true).siblings()
			.attr('checked', false).prop('checked', false);
		$(this).parents('.goods-size').siblings('.goods-size').find('input').attr('checked', false).prop('checked', false);

	});

	// 立即购买
	function dialog_show(flag) {
		setTimeout(function() {
			$('body').addClass('ohm');
			$('#dialog').show();
		}, 200)
		$('.goods_buy').stop().animate({
			top: '20%'
		}, 300);
		if (flag == '1') {
			$('.btn-addBag2').stop().animate({
				bottom: 0
			}, 300);
		} else {
			$('.btn-buy2').stop().animate({
				bottom: 0
			}, 300);
		}

	}

	function dialog_hide() {
		$('body').removeClass('ohm');
		$('#dialog').hide();
		$('.goods_buy').stop().animate({
			top: '120%'
		}, 300);
		$('.btn-buy2').stop().animate({
			bottom: '-120%'
		}, 300);
		$('.btn-addBag2').stop().animate({
			bottom: '-120%'
		}, 300);
	}
	$('#UPTRE_AddCart').click(function() {
		dialog_show('1')
	})
	$('#UPTRE_Buy').click(function() {
		dialog_show('2')
	})
	$('#dialog').click(function() {
		dialog_hide()
	})


})
