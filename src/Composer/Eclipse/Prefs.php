<?php

namespace Composer\Eclipse;


class Prefs {

	private $includes = array();
	
	public function __construct() {
		$this->includes = array();
	}
	
	public function addIncludePath($path) {
		array_push($this->includes, $path);
	}
	
	public function save($filename) {

		$data = "eclipse.preferences.version=1\n";
		$data .= "include_path=1;".implode('\u00050;', $this->includes). "\n";		
		file_put_contents($filename, $data);		
	}
	
	
}



