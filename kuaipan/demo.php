<?php
/**
 * api call demo
 */
//session_start ();
require_once dirname ( __FILE__ ) . '/sdk/kuaipan.class.php';
$config = include dirname ( __FILE__ ) . '/config.inc.php';
$kp = new Kuaipan($config['consumer_key'], $config['consumer_secret']);

$action = isset ( $_REQUEST ['action'] ) ? $_REQUEST ['action'] : null;
if (! is_null ( $action )) {
    $source_file = dirname ( __FILE__ ) . '/calls/' . str_replace ( '/', '_', $action ) . '.php';
    $result = array (
            'source' => '',
            'result' => '' 
    );
    if (file_exists ( $source_file )) {
        // php source
        $result ['source'] = 'api call file <br />' . highlight_file ( $source_file, true ) . '<br />' . 
                            'config.inc.php <br />' . highlight_file ( dirname ( __FILE__ ) . '/config.inc.php', true );
        // source file returned the api call result
        $r = include $source_file;
        $result ['result'] = var_export ( $r, true );
        
    }
    die ( json_encode ( $result ) );
}
$token = $kp->getAccessToken();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>快盘 OpenAPI DEMO</title>
</head>
<body>
    <p>
        <p style="color: red;">
            Notice: if didn't got a access token, you can get one from 
            <a href="index.php" >here</a>
        </p>
        access_token：
        <input id="access_token" value="<?php echo $token['oauth_token']?>" style="width: 300px;" />
        <br />
        <br /> call api:
        <select name="api_demo" class="call_demo">
            <option>account_info</option>
            <option>metadata</option>
            <option>shares</option>
            <option>fileops/create_folder</option>
            <option>fileops/delete</option>
            <option>fileops/move</option>
            <option>fileops/copy</option>
            <option>fileops/upload_file</option>
            <option>fileops/download_file</option>
        </select>
        <input type="button" id="call_again" value="call again" />
        <span id="call_tips" style="color: red; display: none;">calling api...</span>
    </p>
    <div>
        <p>
            Call result: <br />
            <textarea id="result" style="width: 50%; height: 300px;"></textarea>
        </p>
        <div style="border: 1px goldenrod dashed; width: 50%;">
            Source code:
            <pre id="source" style="font: 13px; padding: 5px;"></pre>
        </div>
    </div>
    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script>
            $().ready(function (){
                var action = function (){
                    var url = 'demo.php';
                    if ($('#access_token').val() == '') {
                        alert('pls input access token');
                        document.getElementById('access_token').focus();
                        return;
                    }
                    var data = {
                        'action':$('select option:selected').html(),
                        'access_token':$('#access_token').val()
                    };
                    $.ajax({
                        type: "post",
                        url: url,
                        data:data,
                        beforeSend:function () {
                            $('#call_tips').show();  
                            $('#result').val('');
                            $('#source').html('');
                        },
                        success: function(msg){
                            var json = eval('('+msg+')');
                            if (json.result) {
                                $('#result').val(json.result);
                            }
                            if (json.source) {
                                $('#source').html(json.source);
                            }
                            $('#call_tips').hide();  
                        }
                    });
                    return false;
                };
                $('.call_demo').change(action);
                $('#call_again').click(action);
            });
        </script>
</body>
</html>




