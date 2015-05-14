<?php

function parse_plugin_info($info_l) {
	$info_l = trim($info_l);

	$info = array();
	foreach (split("\n", $info_l) as $l) {
		preg_match('/(^[a-z]*) *(.*$)/', $l, $d);
		$info[$d[1]] = $d[2];
	}
	return $info;
}

function get_plugins($plugins='all') {
	$handle = opendir('plugins');
	if (!$handle) 
		die('cannot open dir');

	$plugs = array();
	while (false !== ($entry = readdir($handle))) {
		$cdir = 'plugins/'.$entry;
		$info_f = $cdir.'/plugin.info.txt';
		if ($entry != "." && $entry != ".." && file_exists($info_f)) {
			$info = parse_plugin_info(file_get_contents($info_f));
			if ($plugins == 'all' || in_array($info['name'], $plugins)) 
				$plugs[] = array($info, $cdir);
		}
	}
	closedir($handle);

	return $plugs;
}

function get_plugins_ftp($conn_id) {

	$data = array();
	$plugins = ftp_nlist($conn_id, "lib/plugins");
	if (!$plugins)
		return array();

	foreach ($plugins as $plugin) {
		if (preg_match('/\.[a-z]*$/', $plugin))
			continue;
		$handle = fopen('php://memory', 'r+');
		$vfile = join('/', array($plugin, 'plugin.info.txt'));

		$result = ftp_fget($conn_id, $handle, $vfile, FTP_BINARY, 0);
		if (!$result)
			continue;

		fseek($handle, 0);
		$fstats = fstat($handle); 
		$info_c = fread($handle, $fstats['size']);
		$info = parse_plugin_info($info_c);

		$data[] = $info;

		fclose($handle);
	}

	return $data;
}

/*returns archive file name*/
function create_plugins_archive($plugins) {
	
	$plugins = get_plugins($plugins);
	
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
						$data[$new_dir] = file_get_contents($file);
					}
				}
			}
		}
		closedir($handle);
	}

	return $fname;
}
