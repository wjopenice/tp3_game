<?php

//游戏模型类

namespace Android\Model;

use Think\Model;

class GameModel extends Model
{
	//默认查询条件,如果有其他条件则进行合并
	//protected $where = array('game_status'=>1);
	//protected $where['game_status'] = 1;
	//游戏需要的字段
	protected $field = 'id,game_name,icon,game_size,introduction,version_num,version,and_dow_address,apk_pck_name,dow_mynum';
	/**
     * 构造函数
     * @param string $name 模型名称
     * @param string $tablePrefix 表前缀
     * @param mixed $connection 数据库连接信息
     */
    public function __construct($name = '', $tablePrefix = '', $connection = '') {
        /* 设置默认的表前缀 */
        $this->tablePrefix ='tab_';
        /* 执行构造方法 */
        parent::__construct($name, $tablePrefix, $connection);
    }
    /**
     * 根据条件查询游戏
     * @param array where 查询条件
     * @param string field 需要查询的字段
     * @param int p 页数
     * @param int size 每页显示的条数
     */
	public function select_game($where= '', $size=10, $p=1, $field ='')
	{
		/*if(!empty($where))
		{
			$where = array_merge($this->where,$where);
		}else
		{
			$where = $this->where;
		}*/
		if(empty($field))
		{
			$field = $this->field;
		}
		//如果查询的字段中包含包名字段  则删除掉，会在下面调用get_apk_pck_name查询
		if(strpos($field,'apk_pck_name') !== false)
		{
			$field_arr = explode(',',$field);
			$k = array_search('apk_pck_name',$field_arr);
			unset($field_arr[$k]);
			$field_str = implode(',',$field_arr);
		}else
		{
			$field_str = $field; 
		}
		$data = $this->field($field_str)->where($where)->page($p,$size)->order('app_sort')->select();
		//echo $sql=$this->getlastsql();exit;
		//查询字段如果有icon则调用 get_cover 将数值转化成 路径
		if(strpos($field,'icon') !== false || $field = '*')
		{
			foreach($data as $key=>&$v)
			{
            	$v['icon']=get_cover($v['icon'],'path');
        	}
		}
		if(strpos($field,'apk_pck_name') !== false)
		{
			foreach($data as $key=>&$v)
			{
            	$v['apk_pck_name']=get_apk_pck_name($v['id']);
        	}
		}
		return $data;
	}
	/**
	 * 生成查询条件
	 * @param type string 查询的类型
	 * @param id string 查询的类型对应的id 如可以匹配到case 则可为空
	 * @return array 查询结果数据
	 * @author zdd
	 */
	public function type_to_select($type,$id,$size,$p,$field)
	{
		switch ($type) 
		{
			//单机游戏
			case 1:
				$where= array(
						'cp_name'=>'cps',
						'game_status'=>1,
				);
				break;
			//网络游戏
			case 2:
				$where= array(
						'cp_name'=>array(
									'neq',
									'cps',
						),
						'game_status'=>1,

				);
				break;
			//根据关键字模糊匹配游戏名
			case 3:
				$where = array(
						'game_name' => array(
											'like',
											"%{$id}%",
										),
						'game_status'=>1,

					);
				break;
			default:
				$where= array(
						$type =>$id,
						'game_status'=>1,

				);
				break;
		}
		//根据生成的条件查询结果
		return $this->select_game($where,$size,$p,$field);
	}

	
	

}