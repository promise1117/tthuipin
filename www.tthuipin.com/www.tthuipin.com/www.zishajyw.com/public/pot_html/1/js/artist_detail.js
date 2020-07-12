$(function() {
	let data_index = 1;
	// Tab切换
	$('.van-tab').click(function() {
		const index = $(this).attr('data-index');
		data_index = index;
		$(this).addClass('van-tab--active').siblings().removeClass('van-tab--active');
		console.log(data_index)
	});

	const vm = new Vue({
		el: '#app',
		data: {
			goods: [],
			more:true
		},
		methods: {
			collpse() {
				if (this.more) {
					this.more = false;
					$('.artist_des').css({height:'auto'});
				}else{
					this.more = true;
					$('.artist_des').css({height:'50px'});
				}
			}
		},
		created() {



		}
	})
	console.log(__NUXT__)
	console.log(vm.goods)

	function checkWillLoad() {
		//直接取出最后一个盒子
		var lastBox = $('.list__item').last();
		//取出最后一个盒子高度的一半 + 头部偏离的位置
		var lastBoxDis = Math.floor($(lastBox).outerHeight() / 2) + $(lastBox).offset().top;
		//求出浏览器的高度
		var clientHeight = $(window).height();
		//求出页面偏离浏览器高度
		var scrollTopHeight = $(window).scrollTop();
		//比较返回
		return lastBoxDis <= clientHeight + scrollTopHeight;

	}

	let page = 1;
	let page_all = 2;

	// $(window).on('scroll', function() {
	// 	console.log(checkWillLoad())
	// 	if (checkWillLoad()) {
	// 		$('.van-toast').show();
	// 		setTimeout(function() {
	// 			$('.van-toast').hide();
	// 			vm.goods = __NUXT__.data[0].artistList[data_index].Data;
	// 		}, 2000);

	// 		// page++;
	// 		// if (page <= page_all) {
	// 		// 	$.ajax({
	// 		// 		url: "/get_category_list_api",
	// 		// 		type: 'post',
	// 		// 		data: {
	// 		// 			'cat_id': 18,
	// 		// 			'cat_name': "女生衣著",
	// 		// 			'parent_id': 0,
	// 		// 			'page': page,
	// 		// 			'nav_id': 4
	// 		// 		},
	// 		// 		dataType: 'json',
	// 		// 		success: function(data) {
	// 		// 			page_all = data.page_all;
	// 		// 			vm.goods.push.apply(vm.goods, data.goods);
	// 		// 			console.log(vm.goods.length)
	// 		// 		}
	// 		// 	})
	// 		// }


	// 	}
	// })
// 询价
	$('.btn_qry').click(function() {
		$('.consult_body').show();
	});
	$('.van-overlay,.colse_consult').click(function() {
		$('.consult_body').hide();
	})


})
