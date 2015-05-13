<?php

$handle = opendir('.');
if (!$handle) 
	die('cannot open dir');

$dw = '';
while (false !== ($entry = readdir($handle)))
		if (preg_match('/^dokuwiki*.\.tgz$/', $entry))
			$dw = $entry;

closedir($handle);

if ($dw == '')
	die('cannot find dokuwiki archive');


$phar = new PharData($dw);
$phar->decompress();

$pi = pathinfo($dw);
$dwtar = $pi['filename'].'.tar';

$phar = new PharData($dwtar);
$phar->extractTo('.', null, true);

unlink($dw);
unlink($dwtar);

$handle = opendir('dokuwiki');
if (!$handle) 
	die('cannot open dir');

$dws = array();
while (false !== ($entry = readdir($handle)))
	if ($entry != "." && $entry != "..") 
		rename('dokuwiki/'.$entry, $entry);
closedir($handle);


//plugins
$phar = new PharData(getcwd() . '/plugins.tar');
$phar->extractTo('.');
unlink('plugins.tar');

header('Location: install.php');
