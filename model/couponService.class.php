<?php
/**
 * +----------------------------------------------------------------------
 * | 优惠信息业务服务类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace model;

use dal\goodsCoupon;
use dal\coupon;
class couponService{
	private $type = array(); //优惠类型
	private $goodsCouponObj = null;
	private $couponObj = null;
	
	/**
	 * 构造函数
	 */
	public function __construct($db = null){
	    
	    $this->couponObj = new coupon($db);
		$this->goodsCouponObj = new goodsCoupon($db);
	}
	
	/**
	 * 根据条形码获取当前可用优惠详情信息
	 * @param string $barcode  商品唯一条形码
	 */
	public function getCouponDetail($barcode){
	    //根据优惠级别获取当前商品对应的唯一优惠信息
        $res = $this->goodsCouponObj->getGoodsCouponList ( 
                array (
                    'barcode' => $barcode 
                ), 
                array (
                    'level' => 'DESC' 
                )
        );
        $goodsCouponDetail = $res[0];
        if (!empty($goodsCouponDetail)) {
            $couponDetail = $this->couponObj->getCouponById($goodsCouponDetail['coupon_id']);
            return array_merge($goodsCouponDetail, $couponDetail);
        } else {
        	return null;
        }
	}
	
	/**
	 * 根据优惠规则获取单个商品优惠后的数量和金额
	 * @param data     $coupon     优惠详情
	 * @param int      $number     商品数量
	 * @param float    $price      商品单价
	 * @return 优惠后数量小计和优惠后金额小计
	 */
	public function getTotalByCoupon($coupon, $number, $price){
		if (empty($coupon)) {
			throw new \Exception('优惠信息为空！');
		}
		if ('discount' == $coupon['type']) { //打折类
		    $real_number = $number;   //优惠后数量
		    $real_price = round($number * $price * $coupon['rules'], 2); //优惠后金额
		} elseif ('deliver' == $coupon['type']) { //买赠类
		    list($max, $num) = explode(':', $coupon['rules']);
		    
		    //赠送数量 = 购买总数 / (赠送边界值 + 赠送个数)
		    $save_number = floor($number / ($max + $num));
		    $real_number = $number - $save_number; //优惠后数量
		    $real_price = round($real_number * $price, 2); //优惠后金额
		}
		return array('real_total_number' => $real_number, 'real_total_price' => $real_price, 'save_total_number' => $save_number);
	}
}