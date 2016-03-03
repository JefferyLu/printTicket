<?php
/**
 * +----------------------------------------------------------------------
 * | 打印小票核心功能类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace core;

use lib\db\mysql;
use model\goodsService;
class printTicket{
    
    private static $_instance;
    public static $config = null;
    public $db = null;
    
    /**
     * 生成单例对象
     * @return \printTicket\printTicket
     */
    public static function &getInstance(){
        if (! self::$_instance instanceof printTicket) {
            self::$_instance = new printTicket();
        }
        return self::$_instance;
    }
    
    /**
     * 构造函数
     */
    private function __construct(){
        $config = include_once MODEL_ROOT_PATH . '/config/config.inc.php';
        // 注册自动加载器
        spl_autoload_register ( 'self::splAutoload' );
        
        $this->db = mysql::getInstance($config['db_config']);
        self::$config = $config;
    }
    
    /**
     * 获取当前db连接
     */
    public function getDB(){
        return $this->db;
    }
    
    /**
     * 自动加载器
     * @param string $className
     */
    public static function splAutoload($className)
    {
        $className = str_replace ( '\\', '/', $className );
        $filename = MODEL_ROOT_PATH . $className . '.class.php';
        if (file_exists ( $filename )) {
            include_once $filename;
        }
    }
    
    /**
     * 执行打印小票
     * @param json $inputs 
     */
    public function execPrint($inputs){
    	$goodsServiceObj = new goodsService($this->db);
    	$display = new display();
    	$goodsServiceObj->setFormatInput($inputs)->process();
    	$display->setBusinessName('清风超市')->setViewData(
    	    $goodsServiceObj->goodsDeliverCouponName, 
            $goodsServiceObj->buyGoodsList, 
            $goodsServiceObj->buyGoodsDiscountCouponList, 
    	    $goodsServiceObj->buyGoodsDeliverCouponList, 
    	    $goodsServiceObj->total, 
    	    $goodsServiceObj->sub_total, 
    	    $goodsServiceObj->save_total
    	)->view();
    }
}