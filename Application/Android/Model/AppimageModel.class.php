<?php

//广告图片模型类

namespace Android\Model;

use Think\Model;

class AppimageModel extends Model
{
	//默认查询条件
	protected $where = array('status'=>1);
    //图片需要的字段
    protected $image_field = 'title,image_url,game_id,location,adv_url,adv_type,adv_jump_id';
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
     * 根据位置查询图片
     * @param array where 查询条件
     * @param string field 需要查询的字段
     */
	public function select_adv($where,$size,$p,$field='')
	{
		if(!empty($where))
		{
			$where = array_merge($this->where,$where);
		}else
		{
			$where = $this->where;
		}
		$data = $this->where($where)->field($field)->order('sort')->select();
		foreach($data as $key=>&$v)
		{
            $v['image_url']=get_cover($v['image_url'],'path');
            
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
    public function where_to_select($type,$id='',$size='',$p='',$field='')
    {
        switch ($type) 
        {
            case '1':
                $where= array(
                        'pos_id'=>1,
                );
                break;
            default:
                $where= array(
                        $type =>$id,
                );
                break;
        }
        return $this->select_adv($where,$size,$p,$field);
    }

	

}