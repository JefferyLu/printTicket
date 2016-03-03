<?php
/**
 * +----------------------------------------------------------------------
 * | 商品优惠信息对应数据模块操作类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace dal;

use dal\model\goodsCouponModel;

class goodsCoupon {
    private $goodsCouponModel = null;
    private $couponObj = null;
    
    /**
     * 构造函数
     * @param string $db            
     */
    public function __construct($db = null) {
        $this->goodsCouponModel = new goodsCouponModel ( $db );
        $this->couponObj = new coupon($db);
    }
    
    /**
     * 获取对应商品优惠信息列表
     * @param string $where 筛选条件 
     * @param string $sort  排序条件
     * @return array 列表结果集
     */
    public function getGoodsCouponList($where = array(), $sort = array()) {
        return $this->goodsCouponModel->selectTable($where, $sort);
    }
}