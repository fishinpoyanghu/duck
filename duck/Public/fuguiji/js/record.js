//获取用户信息
var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
var OrderRecoredList = null; //转账记录
getUserInfo();
$('.deal-content .sale_egg .feasible-num').html(userInfo.currency);
//切换子功能
$('.left-sd ul').delegate('li', 'click', function() {
	var me = $(this),
		clickIndex = parseInt(me.attr('data-tab')) - 1;
	for(var i = 1; i <= 4; i++) {
		$(".left-sd ul li").eq(i - 1).attr("class", "nth-give" + i);
	}
	me.attr("class", "nth" + (clickIndex + 1));

	$(".package .content").eq(clickIndex).siblings(".content").hide().end().show();
	switch(clickIndex) {
		case 0:
			console.log('开发新鸡场')
			break;
		case 1:
			console.log('出售鸡蛋');
			$('.deal-content .sale_egg .feasible-num').html(userInfo.currency);
			break;
		case 2:
			console.log('出售小鸡')
			$('.deal-content .sale_chicken .feasible-num').html(userInfo.animal_num);
			break;
		case 3:
			console.log('交易记录');
			getSaleList();
			break;
	}
})

function getSaleList() {

	$.ajax({
		url: host + "/User/salelist",
		type: "post",
		data: { token: userInfo.token },
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				cleanFriend_alert(data.msg);
				$('.deal-table-div .deal-table tbody').html('');
			} else {
				//				OrderRecoredList = data.result;
				var trHtml = '',
					bgColor = 'transparent';;
				$.each(data.result, function(i, n) {
					bgColor = (n.paystatus == 2 ? '#f1951e' : 'transparent');
					//					n.paystatus==2&&(bgColor='#f1951e')
					trHtml += '<tr><td>' +
						n.account +
						'</td><td>' +
						n.realprice +
						'</td><td>' + ['鹅', '蛋'][n.type - 1] +
						'</td><td>' +
						n.create_time +
						'</td><td style="background-color:' + bgColor + ';" data-uid="' + n.id + '" data-td="' + n.paystatus + '" class="confirmDeal">' + ['等待付款', '确认收款', '交易完成', '0'][n.paystatus - 1] +
						'</td></tr>';
				});
				$('.deal-table-div .deal-table tbody').html(trHtml);
			}
		}
	});
}

