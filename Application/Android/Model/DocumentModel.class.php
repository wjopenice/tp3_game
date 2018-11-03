<?php
//文档模型
namespace Android\Model;

use Think\Model;

class DocumentModel extends Model
{
	//获取文档详情
	public function detail($id)
	{
		$where['d.id'] = $id;
		$info = $this->alias('d')->field('d.title title,d.uid,d.create_time,a.content send_content')->join('sys_document_article a on a.id = d.id')->where($where)->find();
		$info['send_account'] = get_admin_name($info['d.id'] );
		return $info;
	}
}