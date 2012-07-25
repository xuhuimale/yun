<?php



// Require the bootstrap
require_once('Dropbox/bootstrap.php');

$rev = "e05e6ac34";
$limit = 10000;
$hash = false;
$list = true;
$deleted = false

		//$call = "metadata/dropbox/";
		$params = array(
			'file_limit' => ($limit < 1) ? 1 : (($limit > 10000) ? 10000 : (int) $limit),
			'hash' => (is_string($hash)) ? $hash : 0,
			'list' => (int) $list,
			'include_deleted' => (int) $deleted,
			'rev' => (is_string($rev)) ? $rev : null,
		);
		$response = $dropbox->fetch('POST', self::API_URL, "metadata/dropbox/", $params);

?>