function getBuylist() {
	$.ajax({
		url: host + "/User/buylist",
		type: "post",
		data: { token: userInfo.token },
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				$('.deal-table-div .deal-table tbody').html('');
				cleanFriend_alert(data.msg);
			} else {
				//				OrderRecoredList = data.result;
				var trHtml = '';
				console.log(trHtml);
				var bgColor = 'transparent';
				$.each(data.result, function(i, n) {
					bgColor = (n.paystatus == 1 ? '#f1951e' : 'transparent');
					console.log(bgColor)
					//					n.paystatus==1&&(bgColor='#f1951e');
					trHtml += '<tr ><td>' +
						n.account +
						'</td><td>' +
						n.realprice +
						'</td><td>' + ['鹅', '蛋'][n.type - 1] +
						'</td><td>' +
						n.create_time +
						'</td><td style="background-color:' + bgColor + ';" data-uid="' + n.id + '"   data-td="' + n.paystatus + '" class="confirmDeal">' + ['确认付款', '等待对方确认收款', '交易完成', 0][n.paystatus - 1] +
						'</td></tr>';
					//						console.log(n.id)
				});
				//				console.log(trHtml)
				$('.deal-table-div .deal-table tbody').html(trHtml);
			}
		}
	});
}
//确认收款和确认付款的事件
$('.deal-table-div .deal-table tbody').delegate('td.confirmDeal', 'click', function() {
	//2是付款   3是收款
	var payStatus = parseFloat($(this).attr('data-td')),
		keep_num = parseFloat($('.box .keep').attr('data-tab')); //1就是购买   2就是出售
	uid = $(this).attr('data-uid'),
		port = '';
	if(keep_num == 1) {
		if(payStatus != 1) {
			return false;
		}
		port = 'surepay';
	}
	if(keep_num == 2) {
		if(payStatus != 2) {
			return false;
		}
		port = 'sureaccept';
	}
	//	console.log(payStatus)
	//	return;
	$.ajax({
		url: host + "/User/" + port,
		type: "post",
		data: {
			token: userInfo.token,
			id: uid
		},
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				cleanFriend_alert(data.msg);
			} else {
				cleanFriend_alert(data.msg);
				window.location.reload()
			}
		}
	});
	if(payStatus == 2) { //付款

	}
	if(payStatus == 3) { //收款

	}
	console.log(payStatus)
	console.log(payStatus == 2)
	console.log(payStatus == 3)
})
//出售鸡蛋和小鸡的失去焦点事件
$('.deal-content .sale_egg,.deal-content .sale_chicken').delegate('.eggChickenNum', 'blur', function() {
	var me = $(this),
		val = me.val();
	//	userInfo.currency = 100
	//	if(val > parseFloat(userInfo.currency)) {
	//		me.val('');
	//		alert('库存不够，请重新输入');
	//	}
	if(val % 10) {
		me.val('');
		cleanFriend_alert('出售数量必须为10的倍数，请重新输入')
	}
})
//出售鸡蛋
$('.deal-content .sale_egg,.deal-content .sale_chicken').delegate('#confirm2', 'click', function() {
	//	console.log($(this).parent().parent().parent().hasClass('sale_egg'))
	var sale_num = $(this).parent().parent().find('.eggChickenNum').val(),
		targetNickname = $(this).parent().parent().find('.target_nickname').val(),
		targetAccount = $(this).parent().parent().find('.target_account').val(),
		saleTypeEgg = ($(this).parent().parent().parent().hasClass('sale_egg') ? 2 : 1);
	var superTransfer = Number($(this).parent().prev().find('.close1').children().hasClass('open2'));
	var target_paypwd = $(this).parent().parent().find('.target_paypwd').val();
	console.log(target_paypwd)
	if(!sale_num || !targetAccount || !targetNickname) {
		cleanFriend_alert('转账信息不完整，请重新填写');
		return;
	}
	console.log($('.xj-content .transfer_open .close1'));
	console.log($(this).parent().prev().find('.close1').children().hasClass('open2'));
	//	return;
	$.ajax({
		url: host + "/User/transfer",
		type: "post",
		data: {
			token: userInfo.token,
			type: saleTypeEgg,
			num: sale_num,
			account: targetAccount,
			realname: targetNickname,
			paypwd: target_paypwd,
			super: superTransfer
		},
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				cleanFriend_alert(data.msg);
			} else {
				cleanFriend_alert(data.msg);
				window.location.reload();
			}
		}
	});
});
//选择切换超级转账
$('body').delegate('.xj-content .transfer_open .close1', 'click', function() {
	var me = this;
	if($(me).children()[0].className == 'close2') {
		$(this).children().removeClass('close2').addClass('open2');
	} else {
		$(this).children().removeClass('open2').addClass('close2');
	}
})
//切换性别
$('.radio-btn').click(function() {
	$('.radio-btn').removeClass('checkedRadio');
	$(this).addClass('checkedRadio')
})
//提交新用户的数据
$('#confirm1').click(function() {
	var recommend_account = $('.deal-content .resgiNewUser .recommend_account').val(),
		registerAccount = $('.deal-content .resgiNewUser .registerAccount').val(),
		realname = $('.deal-content .resgiNewUser .realname').val(),
		nickname = $('.deal-content .resgiNewUser .nickname').val(),
		gender = $('.checkedRadio').children()[1].value, //1男0女
		phoneNum = $('.deal-content .resgiNewUser .phone').val(),
		wechat = $('.deal-content .resgiNewUser .weixin').val(),
		highpass = $('.deal-content .resgiNewUser .highpass').val(),
		alipay = $('.deal-content .resgiNewUser .zhifubao').val();
	//		console.log('我的账号'+ tuijianUser)
	//		console.log('真名'+realname)
	//		console.log('昵称'+nickname)
	//		console.log('性别'+gender)
	//		console.log('号码'+phoneNum)
	//		console.log('微信'+wechat)
	//		console.log('支付宝'+alipay)
	if(!registerAccount || !realname || !phoneNum || !highpass || !nickname) {
		cleanFriend_alert('信息不完整，请重新输入');
		return;
	}
	if(phoneNum < 11) {
		cleanFriend_alert('手机号不正确');
		return;
	}
	$.ajax({
		url: host + "/User/activityuser ",
		type: "post",
		data: {
			token: userInfo.token,
			recommend_account: recommend_account,
			account: registerAccount,
			realname: realname,
			nickname: nickname,
			sex: gender,
			phone: phoneNum,
			highpass: highpass,
			wechat: wechat,
			alipay: alipay
		},
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				cleanFriend_alert(data.msg);
			} else {
				returnSucc_alert('恭喜，注册成功');
			}
		}
	});
	console.log($('.checkedRadio').children()[1].value)
});
$('body').delegate('.buyKeyCleanDay .buffBtn,', 'click', function() {
	$(this).parent().hide();
	$(".shade").hide();
	$(".field li").removeClass('canOpen');
	$('.desktop .cur').removeClass('cur');
});

function getUserInfo() {
	$.ajax({
		url: host + "/User/getUser",
		type: "post",
		data: { token: userInfo.token },
		dataType: "json",
		async: false,
		success: function(data) {
			if(data.errcode != 10000) {
				cleanFriend_alert(data.msg);
			} else {
				userInfo = data.result[0];
				sessionStorage.setItem('userInfo', JSON.stringify(data.result[0]));
			}
		}
	});
}

function gotoMainpage() {
	//	console.log(host+'/Index/home.html?token='+userInfo.token)
	window.location.href = window.location.href.replace('records.html', 'home.html');
}
//返回成功后点击确认刷新
$('body').delegate('#returnSucc_alert .msg-board .only-confirm', 'click', function() {
	window.location.reload();
})
//成功调用的弹框
function returnSucc_alert(text) {
//	$('.shade').show();
	$('#returnSucc_alert').show();
	$('#returnSucc_alert').css('z-index', '110');
	$('#returnSucc_alert').css('top', '0');
	console.log($('#returnSucc_alert .context .text').html(text))
}

function cleanFriend_alert(text) {
	//	$('.shade').show();
	$('#cleanFriend_alert').show();
	$('#cleanFriend_alert').css('z-index', '110');
	$('#cleanFriend_alert').css('top', '0');
	console.log($('#cleanFriend_alert .context .text').html(text))
}