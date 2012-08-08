<?php
/**
 * callback, for get access token
 */
session_start ();
require_once dirname ( __FILE__ ) . '/sdk/kuaipan.class.php';
$config = include dirname ( __FILE__ ) . '/config.inc.php';

$oauth_token = isset ( $_REQUEST ['oauth_token'] ) ? $_REQUEST ['oauth_token'] : '';
$oauth_verifier = isset ( $_REQUEST ['oauth_verifier'] ) ? $_REQUEST ['oauth_verifier'] : '';

$kp = new Kuaipan ( $config ['consumer_key'], $config ['consumer_secret'] );
if (! empty ( $oauth_token ) && ! empty ( $oauth_verifier )) {
    echo 'callback success';exit;
    $access_token = $kp->getAccessToken ( $oauth_token, $oauth_verifier );
    if (false == $access_token) {
        echo 'access token error' . '<br />';
        echo nl2br ( var_export ( $kp->getError () ) );
        exit ();
    } else {
        // after get access token
        header ( 'Location:demo.php' );
    }
} else {
    die ( 'not get oauth_token and  oauth_verifier' );
}

