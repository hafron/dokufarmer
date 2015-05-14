<?php

$wikidb = 'data/wikis';

function wikidb_find($host) {
	global $wikidb;

	$handle = fopen($wikidb, 'r');
	if (!$handle)
		die('cannot open wikidb file');

	while (($line = fgets($handle)) !== false) {
		$d = split(' ', $line);
		if ($d[0] == $host) {
			$ft = ftell($handle);
			fclose($handle);
			return $ft - strlen($line);
		}
	}

	fclose($handle);
	return -1;
}

function wikidb_add($data, $version) {
	global $wikidb;

	if (file_exists($wikidb))
		if (wikidb_find($data['host']) != -1) 
			die('you has already added that wiki');

	$handle = fopen($wikidb, 'a');
	if (!$handle)
		die('cannot open wikidb file');

	$line = "$data[host] $data[dir] $data[user] $data[password] $data[usessl] $version\n";
	fputs($handle, $line, strlen($line));

	fclose($handle);
}

function wikidb_get() {
	global $wikidb;

	$handle = fopen($wikidb, 'r');
	if (!$handle)
		die('cannot open wikidb file');

	$wikis = array();
	while (($line = fgets($handle)) !== false) {
		$data = array();
		$s = split(' ', $line);
		$data['host'] = $s[0];
		$data['dir'] = $s[1];
		$data['user'] = $s[2];
		$data['password'] = $s[3];
		$data['usessl'] = $s[4];
		$data['version'] = $s[5];

		$wikis[] = $data;
	}
	fclose($handle);
	return $wikis;
}
