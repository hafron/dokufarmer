<?php

include "plugins.php";
include "wikidb.php";

function wftp_connect($data) {
	if ($data['usessl'])
		$conn_id = ftp_ssl_connect($data['host']);
	else
		$conn_id = ftp_connect($data['host']);

	if (!$conn_id)
		die('cannot connect with server');

	$login_result = ftp_login($conn_id, $data['user'], $data['password']);

	if (!$login_result)
		die('cannot login');

	ftp_chdir($conn_id, $data['dir']);

	return $conn_id;
}

function get_dwarchive($version) {
	$handle = opendir('dokuwikis/'.$version);
	if (!$handle) 
		die('cannot open dir');

	//version => archive
	$dws = array();
	while (false !== ($entry = readdir($handle)))
		if ($entry != "." && $entry != "..") 
			return $entry;
	closedir($handle);
}

function get_dokuwikis() {
	$handle = opendir('dokuwikis');
	if (!$handle) 
		die('cannot open dir');

	//version => archive
	$dws = array();
	while (false !== ($entry = readdir($handle)))
		if ($entry != "." && $entry != ".." && $entry != "index.php") {
				$dws[] = array($entry, "dokuwikis/$entry/".get_dwarchive($entry));
		}
	closedir($handle);

	return $dws;
}

function install_wiki($conn_id, $plugins) {
	//get avalible dokuwikis
	$dws = get_dokuwikis();

	//możnaby to posortować
	var_dump($dws);

	$result = ftp_put($conn_id, 'dokuwiki.tgz', $dws[0][1], FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload dw');

	$fname = create_plugins_archive($plugins);
	$result = ftp_put($conn_id, 'plugins.tar', $fname, FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload plugins');
	unlink($fname);
	

	$result = ftp_put($conn_id, 'index.php', 'dokuwikis/index.php', FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload index.php');

	return $dws[0][0];
}

function wiki_save($conn_id, $data, $plugins) {


	$vfile = join(DIRECTORY_SEPARATOR, array($data['dir'], 'VERSION'));

	$handle = fopen('php://memory', 'r+');
	$result = ftp_fget($conn_id, $handle, $vfile, FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result) {
		$version = install_wiki($conn_id, $plugins);
	} else {
		$fstats = fstat($handle); 
		fseek($handle, 0);
		$dwv = split(' ', fread($handle, $fstats['size']));
		$version = $dwv[0];
	}
	fclose($handle);

	wikidb_add($data, $version);

	
}
