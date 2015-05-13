<?php

include "modules/wiki.php";

$host = $_POST['host'];
$dir = $_POST['dir'];
$user = $_POST['user'];
$password = $_POST['password'];
$usessl = isset($_POST['usessl']) ? 1 : 0;
$plugins = isset($_POST['plugins']) ? $_POST['plugins'] : array();

if ($usessl)
	$conn_id = ftp_ssl_connect($host);
else
	$conn_id = ftp_connect($host);

if (!$conn_id)
	die('cannot connect with server');

$login_result = ftp_login($conn_id, $user, $password);

if (!$login_result)
	die('cannot login');

//zapisz nowe połączenie
wiki_save($conn_id, $host, $dir, $user, $password, $usessl, $plugins);

ftp_close($conn_id);
