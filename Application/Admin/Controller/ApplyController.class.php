<?php

namespace Admin\Controller;
use User\Api\UserApi as UserApi;
use OSS\OssClient;
use OSS\Core\OSsException;
/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class ApplyController extends ThinkController {
    //private $table_name="Game";
    const model_name = 'Apply';

    public function lists(){
        if(isset($_REQUEST['game_id'])){
            if($_REQUEST['game_id']=='全部'){
                unset($_REQUEST['game_id']);
            }else{
                $map['game_id']=$_REQUEST['game_id'];
            }
            unset($_REQUEST['game_id']);
        }
        /*if(isset($_REQUEST['promote_name'])){
            if($_REQUEST['promote_name']=='全部'){
                unset($_REQUEST['promote_name']);
            }else if($_REQUEST['promote_name']=='自然注册'){
                $map['promote_id']=array("elt",0);
                unset($_REQUEST['promote_name']);
            }else{
                $map['promote_id']=get_promote_id($_REQUEST['promote_name']);
                unset($_REQUEST['promote_name']);
            }
        }*/
        if(isset($_REQUEST['promote_id'])){
                if($_REQUEST['promote_id']=='全部'){
                    unset($_REQUEST['promote_id']);
                }else if($_REQUEST['promote_id']=='自然注册'){
                    $map['promote_id']=array("elt",0);
                    #unset($_REQUEST['promote_name']);
                }else{
                    $map['promote_id']=$_REQUEST['promote_id'];
                   # unset($_REQUEST['promote_name']);
                }
        }
        if(isset($_REQUEST['time-start'])&&isset($_REQUEST['time-end'])){
            $map['apply_time'] =array('BETWEEN',array(strtotime($_REQUEST['time-start']),strtotime($_REQUEST['time-end'])+24*60*60-1));
            unset($_REQUEST['time-start']);unset($_REQUEST['time-end']);
        }
        if(isset($_REQUEST['start'])&&isset($_REQUEST['end'])){
            $map['apply_time'] =array('BETWEEN',array(strtotime($_REQUEST['start']),strtotime($_REQUEST['end'])+24*60*60-1));
            unset($_REQUEST['start']);unset($_REQUEST['end']);
        }
        parent::lists(self::model_name,$_GET["p"],$map);
    }

    public function edit($id=null){
        $id || $this->error('请选择要编辑的用户！');
        $dis_data = I('post.');
        //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$dis_data['id'].'----'.$dis_data['dis_gameid'].'----'.$dis_data['dis_promoteid']);
        //print_r($dis_data);exit;
        //查看审核状态是否改变且为1
        $whereapp['id']=$dis_data['id'];
        $appdata = M('apply','tab_')->where($whereapp)->find();
        if(($appdata['status'] != $dis_data['status']) && $dis_data['status']==1){
            //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$appdata['status'].'----'.$dis_data['status']);
            //审核状态变为1   查看该渠道申请的该游戏是否添加了折扣
            $wheredis['promote_id'] = $dis_data['dis_promoteid'];
            $wheredis['game_id'] = $dis_data['dis_gameid'];
            $is = M('charge','tab_')->where($wheredis)->find();

            if(empty($is))
            {   
                $bind_discount = get_bind_discount($dis_data['dis_gameid']);

                if((!empty($bind_discount)) && $bind_discount>=3)
                {   //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$bind_discount);
                    $add['promote_id'] = $dis_data['dis_promoteid'];
                    $add['promote_name'] = get_promote_name($dis_data['dis_promoteid']);  
                    $add['game_id'] = $dis_data['dis_gameid'];
                    $add['game_name'] = get_game_name($dis_data['dis_gameid']);
                    $add['discount']  = $bind_discount;
                    $add['create_time'] = time();
                    //file_put_contents('/home/wwwroot/test.898sm.com/Data/bbbbb.log',$add['promote_name'].'---'.$add['game_name'].'---'.$add['discount']);
                    $result=M('charge','tab_')->add($add);
                    if($result===false)
                    {
                         $this->error('渠道折扣添加失败！！');
                    }
                }
                
            } 
        }
        
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::edit($model['id'],$id);
    }

    public function set_status($model='Apply'){
        parent::set_status($model);
    }

    public function del($model = null, $ids=null){
        $source = D(self::model_name);
        $id = array_unique((array)$ids);
        $map = array('id' => array('in', $id) );
        $list = $source->where($map)->select();
        foreach ($list as $key => $value) {
            $file_url = APP_ROOT.$value['pack_url'];
            unlink($file_url);
        }
        $model = M('Model')->getByName(self::model_name); /*通过Model名称获取Model完整信息*/
        parent::del($model["id"],$ids,"tab_");
    }

    function copyfiles($file1,$file2){ 
        $contentx =@file_get_contents($file1); 
        $openedfile = fopen($file2, "w"); 
        fwrite($openedfile, $contentx); 
        fclose($openedfile); 
        if ($contentx === FALSE) { 
            $status=false; 
        }else $status=true; 
        return $status; 
    }

    public function package($ids=null)
    {
        header("Content-Type:text/html;charset=utf-8");
        try{
            $ids || $this->error("打包数据不存在");
            $apply_data = D('Apply')->find($ids);
            //验证数据正确性
            if(empty($apply_data) || $apply_data["status"] != 1){$this->error("未审核或数据错误"); exit();}
            #获取原包数据
            $source_file = $this->game_source($apply_data["game_id"],1);
            //验证原包是否存在
            if(empty($source_file) || !file_exists($source_file['file_url'])){$this->error("游戏原包不存在"); exit();}
            //$files   = $_SERVER['DOCUMENT_ROOT'].$source_file['file_url'];
            $newname = "game_package" . $apply_data["game_id"] . "-" . $apply_data['promote_id'] . ".apk";
            #打包新路径
            $to      = "./Uploads/GamePack/" . $newname;
            $this->copyfiles($source_file['file_url'],$to);
            //copy($source_file['file_url'],$to);
            $zip = new \ZipArchive;
            $res = $zip->open($to, \ZipArchive::CREATE);//
            if ($res === TRUE) {
                $pack_data = array(
                    "game_id"    => $source_file["game_id"],
                    "game_name"  => $source_file['game_name'],
                    "game_appid" => get_game_appid($source_file["game_id"],"id"),
                    "promote_id" => $apply_data['promote_id'],
                    "promote_account" => $apply_data["promote_account"],
                );
                //var_dump(111111);exit;
                //var_dump($source_file['game_name']);exit;
                $zip->addFromString('META-INF/mch.properties', json_encode($pack_data));
                $zip->close();
                $source  = $source_file['file_url'];
                switch (get_tool_status("oss_storage")) {
                    case 0://服务器
                        $promote = array('game_id'=>$apply_data['game_id'],'promote_id'=>$apply_data['promote_id']);
                        break;
                    case 1: //OSS
                        $newname = "game_package" . $apply_data["game_id"] . "-" . $apply_data['promote_id'] . ".apk";
                        $to = "http://".C("oss_storage.bucket") . "." . C("oss_storage.domain") . "/GamePak/" . $newname;
                        $updata['savename'] = $newname;
                        $updata['path'] = $files;
                        $promote = array('game_id'=>$apply_data['game_id'],'promote_id'=>$apply_data['promote_id']);
                        $this->upload_game_pak_oss($updata);
                        break;
                }
                 //var_dump(1111111);exit;
                $jieguo = $this->updateinfo($ids,$to,$apply_data);
                if($jieguo){
                    $this->success("成功");
                }
                else{
                    $this->error("操作失败");
                }
            } else {
                throw new \Exception('分包失败');
            }
        }
        catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }

    /**
    *上传到OSS
    */
    public function upload_game_pak_oss($return_data=null){
        /**
        * 根据Config配置，得到一个OssClient实例
        */
        try {
            Vendor('OSS.autoload');
            $ossClient = new \OSS\OssClient(C("oss_storage.accesskeyid"), C("oss_storage.accesskeysecr"), C("oss_storage.domain"));
        } catch (OssException $e) {
            $this->error($e->getMessage());
        }

        $bucket = C('oss_storage.bucket');
        $oss_file_path ="GamePak/". $return_data["savename"];
        $avatar = $return_data["path"];
        try {
         $this->multiuploadFile($ossClient,$bucket,$oss_file_path,$avatar);        
        return true;
        } catch (OssException $e) {
            /* 返回JSON数据 */
           $this->error($e->getMessage());
        }
    }

    /**
    *修改申请信息
    */
    public function updateinfo($id,$pack_url,$promote){
        $model = M('Apply',"tab_");
        $data['id'] = $id;
        $data['pack_url'] = $pack_url;
        $data['dow_url']  = '/index.php?s=/Home/Down/down_file/game_id/' . $promote['game_id'] . '/promote_id/' . $promote['promote_id'];
        $data['dispose_id'] = UID;
        $data['dispose_time'] = NOW_TIME;
        $res = $model->save($data);
        return $res;
    }

    public function game_source($game_id,$type){
        $model = D('Source');
        $map['game_id'] = $game_id;
        $map['type'] = $type;
        $data = $model->where($map)->find();
        return $data;
    }

    public function multiuploadFile($ossClient, $bucket,$url,$file){
        $file = __FILE__;
        $options = array();
        try{
            $ossClient->multiuploadFile($bucket, $url, $file, $options);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            return;
        }
    }
    /**
     *管理员审核通过后添加渠道游戏记录数据
     * @author 采蘑菇的巳寸
     */               
    public function add_promote_game(){
        $pro_game = M('Promote_game','tab_');
        $where['game_id'] = I('post.game_id');
        $where['promote_id'] = I('post.promote_id');
        $res = $pro_game->where($where)->getField('id');
        if($res){
            $data['status'] = 1;
            $data['msg'] = 'ok';
            $this->ajaxReturn($data);
        }else{
            $pro_game->create();
            $pro_where['id'] = I('post.promote_id');
            $promote_nickname = M('Promote','tab_')->where($pro_where)->getField('nickname');
            $pro_game->promote_nickname = $promote_nickname;
            $add_res = $pro_game->add();
            if($add_res){
                $data['status'] = 1;
                $data['msg'] = 'ok';
                $this->ajaxReturn($data);
            }else{
                $data['status'] = 0;
                $data['msg'] = '服务器故障';
                $this->ajaxReturn($data);
            }
        }
        
    }
}
