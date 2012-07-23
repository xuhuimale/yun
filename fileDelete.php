<?php

	require("./function.php"); 
	$fileIds = $_REQUEST["fileIds"];
	$fileIdArray = explode("~", $fileIds);
	// $result = array();
	$i = 0;
	foreach($fileIdArray as $oneFileId) {
		$file = $aService -> get_file_info($oneFileId);
		$deleteResult = $aService -> delete_file_or_dir($file);
		if($deleteResult["err_code"] != 0) {
			$result[$i++] = $file -> fileName;
		}
	}
	
	//$result = array("err_msg" => "测试信息");
	
	echo json_encode($result);
?>