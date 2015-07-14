<?php  

//+-----------------------------------------
// 微信公众平台接口封装程序 - Model模型基类
// @Author: wangfan
// @Date: 2015-07-09 10:45:00
//+-----------------------------------------

require_once "../../Tool/Tool.class.php";

abstract class Model {

	// 定义所有可能会出现在微信接口中的参数
	
	// 公共参数
	protected $ToUserName;      // 开发者微信号
	protected $FromUserName;    // 发送方帐号（一个OpenID）
	protected $CreateTime;      // 消息创建时间 （整型）
	protected $MsgType;         // 消息类型



	// 文本消息
	protected $Content;         // 文本内容
	protected $MsgId;           // 消息id，64位整型

	// 图片消息
	protected $PicUrl;          // 图片链接
	protected $MediaId;         // 图片消息媒体id，可以调用多媒体文件下载接口拉取数据
	// protected $MsgId;           // 消息id，64位整型
	
	// 语音消息 
	// protected $MediaId;         // 语音消息媒体id，可以调用多媒体文件下载接口拉取数据
	protected $Format;          // 语音格式，如amr，speex等
	// protected $MsgId;           // 消息id，64位整型
	
	// 视频消息
	// protected $MediaId;         // 视频消息媒体id，可以调用多媒体文件下载接口拉取数据
	protected $ThumbMediaId;    // 视频消息缩略图的媒体id，可以调用多媒体文件下载接口拉取数据
	// protected $MsgId;           // 消息id，64位整型
	
	// 地理位置消息 
	protected $Location_X;      // 地理位置纬度
	protected $Location_Y;      // 地理位置经度
	protected $Scale;           // 地图缩放大小
	protected $Label;           // 地理位置信息
	// protected $MsgId;           // 消息id，64位整型
	
	// 链接消息
	protected $Title;           // 消息标题
	protected $Description;     // 消息描述
	protected $Url;             // 消息链接
	// protected $MsgId;           // 消息id，64位整型

	// 关注/取消关注事件
	protected $Event;           // 事件类型，subscribe(订阅)、unsubscribe(取消订阅)

	// 扫描带参数二维码事件
	// protected $Event;           // 事件类型，subscribe
	protected $EventKey;        // 事件KEY值，qrscene_为前缀，后面为二维码的参数值
	protected $Ticket;          // 二维码的ticket，可用来换取二维码图片

	// 上报地理位置事件
	// protected $Event;           // 事件类型，LOCATION 
	protected $Latitude;        // 地理位置纬度
	protected $Longitude;       // 地理位置经度
	protected $Precision;       // 地理位置精度

	// 自定义菜单事件 - 点击菜单拉取消息时的事件推送
	// protected $Event;           // 事件类型，CLICK
	// protected $EventKey;        // 事件KEY值，与自定义菜单接口中KEY值对应

	// 自定义菜单事件 - 点击菜单跳转链接时的事件推送
	// protected $Event;           // 事件类型，VIEW
	// protected $EventKey;        // 事件KEY值，设置的跳转URL
	



	// 响应类型
	private $responsetype;
	// 可以使用的响应类型组 (数组)
	private $responselist;
	// 子Model类的类名
	protected $classname;



	// 所有可以使用的响应参数列表 (用户想进行响应就必须对它们的内容进行设置)
	protected $res_Content;
	protected $res_ArticleCount;
	protected $res_Articles;
	protected $res_Title;
	protected $res_Description;
	protected $res_PicUrl;  
	protected $res_Url;
	protected $res_MediaId;
	protected $res_MusicURL;
	protected $res_HQMusicUrl;
	protected $res_ThumbMediaId;
	// 公共的响应参数
	protected $res_MsgType;  
	protected $res_ToUserName;
	protected $res_FromUserName;
	protected $res_CreateTime;



	/**
	 * 构造函数
	 * 根据存在的请求参数对对应的成员变量进行赋值
	 */
	public function __construct(){
		$this->parseClassName();
		$config = $this->getWechatInterfaceInfo();
		// 根据类名得到此次请求的所有参数
		// 使用参数对对应的成员变量进行赋值
		for ($i = 0; $i < count($config['para']); $i++) { 
			$key = $config['para'][$i];
			$this->$key = $_SESSION[$key];
			// 接着unset() SESSION中的变量
			unset($_SESSION[$key]);
		}
		$this->responsetype = $config['default_response'];
		$this->responselist = $config['all_response'];
	}

	/**
	 * 解析得到Model子类的类名
	 */
	protected function parseClassName(){
		$classname = __CLASS__;
		// 对类名进行解析，得到关键部分(结合配置文件，能够映射得到该类处理的是微信的哪个接口功能)
		$pattern = "#(.*)Model\.class\.php#is";
		$arr = preg_match($pattern, $classname);
		$this->classname = $arr[0][1];
	}

	/**
	 * 获取当前Model子类所对应的微信接口信息
	 * @return array 对应的微信接口信息
	 */
	protected function getWechatInterfaceInfo(){
		$config = include '../../Config/wechat-api-relationship.php';
		for ($i = 0; $i < count($config); $i++) { 
			if ($config[$i]['classname'] == $this->classname) {
				// 返回对应的微信接口信息
				return $config[$i];
			}
		}
		return false;
	}

	/**
	 * 主要的业务和数据处理函数
	 * 这部分根据使用者的需求自己在子类中去具体实现
	 */
	public abstract function work();

	/**
	 * 响应函数
	 * 对微信用户的事件推送进行自动响应
	 */
	public function response(){
		// 目标：
		// 根据子类的类名自动判断响应的方式和数据格式(半自动，使用者也可以自己显示地进行设置)
		// (要想实现这个，就必须强制要求使用者在创建子类时，使用系统规定的格式)
		// 因为有的接口，可以使用多种方式进行响应，所以必须有一种默认的方式，然后使用者如果有显示的设置，则使用其设置的方式
		$classname = ucwords($this->responsetype)."Response".".class.php";
		require_once "./Response/".$classname;
		$response = new ucwords($this->responsetype)."Response";
		// 使用response对象的响应方法进行相应的响应处理
	}

	/**
	 * 设置响应类型
	 * 给使用者提供设置响应类型的接口
	 * 如不设置 则会使用各个微信接口默认的响应方式
	 */
	protected function setResponseType($responsetype){
		// 必须是合法的响应类型
		if (false !== Tool::getConfig('responsetype', $responsetype)) {
			// 设置的响应类型必须是配置中允许的类型
			if (in_array($responsetype, $this->responselist)) {
				$this->responsetype = $responsetype;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>