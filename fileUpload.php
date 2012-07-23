<?php

	require("./function.php"); 
	$file_path = $_REQUEST["file_path"];

	
	$result = $aService -> upload_file($file_path, $dir);

	echo json_encode($result);
?>