$(function() {
	$(".only-confirm").click(function() {
		$(this).parent().parent().hide();
	})
	$(".close-btn").click(function() {
		$(this).parent().parent().hide();
	})
	$("i.close").click(function() {
		$(this).parent().hide();
		$(this).parent().parent().hide();
	});

	//游戏规则
	$(".rule").on('touchstart', function() {
		$("#gameRule").show();
	});
	//商城
	$(".shop").on('touchstart', function() {
		$.ajax({
			url: host + "/Farms/getProducts",
			type: "post",
			data: {
				token: userInfo.token
			},
			dataType: "json",
			success: function(data) {
				if(data.errcode != 10000) {
					alertMsg(data.msg);
				} else {
					$('#animalPrice').text(data.result[0].animalPrice + "/只");
					$('#feedProce').text(data.result[0].feedProce + "/袋");
					$('#actricePrice').text(data.result[0].actricePrice + "/个");
				}

			}
		});
		//      $("#chickMarket").show();
		$(".shade").show();
		$(".shops").show();

	});
	//首页
	$(".home-menu").click(function() {
		var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
		var token = userInfo.token;
		var url = "home.html?token=" + token;
		//alert(url);
		window.location.href = url;
		console.log(1);
	});
	//伙伴
	$(".log").on('touchstart', function() {
//		$('.shade').show();
//		$('.friend-list').show();
		window.location.href = "./zhuanpan.html";
//		$('#chickMarket').show();
//		getPartner(1);
	});
	//个人中心
	$(".PY").on('touchstart', function() {
		var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
		var token = userInfo.token;
//		var url = "user.html?token=" + token;
		var url = "chickensLog.html?token=" + token;
		//alert(url);
		window.location.href = url;
		console.log(5);
	});
	//公告
	$(".notice").on('touchstart', function() {
		$("#notice").show();
	});

	$("#show-notice").on('touchstart', function() {
		$(".shade").show();
		$("#notice").show();
	});

	$(".bgmusic-btn").click(function() {
		if($(this).hasClass("on")) {
			$(this).removeClass("on").addClass("off");
		} else {
			$(this).removeClass("off").addClass("on");
		}
		if($(".bgmusic-ball").hasClass("on")) {
			$(".bgmusic-ball").removeClass("on").addClass("off");
		} else {
			$(".bgmusic-ball").removeClass("off").addClass("on");
		}
	});

	$(".sound-btn").click(function() {
		if($(this).hasClass("on")) {
			$(this).removeClass("on").addClass("off");
		} else {
			$(this).removeClass("off").addClass("on");
		}
		if($(".sound-ball").hasClass("on")) {
			$(".sound-ball").removeClass("on").addClass("off");
		} else {
			$(".sound-ball").removeClass("off").addClass("on");
		}
	});

	//二维码

	$(".emw-open").click(function() {
		$(".filter").show();
	});

	$(".emw-close").click(function() {
		$(".filter").hide();
	});

	//切换
	$(".cz-title").find("a").each(function(index) {
		$(this).bind("click", function() {
			$(this).siblings().removeClass("cur");
			$(this).addClass("cur");
			$(".cz-con").find(".cz-list").eq(index).siblings().hide();
			$(".cz-con").find(".cz-list").eq(index).show();
		});
	});

	$(".check-je em").click(function() {
		$(this).addClass("cur").siblings().removeClass("cur");
		var ttxt = ($(this).text());
		$("input.je-input").val(ttxt);
	});

	$("ul.zf-list li").click(function() {
		$(this).addClass("cur").siblings().removeClass("cur");
	});

	$(".box-cd").find(".dan").each(function(index) {
		$(this).bind("click", function() {
			$(this).siblings().removeClass("keep");
			$(this).addClass("keep");
			$(".cd-show").find("ul.show").eq(index).siblings().hide();
			$(".cd-show").find("ul.show").eq(index).show();
		});
	});

});