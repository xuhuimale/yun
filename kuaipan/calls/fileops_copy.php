<?php
/**
 * 复制from_folder到to_folder下面并命名为from_copy_folder
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */
try {
    $params = array (
            'root' => 'kuaipan',
            'from_path' => 'from_folder',
            'to_path' => 'to_folder/from_copy_folder' 
    );
    $ret = $kp->api ( 'fileops/copy', '', $params );
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
