<?php
    $second = 60;
    $link = mysqli_connect('localhost','root','12345678','wangluobi') or die('数据库连接失败:'.mysqli_connect_error());
    mysqli_set_charset($link, "utf8");

    #处理业务开始
    $sql = "SELECT * FROM `gs_queue` WHERE state = 1 AND count < ".$second;  #查询需要分红的会员
    $result = mysqli_query($link,$sql);
    $res = [];  #存放需要分红的会员
    while ($row = mysqli_fetch_assoc($result)) {
        $res[] = $row;
    }


    date_default_timezone_set("PRC");

    $daytime = mktime(0,0,0,date("m"),date("d"),date("Y"));     #获取当天00:00:00时间戳
    $cishu = 0;
    foreach ($res as $key => $value) {
       
	   $sql = "SELECT * FROM `gs_queue_log` WHERE `uid` = ". $value['uid'] ." AND `fundid` = ". $value['fundid'] ." AND `dividendtime` >= ".$daytime;
        $result = mysqli_query($link,$sql); #查询以分红的记录
        if ($row = mysqli_fetch_assoc($result)) {   #存在记录说明已经分红 跳过
            continue;
        }else{  #当天不存在分红情况,进行静态分红
            #获取增加账户钱包之前的余额,用于财务交易记录
            $sql = "SELECT `financial_wallet` FROM `gs_member_cash` WHERE `mid` = ".$value['uid'];
            $result = mysqli_query($link,$sql);
            $res = mysqli_fetch_assoc($result);
            $oldMoney = $res['financial_wallet'];

            #分红业务处理
            #
            #开始增加金额
            $sql = "UPDATE `gs_member_cash` SET financial_wallet = financial_wallet + ". $value['bonusmoney']. " WHERE `mid` = ". $value['uid'];

            $result = mysqli_query($link,$sql);
            if(!mysqli_affected_rows($link)){   #分红金额没有增加成功
                file_put_contents("log.txt", date('Y-m-d',time())."Update gs_member_cash is financial_wallet Error".mysqli_connect_error()."<br/>", FILE_APPEND);
                continue;
            }

            #增加钱包金额成功之后,分红次数增1
            $sql = "UPDATE `gs_queue` SET count = count + 1 WHERE `uid` = ". $value['uid']." AND fundid = ".$value['fundid'];
            $result = mysqli_query($link,$sql);



            #获取最新的以分红次数,如果达到条件,把状态改为分红已到期
            $sql = "SELECT `count` FROM `gs_queue` WHERE id = ".$value['id'];
           
            $result = mysqli_query($link,$sql);
            $res = mysqli_fetch_assoc($result);
            
            if($res['count'] >= $second){   //如果分红的次数达到了条件
                $sql = "UPDATE `gs_queue` SET state = 2 WHERE id = ".$value['id'];
                
                $result = mysqli_query($link,$sql);
            }
           



            ##获取最新的钱包余额
            $sql = "SELECT `financial_wallet` FROM `gs_member_cash` WHERE `mid` = ".$value['uid'];
            $result = mysqli_query($link,$sql);
            $res = mysqli_fetch_assoc($result);
            $newMoney = $res['financial_wallet'];


            #获得账户名字
            $sql = "SELECT `account` FROM `gs_member` WHERE `id` = ".$value['uid'];
            $result = mysqli_query($link,$sql);
            $res = mysqli_fetch_assoc($result);
            $account = $res['account'];

            ##分红金额增加到账户完毕,添加账户交易记录
            $sql = "INSERT INTO `gs_member_cash_log`(`before_prize`, `prize`, `after_prize`, `date`, `touch_member`, `recep_member`, `status`, `content`, `wallet`) VALUES ($oldMoney,$value[bonusmoney],$newMoney,".time().",'".$account."','".$account."',0,'基金编号".$value['fundid']."的每日静态分红',0)";
            $result = mysqli_query($link,$sql);
            if(!mysqli_affected_rows($link)){   #分红记录增加失败

                file_put_contents("log.txt", date('Y-m-d',time())."Add gs_member_cash_log is log Error".mysqli_connect_error()."<br/>", FILE_APPEND);
                continue;
            }

		  $cishu++;
            #分红完毕增加到分红记录表,防止重复分红
            $sql = "INSERT INTO `gs_queue_log`(`uid`, `fundid`, `bonusmoney`, `dividendtime`) VALUES (".$value['uid'].",".$value['fundid'].",".$value['bonusmoney'].",".time().")";
            $result = mysqli_query($link,$sql);
            if($result){    #分红成功,跳出此次循环
                continue;
            }else{          #分红失败,存到Log日志
                file_put_contents("log.txt", date('Y-m-d',time())."Add gs_queue_log Error".mysqli_connect_error()."<br/>", FILE_APPEND);
                continue;
            }

        }
    }

echo date('Y-m-d H:i:s')."号-------------分红完毕,分红次数".$cishu."<br>";

mysqli_close($link);
