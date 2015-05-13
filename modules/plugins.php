<?php

function parse_plugin_info($f) {
	$info_l = trim(file_get_contents($f));
	$info = array();
	foreach (split("\n", $info_l) as $l) {
		$d = split(' ', $l);
		$info[$d[0]] = $d[1];
	}
	return $info;
}

function get_plugins() {
	$handle = opendir('plugins');
	if (!$handle) 
		die('cannot open dir');

	$plugins= array();
	while (false !== ($entry = readdir($handle))) {
		$cdir = 'plugins/'.$entry;
		$info_f = $cdir.'/plugin.info.txt';
		if ($entry != "." && $entry != ".." && file_exists($info_f)) {
			$info = parse_plugin_info($info_f);
			$plugins[] = array($info, $cdir);
		}
	}
	closedir($handle);

	return $plugins;
}

/*returns archive file name*/
function create_plugins_archive($plugins) {
	

	$plugins = get_plugins();
	
	$fname = '/tmp/plugins.tar';
	$data = array();
	$data = new PharData($fname, 0, null, Phar::TAR);
	foreach ($plugins as $plugin) {
		$queue = array();
		array_push($queue, $plugin[1]);
		$plugin_name = 'lib/plugins/'.$plugin[0]['name'];
		while (count($queue) > 0) {
			$dir = array_pop($queue);

			$handle = opendir($dir);
			if (!$handle) 
				die('cannot open dir');

			$plugins = array();
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != ".." && $entry != '.git') { 
					$file = $dir.'/'.$entry;
					if (is_dir($file))
						array_push($queue, $file);
					else {
						$t = split('/', $file);
						unset($t[0]);
						$t[1] = $plugin_name;
						$new_dir = join('/', $t);
						$data[$new_dir] = $file;
					}
				}
			}
		}
		closedir($handle);
	}

	return $fname;
}
