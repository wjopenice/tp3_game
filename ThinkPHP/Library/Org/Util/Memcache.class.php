<?php
namespace Org\Util;
class Memcache {
	private $_memcache_obj;
	static $mc;

	public function __construct($prefix = '') {
		$this->connect($prefix);
	}

	public static function instance($prefix = '') {
		if (!self::$mc) {
			self::$mc = new Memcache($prefix);
		}

		return self::$mc;
	}
	
	public function connect($prefix = '') {
		$this->_memcache_obj = new \Think\Cache\Driver\Memcache(array('prefix'=>$prefix));
	}

	public function get($key) {
		$value = $this->_memcache_obj->get($key);
		return json_decode($value, true);
	}

	public function set($key, $value, $timeout = 3600) {
		return $this->_memcache_obj->set($key, json_encode($value), $timeout);
	}

	public function close() {
	}

	/**
	* 删除缓存
	* @access public
	* @param string $name 缓存变量名
	* @return boolean
	*/
	public function rm($key) {
		$this->_memcache_obj->rm($key);
	}

	/**
	* 清除缓存
	* @access public
	* @return boolean
	*/
	public function clear() {
		return $this->_memcache_obj->clear();
	}

    public function get_all_data(){
    	$host = '127.0.0.1';
      	$port = '11211';
       $mem=self::$mc;

       $mem->connect($host,$port);
       print_r($mem);
       $mem->set('key','value',1800);
       
       $items = $mem->getExtendedStats('items');
       $items=$items["$host:$port"]['items'];
       for($i=0,$len=count($items);$i<$len;$i++){
            $number=$items[$i]['number'];
          $str=$mem->getExtendedStats("cachedump",$number,0);
        $line=$str["$host:$port"];
        if( is_array($line) && count($line)>0){
             foreach($line as $key=>$value){
                echo $key.'=>';
                 print_r($mem->get($key));
                 echo "\r\n";
            }
         }
    }
    }

	public function __destruct() {
	}
}
