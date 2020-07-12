$(function() {
	const router = new VueRouter({
		routes: [{
				path: '/',
				component: {
					template: '#tem'
				}
			}, {
				path: '/view_more',
				component: {
					template: '#view_more'
				}
			}, {
				path: '/propertyDetail',
				component: {
					template: '#propertyDetail'
				}
			},
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
