<?php

//领取礼包记录模型类

namespace Android\Model;

use Think\Model;

class GiftRecordModel extends Model
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

	public function check_gift_receive($user_id, $gift_id)
	{
		if(empty($user_id) || empty($gift_id))
		{
			return false;
		}

		$map = array();
		$map['user_id'] = $user_id;
		$map['gift_id'] = $gift_id;
		$info = $this->where($map)->getField('id');
		if(empty($info))
		{
			return true;
		}

		return false;
	}

	public function insert($data)
	{
		if(empty($data))
		{
			return false;
		}

		$id = $this->add($data);

		return $id;
	}

	/**
     * 根据条件查询领取礼包记录
     * @param array where 查询条件
     * @param string field 需要查询的字段
     */
	public function search_gift_record_list($where = array())
	{
		if(empty($where))
		{
			return array();
		}

		$result = array();

		$data = $this->where($where)->order('create_time desc')->select();
		if($data)
		{
			foreach($data as $key => $value)
			{
				$result[$value['gift_id']] = $value;
			} 
		}

		return $result;
	}
}
