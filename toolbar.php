

<!-- 调整进度条的样式：使文字居中 -->
<style type="text/css">
.ui-progressbar { height:2em; line-height:2em; text-align: left; overflow: hidden;   background-image: none; background-color: transparent; color:#000000;}
.ui-progressbar .ui-progressbar-value {margin: -1px; height:100%; background-image: none; background-color: #91bd09;}
</style>


<?php 
	$r = $aService->get_quota(); 
	if($r['total'] != 0) {
		$toolbarUseRate = sprintf("%6.2f", $r['used']/$r['total']*100); 
	} else {
		$toolbarUseRate = 0;
	}
?>

<script>

function jsonToString (obj){   
        var THIS = this;    
        switch(typeof(obj)){   
            case 'string':   
                return '"' + obj.replace(/(["\\])/g, '\\$1') + '"';   
            case 'array':   
                return '[' + obj.map(THIS.jsonToString).join(',') + ']';   
            case 'object':   
                 if(obj instanceof Array){   
                    var strArr = [];   
                    var len = obj.length;   
                    for(var i=0; i<len; i++){   
                        strArr.push(THIS.jsonToString(obj[i]));   
                    }   
                    return '[' + strArr.join(',') + ']';   
                }else if(obj==null){   
                    return 'null';   
  
                }else{   
                    var string = [];   
                    for (var property in obj) string.push(THIS.jsonToString(property) + ':' + THIS.jsonToString(obj[property]));   
                    return '{' + string.join(',') + '}';   
                }   
            case 'number':   
                return obj;   
            case false:   
                return obj;   
        }   
    }
	


/** 使用率统计进度条初始化 */
$(function() {
	if($("#serviceId").val() != "") {
		$("#progressbar").show();
		$("#progressbar").progressbar({
			value: <?php echo $toolbarUseRate; ?>
		});
	}
});
</script>

				<!-- 空间统计、上传 -->
				<div id="spaceStatistic" style="float:left">
					<div style="width:300px;display:none;" id="progressbar">已使用<?php echo $toolbarUseRate; ?>%</div>
					<a class="large button green" id="btnUpload">上传</a>
				</div>

				
<!-- ALL jQuery Tools. No jQuery library -->
<script src="http://cdn.jquerytools.org/1.2.7/all/jquery.tools.min.js"></script>
<!-- 上传按钮 相关 -->
  <style>
    #uploadbox {
		/* overlay is hidden before loading */
		display:none;
		/* standard decorations */
		width:400px;
		border:10px solid #666;
		/* for modern browsers use semi-transparent color on the border. nice! */
		border:10px solid rgba(82, 82, 82, 0.698);
		/* hot CSS3 features for mozilla and webkit-based browsers (rounded borders) */
		-moz-border-radius:8px;
		-webkit-border-radius:8px;
    }

    #uploadbox div {
		padding:10px;
		float:none;
		border:1px solid #749A02;
		background-color:#fff;
		font-family:"lucida grande",tahoma,verdana,arial,sans-serif
    }

    #uploadbox h2 {
		margin:-11px;
		margin-bottom:0px;
		color:#fff;
		background-color:#91BD09;
		padding:5px 10px;
		border:1px solid #749A02;
		font-size:20px;
    }
	#uploadbox #uploadFileName {
		padding:0px;
	}
	#uploadbox #uploadProcess {
		border:0px;
		padding:0px;
	}
	#uploadbox .ui-progressbar-value { background-image: url(./image/pbar-ani.gif) !important; }

  </style>
  <!--[if IE]>
  <style type="text/css">
    .uploadbox {
	    background:transparent;
	    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#333333,endColorstr=#333333);
	    zoom: 1;
    }
  </style>
  <![endif]-->
<!-- upload dialog -->
<div id="uploadbox">
  <div>
    <h2>上传文件</h2>
    <p>
      单个文件最大支持10M。
    </p>
    <form ENCTYPE="multipart/form-data" method="post">
      <input type="file" onchange="doUploadFile(this)"/>
	  <div id="uploadFileName" style="display:none"></div>
	  <div id="uploadProcess" style="display:none"></div>
    </form>
    <p style="color:#666">
      To close, click the Close button or hit the ESC key.
    </p>
    <!-- yes/no buttons -->
    <p>
      <button class="close"> 取消上传 </button>
    </p>
  </div>
