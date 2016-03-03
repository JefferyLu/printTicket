<?php
/**
 * +----------------------------------------------------------------------
 * | 商品数据模块操作类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace dal;

use dal\model\goodsModel;
class goods{
    private $goodsModel = null;
    /**
     * 构造函数
     * @param string $db
     */
    public function __construct($db = null)
    {
        $this->goodsModel = new goodsModel($db);
    }
    
    /**
     * 获取商品表详情
     * @param string $barcode   条形码
     */
    public function getGoodsDetail($barcode){
        $res = $this->goodsModel->selectTable ( array (
                'barcode' => $barcode 
        ) );
        return $res[0];
    }
}