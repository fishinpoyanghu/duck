/**
 * Created by jiaruo on 17/2/25.
 */
var host = "http://www.mmo-bar.com/Api.php";
/**
 * 弹窗
 * @param text
 */
function alertMsg(text,fn) {
    // this.$el.append(util.loadTpl($('#alertMsgTpl')));
    $('#alertMsg').show();
    var w = $('#alertMsg .msg-board').width();
    var h = w * 481 / 587;
    $('#alertMsg .msg-board').height(h);
    $('#alertMsg .msg-board').css({top: 'calc((100% - ' + h + 'px)/2)'});
    if (text)$('#alertMsg .msg-board .text').text(text);
    $(".only-confirm").click(function () {
        $('#alertMsg').hide();
		//history.go(0);
        if(fn)
        {
            fn();
        }
        if(refresh == true){
            alert(1);
            history.go(0);
        }
    })

}

function refresh(){
    history.go(0);
}



/**
 * 增养小鸡
 */
$('#buyAnimal').click(function(){
    $.ajax({
        url:host + "/Farms/buyAnimal",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        success:function(data){
            if(data.errcode != 10000){
                //$(".shade").hide();
                //$(".shops").hide();
                alertMsg(data.msg);
				//alert(1);
            }else{
                alertMsg("增养成功,返回鸡场查看",refresh);
            }
        }
    });
});

/**
 * 增养饲料
 */
$('#buyFeed').click(function(){
    $.ajax({
        url:host + "/Farms/buyFeed",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                $('.shade').hide();
                $('.shops').hide();
                alertMsg("购买成功,请到仓库查看",refresh);

            }
        }
    });
});

/**
 * 增养激活码
 */
$('#buyPrice').click(function(){
    $.ajax({
        url:host + "/Farms/buyActrice",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        success:function(data){
            if(data.errcode != 10000){

                alertMsg(data.msg);
            }else{

                alertMsg("购买成功,请到仓库查看",refresh);
            }
        }
    });
});
// /**
//  * 商城列表
//  */
// $(".shop").click(function(){
//     $.ajax({
//         url:host + "/Farms/getProducts",
//         type:"post",
//         data:{token:userInfo.token},
//         dataType:"json",
//         success:function(data){
//             if(data.errcode != 10000){
//                 alertMsg(data.msg);
//             }else{
//                 $('#animalPrice').text(data.result[0].animalPrice + "/只");
//                 $('#feedProce').text(data.result[0].feedProce + "/袋");
//                 $('#actricePrice').text(data.result[0].actricePrice + "/个");
//             }

//         }
//     });
//     $(".shade").show();
//     $(".shops").show();

// });
/**
 * 商城关闭
 */
$(".shops .close").click(function(){
    $(".shade").hide();
    $(".shops").hide();
});
/**
 *我的伙伴
 */
// $('.log').click(function () {
//     getPartner(1);
// });
function getPartner(size) {
    $.ajax({
        url:host + "/User/getPartner",
        type:"post",
        data:{token:userInfo.token,size:size},
        dataType:"json",
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                var res = data.result[0].partner;
                var count = data.result[0].count;
                $('#count').empty();
                $('#count').append('<span>直推'+count+'人</span>');
                $('.friend-list .show').empty();
                $(res).each(function(){
                    if(this.active == 1){
                        $('.friend-list .show').append("<li class=\"number\"><i class=\"ico chick\"></i><div class=\"content\"> <em id=\"username\">"+this.account+"</em> <em id=\"regtime\">"+new Date(parseInt(this.reg_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ")+"</em> </div> <button class=\"grayListBtn keyCleanDay\" ><span>已送鸡</span></button> </li>");
                    }else{
                        $('.friend-list .show').append("<li class=\"number\"><i class=\"ico chick\"></i><div class=\"content\"> <em id=\"username\">"+this.account+"</em> <em id=\"regtime\">"+new Date(parseInt(this.reg_time) * 1000).toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ")+"</em> </div> <button class=\"redListBtn goActive\"  data-id="+this.id+"><span>送鸡</span></button> </li>");
                    }
                });
            }
        }
    });
}
/*
    激活下级
 */
$(".goActive").live("click",function(){
    var userId = $(this).attr('data-id');
    var thas = this;
    $.ajax({
        url:host + "/User/activeSubordinate",
        type:"post",
        data:{token:userInfo.token,toUserId:userId},
        dataType:"json",
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
               $(thas).removeClass('redListBtn');
               $(thas).removeClass('goActive');
               $(thas).addClass('grayListBtn');
               $(thas).addClass('keyCleanDay');
               $(thas).append('<span>已送鸡</span>')
               alertMsg('送鸡成功');
               

            }

        }
    });
});
/*
    消费记录
 */
function getSale($size){
    var result;
    $.ajax({
        url:host + "/User/getUserFinance",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        async:false,
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                result = data.result;
                return ;
            }

        }
    });
    return result;
}

/**
    使用记录
 */
function getUseLog(){
    var result;
    $.ajax({
        url:host + "/User/getUseLog",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        async:false,
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                result = data.result;
                return ;
            }

        }
    });
    return result;
}
/*
    产蛋记录
 */
function geBroodingLog() {
    var result;
    $.ajax({
        url:host + "/User/getBroodingLog",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        async:false,
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                result = data.result;
                return ;
            }

        }
    });
    return result;
}
/*
    奖励记录
 */
function getRewardLog() {
    var result;
    $.ajax({
        url:host + "/User/getRewardLog",
        type:"post",
        data:{token:userInfo.token},
        dataType:"json",
        async:false,
        success:function(data){
            if(data.errcode != 10000){
                alertMsg(data.msg);
            }else{
                result = data.result;
                return ;
            }

        }
    });
    return result;
}