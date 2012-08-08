
<link type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/css/jquery.dataTables.css" rel="stylesheet" />

<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.0/jquery.dataTables.min.js"></script>
<script src="./FixedHeader-2.0.6/js/FixedHeader.min.js"></script>

<style type="text/css">
#fileTable {
	width:800px;
}
</style>

<script>
$(document).ready(function() {
	if($("#serviceId").val() != "") {
		$('#fileTable').show();

        /* 画表格方法 
		var oTable = $('#fileTable').dataTable({
			"aaSorting": [],
			//"bSort": false,
			"bPaginate": false,
			"bFilter": false,
			"bInfo": false,
			"aoColumns": [
				{ "bSortable": false, "sWidth":"18px" },
				null,
				// null,
				{"sWidth":"60px", "sSortDataType":"dom-filebytes", "sType":"numeric"},
				{"sWidth":"80px", "sType":"date"}
			]
		});*/
		/* 固定表格头的位置 */
		new FixedHeader( oTable, {
			offsetTop:180
		} ); 
		
		/* 修改文件大小一列的排序方法，按照实际的字节数进行排序。 */
        $.fn.dataTableExt.afnSortData['dom-filebytes'] = function ( oSettings, iColumn )
        {
            var aData = [];
            $( 'td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () {
                // alert(this.value);
        		var value = this.value == "" ? -1 : this.value;
        		// alert(value);
                aData.push( value );
            } );
            //alert(aData.join("~"));
            return aData;
        };
		
		/* 监听复选框的选中事件 */
		$("#fileTable input[name='fileId']").bind("checkEvent", function (event) {
			// 调用删除按钮的自定义事件，更新文字状态
			$("#btnDelete").trigger("deleteTrigger");
		});

		/* 列表上的复选框选中时，执行”复选框选中事件*/
		$("#fileTable input[name='fileId']").click( function () {
			$(this).trigger("checkEvent");
		});
	
	
	}
} );

	/*全选按钮的onclick事件*/
	function funFileListSelectAll(box) {
		//$("#fileTable input[name='fileId']").trigger("checkEvent");
		var ifSelAll = box.checked;
		$("#fileTable input[name='fileId']").each(function(){
			$(this).attr("checked",ifSelAll);
			$(this).trigger("checkEvent");
		});  
		//$("#btnDelete").trigger("myEvent");
	}
	
</script>


<!-- 文件列表 -->
<table cellpadding="0" cellspacing="0" border="0" class="display" id="fileTable">
	<thead>
		<tr>
			<th><input type="checkbox" id="fileListSelectAll" onclick="funFileListSelectAll(this);"></th>
			<th>文件名</th>
			<!--<th>文件类型</th>-->
			<th style="width:80px;">文件大小</th>
			<th style="width:80px;">添加时间</th>
		</tr>
	</thead>
	<tbody>
		<?php 
		$fileListTable = $aService -> get_list($dir); 
		foreach ($fileListTable as $aFile) {
			echo "<tr>\n";
			echo "    <td style=\"text-align:center;\">\n";
			echo "        <input type=\"checkbox\" name=\"fileId\" value=\"".$aFile -> fileId."\" fileName=\"".$aFile -> fileName."\">\n";
			echo "    </td>\n";
			echo "    <td><a target=\"_blank\" href=\"" . $aFile -> fileUrl . "\">" . $aFile -> fileName . "</a></td>\n";
			//echo "    <td>" . $aFile -> fileType . "</td>\n";
			echo "    <td bytes=".$aFile -> fileBytes." style=\"text-align:right;\">" . $aFile -> fileSize . "<input type=hidden value=".$aFile -> fileBytes."></td>\n";
			echo "    <td style=\"text-align:center;\">" . $aFile -> fileAddTime . "</td>\n";
			echo "</tr>\n";
		}
		?>
	</tbody>

</table>

