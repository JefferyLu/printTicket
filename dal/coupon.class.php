<?php
/**
 * +----------------------------------------------------------------------
 * | 优惠信息数据模块操作类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace dal;

use dal\model\couponModel;
class coupon{
    private $couponModel = null;
    /**
     * 构造函数
     * @param string $db
     */
    public function __construct($db = null)
    {
        $this->couponModel = new couponModel($db);
    }
    
    /**
     * 根据优惠表id获取优惠信息数据
     * @param int $coupon_id    优惠表id
     */
    public function getCouponById($coupon_id){
    	$res = $this->couponModel->selectTable ( array (
                'id' => $coupon_id,
        ) );
    	return $res[0];
    }
}