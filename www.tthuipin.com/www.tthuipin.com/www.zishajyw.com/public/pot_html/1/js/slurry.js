$(function() {
	const router = new VueRouter({
		routes: [{
				path: '/',
				component: {
					template: '#tem'
				}
			}, {
				path: '/mud_mixing',
				component: {
					template: '#mud_mixing'
				}
			}, {
				path: '/propertyDetail',
				component: {
					template: '#propertyDetail'
				}
			},
			/* {
						path: '/second',
						component: second
					}, {
						path: '/xingfang',
						component: xingfang
					}*/
		]
	})



	const vm = new Vue({
		el: '#app',
		data: {},
		methods: {
			// 询价
			inquiry() {
				$('.consult_body').show();
			},
			close_inquiry() {
				$('.consult_body').hide();
			}
		},
		router,
		components: {
			com: {
				template: '#propertyDetail',
				methods: {
					// 询价
					inquiry() {
						$('.consult_body').show();
					},
					close_inquiry() {
						$('.consult_body').hide();
					}
				},
				
			},
		},
		created() {

		},
		mounted() {

		}

	})








})
