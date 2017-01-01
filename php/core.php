<?php

require 'lib/config.php';
require 'lib/telebot.php';

// Daftar seluruh plugins
$plugin_dir = array_diff(scandir(__DIR__ .'/plugin'), array('..', '.'));
$plugin_name = array();

if (!empty($plugin_dir)){
	foreach ($plugin_dir as $plugin_file) {
		$pInfo = pathinfo($plugin_file);
		if(strtolower($pInfo['extension'])=='php'){
			require __DIR__.'/plugin/'.$plugin_file;
			$plugin_name[] = str_replace('.php', '', $plugin_file);
		}
	}
}

$bot = new Telebot(TOKEN);
