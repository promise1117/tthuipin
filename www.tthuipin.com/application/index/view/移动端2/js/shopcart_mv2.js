$(function() {
	var total_point = 0;

	function total_sell_price() {
		total_point = 0;
		var total_sell_price = 0;
		$.each($(".sell_price"), function(i, val) {
			total_point++;
			if ($(this).parents('.media').find('.ckbtn')[0].checked) {
				total_sell_price = (parseInt($(this).html()) + total_sell_price);
			}
		});

		$('.total_sell_price').html(total_sell_price);
	};

	function total_cheap_price() {
		var total_market_price = 0;
		var total_sell_price = 0;
		var countNum = 0;
		$.each($(".sell_price"), function(i, val) {
			if ($(this).parents('.media').find('.ckbtn')[0].checked) {
				total_sell_price = parseInt($(this).html()) + total_sell_price;
			}
		})
		$.each($(".market_price"), function(i, val) {
			if ($(this).parents('.media').find('.ckbtn')[0].checked) {
				total_market_price = parseInt($(this).html()) + total_market_price;
			}
		})
		$.each($(".numbox2"), function(i, val) {
			if ($(this).parents('.media').find('.ckbtn')[0].checked) {
				countNum = parseInt($(this).val()) + countNum;
			}
		})

		var total_cheap_price = (total_market_price - total_sell_price).toFixed(2);
		$('input[name="total_sell_price"]').val(total_sell_price);
		$('input[name="total_cheap_price"]').val(total_cheap_price);

		$('.total_cheap_price').html(total_cheap_price);
		$('.countNum').html(countNum);
	};

	// 全选按钮
	let flag = false;
	$('#checkbox_all').click(function() {
		if (flag) {
			$('.ckbtn').prop("checked", true);
			$('input[name="random_num[]"]').removeAttr("disabled");
			flag = false;
		} else {
			$('.ckbtn').prop("checked", false);
			$('input[name="random_num[]"]').attr("disabled", "disabled");
			flag = true;
		}
		total_sell_price();
		total_cheap_price();
	})
	// 选择按钮
	$('.ckbtn').click(function() {
		var index = 0;
		flag = true;
		$('#checkbox_all').prop("checked", false);
		$('.ckbtn').each(function() {
			if ($(this)[0].checked) {
				index++;
				$(this).prev().removeAttr("disabled");
			} else {
				$(this).prev().attr("disabled", "disabled");
			}
		})
		if (index == total_point) {
			$('#checkbox_all').prop("checked", true);
			flag = false;
		}
		total_sell_price();
		total_cheap_price();
	})

	$('.ckbtn').prop("checked", true);
	total_sell_price();
	total_cheap_price();



	// 删除按钮
	$('.cart-item__delete').click(function() {
		var data_number = $(this).attr('data-number');

		// if(confirm("确定删除该商品?")){
		layer.confirm('確定刪除該商品?', {
			icon: 3,
			title: '提示'
		}, function(index) {

			layer.msg('刪除成功!', {
				icon: 1,
				time: 1000
			});
			// layer.close(index);
			setTimeout($.post('/del', {
				'data_number': data_number
			}, function(data) {
				// console.log(data);
				// if(data.length == 0){
				window.location.href = '/sure';
				// }
			}), 1000);


		})
		// $(this).parents('tr.cart-container').remove();

		// total_sell_price();
		// total_cheap_price();

		// }
	})






	// 递减按钮
	$('.numbox1').click(function() {
		var num = $(this).next().val();
		if (num <= 1) {
			$(this).next().val(1);
			var sell_price = (parseInt($(this).parents('.cart-item__qty').prev().find('.single_sell_price').html()) * 1)
				.toFixed(2);
			var market_price = (parseInt($(this).parents('.cart-item__qty').prev()
				.find('.single_market_price').html()) * 1).toFixed(2);
			$(this).parents('.cart-item__qty').prev().find('.sell_price').html(sell_price);
			$(this).parents('.cart-item__qty').prev().find('.market_price').html(market_price);

		} else {
			num--;
			$(this).next().val(num);
			var sell_price = (parseInt($(this).parents('.cart-item__qty').prev()
				.find('.single_sell_price').html()) * num).toFixed(2);
			var market_price = (parseInt($(this).parents('.cart-item__qty').prev()
				.find('.single_market_price').html()) * num).toFixed(2);
			$(this).parents('.cart-item__qty').prev().find('.sell_price').html(sell_price);
			$(this).parents('.cart-item__qty').prev().find('.market_price').html(market_price);

		}
		total_sell_price();
		total_cheap_price();
	})
	// 递增按钮
	$('.numbox3').click(function() {
		var num = $(this).prev().val();
		num++
		$(this).prev().val(num);
		var sell_price = (parseInt($(this).parents('.cart-item__qty').prev().find('.single_sell_price').html()) * num)
			.toFixed(2);
		var market_price = (parseInt($(this).parents('.cart-item__qty').prev().find('.single_market_price').html()) *
			num).toFixed(2);
		$(this).parents('.cart-item__qty').prev().find('.sell_price').html(sell_price);
		$(this).parents('.cart-item__qty').prev().find('.market_price').html(market_price);
		total_sell_price();
		total_cheap_price();
	})


})
