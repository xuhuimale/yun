

	<link type="text/css" href="./style/breadcrumb.css" rel="stylesheet" />

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


