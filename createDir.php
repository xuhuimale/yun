<?php

	require("./function.php"); 
	$create_name = $_REQUEST["create_name"];
	$dir_parent_id = $_REQUEST["dir_parent_id"];
	
	//echo "create_name=$create_name             dir_parent_id=$dir_parent_id";
	
	$result = $aService -> create_dir($create_name, $dir_parent_id);
	
	//print_r($result);
	
	echo json_encode($result);
?>