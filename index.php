<?php
/**
 * +----------------------------------------------------------------------
 * | 打印小票模块调用示例文件
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
use core\printTicket;
include_once dirname(__FILE__) . '/config/define.inc.php';
include_once MODEL_CORE_PATH . 'printTicket.class.php';

switch ($_GET['action']) {
    case 'test1': //当购买的商品中，有符合“买二赠一”优惠条件的商品时
        test1();
        break;
    case 'test2': //当购买的商品中，没有符合“买二赠一”优惠条件的商品时
        test2();
        break;
    case 'test3': //当购买的商品中，有符合“95折”优惠条件的商品时
        test3();
        break;
    case 'test4': //当购买的商品中，有符合“95折”优惠条件的商品，又有符合“买二赠一”优惠条件的商品时
        test4();
    default:
        test4();
        break;
}

//当购买的商品中，有符合“买二赠一”优惠条件的商品时
function test1(){
    $input = <<<EOF
[
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000006",
    "ITEM000006",
    "ITEM000006",
    "ITEM000006"
]
EOF;
    $ticketObj = printTicket::getInstance();
    $ticketObj->execPrint($input);
}

//当购买的商品中，没有符合“买二赠一”优惠条件的商品时
function test2(){
    $input = <<<EOF
[
    "ITEM000006",
    "ITEM000006",
    "ITEM000006",
    "ITEM000006",
    "ITEM000007",
    "ITEM000007",
    "ITEM000007",
    "ITEM000008"
]
EOF;
    $ticketObj = printTicket::getInstance();
    $ticketObj->execPrint($input);
}

//当购买的商品中，有符合“95折”优惠条件的商品时
function test3(){
    $input = <<<EOF
[
    "ITEM000007",
    "ITEM000007",
    "ITEM000007",
    "ITEM000008",
    "ITEM000008",
    "ITEM000008",
    "ITEM000003-3"
]
EOF;
    $ticketObj = printTicket::getInstance();
    $ticketObj->execPrint($input);
}

//当购买的商品中，有符合“95折”优惠条件的商品，又有符合“买二赠一”优惠条件的商品时
function test4(){
    $input = <<<EOF
[
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000001",
    "ITEM000003-2",
    "ITEM000005",
    "ITEM000005",
    "ITEM000005"
]
EOF;
    $ticketObj = printTicket::getInstance();
    $ticketObj->execPrint($input);
}

