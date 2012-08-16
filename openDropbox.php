<?php

$a = array('a','b');
$b = array('c', 'd');
$c = $a + $b;
var_dump($a);
var_dump(array_merge($a, $b));

$a = array(0 => 'a', 1 => 'b');
$b = array(0 => 'c', 1 => 'b');
$c = $a + $b;
var_dump($c);
var_dump(array_merge($a, $b));

$a = array('a', 'b');
$b = array('0' => 'c', 1 => 'b');
$c = $a + $b;
var_dump($c);
var_dump(array_merge($a, $b));

$a = array('aa0' => 'a', 'bb1' => 'b');
$b = array('00' => 'c', '1111' => 'b');
$c = $a + $b;
var_dump($c);
var_dump(array_merge($a, $b));

//// Require the bootstrap
//require_once('Dropbox/bootstrap.php');
//
//$rev = "e05e6ac34";
//$limit = 10000;
//$hash = false;
//$list = true;
//$deleted = false
//
//		//$call = "metadata/dropbox/";
//		$params = array(
//			'file_limit' => ($limit < 1) ? 1 : (($limit > 10000) ? 10000 : (int) $limit),
//			'hash' => (is_string($hash)) ? $hash : 0,
//			'list' => (int) $list,
//			'include_deleted' => (int) $deleted,
//			'rev' => (is_string($rev)) ? $rev : null,
//		);
//		$response = $dropbox->fetch('POST', self::API_URL, "metadata/dropbox/", $params);

?>