<?php  

//+------------------------------------------
// 微信公众平台接口封装程序 - 工具类
// 本类中的所有方法均以静态方法的方式提供
// @Author: wangfan
// @Date: 2015-07-09 10:01:00
//+------------------------------------------


class Tool {

	private $distance = false;

	/**
	 * 匹配微信接口
	 * @param string $MsgType MsgType
	 * @param string $Event   Event
	 * @return object         对象实例
	 */
	public static function matchWechat($MsgType, $Event = ''){
		// 根据微信接口类型参数等依据匹配到应该调用的Model子类
		$this->setInstance($MsgType, $Event);
		// 返回对应类的实例对象
		return $this->distance;
	}

	/**
	 * 匹配类名，再实例化类得到对象
	 * @param string $MsgType MsgType
	 * @param string $Event   Event
	 * @return boolean        
	 */
	private function setInstance($MsgType, $Event){
		$config = include '../Config/wechat-api-relationship.php';
		for ($i = 0; $i < count($config); $i++) { 
			if ($config[$i]['MsgType'] == $MsgType && $config[$i]['Event'] == $Event) {
				// 得到业务Model类名
				$classname = $config[$i]['classname']."Model";  
                if (file_exists("../Model/".$classname.".class.php")) {
                    require_once "../Model/".$classname.".class.php";
                    // 实例化业务Model类
                    $this->distance = new $classname;
                    return true;
                } else {
                    // 使用者没有创建该Model类 即不打算提供对应接口功能
                    return false;
                }
			}
		}
        // 如果遍历配置文件无法找到对应的Model类, 说明配置文件中没有定义该微信接口的配置信息
		return false;
	}

	/**
     * 解析XML格式的字符串
     * @param string $str xml字串
     * @return 解析正确就返回解析结果,否则返回false,说明字符串不是XML格式
     */
    public static function xml_parser($str){
        $xml_parser = xml_parser_create();
        if (!xml_parse($xml_parser, $str, true)) {
            xml_parser_free($xml_parser);
            return false;
        } else {
            return (json_decode(json_encode(simplexml_load_string($str)), true));
        }
    }

    /**
     * PHP要用json格式的数据，但通过json_decode()转出来的并不是标准的array
     * 所以需要用下面的自定义函数进行转换
     * @param  object $array 数组对象(非标准的array)
     * @return array         标准的数组
     */
    public static function object_array($array){
      	if(is_object($array)){
        	$array = (array)$array;
      	}
      	if(is_array($array)){
        	foreach($array as $key => $value){
          		$array[$key] = $this->object_array($value);
        	}
      	}
      	return $array;
    }

    /**
     * 获取指定的配置项内容
     * @param  string $key1 key1 (配置文件数组第一维key)
     * @param  string $key2 key2 (配置文件数组第二维key)
     * @return string/boolean 对应的配置项内容
     */
    public static function getConfig($key1, $key2 = null){
    	$config = include '../Config/sys-conf.php';
    	if ($key2 == null) {
    		if (array_key_exists($key1, $config)) {
    			return $config[$key1];
    		} else {
    			return false;
    		}
    	} else {
    		if (array_key_exists($key1, $config)) {
    			$tmp = $config[$key1];
    			if (array_key_exists($key2, $tmp)) {
    				return $tmp[$key2];
    			} else {
    				return false;
    			}
    		} else {
    			return false;
    		}
    	}
    }


    /**
     * 修改配置文件中指定配置项对应的值 (sys-conf.php)
     * @param  string $key   配置项
     * @param  mixed  $value 配置项对应的值
     * @param  string $type  配置项的值的变量类型(默认为string)
     * @return boolean       是否修改成功
     */
    public static function updateConfig($key, $value, $type = 'string'){
        $file = "../Config/sys-conf.php";
        $str  = file_get_contents($file); 
        $str2 = ""; 
        if ($type == "int") { 
            $str2 = preg_replace("/" . $key . "=>(.*),/", $key . "=>" . $value . ",", $str); 
        } else { 
            $str2 = preg_replace("/" . $key . "=>(.*),/", $key . "=>'" . $value . "',", $str); 
        }
        file_put_contents($file, $str2);
        return false;
    }

}

?>