</div>

<script>

 /* 根据全路径获取文件名 */
 function getFileName(str){ 
  var reg = /[^\\\/]*[\\\/]+/g; 
  //xxx\或者是xxx/ 
  str = str.replace(reg,''); 
  return str; 
 }
 /* 选择文件后立即执行上传 */
 function doUploadFile(objFile){
	// 上传文件路径
	var filePath = $(objFile).val();
	// alert(filePath);
	
	// 上传文件文件名
	var fileName = getFileName(filePath);
	//alert(fileName);
	
	$(objFile).hide(); //隐藏上传文件框
	// 进度条
	$("#uploadFileName").text(fileName).show();
	$("#uploadProcess").show();
	
	// ajax上传
	var param = {"drive": $("input[type='hidden'][id='serviceId']").val(),
				 "dir":$("input[type='hidden'][id='dir']").val(),
				 "filePath":filePath};
	alert(param);
	$.getJSON("/yun/fileUpload.php", param, function(errCode){
		alert(jsonToString(errCode));
		//window.location.reload();
	});
 }
 
 
/* 上传按钮 */
$(document).ready(function() {
    $("#uploadProcess").progressbar({
      value: 100
    });


  // 上传按钮点击弹出对话框
  $("#btnUpload").click(function() {
	  // 清空已经选择的文件
	  var objFile = $("#uploadbox form input")[0];
	  objFile.outerHTML=objFile.outerHTML;
	  //alert(objFile.outerHTML);

	  $("#uploadbox form input").show();
      $("#uploadbox").overlay().load();
  });


  $("#main").append(document.getElementById("uploadbox").outerHTML);
  $("#uploadbox").remove();

    // select the overlay element - and "make it an overlay"
  $("#uploadbox").overlay({
    // custom top position
    top: 180,
    // some mask tweaks suitable for facebox-looking dialogs
    mask: {
		// you might also consider a "transparent" color for the mask
		color: '#fff',
		// load mask a little faster
		loadSpeed: 200,
		// very transparent
		opacity: 0.5
    },
    // disable this for modal dialog-type of overlays
    closeOnClick: false,
    // load it immediately after the construction
    load: false

  });


});
</script>
				
				

