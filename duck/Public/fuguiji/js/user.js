/**
 * Created by jiaruo on 17/2/25.
 */
//处理页面显示业务
var userInfo = JSON.parse(sessionStorage.getItem('userInfo'));
if(!userInfo.nickname) userInfo.nickname = "注册会员";
$('.item-content .name').text(userInfo.nickname);

if(!userInfo.currency) userInfo.currency = 0;
$('.item-content .round:eq(0)').text(userInfo.currency);

if(!userInfo.animal_count) userInfo.animal_count = 0;
$('.item-content .round:eq(1)').text(userInfo.animal_count);