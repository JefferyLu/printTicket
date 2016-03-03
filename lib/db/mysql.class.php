<?php
/**
 * +----------------------------------------------------------------------
 * | mysql数据库基础操作类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace lib\db;

class mysql{
    protected $_dblink;                         // 数据库连接信息
    protected $_db_show_error       = false;    // 是否记录错误信息到日志
    protected $_db_display_error    = true;     // 是否将db错误日志输出到浏览器客户端
    protected $_mysql_error_path    = '';
    
	protected $_dbserver     = null;
	protected $_dbuser       = null;
	protected $_dbpassword   = null;
	protected $_database     = null;
	protected $_port         = null;
	protected $_prefix       = null;
	protected $_charset      = null;
	
	private static $instance = null;
	
	/**
	 * 生成单例对象
	 * @param array $config   数据库相关配置
	 */
	public static function &getInstance($db_config) {
	    if (! isset ( self::$instance )) {
	        self::$instance = new mysql ( $db_config );
	    }
	    return self::$instance;
	}
	
	/**
	 * 构造函数
	 */
	public function __construct($db_config){
        $this->_dbserver            = $db_config['dbsever']; 
        $this->_database            = $db_config['database'];
        $this->_dbuser              = $db_config['dbuser'];
        $this->_dbpassword          = $db_config['dbpassword'];
        $this->_port                = $db_config['port'];
        $this->_prefix              = $db_config['prefix'];
        $this->_charset             = $db_config['charset'];
	    $this->_db_show_error       = false;
	    $this->_db_display_error    = isset ( $db_config ['db_display_error'] ) ? $db_config ['db_display_error'] : false;
	    $this->_dblink              = $this->connection( $db_config );
	    $this->_mysql_error_path    = isset ( $db_config ['mysql_error_path'] ) ? $db_config ['mysql_error_path'] : 'data';
	}
	
	/**
	 * 连接数据库
	 * @param array $db_config
	 * @return dblink  数据库连接
	 */    
	public function connection($db_config) {
	    // 检查服务器上是否已经安装了连接数据库的此函数
	    if (! function_exists ( 'mysql_connect' )) {
	        $this->db_error_msg ( '您的数据库还未安装此扩展' );
	    }
	
	    $db_config ['dbsever']     = isset ( $db_config ['dbsever'] ) ? urldecode ( $db_config ['dbsever'] ) : '';
	    $db_config ['dbuser']      = isset ( $db_config ['dbuser'] ) ? urldecode ( $db_config ['dbuser'] ) : '';
	    $db_config ['dbpassword']  = isset ( $db_config ['dbpassword'] ) ? urldecode ( $db_config ['dbpassword'] ) : '';
	    $db_config ['database']    = isset ( $db_config ['database'] ) ? urldecode ( $db_config ['database'] ) : '';
	    $db_config ['charset']     = isset ( $db_config ['charset'] ) ? urldecode ( $db_config ['charset'] ) : '';
	    // 是否有端口存在
	    if (isset ( $db_config ['port'] )) {
	        $db_config ['dbsever'] = $db_config ['dbsever'] . ':' . $db_config ['port'];
	    }
	    // 连接数据库
	    $db_link = @mysql_connect ( $db_config ['dbsever'], $db_config ['dbuser'], $db_config ['dbpassword'], TRUE, 2 );
	    if (! $db_link || ! mysql_select_db ( $db_config ['database'], $db_link )) {
	        return $this->db_error_msg ( "数据库连接失败：mysql_error:" . mysql_error () );
	    }
	    
	    mysql_query ( 'SET NAMES "' . $db_config ['charset'] . '"', $db_link );
	    return $db_link;
	}
	
	/**
	 * sql预处理
	 * @param string $sql  sql语句
	 * @return string $sql 处理后的sql
	 */
	public function db_sql_pre($sql) {
	    while ( preg_match ( '/{([a-zA-Z0-9_-]+)}/', $sql, $regs ) ) {
	        $found = $regs [1];
	        $sql = preg_replace ( "/\{" . $found . "\}/", $this->_prefix . $found, $sql );
	    }
	    return $sql;
	}
	
	/**
	 * 执行db查询
	 *
	 * @param type $sql
	 * @param boolean $noreplace
	 *        	true：不替换 false 替换
	 * @return 执行结果
	 */
	public function db_query($sql = "", $noreplace = false) {
	    $sql = trim ( $sql );
	    if (empty ( $sql )) {
	        return false;
	    }
	    if (! $noreplace) {
	        // 如果没设置不替换，则强制替换
	        $sql = $this->db_sql_pre ( $sql );
	    }

	    $result = @mysql_query ( $sql, $this->_dblink );

	    if (! $result) {
	        $this->db_error_msg ( mysql_error () . "执行SQL语句错误!" . $sql );
	    }
	    return $result;
	}
	
	/**
	 * 处理多条sql
	 */
	public function db_querys($sql = "", $noreplace = false) {
	    $_sql = explode ( ";", $sql ); // 多条sql用“顿号：;”隔开
	    foreach ( $_sql as $value ) {
	        $value = trim ( $value );
	        if (! empty ( $value )) {
	            $result = $this->db_query ( $value, $noreplace );
	        }
	    }
	    if (! $result) {
	        $this->db_error_msg ( mysql_error () . "执行SQL语句错误!" . $sql );
	    }
	    return $result;
	}
	
	/**
	 * 执行一个SQL语句,返回前一条记录或仅返回一条记录
	 */
	public function db_fetch_array($sql) {
	    $result = $this->db_query ( $sql );
	    $row = mysql_fetch_array ( $result, MYSQL_ASSOC );
	    $_res = array();
	    if (is_array ( $row ) && count ( $row ) > 0) {
	        $_res = $row;
	    }
	    return $_res;
	}
	
	/**
	 * 获取全部的记录
	 *
	 * 查询sql的全部记录
	 *
	 * @param type $sql
	 * @return type
	 */
	public function db_fetch_arrays($sql) {
	    $result = $this->db_query ( $sql );
	    $_res = array();
	    while ( $row = @mysql_fetch_array ( $result, MYSQL_ASSOC ) ) {
	        $_res [] = $row;
	    }
	    $this->db_free_result ( $result ); // 释放资源
	
	    return $_res;
	}
	
	/**
	 * 释放记录集占用的资源
	 */
	public function db_free_result($result) {
	    if (is_array ( $result )) {
	        foreach ( $result as $key => $value ) {
	            if ($value) {
	                @mysql_free_result ( $value );
	            }
	        }
	    } else {
	        @mysql_free_result ( $result );
	    }
	}
	
	/**
	 * 获取查询的总记录数
	 */
	public function db_num_rows($sql, $noreplace = false) {
	    $result = $this->db_query ( $sql, $noreplace );
	    $cnt = mysql_num_rows ( $result );
	    $this->db_free_result ( $result );
	    return $cnt;
	}
	
	/**
	 * 获取修改删除的总记录数
	 * 失败为-1
	 */
	public function db_affected_rows($sql, $noreplace = false) {
	    $result = $this->db_query ( $sql, $noreplace );
	    return mysql_affected_rows ();
	}
	
	/**
	 * 获取插入进去的ID
	 */
	public function db_insert_id() {
	    return mysql_insert_id ();
	}
	
	/**
	 * 返回服务器中mysql的版本.
	 */
	public function db_version() {
	    list ( $version ) = explode ( '-', mysql_get_server_info () );
	    return $version;
	}
	
	/**
	 * 关闭数据库
	 */
	public function db_close() {
	    @mysql_close ( $this->_dblink );
	}
	
	/**
	 * 是否打印错误信息
	 */
	public function db_show_msg($i = false) {
	    $this->_db_show_error = $i;
	}
	
	/**
	 * 设置数据库连接字符集
	 * @param string $charset
	 */
	public function setCharset($charset){
		$this->_charset = $charset;
	}
	
	/**
	 * 显示数据链接错误信息
	 * @param string $msg  错误信息
	 * @return boolean 
	 */
	public function db_error_msg($msg) {
	    if ($this->_db_show_error) {
	        $mysql_dir = $this->_mysql_error_path;
	        $dtime = date ( "Y-m-d", time () );
	        $file = "http://" . $_SERVER ['HTTP_HOST'] . $_SERVER ["REQUEST_URI"];
	        if (! file_exists ( $mysql_dir . "/mysql_error" )) {
	            mkdir ( $mysql_dir . "/mysql_error", 0777 );
	        }
	        $fp = @fopen ( $mysql_dir . "/mysql_error/" . $dtime . ".log", "a+" );
	        $time = date ( "H:i:s" );
	        $str = "{visitedtime:$time}\t{errormsg:" . $msg . "}\t{file:" . $file . "}\t\r\n";
	        @fputs ( $fp, $str );
	        @fclose ( $fp );
	    }
	    if ($this->_db_display_error) {
	        die ( $msg );
	    }
	    return false;
	}
}