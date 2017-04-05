<?php
/**
 * Created by PhpStorm.
 * User: jiaruo
 * Date: 17/2/15
 * Time: 下午2:04
 */
/**
 * 数组格式化后输出json.
 */
function echoJson($data){
    $errormsg =  C('msg');
    if (is_array($data)) {
        $errCode = 10000;
        $data = $data == array() ? array() :
            (is_array(reset($data)) ? $data : array($data));
    } elseif ($data === true) {
        $errCode = 10000;
        $data = array();
    } elseif (is_numeric($data)) {
        $errCode = $data;
        $data = array();
    } elseif (is_string($data) && !empty($data)) {
        $errCode = -1;
        $msg = $data;
        $data = array();
    } else {
        $errCode = -1;
        $data = array();
    }

    $msg = $msg?$msg:$errormsg[$errCode];
    $data = array(
        'errcode' => $errCode,
        'msg' => $msg,
        'result' => $data,
    );
    return $data;
}
