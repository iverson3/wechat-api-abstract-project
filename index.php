<?php  

//+-----------------------------------------
// 微信公众平台接口封装程序 - 入口文件
// @Author: wangfan
// @Date: 2015-07-09 10:00:00
//+-----------------------------------------

require_once "./Handler.class.php";

// 判断是否属于自定义的特殊动作
if (isset($_GET['special_action']) || isset($_POST['special_action'])) {
	$handler = new Handler('');
	$special_action = isset($_GET['special_action'])? $_GET['special_action'] : $_POST['special_action'];
	if ($special_action == 'access_token') {   // 获取access_token
		$handler->getAccessToken();
	} else if ($special_action == 'createmenu') {  // 创建自定义菜单
		$res = $handler->createMenu();
		if ($res) {
			echo "result: menu is created successfully !";
		} else {
			echo "error: there appear a error, create menu failed";
		}
	} else if ($special_action == "uploadfile") {  // 上传素材
		$handler->selectfile();
	} else if ($special_action == 'do_upload') {   // 实现文件的上传
		$handler->uploadFile();
	}
	else {  // 错误的特殊参数
		echo 'error: wrong parameter';
	}
} else {
	// 获取http请求体中的post数据
	$postStr = file_get_contents('php://input');
	if (!empty($postStr)) {
		$handler = new Handler($postStr);
		// 判断是否属于服务器可用性验证请求
		if (!isset($_GET["echostr"])) {
			$handler->run();
		} else {
			// 进行服务器可用性验证
			$handler->valid();
		}
	} else {
		echo "error: no post data";
		exit;
	}
}

?>