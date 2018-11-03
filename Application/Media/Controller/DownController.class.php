<?php

namespace Media\Controller;
use Think\Controller;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class DownController extends Controller {
	
	public function down_file($game_id=0,$type=1){
		$model = M('Game','tab_');
		$map['tab_game.id'] = $game_id;
		//$map['file_type'] = $type;
		$data = $model
        ->field('tab_game_source.*,tab_game.game_name,tab_game.game_address')
        ->join("left join tab_game_source on tab_game.id = tab_game_source.game_id")->where($map)->find();
        if(!varify_url($data['game_address'])){
            $this->down($data['file_url'],$data['game_name'],$game_id,$type);
        }
		else{
            Header("HTTP/1.1 303 See Other");
            Header("Location: ".$data['game_address']); 
        }
        /*M('Game','tab_')->where('id='.$game_id)->setInc('dow_num');
        $this->add_down_stat($game_id);*/
	}

    function access_url($url) {    
        if ($url=='') return false;    
        $fp = fopen($url, 'r') or exit('Open url faild!');    
        if($fp){  
        while(!feof($fp)) {    
            $file.=fgets($fp)."";  
        }  
        fclose($fp);    
        }  
        return $file;  
    }
    public function down($file, $rename = NULL,$game_id,$type)
    {
        $sourceFile = $file; //要下载的临时文件名
        $outFile = $rename;
        $file_extension = strtolower(substr(strrchr($sourceFile, "."), 1)); //获取文件扩展名
        //检测文件是否存在
        if (!is_file($sourceFile)) {
            die("<b>404 File not found!</b>");
        }
        $where['game_name'] = $rename;
        M('game','tab_')->where($where)->setInc('dow_num',1);
        
        //添加官网下载记录
        $suser=session("member_auth");
        //print_r($suser);exit;
        $sdata['game_name']=$rename;
        $sdata['game_id']=$game_id;
        $sdata['user_id']= $suser['mid'];
        $sdata['user_account']= $suser['account'];
        $whereuser['id']=$suser['mid'];
        $udata=M('user','tab_')->where($whereuser)->field('promote_id,promote_account')->find();
        $sdata['promote_id']=$udata['promote_id'];
        $sdata['promote_account']=$udata['promote_account'];
        if ($type==3) {
            $sdata['down_way']=3;//web
        } else {
            $sdata['down_way']=0;//web
        }
        $sdata['create_time']= time();
        $downmodel=M('down_record','tab_')->add($sdata);


        $len = filesize($sourceFile); //获取文件大小
        $filename = basename($sourceFile); //获取文件名字

        $outFile_extension = $file_extension; //获取文件扩展名
        header("Content-Type:application/octet-stream"); //发送指定文件MIME类型的头信息
        header("Content-Disposition:attachment; filename=".$filename); //发送描述文件的头信息，附件和文件名
        header("Content-Length:".$len);
        Header("Location:".$file);
        exit;
        //根据扩展名 指出输出浏览器格式
        switch ($outFile_extension) {
            case "exe" :
                $ctype = "application/octet-stream";
                break;
            case "zip" :
                $ctype = "application/zip";
                break;
            case "mp3" :
                $ctype = "audio/mpeg";
                break;
            case "mpg" :
                $ctype = "video/mpeg";
                break;
            case "avi" :
                $ctype = "video/x-msvideo";
                break;
            default :
                $ctype = "application/force-download";
        }

        //Begin writing headers
        header("Cache-Control:");
        header("Cache-Control: public");

        //设置输出浏览器格式
        header("Content-Type: $ctype");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Accept-Ranges: bytes");
        $size = filesize($sourceFile);
        //如果有$_SERVER['HTTP_RANGE']参数
        if (isset ($_SERVER['HTTP_RANGE'])) {
            if (!preg_match('^bytes=\d*-\d*(,\d*-\d*)*$', $_SERVER['HTTP_RANGE'])) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header('Content-Range: bytes */' . $size); // Required in 416.
                exit;
            }

            $ranges = explode(',', substr($_SERVER['HTTP_RANGE'], 6));
            foreach ($ranges as $range) {
                $parts = explode('-', $range);
                $start = $parts[0]; // If this is empty, this should be 0.
                $end = $parts[1]; // If this is empty or greater than than filelength - 1, this should be filelength - 1.

                if ($start > $end) {
                    header("HTTP/1.1 206 Partial Content");
                    header("Content-Length: $new_length"); //输入总长
                    header("Content-Range: bytes $range$size2/$size"); //Content-Range: bytes 4908618-4988927/4988928   95%的时候
                    exit;
                }

                // ...
            }
        } else {
            //第一次连接
            $size2 = $size - 1;
            header("Content-Range: bytes 0-$size2/$size"); //Content-Range: bytes 0-4988927/4988928
            header("Content-Length: " . $size); //输出总长
        }
        //打开文件
        $fp = fopen("$sourceFile", "rb+");
        //设置指针位置
        fseek($fp, $range);
        //虚幻输出
        while (!feof($fp)) {
            //设置文件最长执行时间
            set_time_limit(0);
            print (fread($fp, 1024 * 8)); //输出文件
            flush(); //输出缓冲
            ob_flush();
        }
        fclose($fp);
        exit ();
    }
	/*public function down($file, $isLarge = false, $rename = NULL)
	{
		if(headers_sent())return false;
        if(!$file) {
            $this->error('文件不存在哦 亲!');
            //exit('Error 404:The file not found!');
        }
        if($rename==NULL){
            if(strpos($file, '/')===false && strpos($file, '\\')===false)
                $filename = $file;
            else{
                $filename = basename($file);
            }
        }else{
            $filename = $rename;
        }

        header('Content-Description: File Transfer.php');
        header("Content-Type: application/force-download;");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer.php-Encoding: binary");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: '.filesize($file));//$_SERVER['DOCUMENT_ROOT'].
        header("Pragma: no-cache"); //不缓存页面
        ob_clean();
        flush();
        if($isLarge)
            self::readfileChunked($file);
        else
            readfile($file);
    }*/

    /**
    *游戏下载统计
    */
    public function add_down_stat($game_id=null){
        $model = M('down_stat','tab_');
        $data['promote_id'] = 0;
        $data['game_id'] = $game_id;
        $data['number'] = 1;
        $data['type'] = 0;
        $data['create_time'] = NOW_TIME;
        $model->add($data);
    }
}
