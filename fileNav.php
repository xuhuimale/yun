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
	
</style>

			<!-- 文件导航 start -->
			<!-- 云服务logo图片 -->
			<a id="serviceLogo" style="float:left;width:32px;" title="回到根目录" href="/yun/frame.php?drive=<?php echo $aService -> serviceId; ?>">
				<IMG style="border:0px" SRC='<?php echo $aService -> logoUrl; ?>'></img>
			</a>
			<!-- 云服务名称 -->
			<div id="serviceName" style="float:left;text-align:left;"><?php echo $aService -> serviceName; ?></div>
			<!-- 当前位置，根目录时为空 -->
			<div id="currentDir">
				<?php 
				//echo $dir;
				$fileNavDirStruct = ($aService -> get_dirStruct($dir)); 
				// print_r($fileNavDirStruct);
				if($fileNavDirStruct != null && count($fileNavDirStruct) > 0) {
					$htmlFileNavDirStruct .= "<a href=\"".$fileNavDirStruct[0]->fileUrl."\" title=\"".$fileNavDirStruct[0]->fileName."\">\n";
					$htmlFileNavDirStruct .= "<span class=\"ellipsis\">".$fileNavDirStruct[0]->fileName."</span>\n";
					$htmlFileNavDirStruct .= "</a>\n";
					for($i = 1; $i < count($fileNavDirStruct); $i++) {
						$htmlFileNavDirStruct .= "<span style=\"float:left;\">&#160;&#160;-->&#160;&#160;</span>";
						$htmlFileNavDirStruct .= "<a href=\"".$fileNavDirStruct[$i]->fileUrl."\" title=\"".$fileNavDirStruct[$i]->fileName."\">\n";
						$htmlFileNavDirStruct .= "<span class=\"ellipsis\">".$fileNavDirStruct[$i]->fileName."</span>\n";
						$htmlFileNavDirStruct .= "</a>\n";
					}
					echo $htmlFileNavDirStruct;
				}
				?>
			</div>
			<!-- 文件导航 end -->


