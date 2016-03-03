<?php
/**
 * +----------------------------------------------------------------------
 * | 打印小票商品业务模块服务类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace model;

use dal\goods;
class goodsService{
    private $goodsObj = null;
    private $coupon = null;
    private $barcodes = array();
    
    public $buyGoodsList = array(); //未参与优惠活动的商品列表清单
    public $buyGoodsDiscountCouponList = array(); //参与折扣类优惠活动的商品清单
    public $goodsDiscountCouponName = ''; //折扣类活动名称
    public $buyGoodsDeliverCouponList = array(); //参与买赠类优惠活动的商品清单
    public $goodsDeliverCouponName = ''; //买赠类活动名称
    public $total = 0; //购买商品金额总计（不计优惠金额）
    public $sub_total = 0; //购买商品金额总计（扣除优惠金额后）
    public $save_total = 0; //优惠金额总计
    
    /**
     * 构造函数
     */
    public function __construct($db = null){
    	$this->goodsObj = new goods($db);
    	$this->coupon = new couponService($db);
    }
    /**
     * 购买商品清单处理
     */
    public function process(){
        foreach($this->barcodes as $value){
            $barcode = $value['code'];
            $number = $value['num'];
    
            $goodsRes = $this->goodsObj->getGoodsDetail($barcode);
            
            $this->buyGoodsList[$barcode] = $goodsRes;
            $this->buyGoodsList[$barcode]['pay_total_number'] = $number;
            $this->buyGoodsList[$barcode]['pay_total_price'] = round($goodsRes['price'] * $number, 2);
            $this->buyGoodsList[$barcode]['real_total_number'] = $number;
            $this->buyGoodsList[$barcode]['real_total_price'] = round($number * $goodsRes['price'], 2);
            
            $coupon = $this->coupon->getCouponDetail($barcode);
            if (!empty($coupon)) { //如果有优惠信息则按照优惠规则进行计算
                $real_res = $this->coupon->getTotalByCoupon($coupon, $number, $goodsRes['price']);
                $this->buyGoodsList[$barcode]['pay_total_number'] = $number;
                $this->buyGoodsList[$barcode]['pay_total_price'] = round($goodsRes['price'] * $number, 2);
                $this->buyGoodsList[$barcode]['real_total_number'] = $real_res['real_total_number'];
                $this->buyGoodsList[$barcode]['real_total_price'] = $real_res['real_total_price'];
                
                if ('discount' == $coupon['type']) { //折扣类
                    $this->goodsDiscountCouponName = $coupon['title'];
                    $this->buyGoodsDiscountCouponList[$barcode] = $goodsRes;
                    $this->buyGoodsList[$barcode]['save_total_price'] = round($this->buyGoodsList[$barcode]['pay_total_price'] - $this->buyGoodsList[$barcode]['real_total_price'], 2);
                } elseif ('deliver' == $coupon['type']) { //买赠类
                    $this->goodsDeliverCouponName = $coupon['title'];
                    $this->buyGoodsDeliverCouponList[$barcode] = $goodsRes;
                    $real_res = $this->coupon->getTotalByCoupon($coupon, $number, $goodsRes['price']);
                    $this->buyGoodsDeliverCouponList[$barcode]['save_total_number'] = $real_res['save_total_number'];
                }
            }
            $this->total += round($this->buyGoodsList[$barcode]['pay_total_price'], 2);
            $this->sub_total += round($this->buyGoodsList[$barcode]['real_total_price'], 2);
        }
        $this->save_total = $this->total - $this->sub_total;
    }
    
    /**
     * 输入参数格式化处理
     */
    public function setFormatInput($inputs){
        $barcodes = array();
    	$inputs = json_decode($inputs, true);
    	$inputs = array_count_values($inputs);
        foreach ($inputs as $key => $item) {
            $barcodes[$key]['code'] = $key;
            $barcodes[$key]['num'] = $item;
        	$count = mb_substr_count($key, '-');
        	if ($count > 0) {
        	    unset($barcodes[$key]);
        	    list($code, $num) = explode('-', $key);
                $barcodes[$code]['code'] = $code;
                $barcodes[$code]['num'] = $num;
        	}
        }
        $this->barcodes = $barcodes;
        return $this;
    }
    
    /**
     * 析构函数
     */
    public function __destruct(){
    	$this->barcodes = array();
    	$this->buyGoodsList = array();
    	$this->buyGoodsDeliverCouponList = array();
    	$this->buyGoodsDiscountCouponList = array();
    	$this->goodsDeliverCouponName = '';
    	$this->goodsDiscountCouponName = '';
    	$this->total = 0;
    	$this->sub_total = 0;
    	$this->save_total = 0;
    }
}