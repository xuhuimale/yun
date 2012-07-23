<?php

/**
 * 大文件上传, 命令行方式执行 
 *
 * php uploader <file> <dir_id> [force:0/1]
 *
 * @param string $argv[1] 文件的真实路径
 * @param string $argv[2] 要上传到微盘的目录id
 * @param string $argv[3] 是否强制重传. 1: 重传; 0: 断点续传; 默认为: 0
 * 
 * @author Bruce Chen
 *
 */

include_once("vDiskUpload.class.php");
$config = include('config.php');

if(count($argv) < 3)
{
	echo "\nError: wrong arguments\n\n";
	echo "-------------------------------------------------\n\n";
	echo "usage: php {$argv[0]} <file> <dir_id> [force:0/1]\n\n";
	exit;
}

$vdisk = new vDisk($config['app_key'], $config['app_secret']);
$vdisk->get_token($config['username'], $config['password']);
$uploader = new vDiskUpload($config['app_key'], $config['app_secret'], $vdisk->token);


$uploader->setDebugOn(); 								//调试模式
$uploader->setProgressOn(); 							//显示上传进度
$uploader->setProgressStep(1); 							//进度的Step
$uploader->setChunkSize(1024*1024*10);					//分隔大小
//$uploader->setProxy($host, $port, $user, $passwd);	//设置代理


//默认为续传, 如果要强制重新上传, 则设置第三个参数为: 1
if(!$uploader->upload($argv[1], $argv[2], $argv[3]))
{
	echo $uploader->errno(). " ". $uploader->error(). "\n";
} 
else 
{
	echo "OK\n";
}




?>
