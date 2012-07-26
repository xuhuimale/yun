<?php
  	require("./function.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title>个人云存储整合运营商</title>
	<meta name="Author" content="xuhui.male@gmail.com">
	<meta name="Keywords" content="">
	<meta name="Description" content="">
  
	<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.7.2.min.js"></script>
	<script src="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.19/jquery-ui.min.js"></script>
	<link type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.ui/1.8.19/themes/smoothness/jquery-ui.css" rel="stylesheet" />
 </head>

<style type="text/css">
	body,p,b,dl,dd,table,td,th,input,button,textarea,xmp,pre,img,form,div,ul,ol,li,h1,h2,h3,h4,h5,h6{margin:0;padding:0;}

	body{
		font:14px/1.14 Arial;
	}
	div {
		border:0px solid;
		background-color:white;
		float:left;
	}
    ul,li{
        list-style-type:none;
        padding:0;
        margin:0;
    }
    
	#main {
		width:960px;
		margin-left:auto;
		margin-right:auto;
		float:none;
	}
	#top {
		height:80px;
		width:960px;
		position:fixed;
		z-index:1;
		float:none;
		background:url('./image/top_bg.gif') left;
	}
	#logo {
		float:left;
		height:100%;
		width:160px;
		background-color:transparent;
	}
	#loginfo {
		float:right;
		height:100%;
		background-color:transparent;
	}
	#left {
		width:160px;
		margin-top:80px;
		margin-bottom:0px;
		margin-left:auto;
		margin-right:auto;
		float:left;
		position:fixed;
		z-index:1;
	}
	#used {
		height:60px;
	}

	#unused {
		height:60px;
	}
	
	#work {
		width:800px;
		margin-top:80px;
		margin-bottom:0px;
		margin-left:auto;
		margin-right:auto;
		float:right;
	}
	#fileNav {
		width:800px;
		height:50px;
		line-height:50px;
		margin-top:0px;
		margin-bottom:0px;
		position:fixed;
		z-index:1;
		overflow: hidden;
		text-align: center;
	}
	#toolBar {
		width:800px;
		height:50px;
		margin-top:50px;
		margin-bottom:0px;
		position:fixed;
		z-index:1;
	}
	#fileList {
		margin-top:100px;
		width:800px;
	}


	
	#toolBar li{ 
		list-style:none;
		padding-top:10px;
		padding-bottom:10px;
	}	

	.button, .button:visited {
		background: #222 url(overlay.png) repeat-x; 
		display: inline-block; 
		padding: 5px 10px 6px; 
		color: #fff; 
		text-decoration: none;
		-moz-border-radius: 6px; 
		-webkit-border-radius: 6px;
		-moz-box-shadow: 0 1px 3px rgba(0,0,0,0.6);
		-webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.6);
		text-shadow: 0 -1px 1px rgba(0,0,0,0.25);
		border-bottom: 1px solid rgba(0,0,0,0.25);
		position: relative;
		cursor: pointer
	}
 
	.button:hover							{ background-color: #111; color: #fff; }
	.button:active							{ top: 1px; }
	.small.button, .small.button:visited 	{ font-size: 11px}
	.button, .button:visited,
	.medium.button, .medium.button:visited 	{ font-size: 13px; 
											  font-weight: bold; 
											  line-height: 1; 
											  text-shadow: 0 -1px 1px rgba(0,0,0,0.25); 
											}
												  
	.large.button, .large.button:visited        { font-size: 14px; 
                                                    padding: 8px 14px 9px; }

	.super.button, .super.button:visited        { font-size: 34px; 
                                                    padding: 8px 14px 9px; }
	
	.pink.button, .magenta.button:visited       { background-color: #e22092; }
	.pink.button:hover                          { background-color: #c81e82; }
	.green.button, .green.button:visited        { background-color: #91bd09; }
	.green.button:hover                         { background-color: #749a02; }
	.red.button, .red.button:visited            { background-color: #e62727; }
	.red.button:hover                           { background-color: #cf2525; }
	.orange.button, .orange.button:visited      { background-color: #ff5c00; }
	.orange.button:hover                        { background-color: #d45500; }
	.blue.button, .blue.button:visited          { background-color: #2981e4; }
	.blue.button:hover                          { background-color: #2575cf; }
	.yellow.button, .yellow.button:visited      { background-color: #ffb515; }
	.yellow.button:hover                        { background-color: #fc9200; }


</style>

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
		</ul>
		<ul id="unused">
			<li><a href="javascript:alert('待添加');">阿里云</a></li>
			<li><a href="javascript:alert('待添加');">百度网盘</a></li>
			<li><a href="javascript:alert('待添加');">MicroSoft Skydrive</a></li>
			<li><a href="javascript:alert('待添加');">Google Drive</a></li>
			<li><a href="javascript:alert('待添加');">Amazon Cloud Drive</a></li>
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

