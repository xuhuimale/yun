<?php
/**
 * 创建并获取一个文件或文件夹的分享链接
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */

try {
    $root_path = 'kuaipan/share_folder'; // 应用拥有整个快盘的权限，否则可以使用ap_folder
    $ret = $kp->api ( 'shares', $root_path, $params );
    if (false === $ret) {
        $ret = $kp->getError ();
    }
    return $ret;
} catch ( Exception $e ) {
    error_log ( $e->getMessage () );
    return $ret = array (
            'exception' => $e->getMessage ()
    );
}
