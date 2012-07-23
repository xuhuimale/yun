<?

/**
 * 上传目录, 命令行方式执行 
 *
 * php uploader <dir> <dir_id>
 *
 * @param string $argv[1] 目录的真实路径
 * @param string $argv[2] 要上传到微盘的目录id
 * 
 * @author Bruce Chen
 *
 */

include_once("vDiskUpload.class.php");
$config = include('config.php');

if(count($argv) < 2)
{
	echo "\nError: wrong arguments\n\n";
	echo "-------------------------------------------------\n\n";
	echo "usage: php {$argv[0]} <dir> <dir_id> \n\n";
	exit;
}

$array = array();
ls_R($argv[1], &$array);

$vdisk = new vDisk($config['app_key'], $config['app_secret']);
$vdisk->get_token($config['username'], $config['password']);


foreach($array as $v)
{

	$the_dir_array = scandir($v);

	for($i=0; $i<count($the_dir_array); $i++)
	{
		if(is_file($v.'/'.$the_dir_array[$i]))
		{
			echo "Start upload: $v/$the_dir_array[$i] \n";
	
			$uploader = new vDiskUpload($config['app_key'], $config['app_secret'], $vdisk->token);
			//$uploader->setDebugOn(); 								//调试模式
			$uploader->setProgressOn(); 							//显示上传进度
			$uploader->setProgressStep(1); 							//进度的Step
			$uploader->setChunkSize(1024*1024);						//分隔大小
			//$uploader->setProxy($host, $port, $user, $passwd);	//设置代理
	
			//默认为续传, 如果要强制重新上传, 则设置第三个参数为: 1
			if(!$uploader->upload($v.'/'.$the_dir_array[$i], $argv[2]))
			{
				echo $uploader->errno(). " ". $uploader->error(). "\n";
			}
			else 
			{
				echo "OK\n";
			}
		}
		sleep(1);	
	}
}



function ls_R($the_dir_path, $array)
{
	if(!is_dir($the_dir_path))
		return false;
		
	$arr = scandir($the_dir_path);
	foreach($arr as $v)
	{
		if($v=='..' || $v=='.')
			continue;
		
		if(is_dir($the_dir_path.'/'.$v))
		{
			array_push($array, $the_dir_path.'/'.$v);
			ls_R($the_dir_path.'/'.$v, &$array);
		}
	}
}


?>