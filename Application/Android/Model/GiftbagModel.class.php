<?php

//礼包模型类

namespace Android\Model;

use Think\Model;

class GiftbagModel extends Model
{

	/**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '')
    {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }

    /**
     * 根据条件查询游戏礼包
     * @param array where 查询条件
     * @param string field 需要查询的字段
     * @param int p 页数
     * @param int size 每页显示的条数
     */
	public function search_gift_list($where = array(), $field = '*', $p = '', $size = '')
	{
		if(empty($where))
		{
			return array();
		}

		$where = array_merge(array('end_time'=>array("GT",time()), 'status'=>1), $where);

		if(!empty($p) && !empty($size))
		{
			$list = $this->field($field)->where($where)->page($p, $size)->order('start_time')->select();
			//总数
			$count_data = $this->field("count(id) as count")->where($where)->select();
			$count = intval($count_data[0]['count']);

			$data = array('list'=>$list, 'count'=>$count);
		}
		else
		{
			$data = $this->field($field)->where($where)->order('start_time')->select();
		}

		return $data;
	}

	/**
     * 根据礼包id查询礼包详情
     * @param int gift_id 礼包id
     * @param string field 需要查询的字段
     */
	public function get_gift_info($gift_id, $field = '*')
	{
		if(empty($gift_id))
		{
			return array();
		}

		$info = $this->field($field)->find($gift_id);

		return $info;
	}

	/**
     * 根据礼包id修改礼包信息
     * @param int gift_id 礼包id
     * @param array data 需要修改数据
     */
	public function update($gift_id, $data)
	{
		if(empty($gift_id) || empty($data))
		{
			return false;
		}

		$status = $this->where("id=" . $gift_id)->save($data);

		return $status;
	}
}
