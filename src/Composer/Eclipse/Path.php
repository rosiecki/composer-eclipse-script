<?php
namespace Composer\Eclipse;

use Devlab\Utils\StringUtils;

class Path
{
	private $source;
	
	private $vendorPath;

	public function __construct($source, $vendorPath) {
		$this->source = $source;
		$this->vendorPath = $vendorPath;
	}

	public function isExists()
	{
		return is_dir($this->source);
	}

	public function isVendorSource()
	{
		return StringUtils::startsWith($this->source, $this->vendorPath);
	}

	public function vendorize() {
		$dirs = explode(DIRECTORY_SEPARATOR, $this->vendorPath);
		$vendorDirName = end($dirs); 
		return $vendorDirName.substr($this->source, strlen($this->vendorPath));
	}

}