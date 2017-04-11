$(function() {
	$('.desktop .action_chicken_egg button').click(function() {
		//样式操作
		var me = this;
		$(me).parent().children().each(function(i, n) {
			if(me != n) {
				$(n).removeClass('cur');
			}
		})
		$(me).toggleClass('cur');
		if(!$(me).hasClass('cur')) {
			$(".field li").removeClass('canOpen');
			return;
		}
		//功能操作
		userAction = me.className.split(' ')[0];
		switch(userAction) {
			case 'reclaim':
				console.log('开地');
				$(".field li").addClass('canOpen');
				$(".field li").find('.chicken-box').parent().removeClass('canOpen');
				break;
			case 'Up':
				console.log('增养');
				//				break;
			case 'hatch':
				console.log('孵化');
				$(".field li").removeClass('canOpen');
				$(".field li").find('.chicken-box').parent().addClass('canOpen');
				break;
			case 'harvest':
				console.log('收获');
				harvest()
				break;
			case 'machine':
				console.log('孵化机');
				machine();
				break;
		}

		function machine() { //孵化机
			//delete
			//						userInfo.machine = 1;
			console.log(userInfo.machine_animal)
			if(userInfo.machine == 1) {
				$('.shade .has-mchine').show();
				userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
				$('.shade .has-mchine .machine_has_egg').html(userInfo.machine_egg);
				//delete
				//				userInfo.machine_animal = '0';
				console.log(userInfo)
				if(parseFloat(userInfo.machine_animal)) {
					$('.shade .has-mchine .getChick').css('display', 'block');
					$('.shade .has-mchine .no_chick').css('display', 'none');
				} else {
					$('.shade .has-mchine .getChick').css('display', 'none');
					$('.shade .has-mchine .no_chick').css('display', 'block');
				}
				//				console.log($('.shade .has-mchine .machine_has_egg').html(100008));
			} else {
				$('.shade .no-mchine').show();
			}
			$('.shade').show();
			$('.desktop .cur').removeClass('cur');
		}

		function harvest() { //收获
			$(".field li").removeClass('canOpen');
			$(".field li").each(function(i, n) {
				if($(n).find('.Bubble').children().css('opacity') == 1) {
					$(n).addClass('canOpen');
				}
			})
			$.each(field_open_ed, function(i, n) {
				if(n == null) {
					return true;
				}
				if(n.add_num > 0) {
					$($('.field li')[i.replace('field', '')]).addClass('canOpen');
				}
			});
		}
	});

	$('.fieldBox .field li').click(function() {
		var me = this;
		//栅栏的等级
		var fieldIndex = $.inArray(me, $('.field li'));
		currentField = field_open_ed['field' + fieldIndex];
		if($(me).find('.chicken-box').length && !$(me).hasClass('canOpen')) {
			console.log(field_open_ed['field' + fieldIndex])
			console.log(currentField.add_num)
			var showboxHtml = '<div class="clickShowBox show" style="top: 0px;"><p><b>小鹅总数:</b><em>' +
				(parseFloat(currentField.add_num) + parseFloat(currentField.base_num)) +
				'</em></p><p><b>本日产蛋:</b><em>' +
				currentField.egg_rate + '</em></p><p><b>历史产蛋:</b><em>' +
				currentField.egg_allrate + '</em></p><i></i></div>';
			$($(me).children()[0]).append(showboxHtml);
			$('body').append('<section class="clickShowBox-shade"></section>');
		}
		if($(me).hasClass('canOpen')) {
			//开地
			switch(userAction) {
				case 'reclaim':
					console.log('开地');
					fun_field_open(me)
					break;
				case 'Up':
					console.log('增养');
					upChicken();
					break;
				case 'hatch':
					console.log('孵化');
					hatchChicken();
					break;
				case 'harvest':
					console.log('收获');
					getChickenEgg(me)
					break;
				case 'machine':
					console.log('孵化机')
					break;
			}
		}
		//收获
		function getChickenEgg() {
			console.log(currentField)
			//			return;
			$.ajax({
				url: host + "/Farms/getfarmsanimal",
				type: "post",
				data: {
					token: userInfo.token,
					id: currentField.id
				},
				dataType: "json",
				success: function(data) {
					if(data.errcode == 10000) {
						window.location.reload();
					} else {
						alert(data.msg);
					}
				},
				error: function(data) {

				}

			});
			console.log(me)
		}
		//孵化
		function hatchChicken() {
			$(".shade").show();
			$('.shade .hatch').show();
		}
		//增养
		function upChicken() {
			$(".shade").show();
			$('.shade .Up').show();
		}
		//开地
		function fun_field_open(me) {
			var fieldType = $(me).hasClass('green') ? 1 : 2;
			if(fieldType == 1) {
				console.log(userInfo.animal_num)
				if(userInfo.animal_num < 300) {
					$('.NoChick').css('display', 'block');
					$(".shade").show();
					return;
				} else {
					$('.NoChick').css('display', 'none');
					$(".shade").hide();
				}
			} else if(fieldType == 2) {
				if(userInfo.animal_num < 3000) {
					console.log($('.NoChick').css('display', 'block'));
					$(".shade").show();
					return;
				} else {
					console.log($('.NoChick').css('display', 'none'));
					$(".shade").hide();
				}

			}
			$.ajax({
				url: host + "/Farms/openfarm",
				type: "post",
				data: {
					token: userInfo.token,
					type: fieldType
				},
				dataType: "json",
				success: function(data) {
					if(data.errcode == 10000) {
						window.location.reload();
					} else {
						alert(data.msg);
					}
				},
				error: function(data) {

				}

			});

		}
	});
	$('body').delegate('.clickShowBox-shade', 'click', function() {
		$(this).remove();
		$('.fieldBox .clickShowBox').remove();
	});
	$('body').delegate('.NoChick .yellowBtn,.shade .UpDog .buffBtn,.buyKeyCleanDay .buffBtn,.Up .buffBtn,.pull-egg .buffBtn,.push-egg .buffBtn,.hatch .buffBtn,.buySuper .buffBtn', 'click', function() {
		$(this).parent().hide();
		$(".shade").hide();
		$(".field li").removeClass('canOpen');
		$('.desktop .cur').removeClass('cur');
	});
	//增养
	$('body').delegate('.Up .yellowBtn', 'click', function() {
		var enterChickenNum = $('.shade .Up input').val();
		if(parseFloat(enterChickenNum) < 1) {
			alert('输入错误');
			$('.NoChick').css('display', 'none');
			$(".shade").hide();
			$(".field li").removeClass('canOpen');
			$(".shade .Up").hide();
			$('.desktop .cur').removeClass('cur');
			return;
		}
		if(parseFloat(userInfo.animal_num) < parseFloat(enterChickenNum)) {
			$('.NoChick').css('display', 'block');
			$(".shade .Up").hide();
			$(".shade").show();
			$('.desktop .cur').removeClass('cur');
			return;
		} else {
			$('.NoChick').css('display', 'none');
			$(".shade").hide();
		}
		$.ajax({
			url: host + "/Farms/addanimal ",
			type: "post",
			data: {
				token: userInfo.token,
				animal_num: enterChickenNum,
				id: currentField.id
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	//孵化
	$('body').delegate('.hatch .yellowBtn', 'click', function() {
		console.log(this)
		var enterEggNum = $('.shade .hatch input').val();
		//		userInfo.currency = 200;
		//		console.log(enterEggNum);
		//		console.log(userInfo.currency)
		//		return;
		$('.desktop .cur').removeClass('cur');
		$(".field li").removeClass('canOpen');
		if(parseFloat(userInfo.currency) < parseFloat(enterEggNum)) {
			alert('仓库鹅蛋不够')
			//			$('.NoChick').css('display', 'block');
			$(".shade .hatch").hide();
			//			$(".shade").show();
			$(".shade").hide();
			return;
		} else {
			$('.NoChick').css('display', 'none');
			$(".shade").hide();
		}
		$.ajax({
			url: host + "/Farms/hatchEgg ",
			type: "post",
			data: {
				token: userInfo.token,
				egg_num: enterEggNum,
				id: currentField.id
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	$('body').delegate('.shade .Up input', 'change', function() {
		console.log(this)
	});
	//孵化机
	//消耗鹅获取超级孵化机
	$('body').delegate('.shade #chickBuyMchine', 'click', function() {
		console.log('get');
		$('.shade .no-mchine').hide();
		$('.shade .buySuper').show();
	});
	$('body').delegate('.buySuper .yellowBtn', 'click', function() {
		console.log(this)
		$('.shade .buySuper').hide();
		$('.shade').hide();
		//		return;
		//		console.log(userInfo.animal_num=36);
		if(parseFloat(userInfo.animal_num) < 1000) {
			$('.NoChick').css('display', 'block');
			$(".shade .Up").hide();
			$(".shade").show();
			$('.desktop .cur').removeClass('cur');
			return;
		} else {
			$('.NoChick').css('display', 'none');
			$(".shade").hide();
		}
		$.ajax({
			url: host + "/Farms/buymachine",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	$('body').delegate('.shade .mchine .push', 'click', function() {
		$('.shade .has-mchine').hide();
		//		$('.shade').hide();
		$('.shade .push-egg').show();
	});
	$('body').delegate('.shade .mchine .pull', 'click', function() {
		//		$('.shade').hide();
		$('.shade .has-mchine').hide();
		$('.shade .pull-egg').show();
	});
	//放入鹅蛋
	/*
	 *未完成 
	 */
	$('body').delegate('.shade .push-egg .yellowBtn', 'click', function() {
		var pushEggNum = parseFloat($('.shade .push-egg .push_egg_num').val());
		if(userInfo.animal_num < pushEggNum) {
			alert('没有这么多的鹅蛋，请重新输入');
			return;
		}
		if(pushEggNum % 100) {
			alert('只能输入100的倍数');
			return;
		}
		$.ajax({
			url: host + "/User/pushMachine",
			type: "post",
			data: {
				token: userInfo.token,
				machine_egg: pushEggNum
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	//取出鹅蛋
	/*
	 *未完成 
	 */
	$('body').delegate('.shade .pull-egg .yellowBtn', 'click', function() {
		var pullEggNum = parseFloat($('.shade .pull-egg .pull_egg_num').val());
		//delete
		//		userInfo.machine_egg = 110;
		if(userInfo.machine_egg < pullEggNum) {
			alert('没有这么多的鹅蛋，请重新输入');
			return;
		}
		if(pullEggNum % 100) {
			alert('只能输入100的倍数');
			return;
		}
		$.ajax({
			url: host + "/User/getMachineEgg",
			type: "post",
			data: {
				token: userInfo.token,
				machine_egg: pullEggNum
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	//取出孵好的鹅
	/*
	 *未完成 
	 */
	$('body').delegate('.shade .has-mchine .getChick', 'click', function() {
		$.ajax({
			url: host + "/User/getMachineAnimal",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	//我的仓库
	//仓库孵化
	$('body').delegate('#storage-a #gotoUp', 'click', function() {
		$('#storage-a').hide();
		$('.shade').hide();
		$('.desktop .Up').addClass('cur');
		$(".field li").removeClass('canOpen');
		$(".field li").find('.chicken-box').parent().addClass('canOpen');
	})
	//仓库鹅蛋
	$('body').delegate('#storage-a #hatch', 'click', function() {
		$('#storage-a').hide();
		$('.shade').hide();
		$('.desktop .hatch').addClass('cur');
		$(".field li").removeClass('canOpen');
		$(".field li").find('.chicken-box').parent().addClass('canOpen');
	})
	//仓库木栅栏
	$('body').delegate('#storage-a .fenceLevel', 'click', function() {
		window.location.href = "./records.html?token=" + userInfo.token;
		console.log(this);
		return;
		$('#storage-a').hide();
		$('.shade').hide();
		$('.desktop .hatch').addClass('cur');
		$(".field li").removeClass('canOpen');
		$(".field li").find('.chicken-box').parent().addClass('canOpen');
	})
	//仓库哈士奇
	$('body').delegate('#storage-a .library-dog', 'click', function() {
		$('#storage-a').hide();
		$('.shade').show();
		$('.shade .UpDog').show();
		$('.shade .UpDog .chicken-num').html(userInfo.dog_lev * 100 + 100)
		$(".field li").removeClass('canOpen');
		$(".field li").find('.chicken-box').parent().addClass('canOpen');
	})
	//升级狗
	$('body').delegate('.shade .UpDog .yellowBtn', 'click', function() {
		var costChicken = parseFloat($('.shade .UpDog .chicken-num').html());
		//		userInfo.animal_num = 100
		if(costChicken > parseFloat(userInfo.animal_num)) {
			alert('鸡的数量不够');
			return;
		}
		console.log($('.shade .UpDog .chicken-num').html())
		console.log(userInfo.animal_num)
		//		return;
		console.log(this);
		$.ajax({
			url: host + "/User/dogUpgrade",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	})
	//仓库孵化机
	$('body').delegate('#storage-a #machine', 'click', function() {
		$('#storage-a').hide();
		$('.shade').hide();
		//		machine();
		$('.desktop .machine').addClass('cur');
		$(".field li").removeClass('canOpen');
		//		$(".field li").find('.chicken-box').parent().addClass('canOpen');
		if(userInfo.machine == 1) {
			$('.shade .has-mchine').show();
			userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
			$('.shade .has-mchine .machine_has_egg').html(userInfo.machine_egg);
			//delete
			//				userInfo.machine_animal = '0';
			if(parseFloat(userInfo.machine_animal)) {
				$('.shade .has-mchine .getChick').css('display', 'block');
				$('.shade .has-mchine .no_chick').css('display', 'none');
			} else {
				$('.shade .has-mchine .getChick').css('display', 'none');
				$('.shade .has-mchine .no_chick').css('display', 'block');
			}
			//				console.log($('.shade .has-mchine .machine_has_egg').html(100008));
		} else {
			$('.shade .no-mchine').show();
		}
		$('.shade').show();
		$('.desktop .cur').removeClass('cur');
	});
	//仓库一键打扫
	$('body').delegate('#storage-a .redListBtn.keyCleanDay', 'click', function() {
		$('#storage-a').hide();
		$('.shade').show();
		$('.shade .buyKeyCleanDay').show();
	})
	//购买一键打扫
	$('body').delegate('.shade .buyKeyCleanDay .yellowBtn', 'click', function() {
		//		var costChicken = parseFloat($('.shade .buyKeyCleanDay .chicken-num').html());
		//		userInfo.animal_num = 100
		//		userInfo.animal_num = 10
		if(parseFloat(userInfo.animal_num) < 100) {
			alert('鸡的数量不够');
			$('.shade').hide();
			$('.shade .buyKeyCleanDay').hide();
			return;
		}
		$.ajax({
			url: host + "/User/buyoneclean",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {

			}

		});
	});
	/**
	 * 我的伙伴
	 */
	$('.friend').click(function() {
		//		$('#market-a .show').empty();
		$('.shade').show();
		$('.friend-list').show();

//		return;
		$.ajax({
			url: host + "/User/friendlist",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				if(data.errcode != 10000) {
					alertMsg(data.msg);
				} else {
					var res = data.result;
					console.log(res);
					$('.friend-list .show').html('');
//					var me = this;
					$(res).each(function() {
						var BoolBlock = this.egg_parent_status==1?'hidden':'visible';
						
						var html = '<li style="height: 5.555556rem;"><div class="userBox" style="background-image: url(../../Public/fuguiji/images/home/portrait/portrait-man.png);top:29%"></div><div class="info" style="height: 50%;"><span class="name">'
						+this.nickname+'<img style="width: 45%;visibility:'
						+BoolBlock+';" src="../../Public/fuguiji/images/farm/stop_Clean.png"></span><p><i class="icon"></i><em>'
						+this.all_rate+'只</em></p></div><button style="top: 29%;" data-egg-status="'
						+this.egg_parent_status+'" data-id="'
						+this.id+'" class="redListBtn"><span>拜访</span></button><div style="position: absolute;top: 74%;font-size: 0.7rem;"><b><img style="vertical-align: middle; width: 22%;" src="../../Public/fuguiji/images/farm/info_then.png"></b><b style="-webkit-border-radius: 100px; -webkit-box-sizing: border-box;-moz-border-radius: 100px;background: #fffae4;border-radius: 100rem;padding: 0.15rem 0.15rem;color: #864714;margin-left: 0.3rem; box-sizing: border-box;"><img style="vertical-align: middle; width: 7%;" src="../../Public/fuguiji/images/farm/heder_then.png"><strong style="margin-left: 0.1rem;">'
						+this.realname+'</strong></b><b style="-webkit-border-radius: 100px; -webkit-box-sizing: border-box;-moz-border-radius: 100px;background: #fffae4;border-radius: 100rem;padding: 0.15rem 0.1rem;color: #864714;margin-left: 0.3rem;"><img style="vertical-align: middle; width: 7%;" src="../../Public/fuguiji/images/farm/phone_then.png"><strong style="margin-left: 0.1rem;">'
						+this.account+'</strong></b></div></li>';
						$('.friend-list .show').append(html);
					});
				}

			}
		});
	});
	//拜访
	$('body .friend-list').delegate('.show .redListBtn','click',function(){
//		window.location.href = host''
		var me = this,	
			egg_parent_status = parseFloat($(me).attr('data-egg-status')),
			friend_id = parseFloat($(me).attr('data-id'));
		window.location.href = window.location.href.split('farm')[0]+'friendFarm.html?id='+friend_id;
		return;
		if (egg_parent_status==2) {			//为2时表示已经打扫
			return;
		}
		$.ajax({
			url: host + "/User/clearnfriend",
			type: "post",
			data: {
				token: userInfo.token,
				toUserId: friend_id
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					window.location.reload();
				} else {
					alert(data.msg);
				}
			},
			error: function(data) {
			}
		});
	})
	//一键打扫
	console.log($('.friend-list .yellowBtn.one_key'))
	$('body .friend-list').delegate('.yellowBtn.one_key','click',function(){
		console.log($(' #cleanFriend_alert'))
//		cleanFriend_alert();
//		return
		$.ajax({
			url: host + "/User/clearnallfriend",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				console.log(data)
				if(data.errcode == 10000) {
					cleanFriend_alert(data.msg)
					window.location.reload();
				} else {
					cleanFriend_alert(data.msg)
				}
			},
			error: function(data) {
			}
		});
	});
	function cleanFriend_alert(text){
		$('.shade').show();
		$('#cleanFriend_alert').show();
		$('#cleanFriend_alert').css('z-index','110');
		$('#cleanFriend_alert').css('top','0');
		console.log($('#cleanFriend_alert .context .text').html(text))
	}
})




































































