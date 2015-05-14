<?php

include "modules/wiki.php";

$conn = array();
$conn['host'] = $_POST['host'];
$conn['dir'] = $_POST['dir'];
$conn['user'] = $_POST['user'];
$conn['password'] = $_POST['password'];
$conn['usessl'] = isset($_POST['usessl']) ? 1 : 0;
$plugins = isset($_POST['plugins']) ? array_keys($_POST['plugins']) : array();

$conn_id = wftp_connect($conn);
wiki_save($conn_id, $conn, $plugins);

ftp_close($conn_id);
