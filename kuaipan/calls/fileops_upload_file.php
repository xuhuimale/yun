<?php
/**
 * 将本地的log.gif上传到网盘根目录，并命名 logo_<timestamp>.gif
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */

try {
    // get upload url
    $upload = $kp->api ( 'fileops/upload_locate' );
    
    if (false !== $upload) {
        // file to upload
        $params = array (
                'root' => 'kuaipan',
                'overwrite' => 'true',
                'path' => 'logo' . time () . '.gif' 
        );
        $filename = dirname ( __FILE__ ) . '/../resources/logo.gif';
        // 传入upload的绝对地址
        $ret = $kp->api ( sprintf ( '%s/1/fileops/upload_file', rtrim ( $upload ['url'], '/' ) ), '', $params, 'POST', $filename );
        if (false === $ret) {
            $ret = $kp->getError ();
        }
    } else {
        $ret = $kp->getError ();
    }
    return $ret;
} catch ( Exception $e ) {
    error_log ( $e->getMessage () );
    return $ret = array (
            'exception' => $e->getMessage ()
    );
}


