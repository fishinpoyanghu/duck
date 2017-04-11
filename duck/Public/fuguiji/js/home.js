
//处理底部跳转

$('.home-menus .changeCenter').click(function(){
	console.log(window.location.href.split('?')[1])
//	console.log(window.location.href)
	window.location.href = "./records.html?"+window.location.href.split('?')[1];
})
