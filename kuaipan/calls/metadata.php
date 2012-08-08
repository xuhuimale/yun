<?php
/**
 * 获取用户快盘的根目录的信息
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */

try {
    $root_path = 'kuaipan'; // 应用拥有整个快盘的权限，否则可以使用ap_folder
    $ret = $kp->api ( 'metadata', $root_path, $params );
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

