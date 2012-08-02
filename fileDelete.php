<?php
	require("./function.php"); 
	$fileIds = $_REQUEST["fileIds"];
	$fileIdArray = explode("~", $fileIds);
	$result = "";
	$i = 0;
	foreach($fileIdArray as $oneFileId) {
		$file = $aService -> get_file_info($oneFileId);
		$deleteResult = $aService -> delete_file_or_dir($file);
		if($deleteResult["err_code"] != 0) {
			$result[$i++] = $file -> fileName;
		}
	}
	
	echo json_encode($result);
?>