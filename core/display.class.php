<?php
/**
 * +----------------------------------------------------------------------
 * | 打印结构模板显示类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace core;
class display{
	protected $businesses = ''; //商家名称
	protected $deliverCouponName = ''; //优惠活动标题
	
	public $buyGoodsList = array(); //未参与优惠活动的商品列表清单
	public $buyGoodsDiscountCouponList = array(); //参与折扣类优惠活动的商品清单
	public $buyGoodsDeliverCouponList = array(); //参与买赠类优惠活动的商品清单
	public $total = 0; //购买商品金额总计（不计优惠金额）
	public $sub_total = 0; //购买商品金额总计（扣除优惠金额后）
	public $save_total = 0; //优惠金额总计
	
	/**
	 * 设置商家名称
	 * @param string $business
	 */
	public function setBusinessName($business = ''){
		$this->businesses = $business;
		return $this;
	}
    
    /**
     * 设置显示参数
     * @param string $deliverCouponName               
     * @param list $buyGoodsList
     * @param list $buyGoodsDiscountCouponList
     * @param list $buyGoodsDeliverCouponList
     * @param list $total
     * @param list $sub_total
     * @param list $save_total
     * @return display
     */
    public function setViewData($deliverCouponName, $buyGoodsList, $buyGoodsDiscountCouponList, 
            $buyGoodsDeliverCouponList, $total, $sub_total, $save_total) 
    {
        $this->deliverCouponName = $deliverCouponName;
        $this->buyGoodsList = $buyGoodsList;
        $this->buyGoodsDeliverCouponList = $buyGoodsDeliverCouponList;
        $this->buyGoodsDiscountCouponList = $buyGoodsDiscountCouponList;
        $this->total = $total;
        $this->sub_total = $sub_total;
        $this->save_total = $save_total;
        return $this;
    }
	
    /**
     * 打印输出小票内容
     */
	public function view(){
	    header("Content-type: text/html; charset=utf8");
	    
	    $show = "***<{$this->businesses}>购物清单***</br>";
	    if (count($this->buyGoodsList) > 0) {
	        foreach ($this->buyGoodsList as $item) {
	            if ($item['save_total_price'] > 0) {
	                $show .= "名称:{$item['name']},数量:{$item['pay_total_number']}({$item['unit']}),单价:{$item['price']}(元),小计:{$item['real_total_price']}(元),节省{$item['save_total_price'] }(元)</br>";
	            } else {
	               $show .= "名称:{$item['name']},数量:{$item['pay_total_number']}({$item['unit']}),单价:{$item['price']}(元),小计:{$item['real_total_price']}(元)</br>";
	            }
	        }
	    }
	    if (count($this->buyGoodsDeliverCouponList) > 0) {
	        $show .= "----------------------</br>";
	        $show .= "{$this->deliverCouponName}商品：</br>";
	        foreach ($this->buyGoodsDeliverCouponList as $item) {
	            $show .= "名称:{$item['name']},数量:{$item['save_total_number']}{$item['unit']}</br>";
	        }
	    }
	    $show .= "----------------------</br>";
	    $show .= "总计:{$this->sub_total}(元)</br>";
	    if ($this->save_total > 0) {
	        $show .= "节省:{$this->save_total}(元)</br>";
	    }
	    $show .= "**********************</br>";
	    
	    die($show);
	}
}