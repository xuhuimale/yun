<?php
/**
 * 仅demo演示，未作任何安全考虑，不可用于生产环境
 */
session_start ();
require_once dirname ( __FILE__ ) . '/sdk/kuaipan.class.php';
$config = include dirname ( __FILE__ ) . '/config.inc.php';

$kp = new Kuaipan ( $config ['consumer_key'], $config ['consumer_secret'] );
$authorization_uri = $kp->getAuthorizationUri ( $config ['cb_uri'] );

if (false === $authorization_uri) {
    echo 'request token error' . '<br />';
    echo nl2br ( var_export ( $kp->getError () ) );
    exit ();
} else {
    header ( 'Location:' . $authorization_uri );
}
