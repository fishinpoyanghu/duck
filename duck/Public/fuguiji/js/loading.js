$(function(){
	

	var controller = function () {
		if(arguments[0].baseController.attributes.base){
    		arguments[0].baseController.get('base').render();
    	}else{
    		new Base(arguments[0]);
    	}
		var view = new View();
		arguments[0].baseController.set('view' , view);
	};
    
	return controller;

    var View = Backbone.View.extend({

    initialize : function() {
        this.template = _.template(template);
        this.render();
    },
    render : function() {
        this.picCount = this.picCount || new PicCount();
        this.$el.html(this.template());
        this.setting();
        this.schedule();
        this.loadCache();
        this.showTips();
    },
    setting:function(){
        var w = $('.loading').width();
        var h = w*95/639 - 12*95/639;
        $('.loading > div').css({top: 14*w/639 +'px',height: 49*w/639 +'px'});
        $('.loadbg .loading .percent').css({top: 7-49*w/639+'px'});
        $('.loadbg .loading span').css({top: 7-98*w/639+'px'});
    },
    showTips:function(){
        var _this=this;
        var list = [
            '收蛋以后记得孵化成小鸡哦，小鸡越多下蛋越多...',
            '超级孵化机可以使鸡蛋生产出小鸡，鸡蛋数量不变...',
            '开垦十块绿地以后出售鸡蛋免手续费哦…',
            '所有土地开垦完以后出售小鸡免手续费哦…',
            '每推荐十个好友可以提升一级围栏…',
            '每天帮好友打扫可以获得鸡蛋奖励…',
            '用小鸡喂养小狗，提高鸡蛋生产率…',
            '绿地最少必须保留300只小鸡，金地3000…',
            '仓库有330只小鸡可以帮新会员注册哦…',
            '妈妈说，我除了下蛋什么都不会…'
        ];
        var n=0;
        var tipTimer = function(){
            if(n < list.length-1){
                $('.loadbg .note').text(list[n]);
                n++;
            }else{
                $('.loadbg .note').text(list[n]);
                window.clearInterval(_this.countTip);
            }
        };
        this.countTip = self.setInterval(tipTimer,3000);
    },
    schedule:function(){
        var _this=this;
        this.picCount.set('loadPicCount',0);
        var sd = new Date().getTime();
        var l = 179;
        var st = 0,count=5,act=0;
        this.picCount.on('change:loadPicCount', function(){
            var n = this.get('loadPicCount');
            var t = this.get('time');
            act = n==1 ? t : act;	//上一次有进度的时间戳
            var nt = n==1 ? t-sd : t-st; //这次减上次的时间
            if(n == l){
                _this.loading(count,t-sd);
                console.log(n + '张图片加载成功!');
            }
            if(nt > 150){
                count++;
                $('.loading-status div').animate({width: count +'%'},150,'linear');
                $('.percent').text(count + '%');
                act = t;
            }else{
                if(t-act > 150){
                    count++;
                    $('.loading-status div').animate({width: count +'%'},150,'linear');
                    $('.percent').text(count + '%');
                    act = t;
                }
            }
            st = t;
            if(count == 100){
                _this.toLogin();
            }
        });
    },
    toLogin:function(){
        var sd = new Date(),ed = new Date();
        sd.setHours(0, 0, 0, 0);
        ed.setHours(0, 40, 0, 0);
        var d = new Date();
        if(d-sd >0 && d-ed < 0){
            if(this.countTip)clearInterval(this.countTip);
            $('.loadbg .note').text('00:00-00:40 是系统正在计算利率时间段，过后再登陆哟！');
            location.href="405.html";
        }else{
            if(this.countTip)clearInterval(this.countTip);
            Backbone.history.navigate('login',{trigger:true});
        }
    },
    loading:function(c,t){
        var _this = this;
        var percent = c;
        //var su = t < 16000 && c < 100 ? Math.ceil((16000-t)/(100-c)) : 100;
        var su = 5;
        var timer = function(){
            percent ++;
            $('.percent').text(percent+'%');
            if(percent < 100){
                $('.loading-status div').animate({width: percent +'%'},su,'linear',function(){
                    timer();
                });
            }else{
                $('.loading-status div').animate({width: percent +'%','border-top-right-radius': '6px','border-bottom-right-radius': '6px'},su,'linear',function(){
                    _this.toLogin();
                });
            }
        };
        if(t < 16000 && c < 100){
            timer();
        }
    },
    loadCache:function(){
        require('css!assets/css/login/login.css');
        require('css!assets/css/home/home.css');
        require('css!assets/css/home/popup.css');
        require('css!assets/css/chickensLog/log.css');
        require('css!assets/css/records/records.css');
        require('css!assets/css/style.css');

        require('login/view.login');
        require('home/view.home');
        require('farm/view.farm');
        require('chickensLog/view.chickensLog');
        require('records/view.records');

        var picUrl = [
            'loadchick.gif',
            //login
            'login/btn.png',
            'login/caps.png',
            'login/f1.png',
            'login/f2.png',
            'login/f3.png',
            'login/f4.png',
            'login/f5.png',
            'login/f6.png',
            'login/f7.png',
            'login/feather.png',
            'login/forget.png',
            'login/forgetpassword-board.png',
            'login/loginbg.png',
            'login/send.png',
            'login/sended.png',
            //home
            'home/alertBoard.png',
            'home/board.png',
            'home/btn-active.png',
            'home/btn-none.png',
            'home/btn-off.png',
            'home/btn-on.png',
            'home/changePassword-board.png',
            'home/chick.png',
            'home/close.png',
            'home/confirm-change-btn.png',
            'home/confirm-longbtn.png',
            'home/dataEdit.png',
            'home/egg.png',
            'home/floor.png',
            'home/gameset-board.png',
            'home/gameSetBtn.png',
            'home/home-bg.png',
            'home/home-footer.png',
            'home/horn.png',
            'home/logout-btn.png',
            'home/mailBtn.png',
            'home/music-off.png',
            'home/music-on.png',
            'home/paper.png',
            'home/tip.png',
            'home/title.png',
            'home/portrait/portrait-chick.png',
            'home/portrait/portrait-dog.png',
            'home/portrait/portrait-man.png',
            'home/portrait/portrait-woman.png',
            'home/portrait/select-btn.png',
            'home/portrait/selected-chick.png',
            'home/portrait/selected-dog.png',
            'home/portrait/selected-man.png',
            'home/portrait/selected-woman.png',
            'home/rule/ds.png',
            'home/rule/num.png',
            'home/rule/wl.png',
            'home/rule/xg.png',
            'home/rule/egg.png',
            //farm
            'farm/Bubble-egg.png',
            'farm/chicken.gif',
            'farm/chicken.png',
            'farm/desktop.png',
            'farm/egg.png',
            'farm/em.btn-l.png',
            'farm/em.btn-r.png',
            'farm/floor.png',
            'farm/friend.png',
            'farm/gold-field.png',
            'farm/grain.png',
            'farm/green-field.png',
            'farm/horn.png',
            'farm/l-vine.png',
            'farm/library.png',
            'farm/lv.1.png',
            'farm/lv.2.png',
            'farm/lv.3.png',
            'farm/lv.4.png',
            'farm/r-vine.png',
            'farm/userBox.png',
            'farm/dog/01.gif',
            'farm/dog/02.gif',
            'farm/dog/03.gif',
            'farm/dog/04.gif',
            'farm/dog/05.gif',
            'farm/dog/06.gif',
            'farm/dog/07.gif',
            'farm/dog/08.gif',
            'farm/dog/09.gif',
            'farm/footer/footer-1-0.png',
            'farm/footer/footer-1-1.png',
            'farm/footer/footer-2-0.png',
            'farm/footer/footer-2-1.png',
            'farm/footer/footer-3-0.png',
            'farm/footer/footer-3-1.png',
            'farm/footer/footer-4-0.png',
            'farm/footer/footer-4-1.png',
            'farm/footer/footer-5-0.png',
            'farm/footer/footer-5-1.png',
            'farm/friend/clean.png',
            'farm/friend/cleaned.png',
            'farm/run/1.png',
            'farm/run/2.png',
            'farm/run/3.png',
            'farm/run/4.png',
            'farm/run/5.png',
            'farm/run/5r.png',
            'farm/run/6.png',
            'farm/run/6r.png',
            'farm/run/7.png',
            'farm/run/7r.png',
            'farm/run/8.png',
            'farm/run/8r.png',
            'farm/run/9.png',
            'farm/run/run.png',
            'farm/run/run0.png',
            'farm/run/run1.png',
            'farm/walk/1.png',
            'farm/walk/2.png',
            'farm/walk/3.png',
            'farm/walk/4.png',
            'farm/walk/5.png',
            'farm/walk/6.png',
            'farm/walk/7.png',
            'farm/walk/walk.png',
            'farm/walk/walk0.png',
            'farm/walk/walk1.png',
            //pop
            'pop/1-1.png',
            'pop/1.btn-1.png',
            'pop/1.btn.png',
            'pop/1.png',
            'pop/2-1.png',
            'pop/2.btn-1.png',
            'pop/2.btn.png',
            'pop/2.png',
            'pop/3-1.png',
            'pop/3.btn-1.png',
            'pop/3.btn.png',
            'pop/3.png',
            'pop/4-1.png',
            'pop/4.btn-1.png',
            'pop/4.btn.png',
            'pop/4.png',
            'pop/big-header.png',
            'pop/btn-code.png',
            'pop/btn-left.png',
            'pop/btn-reigth.png',
            'pop/btn-submit.png',
            'pop/chickens-sign.png',
            'pop/chickens.png',
            'pop/deal-bnt.png',
            'pop/deal-Manteng.png',
            'pop/deal-top.png',
            'pop/deal-with.png',
            'pop/log-foot.png',
            'pop/logo-text.png',
            'pop/lower.png',
            'pop/no-back.png',
            'pop/number.png',
            'pop/paper.png',
            'pop/paper2.png',
            'pop/pop-panel.png',
            'pop/pop-panel2.png',
            'pop/tack-background.png',
            'pop/tack-left.png',
            'pop/tack-right.png',
            'pop/top-mask.png',
            'pop/tree.png',
            'pop/upper.png',
            'pop/wuding-top.png',
            'pop/xj-01.png',
            'pop/xj-back.svg',
            'pop/xj-egg.png',
            'pop/xj-svg.png',
            //window
            'window/cleanAnimation.gif',
            'window/close.png',
            'window/egg.png',
            'window/invite.png',
            'window/left_y.png',
            'window/min-shade-content-bg.png',
            'window/register.png',
            'window/right_y.png',
            'window/storage-ico.png',
            'window/title-bg.png',
            'window/mchine/has-mchine.png',
            'window/mchine/no-mchine.png'
        ];
        var newimages = [],_this = this;
        for(var i = 0; i < picUrl.length; i++){
            newimages[i] = new Image();
            newimages[i].src = 'assets/images/' + picUrl[i];
            if(newimages[i].complete){
                //已经加载了
                //console.log(i);
            }
            newimages[i].onload = function(){
                //console.log(this.sd);
                var n = _this.picCount.get('loadPicCount');
                n++;
                _this.picCount.set('loadPicCount',n);
                _this.picCount.set('time',new Date().getTime());
            };
            newimages[i].onerror = function(){
                console.log(picUrl[i] + '加载失败!');
            };
        }

    }
    });
    
    return View;
	

})