<!-- “新建文件夹”按钮 相关 -->
  <style>
    #newdirbox {
		/* overlay is hidden before loading */
		display:none;
		/* standard decorations */
		width:400px;
		border:10px solid #666;
		/* for modern browsers use semi-transparent color on the border. nice! */
		border:10px solid rgba(82, 82, 82, 0.698);
		/* hot CSS3 features for mozilla and webkit-based browsers (rounded borders) */
		-moz-border-radius:8px;
		-webkit-border-radius:8px;
    }

    #newdirbox div {
		padding:10px;
		float:none;
		border:1px solid #749A02;
		background-color:#fff;
		font-family:"lucida grande",tahoma,verdana,arial,sans-serif
    }

    #newdirbox h2 {
		margin:-11px;
		margin-bottom:0px;
		color:#fff;
		background-color:#91BD09;
		padding:5px 10px;
		border:1px solid #749A02;
		font-size:20px;
    }
  </style>
  <!--[if IE]>
  <style type="text/css">
    .newdirbox {
	    background:transparent;
	    filter:progid:DXImageTransform.Microsoft.gradient(startColorstr=#333333,endColorstr=#333333);
	    zoom: 1;
    }
  </style>
  <![endif]-->
<!-- newdirbox dialog -->
<div id="newdirbox">
  <div>
    <h2>新建文件夹</h2>
    <p>
      请输入文件夹名称
    </p>
    <!-- input form. you can press enter too -->
    <form>
      <input />
      <button type="submit"> 确定 </button>
      <button type="button" class="close"> 取消 </button>
    </form>
  </div>
</div>

<script>
/* 上传按钮 */
$(document).ready(function() {
	$("#btnNewdir").click(function() {
		$("#newdirbox").overlay().load();
	});


	$("#main").append(document.getElementById("newdirbox").outerHTML);
	$("#newdirbox").remove();

    // select the overlay element - and "make it an overlay"
	var newdirboxTrigger = $("#newdirbox").overlay({
		// custom top position
		top: 180,
		// some mask tweaks suitable for facebox-looking dialogs
		mask: {
			// you might also consider a "transparent" color for the mask
			color: '#fff',
			// load mask a little faster
			loadSpeed: 200,
			// very transparent
			opacity: 0.5
		},
		// disable this for modal dialog-type of overlays
		closeOnClick: false,
		// load it immediately after the construction
		load: false
	});

    $("#newdirbox form").submit(function(e) {
		// close the overlay
		//$("#newdirbox").overlay().close();
		// get user input	
		var input = $("input", this).val();
		// do something with the answer
		var param = {"drive": $("input[type='hidden'][id='serviceId']").val(),
				 "dir":$("input[type='hidden'][id='dir']").val(),
				 "create_name": input,
				 "dir_parent_id":$("input[type='hidden'][id='dir']").val()};
	    //alert(jsonToString(param));
		if(input != "") {
			$.getJSON("/yun/createDir.php", param, function(error){
				//alert(jsonToString(error));
				if(error.err_code != 0) {
					alert("新建时出错了，请尝试更换文件夹名称！\n" + error.err_msg);
				} else {
					window.location.reload();
				}
			});
		}
		// do not submit the form
		return e.preventDefault();
	});

});
</script>


<script>
/** 删除按钮的控制 */
$(function() {
	if($("#serviceId").val() != "") {
		/* 删除按钮的”监听“事件*/
		$("#btnDelete").bind("deleteTrigger", function (event) {
			var count = ($("#fileTable tbody input:checked").length);
			if(count > 0) {
				$(this).text("删除(" + count + ")");
			}else {
				$(this).text("删除");
			}
		});
	}

});

/* 删除按钮事件 */
function deleteFile() {
	var checkedBoxes = ($("#fileTable tbody input:checked"));
	var count = 0;
	var checkedLength = checkedBoxes.length;
	var fileIds = new Array();
	for (var i = 0; i < checkedLength; i++) {
		count++;
		fileIds.push(checkedBoxes[i].value);
	}
	//alert(fileIds.join("~"));
	
	var param = {"drive": $("input[type='hidden'][id='serviceId']").val(),
				 "dir":$("input[type='hidden'][id='dir']").val(),
				 "fileIds":fileIds.join("~")};
	
//	$.getJSON("/yun/fileDelete.php", param, function(arrayErrFileNames){
//		if(arrayErrFileNames != null && arrayErrFileNames.length > 0) {
//			alert("文件删除失败，请重新操作！\n" + arrayErrFileNames.join(", "));
//		}
//		window.location.reload();//href="/yun/frame.php?drive="+$("input[type='hidden'][id='serviceId']").val()+"&dir="+$("input[type='hidden'][id='dir']").val();
//	});
	
	$.ajax({
        url: "/yun/fileDelete.php",
        async: false,
        dataType: "json",
        data: param,
        success: function(arrayErrFileNames){
    		if(arrayErrFileNames != null && arrayErrFileNames.length > 0) {
    			alert("文件删除失败，请重新操作！\n" + arrayErrFileNames.join(", "));
    		}
    		window.location.reload();//href="/yun/frame.php?drive="+$("input[type='hidden'][id='serviceId']").val()+"&dir="+$("input[type='hidden'][id='dir']").val();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert("异常：\n" + XMLHttpRequest.responseText);
            window.location.reload();
        }
    });

}

</script>

				
				<!-- 操作按钮 -->
				<div style="float:right">
					<a class="large button green" id="btnNewdir">新建文件夹</a>
					<a class="large button green" id="btnDelete" href="javascript:deleteFile();">删除</a>
					<a class="large button green">复制</a>
					<a class="large button green">移动</a>
				<!--<a class="large button green">下载</a>-->
				</div>
				