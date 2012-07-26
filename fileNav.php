<style type="text/css">
	.ellipsis {
		display:block;
		width: 100px; 
		overflow: hidden; 
		white-space: nowrap;
		text-overflow:ellipsis;
		float:left;
		text-align:left;
	}
#breadcrumb
{
    background-image:url('./image/breadcrumb/bc_bg.png'); 
    background-repeat:repeat-x;
    height:30px;
    line-height:30px;
    color:#9b9b9b;
    border:solid 1px #cacaca;
    width:100%;
    overflow:hidden;
    margin:0px;
    padding:0px;
}
#breadcrumb li 
{
    list-style-type:none;
    float:left;
    padding-left:10px;
}
#breadcrumb a
{
    height:30px;
    display:block;
    background-image:url('./image/breadcrumb/bc_separator.png'); 
    background-repeat:no-repeat; 
    background-position:right;
    padding-right: 15px;
    text-decoration: none;
    color:#454545;
}
.home
{
    border:none;
    margin: 8px 0px;
}

#breadcrumb a:hover
{
	color:#35acc5;
}
</style>


			<!-- 文件导航 start -->
			<!-- 当前位置，根目录时为空 -->
			<div id="currentDir" style="width:100%">
			    <ul id="breadcrumb">
                    <li>
            			<!-- 云服务名称 -->
            			<!--<div id="serviceName" style="float:left;text-align:left;background-color:transparent;color:#454545;"><?php echo $aService -> serviceName; ?></div>-->
			            <a href="/yun/frame.php?drive=<?php echo $aService -> serviceId; ?>" title="Home"><img src="./image/breadcrumb/home.png" alt="Home" class="home" /></a>
                    </li>
				<?php 
				//echo $dir;
				$fileNavDirStruct = ($aService -> get_dirStruct($dir)); 
				// print_r($fileNavDirStruct);
				if($fileNavDirStruct != null && count($fileNavDirStruct) > 0) {
					for($i = 0; $i < count($fileNavDirStruct) - 1; $i++) {
						$htmlFileNavDirStruct .= "<li>";
						$htmlFileNavDirStruct .= "<a href=\"".$fileNavDirStruct[$i]->fileUrl."\" title=\"".$fileNavDirStruct[$i]->fileName."\">".$fileNavDirStruct[$i]->fileName;
						//$htmlFileNavDirStruct .= "<span class=\"ellipsis\">".$fileNavDirStruct[$i]->fileName."</span>\n";
						$htmlFileNavDirStruct .= "</a>";
						$htmlFileNavDirStruct .= "</li>\n";
					}
					$htmlFileNavDirStruct .= "<li>".$fileNavDirStruct[count($fileNavDirStruct)-1]->fileName."</li>\n";
					echo $htmlFileNavDirStruct;
				}
				?>
                </ul>
			</div>
			<!-- 文件导航 end -->


