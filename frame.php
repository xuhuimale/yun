<?php
    session_start();
  	require("./function.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title>个人云存储整合运营商</title>
	<Meta http-equiv="Content-Type" Content="text/html; charset=UTF-8">
	<meta name="Author" content="xuhui.male@gmail.com">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
  
	<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.19/jquery-ui.min.js"></script>
	<link type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.19/themes/smoothness/jquery-ui.css" rel="stylesheet" />
	<link type="text/css" href="./style/base.css" rel="stylesheet" />
 </head>



 <body>
  <input type="hidden" id="serviceId" value="<?php echo $aService -> serviceId;?>"></input>
  <input type="hidden" id="dir" value="<?php echo $aService -> dir;?>"></input>
  <div id="main">
    <div id="top">
		<div id="logo"><a href="/yun/frame.php">个人云存储整合运营商</a></div>
		<div id="loginfo">退出</div>
	</div>    
	
	<div id="left">
		<ul id="used">
			<li><a href="/yun/frame.php?drive=dropbox">dropbox</a></li>
			<li><a href="/yun/frame.php?drive=sina">SINA微盘</a></li>
			<li><a href="/yun/frame.php?drive=kingsoft">金山网盘</a></li>
			<li><a href="/yun/frame.php?drive=baidu">百度网盘（api未完全开放）</a></li>
			<li><a href="/yun/frame.php?drive=microsoft">MicroSoft Skydrive</a></li>
		</ul>
		<ul id="unused">
			<li><a href="javascript:alert('待添加');">阿里云</a></li>
			<li><a href="javascript:alert('待添加');">Google Drive</a></li>
			<li><a href="javascript:alert('待添加');">Amazon Cloud Drive</a></li>
			<li><a href="/yun/frame.php?drive=huawei">华为网盘</a></li>
		</ul>
	</div>

	<div id="work">
		<div id="fileNav">
			<?php 
				include("./fileNav.php");
			?>
		</div>
		<div id="toolBar">
			<li>
			<?php 
				include("./toolbar.php");
			?>
			</li>
		</div>
		<div id="fileList">
			<?php 
				include("./fileList.php");
			?>
		</div>
	</div>

  </div>
 </body>
</html>

