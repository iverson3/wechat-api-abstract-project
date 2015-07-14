<?php  

//+-----------------------------------------
// 微信公众平台接口封装程序 - 公共处理类
// @Author: wangfan
// @Date: 2015-07-09 10:34:00
//+-----------------------------------------

require_once "./Tool/Tool.class.php";

class Handler {

	private $paras;
	private $postStr = "";
	
	public function __construct($postStr){
		$this->postStr = $postStr;
	}

	/**
	 * 主要的控制处理方法
	 */
	public function run(){
		// 对xml数据进行解析
		$this->paras = Tool::xml_parser($this->postStr);
		$this->setGlobalParas();
		$distance = Tool::matchWechat($this->paras['MsgType'], isset($this->paras['Event'])? $this->paras['Event'] : '');
		if (false !== $distance) {
			// 通过调用"业务Model子类"的处理方法，完成需要的业务逻辑处理
			$distance->work();
			// 对微信客户端进行响应，各个微信接口的具体响应方法由配置文件决定 (使用者也可自己通过设置改变默认响应方式)
			$distance->response();
		} else {
			// 此次微信接口事件无法处理
			return false;
		}
	}

	/**
	 * 将请求参数存入SESSION中
	 */
	private function setGlobalParas(){
		// 将解析xml得到的参数全部添加到SESSION中, 供Model类在初始化时使用(为成员变量赋值)
		foreach ($this->paras as $key => $value) {
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * 处理微信对服务器可用性的验证
	 * 注意: 仅仅只在微信验证服务器是否正常时被调用
	 */
	public function valid() {
        $echoStr = $_GET["echostr"];  // 随机字符串
        if ($this->checkSignature()) {
        	echo $echoStr;
        	exit;
        }
    }

    /**
     * 验证签名是否正确
     * @return boolean 验证是否通过
     */
	private function checkSignature() {
        $signature = $_GET["signature"];  // 微信加密签名
        $timestamp = $_GET["timestamp"];
        $nonce     = $_GET["nonce"];      // 随机数
		$token  = Tool::getConfig('TOKEN');
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if ( $tmpStr == $signature ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 获取access_token
	 * access_token是公众号的全局唯一票据，公众号调用各接口时都需使用access_token
	 * access_token的有效期目前为2个小时，需定时刷新，重复获取将导致上次获取的access_token失效
	 */
	public function getAccessToken(){
		$config = include './Config/sys-conf.php';
		$url = $config['access_token_url']."grant_type=".$config['grant_type']."&appid=".$config['appid']."&secret=".$config['secret'];
		// 使用https协议进行调用
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		$data = curl_exec($curl);
		curl_close($curl);
		// json转array
		$data = json_decode($data);  
		$arr = Tool::object_array($data);
		// 获取access_token和expires_in，将其更新到配置文件
		Tool::updateConfig('access_token', $arr['access_token']);
		Tool::updateConfig('expires_in', $arr['expires_in'], 'int');
		return true;
	}

	/**
	 * 创建自定义菜单
	 * 必须保证之前已获取access_token且在有效期内
	 * @return boolean 菜单是否创建成功
	 */
	public function createMenu(){
		$config = include './Config/sys-conf.php';
		$menuData = file_get_contents("./Config/menu.json");  
		// 判断access_token是否已获取且是否在有效期内
		if ($config['access_token'] !== '') {
			$url = $config['create_menu_url']."access_token=".$config['access_token'];
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $menuData);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
			$data = curl_exec($curl);
			curl_close($curl);
			// json转array
			$data = json_decode($data);  
			$arr = Tool::object_array($data);
			// 判断菜单是否创建成功
			if ($arr['errcode'] == 0 && $arr['errmsg'] == 'ok') {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}



	// 显示供用户选择文件的界面
	public function selectfile(){
		include "./View/selectfile.html";
	}

    // 新增临时素材
	public function uploadFile(){
		$config = include './Config/sys-conf.php';
		$post_data = array(
			'file' => '@'.$_FILES['files']['tmp_name']
		);
		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$config['access_token']."&type=".$_FILES['files']['type'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_URL, $url);
		$info = curl_exec($ch);
		curl_close($ch);

		var_dump($info);
	}

}

?>