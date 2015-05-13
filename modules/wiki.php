<?php

include "plugins.php";

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

function install_wiki($conn_id, $dir, $plugins) {
	//get avalible dokuwikis
	$dws = get_dokuwikis();

	//możnaby to posortować
	var_dump($dws);


	$result = ftp_put($conn_id, $dir.'/dokuwiki.tgz', $dws[0][1], FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload dw');

	$fname = create_plugins_archive($plugins);
	$result = ftp_put($conn_id, $dir.'/plugins.tar', $fname, FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload plugins');
	unlink($fname);
	

	$result = ftp_put($conn_id, $dir.'/index.php', 'dokuwikis/index.php', FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result)
		die('cannot upload index.php');

	return $dws[0][0];
}

function wiki_save($conn_id, $host, $dir, $user, $password, $usessl, $plugins) {
	$line = "$host $dir $user $password $usessl";

	$handle = fopen('php://memory', 'r+');

	$vfile = join(DIRECTORY_SEPARATOR, array($dir, 'VERSION'));

	$result = ftp_fget($conn_id, $handle, $vfile, FTP_BINARY, 0);
	//resource is not dokuwiki
	if (!$result) {
		$version = install_wiki($conn_id, $dir, $plugins);
	} else {
		$fstats = fstat($handle); 

		fseek($handle, 0);
		$dwv = split(' ', fread($handle, $fstats['size']));
		$version = $dwv[0];
	}

	
	fclose($handle);
}
