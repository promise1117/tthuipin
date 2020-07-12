$(function () {
	// 分类
	$('.menu_left li').click(function() {
		const index = $(this).index();
		$(this).addClass('active').siblings().removeClass('active');
		$('.menu_right').eq(index).show().siblings('.menu_right').hide();
	});
	
	// 咨询
	$('.consult').click(function() {
		$('.consult_body').show();
	});
	$('.van-overlay,.colse_consult').click(function() {
		$('.consult_body').hide();
	})
	
	
	
	
})