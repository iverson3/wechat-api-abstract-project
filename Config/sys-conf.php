<?php 

//+---------------------------------------------
// 微信公众平台接口封装程序 - 系统参数配置信息
// @Author: wangfan
// @Date: 2015-07-09 10:22:00
//+---------------------------------------------

return $config = array(
	'TOKEN' => '',
	'access_token_url' => 'https://api.weixin.qq.com/cgi-bin/token?',
	'create_menu_url'  => 'https://api.weixin.qq.com/cgi-bin/menu/create?',
	'grant_type' => 'client_credential',
	'appid' => '',
	'secret' => '',
	'access_token' => '_bJ5giZjK4EG2eC52Xu2cJox4C7xOOf2Prkx9XF26JXNbc-vVoT3wq19iakbO999JUajTXYeS614s0_pMAQ4JZwCpapMkuVwErtWVEeMY44',
	'expires_in' => 0,
	'responsetype' => array(   // 合法的响应类型集
		'text',    // 文本
		'image',   // 图片
		'voice',   // 语音
		'video',   // 视频
		'music',   // 音乐
		'news'     // 图文
	),
	'responsefield' => array(  // 各种响应类型所对应需要的响应字段
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Content'),
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId'),
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId'),
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'MediaId', 'Title', 'Description'),
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'Title', 'Description', 'MusicURL', 'HQMusicUrl', 'ThumbMediaId'),
		array('ToUserName', 'FromUserName', 'CreateTime', 'MsgType', 'ArticleCount', 'Articles', 'Title', 'Description', 'PicUrl', 'Url')
	)
);

?>