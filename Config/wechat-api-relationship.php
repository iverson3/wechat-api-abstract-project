<?php  

//+-------------------------------------------------
// 微信公众平台接口封装程序 - 微信接口映射关系配置
// 最重要的一个配置文件，它是实现部分自动化处理的关键
// 注意: 被调用最频繁的接口配置信息 要优先放在配置数组的前面
// @Author: wangfan
// @Date: 2015-07-09 20:47:00
//+-------------------------------------------------


return $config = array(
	array(
		'MsgType' => 'event',
		'Event'   => 'location',
		'comment' => '上报地理位置事件',
		'code' => 'event_location',
		'classname' => 'EventLocation',
		'para' => array('Event', 'Latitude', 'Longitude', 'Precision'),
		'default_response' => 'none',
		'all_response' => array('text', 'image')
	),
	array(
		'MsgType' => 'image',
		'Event'   => '',
		'comment' => '普通消息-图片消息',
		'code' => 'normal_image',
		'classname' => 'NormalImage',
		'para' => array('PicUrl', 'MediaId', 'MsgId'),
		'default_response' => 'image',
		'all_response' => array('text', 'image', 'news')
	)
);

?>