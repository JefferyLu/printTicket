<?php
/**
 * +----------------------------------------------------------------------
 * | 数据操作基类
 * +----------------------------------------------------------------------
 * | Author: lujingfeng <chingfeng@foxmail.com>
 * +----------------------------------------------------------------------
 * | Date: 2016-03-01
 * +----------------------------------------------------------------------
 */
namespace dal\model;

class modelBase{
    private static $condition = array(
        '>' => '>',
        '<' => '<',
        '>=' => '>=',
        '<=' => '<=',
        '$gt' => '>',
        '$lt' => '<',
        '$gte' => '>=',
        '$lte' => '<='
    );
    
    protected $tablename = null;
    protected $db = null;
    
    /**
     * 构造函数
     * @param string $db
     * @throws Exception
     */
    public function __construct($db = null)
    {
        $this->db = $db;
        if (empty($this->tablename))
            throw new \Exception('table name not exists.');
        if (empty($this->db))
            throw new \Exception('db is not exists.');
    }
    
    /**
     * 获取表名
     */
    public function getTablename()
    {
        return $this->tablename;
    }
    
    /**
     * 获取db
     */
    public function getDb(){
        return $this->db;
    }

    /**
     * 添加表数据
     * @param array $data   字段数组
     * @return 返回插入后的自增id，如果没有自增id则返回0，失败返回false，判断失败请用 ===false
     */
    public function insertTable($data)
    {
        if (empty($this->db) || empty($this->tablename))
            return false;
        if (! is_array($data) || empty($data))
            return false;
        
        $sql = "insert into `{" . $this->tablename . "}` set ";
        foreach ($data as $key => $value) {
            $_sql[] = "`$key`='" . addslashes($value) . "'";
        }
        $sql = $sql . join(",", $_sql);
        
        $result = $this->db->db_query($sql);
        
        if ($result) {
            return $this->db->db_insert_id();
        } else {
            return false;
        }
    }
    
    /**
     * 查询单条数据信息
     * @param string $where 查询条件
     * @param string $sort  排序
     * @return data         结果集
     */
    public function selectTable($where = array(), $sort = array())
    {
        if (empty($this->db) || empty($this->tablename))
            return false;
    
        $sql = "select * from `{" . $this->tablename . "}` ";
        $sql .= self::getWhereToString($where) . ' ';
        $sql .= self::getOrderToString($sort) . ' ';

        return $this->db->db_fetch_arrays($sql);
    }
    
    /**
     * 查询多条数据
     * @param string $where 查询条件
     * @param number $page  当前页
     * @param number $epage 每页显示条数
     * @param string $order 排序
     * @return data         结果集
     */
    public function selectTableList($where = array(), $page = 1, $epage = 10, $order = array())
    {
        if (empty($this->db) || empty($this->tablename))
            return false;
        if (! is_array($order))
            return false;

        $_sql = self::getWhereToString($where);

        $sql = "select count(*) as num from `{" . $this->tablename . "}` ";
        $_result = $this->db->db_fetch_array($sql . $_sql);
        
        $sql = "select * from `{" . $this->tablename . "}` $_sql";
        if (! empty($order)) {
            $sql .= ' order by '.self::getOrderToString($order);
        }
    
        if (intval($page) <= 0)
            $page = 1;
        if (intval($epage) <= 0)
            $epage = 10;
        $vpage = ($page - 1) * $epage;
    
        $sql .= " limit $vpage,$epage";
 
        $result = $this->db->db_fetch_arrays($sql);
        return array(
            "result" => $result,
            "num" => $_result['num']
        );
    }
    
    /**
     * 修改表数据
     * @param string $where      更新条件
     * @param array $update_data 更新数据
     * @return 返回更新影响行数，失败返回false
     */
    public function updateTable($where, $update_data)
    {
        if (empty($this->db) || empty($this->tablename))
            return false;
        // update 不允许条件为空
        if (empty($where))
            return false;
    
        $updateString = self::getArrayToString($update_data);
        $whsql = self::getWhereToString($where);
        $sql = "update `{{$this->tablename}}` set {$updateString} {$whsql}";
    
        $res = $this->db->db_query($sql);
        if (! $res)
            return false;
        return mysql_affected_rows();
    }
    
    /**
     * 删除表数据
     *
     * @param unknown $table
     * @return unknown
     */
    public function deleteTable($where)
    {
        if (empty($this->db) || empty($this->tablename))
            return false;
        // delete 不允许条件为空
        if (empty($where))
            return false;
    
        $whsql = self::getWhereToString($where);
        if (empty($whsql))
            return false;
    
        $sql = "delete from `{" . $this->tablename . "}` $whsql ";
        return $this->db->db_query($sql);
    }
    
    private function getArrayToString($data = array())
    {
        $returnSql = array();
    
        if (! is_array($data) || empty($data))
            return '';
    
        foreach ($data as $key => $value) {
            $value = addslashes($value);
            $returnSql[] = "`$key` = '$value'";
        }
        return implode(',', $returnSql);
    }
    
    /**
     * 将数组转化成where语句
     *
     * @param array $data
     * @return string 参数传递其他说明：
     *         正常使用：array(key, value)
     *         搜索使用：array(key, array('>', value))
     *         函数使用：array(key, array('func', array('func_name', 'func_format', 'func_tag', 'func_value')))
     */
    private function getWhereToString($data = array())
    {
        if (empty($data))
            return '';
        // 如果是字符串，使用自定义的检索条件
        if (is_string($data))
            return 'WHERE ' . $data;
    
        $where = array();
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $do_key = strtolower($value[0]);
                $do_value = $value[1];
                if (array_key_exists($do_key, self::$condition)) {
                    $do_value = addslashes($do_value);
                    $where[] = "{$key} " . self::$condition[$do_key] . " '{$do_value}'";
                } else
                    if ($do_key == 'like') {
                        $do_value = addslashes($do_value);
                        $where[] = "{$key} like '%{$do_value}%'";
                    } else
                        if ($do_key == 'in') {
                            $where[] = "{$key} in ({$do_value})";
                        } else
                            if ($do_key == 'func') {
                                $func_name = $do_value[0];
                                $func_format = $do_value[1];
                                $func_tag = $do_value[2];
                                $func_value = $do_value[3];
    
                                $structure = ! empty($func_format) ? "{$key}, {$func_format}" : "{$key}";
                                $where[] = "{$func_name}({$structure}) = '{$func_value}'";
                            }
            } else {
                $value = addslashes($value);
                $where[] = "`$key` = '$value'";
            }
        }
    
        return 'WHERE ' . implode(' AND ', $where);
    }
    
    private function getOrderToString($data = array())
    {
        if (empty($data))
            return '';
    
        $order_string = '';
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $order_string .= "`$key` $value,";
            }
            $order_string = 'ORDER BY ' . rtrim($order_string, ',');
        } else {
            $order_string = "ORDER BY {$data}";
        }
    
        return $order_string;
    }
}