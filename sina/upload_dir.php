<?

/**
 * �ϴ�Ŀ¼, �����з�ʽִ�� 
 *
 * php uploader <dir> <dir_id>
 *
 * @param string $argv[1] Ŀ¼����ʵ·��
 * @param string $argv[2] Ҫ�ϴ���΢�̵�Ŀ¼id
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
			//$uploader->setDebugOn(); 								//����ģʽ
			$uploader->setProgressOn(); 							//��ʾ�ϴ�����
			$uploader->setProgressStep(1); 							//���ȵ�Step
			$uploader->setChunkSize(1024*1024);						//�ָ���С
			//$uploader->setProxy($host, $port, $user, $passwd);	//���ô���
	
			//Ĭ��Ϊ����, ���Ҫǿ�������ϴ�, �����õ���������Ϊ: 1
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