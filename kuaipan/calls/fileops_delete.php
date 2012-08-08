<?php
/**
 * 删除一个文件
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */
try {
    $params = array (
            'root' => 'kuaipan',
            'path' => 'folder_to_delete' 
    );
    $ret = $kp->api ( 'fileops/delete', '', $params );
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

