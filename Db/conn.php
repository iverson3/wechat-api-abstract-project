<?php  

//+-----------------------------------------
// 微信公众平台接口封装程序 - 数据库连接
// @Author: wangfan
// @Date: 2015-07-09 10:34:00
//+-----------------------------------------

// 包含配置信息
$config = include '../Config/db-conf.php';

@mysql_connect($config['host'], $config['username'], $config['password']);
mysql_select_db($config['database']);
mysql_query("set names ".$config['charset']);

?>