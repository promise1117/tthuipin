$(function() {
	//  返回上一个页面
	$('.van-nav-bar__left').click(function () {
		window.history.back(-1); 
	})
	//  返回首页
	$('.van-nav-bar__right').click(function () {
		location.href='/';
	})
	
	// 询价
	$('.btn_qry,.consult').click(function() {
		$('.consult_body').show();
	});
	$('.van-overlay,.colse_consult').click(function() {
		$('.consult_body').hide();
	})



	function telphone() {
		let interval = null;
		const telphone = $(".xjTel").val();
		const regCode = /^1\d{10}$/;

		if (regCode.test(telphone) == false) {
			$('.van-toast--fail').show();
			setTimeout(function() {
				$('.van-toast--fail').hide();
			}, 2000);



			return false;
		} else {
			$('.consult_body').hide(500);
			$('.van-toast--text').show();
			setTimeout(function() {
				$('.van-toast--text').hide();
			}, 2000);
			$('.confirm').html(60).attr('disabled', true);
			interval = setInterval(function() {
				var time = $('.confirm').html();
				time--;
				$('.confirm').html(time);
				if (time < 0) {
					clearInterval(interval);
					$('.confirm').html('立即咨询').removeAttr('disabled');
				}
			}, 1000)

			// var demand = [];
			// var demand = JSON.stringify(demand);
			// $.ajax({

			// 	type: 'post',

			// 	url: '/pc/pot/add.html',

			// 	data: {
			// 		'telphone': telphone,
			// 		'demand': demand
			// 	},

			// 	success: function(data) {

			// 		console.log(data);

			// 		layer.close(layer.index);

			// 		if (data.code == 0) {

			// 			layer.msg('提交成功', {
			// 				icon: 6,
			// 				time: 2000
			// 			})

			// 		} else {

			// 			layer.msg('提交失敗', {
			// 				icon: 5,
			// 				time: 2000
			// 			})

			// 		}

			// 	}
			// })

		}

	}

	// 立即咨询
	$('.confirm').click(function() {
		telphone();
	})
})
