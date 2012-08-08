<?php
/**
 * 创建一个文件夹 (new_folder_<timestamp>)
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */
try {
    $params = array (
            'root' => 'kuaipan',
            'path' => 'new_folder_' . time () 
    );
    $ret = $kp->api ( 'fileops/create_folder', '', $params );
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
