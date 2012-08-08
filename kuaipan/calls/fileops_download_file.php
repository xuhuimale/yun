<?php
/**
 * 将网盘的logo_to_download.gif下载到本地，命名logo_to_download_<timestamp>.gif
 * 
 * 
 * $kp = new Kuaipan('consumer_key', 'consumer_secret');
 */
try {
    $params = array (
            'root' => 'kuaipan',
            'path' => 'logo_to_download.gif' 
    );
    // filename is the path save to local
    $filename = dirname ( __FILE__ ) . '/../resources/logo_to_download_' . time () . '.gif';
    $ret = $kp->api ( 'fileops/download_file', '', $params, 'GET', $filename );
    if (false === $ret) {
        $ret = $kp->getError ();
    } else {
        // save to local path
        $ret = array (
                'saved_path' => $filename 
        );
    }
    
    return $ret;
} catch ( Exception $e ) {
    error_log ( $e->getMessage () );
    return $ret = array (
            'exception' => $e->getMessage ()
    );
}

