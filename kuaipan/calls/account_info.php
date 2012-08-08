<?php
/**
 * 查看用户信息
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */
try {
    $ret = $kp->api ( 'account_info' );
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
