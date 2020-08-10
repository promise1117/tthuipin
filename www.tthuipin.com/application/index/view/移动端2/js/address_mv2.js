//  付款方式
	$('.payment label').first().addClass('active');
	$('.payment input').first().attr('checked', true)
	$('.payment label').click(function() {
		$(this).addClass('active').siblings().removeClass('active');
		$(this).prev().attr('checked', true).prop('checked', true).siblings().attr('checked', false).prop('checked', false);
	});

	// 地址弹出层
	function choice() {
		var countryArr = ["台湾"];

		var provArr = area_1;

		var areaArr = area_2;

		var address = area_3;

		var newArray = new Array();

		for (var i = 0; i < areaArr.length; i++) {
			newArray.push({
				'id': i,
				'value': provArr[i]
			});
			var newArray2 = new Array();
			for (var j = 0; j < areaArr[i].length; j++) {

				newArray2.push({
					'id': j,
					'value': areaArr[i][j]
				});

				// console.log((address[i][j][0]).Address)
				var newArray3 = new Array();
				for (var k = 0; k < address[i][j].length; k++) {
					newArray3.push({
						'id': k,
						'value': (address[i][j][k]).Address + '--' + (address[i][j][k]).POIName
					});
				}

				newArray2[j].childs = newArray3;
			}
			newArray[i].childs = newArray2;
		}

		console.log(newArray)

		var mobileSelect = new MobileSelect({
			ensureBtnText: '確認',
			cancelBtnText: '取消',
			ensureBtnColor: 'red',
			cancelBtnColor: 'blue',
			trigger: '#location1',
			title: '選擇地址',
			wheels: [{
				data: newArray
			}, ],
			position: [0, 0, 0],
			transitionEnd: function(indexArr, data) {
				// console.log(data);
			},
			callback: function(indexArr, data) {
				// $('#location1').text(data[0].value);
				// $('#country').val(data[0].value);
				// $('#location2').text(data[1].value);
				// $('#province').val(data[1].value);
				// $('#location3').text(data[2].value);
				// $('#city').val(data[2].value);

				address_flag = true;
				$('#SendAddress-error').hide();

				$('#location1').text(data[0].value + '--' + data[1].value + '--' + data[2].value);
				$('#location2').text(data[0].value + '--' + data[1].value + '--' + data[2].value);
				$('#location3').text(data[0].value + '--' + data[1].value + '--' + data[2].value);
				$('#detail_address').val(data[0].value + '--' + data[1].value + '--' + data[2].value);
			}
		});
	}


	function payment_(payment) {
		switch (payment) {
			case 'payment1':
				roc_711();
				choice();
				break;
			case 'payment2':
				roc_711();
				choice();
				break;
			case 'payment3':
				roc_qj();
				choice();
				break;
		}
	}



	$('input[name="payment"]').click(function() {
		$(this).attr('checked', true).siblings('input').removeAttr('checked');
		$('#location').text('请选择收貨地址+');
		$('#location1').text('國家/地區');
		$('#location2').text('省/市');
		$('#location3').text('鎮/鄉/區');
		$('.mobileSelect').remove();
		var payment = $(this).attr('id');

		payment_(payment);

	})
	payment_('payment1');
