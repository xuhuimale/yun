<?php
require_once('pcs.class.php');

/* 只需修改这两个参数即可 */
$auth = array (
	'access_token' => '07149cef34a91851904278e5f757c3e5',
);
$app = 'pcstest_oauth';

$pcs = new BaiduPCS($auth);
$pcs->set_ssl(true);

$root_dir = '/apps' . '/' . $app;
$my_dir = $root_dir . '/' . 'first';

echo "\n";

/* 查询配额空间和已使用空间 */
if (!($data = $pcs->info_quota())) {
	var_dump($pcs->get_error_message());
	return;
} else {
    echo json_encode($data);
}

echo "\n";

/* 创建目录 */
if (!($data = $pcs->create_dir($my_dir))) {
	var_dump($pcs->get_error_message());
	return;
} else {
    echo json_encode($data);
}

echo "\n";

/* 查看目录信息 */
if (!($data = $pcs->meta_file($my_dir))) {
	var_dump($pcs->get_error_message());
	return;
} else {
    echo json_encode($data);
}

echo "\n";

/* 删除目录 */
if (!($data = $pcs->delete_file($my_dir))) {
	var_dump($pcs->get_error_message());
	return;
} else {
    echo json_encode($data);
}

echo "\n";

?>